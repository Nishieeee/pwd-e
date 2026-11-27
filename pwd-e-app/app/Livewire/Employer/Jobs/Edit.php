<?php

namespace App\Livewire\Employer\Jobs;

use App\Models\Accommodation;
use App\Models\EmployerBranch;
use App\Models\JobPosting;
use App\Models\LocationCity;
use App\Services\Language\ExclusionaryLanguageScanner;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Edit Job Posting')]
#[Layout('layouts.employer')]
class Edit extends Component
{
    public JobPosting $job;

    public string $title = '';

    public string $description = '';

    public string $employment_type = 'full_time';

    public string $work_arrangement = 'onsite';

    public ?int $employer_branch_id = null;

    public string $location = '';

    public string $street_address = '';

    public string $country = 'Philippines';

    public string $province = '';

    public string $city = '';

    public string $barangay = '';

    public string $zip_code = '';

    public ?string $salary_min = null;

    public ?string $salary_max = null;

    public ?string $closes_at = null;

    /** @var array<int, string> */
    public array $required_skills = [];

    public string $newSkill = '';

    /** @var array<int, int> */
    public array $selectedAccommodations = [];

    public bool $showScanModal = false;

    /** @var array<int, array{id: int, phrase: string, suggestion: string, category: string|null, occurrences: int}> */
    public array $scanFlags = [];

    public function mount(JobPosting $job): void
    {
        $user = Auth::user();

        if (! $user || ! $user->isEmployer()) {
            abort(403, 'Unauthorized. Employer access required.');
        }

        $profile = $user->employerProfile;

        if (! $profile || $job->employer_profile_id !== $profile->id) {
            abort(403, 'You do not have permission to edit this job posting.');
        }

        $this->job = $job;
        $this->title = $job->title;
        $this->description = $job->description ?? '';
        $this->employment_type = $job->employment_type ?? 'full_time';
        $this->work_arrangement = $job->work_arrangement ?? 'onsite';
        $this->employer_branch_id = $job->employer_branch_id;
        $this->location = $job->location ?? '';
        $this->street_address = $job->street_address ?? '';
        $this->country = $job->country ?: 'Philippines';
        $this->province = $job->province ?? '';
        $this->city = $job->city ?? '';
        $this->barangay = $job->barangay ?? '';
        $this->zip_code = $job->zip_code ?? '';
        $this->salary_min = $job->salary_min !== null ? (string) $job->salary_min : null;
        $this->salary_max = $job->salary_max !== null ? (string) $job->salary_max : null;
        $this->closes_at = $job->closes_at?->format('Y-m-d');
        $this->required_skills = $job->required_skills ?? [];
        $this->selectedAccommodations = $job->accommodations()->pluck('accommodations.id')->toArray();
    }

    public function updatedEmployerBranchId($branchId): void
    {
        if (! $branchId) {
            $this->employer_branch_id = null;
            return;
        }

        $employerProfile = Auth::user()?->employerProfile;
        if (! $employerProfile) {
            return;
        }

        $branch = EmployerBranch::where('employer_profile_id', $employerProfile->id)->find((int) $branchId);
        if ($branch) {
            $this->employer_branch_id = $branch->id;
            $this->street_address = (string) ($branch->street_address ?? '');
            $this->country = (string) ($branch->country ?: 'Philippines');
            $this->province = (string) ($branch->province ?? '');
            $this->city = (string) ($branch->city ?? '');
            $this->barangay = (string) ($branch->barangay ?? '');
            $this->zip_code = (string) ($branch->zip_code ?? '');
            $this->location = $branch->shortLocation() ?: $branch->name;
        }
    }

    public function updatedCity(string $value): void
    {
        if (Schema::hasTable('location_cities') && trim($value) !== '') {
            $cityRecord = LocationCity::where('name', 'like', trim($value))->first();
            if ($cityRecord) {
                $this->city = $cityRecord->name;
                if (! empty($cityRecord->province_name)) {
                    $this->province = $cityRecord->province_name;
                }
                if (! empty($cityRecord->zip_code)) {
                    $this->zip_code = $cityRecord->zip_code;
                }
            }
        }
        $this->syncChoppedLocation();
    }

    public function updatedProvince(): void
    {
        $this->syncChoppedLocation();
    }

    public function updatedBarangay(): void
    {
        $this->syncChoppedLocation();
    }

    private function syncChoppedLocation(): void
    {
        if (trim($this->city) !== '' || trim($this->province) !== '' || trim($this->barangay) !== '') {
            $this->location = implode(', ', array_filter([
                trim($this->barangay),
                trim($this->city),
                trim($this->province),
                trim($this->country) ?: 'Philippines',
            ]));
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        $isPublished = $this->job->status === 'published';

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'employment_type' => ['required', 'in:full_time,part_time,contract'],
            'work_arrangement' => ['required', 'in:onsite,remote,hybrid'],
            'employer_branch_id' => ['nullable', 'integer', 'exists:employer_branches,id'],
            'location' => [
                $this->work_arrangement === 'remote' ? 'nullable' : 'required',
                'string',
                'max:255',
            ],
            'street_address' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'barangay' => ['nullable', 'string', 'max:120'],
            'zip_code' => ['nullable', 'string', 'max:20'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'gte:salary_min'],
            'closes_at' => [
                'nullable',
                'date',
                $isPublished ? 'after_or_equal:today' : 'nullable',
            ],
            'required_skills' => ['nullable', 'array'],
            'required_skills.*' => ['string', 'max:50'],
            'selectedAccommodations' => [$isPublished ? 'required' : 'array', 'array', $isPublished ? 'min:1' : 'nullable'],
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

    public function saveChanges(bool $ignoreScanWarnings = false): void
    {
        $this->syncChoppedLocation();
        $this->validate();

        $scanner = app(ExclusionaryLanguageScanner::class);
        $fullText = $this->title.' '.$this->description;
        $scan = $scanner->scan($fullText);

        if ($scan['has_flags'] && ! $ignoreScanWarnings) {
            $this->scanFlags = $scan['flags'];
            $this->showScanModal = true;

            return;
        }

        $this->job->update([
            'employer_branch_id' => $this->employer_branch_id,
            'title' => $this->title,
            'description' => $this->description,
            'required_skills' => $this->required_skills,
            'location' => $this->location,
            'street_address' => trim($this->street_address) ?: null,
            'country' => trim($this->country) ?: 'Philippines',
            'province' => trim($this->province) ?: null,
            'city' => trim($this->city) ?: null,
            'barangay' => trim($this->barangay) ?: null,
            'zip_code' => trim($this->zip_code) ?: null,
            'work_arrangement' => $this->work_arrangement,
            'employment_type' => $this->employment_type,
            'salary_min' => filled($this->salary_min) ? (float) $this->salary_min : null,
            'salary_max' => filled($this->salary_max) ? (float) $this->salary_max : null,
            'closes_at' => filled($this->closes_at) ? $this->closes_at : null,
            'scanned_at' => now(),
            'scan_result' => $scan['flags'],
        ]);

        $this->job->accommodations()->sync($this->selectedAccommodations);

        Flux::toast(variant: 'success', text: __('Job posting updated successfully!'));

        $this->redirect(route('employer.jobs.index'));
    }

    public function applyReplacement(string $phrase, string $suggestion): void
    {
        $scanner = app(ExclusionaryLanguageScanner::class);

        $this->description = $scanner->applySuggestion($this->description, $phrase, $suggestion);
        $this->title = $scanner->applySuggestion($this->title, $phrase, $suggestion);

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

    public function dismissAndSave(): void
    {
        $this->showScanModal = false;
        $this->saveChanges(ignoreScanWarnings: true);
    }

    public function render(): View
    {
        $accommodationsByCategory = Accommodation::all()->groupBy('category');
        $branches = Auth::user()?->employerProfile?->branches()->get() ?? collect();

        $psgcProvinces = collect();
        $psgcCities = collect();
        if (Schema::hasTable('location_cities')) {
            $psgcCities = LocationCity::orderBy('name')->get(['name', 'province_name', 'zip_code']);
            $psgcProvinces = $psgcCities->pluck('province_name')->filter()->unique()->sort()->values();
        }

        $categoryLabels = [
            'mobility' => __('Mobility & Physical Accessibility'),
            'visual' => __('Visual & Screen Reader Accommodations'),
            'hearing' => __('Hearing & Deaf Support (e.g. FSL)'),
            'speech' => __('Speech & Communication Options'),
            'cognitive' => __('Cognitive & Neurodiversity Support'),
            'flexible_work' => __('Flexible Work & Scheduling'),
        ];

        return view('livewire.employer.jobs.edit', [
            'accommodationsByCategory' => $accommodationsByCategory,
            'categoryLabels' => $categoryLabels,
            'branches' => $branches,
            'psgcProvinces' => $psgcProvinces,
            'psgcCities' => $psgcCities,
        ]);
    }
}
