<?php

namespace App\Livewire\Employer;

use App\Models\Accommodation;
use App\Models\EmployerProfile;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Company Profile & Accessibility Settings')]
#[Layout('layouts.employer')]
class Profile extends Component
{
    #[Url(as: 'tab')]
    public string $activeTab = 'overview';

    public string $company_name = '';
    public string $industry = '';
    public string $location = '';
    public string $website = '';
    public string $about = '';
    public array $accessibility_highlights = [];
    public string $newHighlight = '';
    public array $selectedAccommodations = [];

    public function mount(): void
    {
        $user = Auth::user();
        if (! $user || ! $user->isEmployer()) {
            abort(403, 'Unauthorized access.');
        }

        $profile = $user->employerProfile ?? EmployerProfile::create([
            'user_id' => $user->id,
            'company_name' => $user->name,
        ]);

        $this->company_name = (string) ($profile->company_name ?? '');
        $this->industry = (string) ($profile->industry ?? '');
        $this->location = (string) ($profile->location ?? '');
        $this->website = (string) ($profile->website ?? '');
        $this->about = (string) ($profile->about ?? '');
        $this->accessibility_highlights = is_array($profile->accessibility_highlights) ? $profile->accessibility_highlights : [];
        $this->selectedAccommodations = $profile->accommodations()->pluck('accommodations.id')->map(fn ($id) => (int) $id)->toArray();
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['overview', 'edit'], true)) {
            $this->activeTab = $tab;
        }
    }

    public function addHighlight(): void
    {
        $trimmed = trim($this->newHighlight);
        if ($trimmed !== '' && ! in_array($trimmed, $this->accessibility_highlights, true)) {
            $this->accessibility_highlights[] = $trimmed;
            $this->newHighlight = '';
        }
    }

    public function removeHighlight(int $index): void
    {
        if (isset($this->accessibility_highlights[$index])) {
            unset($this->accessibility_highlights[$index]);
            $this->accessibility_highlights = array_values($this->accessibility_highlights);
        }
    }

    public function save(): void
    {
        $user = Auth::user();
        if (! $user || ! $user->isEmployer()) {
            abort(403);
        }

        $this->validate([
            'company_name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'about' => 'nullable|string|max:5000',
            'accessibility_highlights' => 'nullable|array',
            'selectedAccommodations' => 'nullable|array',
            'selectedAccommodations.*' => 'integer|exists:accommodations,id',
        ]);

        $profile = $user->employerProfile ?? EmployerProfile::create([
            'user_id' => $user->id,
            'company_name' => $this->company_name,
        ]);

        $profile->update([
            'company_name' => $this->company_name,
            'industry' => $this->industry ?: null,
            'location' => $this->location ?: null,
            'website' => $this->website ?: null,
            'about' => $this->about ?: null,
            'accessibility_highlights' => ! empty($this->accessibility_highlights) ? array_values($this->accessibility_highlights) : null,
            'matrix_completed_at' => ! empty($this->selectedAccommodations) ? ($profile->matrix_completed_at ?? now()) : null,
        ]);

        $profile->accommodations()->sync($this->selectedAccommodations);

        $this->activeTab = 'overview';
        session()->flash('profile_updated_banner', __('Company profile and workplace accessibility matrix saved successfully!'));
    }

    public function render(): View
    {
        $user = Auth::user();
        $profile = $user->employerProfile->load([
            'accommodations',
            'jobPostings' => fn ($q) => $q->latest(),
        ]);

        $allAccommodationsByCategory = Accommodation::all()->groupBy('category');
        $activeJobCount = $profile->jobPostings->where('status', 'published')->count();
        $totalJobCount = $profile->jobPostings->count();

        return view('livewire.employer.profile', [
            'profile' => $profile,
            'allAccommodationsByCategory' => $allAccommodationsByCategory,
            'activeJobCount' => $activeJobCount,
            'totalJobCount' => $totalJobCount,
        ]);
    }
}
