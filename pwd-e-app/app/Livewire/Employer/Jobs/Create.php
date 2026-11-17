<?php

namespace App\Livewire\Employer\Jobs;

use App\Models\Accommodation;
use App\Models\JobPosting;
use App\Services\Language\ExclusionaryLanguageScanner;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Post a Job')]
class Create extends Component
{
    public string $title = '';

    public string $description = '';

    public string $employment_type = 'full_time';

    public string $work_arrangement = 'onsite';

    public string $location = '';

    public ?string $salary_min = null;

    public ?string $salary_max = null;

    public ?string $closes_at = null;

    /** @var array<int, string> */
    public array $required_skills = [];

    public string $newSkill = '';

    /** @var array<int, int> */
    public array $selectedAccommodations = [];

    // Language scanner modal state
    public bool $showScanModal = false;

    /** @var array<int, array{id: int, phrase: string, suggestion: string, category: string|null, occurrences: int}> */
    public array $scanFlags = [];

    public function mount(): void
    {
        $user = Auth::user();

        if (! $user || ! $user->isEmployer()) {
            abort(403, 'Unauthorized. Employer access required.');
        }

        // Pre-fill accommodations with the employer's default matrix if available
        $defaultAccommodations = $user->employerProfile?->accommodations()->pluck('accommodations.id')->toArray() ?? [];
        if (! empty($defaultAccommodations)) {
            $this->selectedAccommodations = $defaultAccommodations;
        }

        // Pre-fill location from company profile if available
        if ($user->employerProfile?->location) {
            $this->location = $user->employerProfile->location;
        }
    }

    /**
     * Validation rules for publishing a job.
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'employment_type' => ['required', 'in:full_time,part_time,contract'],
            'work_arrangement' => ['required', 'in:onsite,remote,hybrid'],
            'location' => [
                $this->work_arrangement === 'remote' ? 'nullable' : 'required',
                'string',
                'max:255',
            ],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'gte:salary_min'],
            'closes_at' => ['nullable', 'date', 'after:today'],
            'required_skills' => ['nullable', 'array'],
            'required_skills.*' => ['string', 'max:50'],
            'selectedAccommodations' => ['array'],
            'selectedAccommodations.*' => ['integer', 'exists:accommodations,id'],
        ];
    }

    public function addSkill(): void
    {
        $trimmed = trim($this->newSkill);

        if ($trimmed !== '' && ! in_array($trimmed, $this->required_skills, true)) {
            $this->required_skills[] = $trimmed;
        }

        $this->newSkill = '';
    }

    public function addSuggestedSkill(string $skill): void
    {
        if (! in_array($skill, $this->required_skills, true)) {
            $this->required_skills[] = $skill;
        }
    }

    public function removeSkill(int $index): void
    {
        unset($this->required_skills[$index]);
        $this->required_skills = array_values($this->required_skills);
    }

    public function saveDraft(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'employment_type' => ['required', 'in:full_time,part_time,contract'],
            'work_arrangement' => ['required', 'in:onsite,remote,hybrid'],
            'location' => ['nullable', 'string', 'max:255'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric'],
            'closes_at' => ['nullable', 'date'],
        ]);

        $employerProfile = Auth::user()->employerProfile;

        $job = JobPosting::create([
            'employer_profile_id' => $employerProfile->id,
            'title' => $this->title,
            'description' => $this->description ?: '',
            'required_skills' => $this->required_skills,
            'location' => $this->location,
            'work_arrangement' => $this->work_arrangement,
            'employment_type' => $this->employment_type,
            'salary_min' => filled($this->salary_min) ? (float) $this->salary_min : null,
            'salary_max' => filled($this->salary_max) ? (float) $this->salary_max : null,
            'closes_at' => filled($this->closes_at) ? $this->closes_at : null,
            'status' => 'draft',
        ]);

        if (! empty($this->selectedAccommodations)) {
            $job->accommodations()->sync($this->selectedAccommodations);
        }

        Flux::toast(variant: 'success', text: __('Job draft saved successfully.'));

        $this->redirect(route('employer.jobs.index'), navigate: true);
    }

    public function publish(bool $ignoreScanWarnings = false): void
    {
        $this->validate();

        $scanner = app(ExclusionaryLanguageScanner::class);
        $fullText = $this->title . ' ' . $this->description;
        $scan = $scanner->scan($fullText);

        // Check for exclusionary language
        if ($scan['has_flags'] && ! $ignoreScanWarnings) {
            $this->scanFlags = $scan['flags'];
            $this->showScanModal = true;

            return;
        }

        $employerProfile = Auth::user()->employerProfile;

        $job = JobPosting::create([
            'employer_profile_id' => $employerProfile->id,
            'title' => $this->title,
            'description' => $this->description,
            'required_skills' => $this->required_skills,
            'location' => $this->location,
            'work_arrangement' => $this->work_arrangement,
            'employment_type' => $this->employment_type,
            'salary_min' => filled($this->salary_min) ? (float) $this->salary_min : null,
            'salary_max' => filled($this->salary_max) ? (float) $this->salary_max : null,
            'closes_at' => filled($this->closes_at) ? $this->closes_at : null,
            'status' => 'published',
            'scanned_at' => now(),
            'published_at' => now(),
            'scan_result' => $scan['flags'],
        ]);

        if (! empty($this->selectedAccommodations)) {
            $job->accommodations()->sync($this->selectedAccommodations);

            // Mark matrix as complete if not set yet
            if (! $employerProfile->isMatrixComplete()) {
                $employerProfile->update(['matrix_completed_at' => now()]);
            }
        }

        Flux::toast(variant: 'success', text: __('Job posting published successfully!'));

        $this->redirect(route('employer.jobs.index'), navigate: true);
    }

    public function applyReplacement(string $phrase, string $suggestion): void
    {
        $scanner = app(ExclusionaryLanguageScanner::class);

        $this->description = $scanner->applySuggestion($this->description, $phrase, $suggestion);
        $this->title = $scanner->applySuggestion($this->title, $phrase, $suggestion);

        // Remove phrase from active scanFlags
        $this->scanFlags = array_values(array_filter(
            $this->scanFlags,
            fn ($flag) => strcasecmp($flag['phrase'], $phrase) !== 0
        ));

        Flux::toast(variant: 'success', text: __("Replaced ':phrase' with ':suggestion'.", ['phrase' => $phrase, 'suggestion' => $suggestion]));

        if (empty($this->scanFlags)) {
            $this->showScanModal = false;
        }
    }

    public function applyAllReplacements(): void
    {
        $scanner = app(ExclusionaryLanguageScanner::class);

        foreach ($this->scanFlags as $flag) {
            $this->description = $scanner->applySuggestion($this->description, $flag['phrase'], $flag['suggestion']);
            $this->title = $scanner->applySuggestion($this->title, $flag['phrase'], $flag['suggestion']);
        }

        $this->scanFlags = [];
        $this->showScanModal = false;

        Flux::toast(variant: 'success', text: __('All suggestions applied.'));
    }

    public function dismissAndPublish(): void
    {
        $this->showScanModal = false;
        $this->publish(ignoreScanWarnings: true);
    }

    public function render(): View
    {
        $accommodationsByCategory = Accommodation::all()->groupBy('category');

        $categoryLabels = [
            'mobility' => __('Mobility & Physical Accessibility'),
            'visual' => __('Visual & Screen Reader Accommodations'),
            'hearing' => __('Hearing & Deaf Support (e.g. FSL)'),
            'speech' => __('Speech & Communication Options'),
            'cognitive' => __('Cognitive & Neurodiversity Support'),
            'flexible_work' => __('Flexible Work & Scheduling'),
        ];

        return view('livewire.employer.jobs.create', [
            'accommodationsByCategory' => $accommodationsByCategory,
            'categoryLabels' => $categoryLabels,
        ]);
    }
}
