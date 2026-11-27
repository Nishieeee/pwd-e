<?php

namespace App\Livewire\Employer;

use App\Models\Accommodation;
use App\Models\EmployerBranch;
use App\Models\EmployerProfile;
use App\Models\LocationCity;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
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
    public string $company_type = '';
    public string $industry = '';
    public string $contact_phone = '';
    public string $location = '';
    public string $street_address = '';
    public string $country = 'Philippines';
    public string $province = '';
    public string $city = '';
    public string $barangay = '';
    public string $zip_code = '';
    public string $website = '';
    public string $about = '';
    public array $accessibility_highlights = [];
    public string $newHighlight = '';
    public array $selectedAccommodations = [];

    // Multi-Branch Form State
    public ?int $editingBranchId = null;
    public string $branchName = '';
    public string $branchStreet = '';
    public string $branchCountry = 'Philippines';
    public string $branchProvince = '';
    public string $branchCity = '';
    public string $branchBarangay = '';
    public string $branchZipCode = '';
    public string $branchPhone = '';
    public bool $branchIsHq = false;

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
        $this->company_type = (string) ($profile->company_type ?? '');
        $this->industry = (string) ($profile->industry ?? '');
        $this->contact_phone = (string) ($profile->contact_phone ?? $user->phone ?? '');
        $this->location = (string) ($profile->location ?? '');
        $this->street_address = (string) ($profile->street_address ?? '');
        $this->country = (string) ($profile->country ?: 'Philippines');
        $this->province = (string) ($profile->province ?? '');
        $this->city = (string) ($profile->city ?? '');
        $this->barangay = (string) ($profile->barangay ?? '');
        $this->zip_code = (string) ($profile->zip_code ?? '');
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
    }

    public function updatedBranchCity(string $value): void
    {
        if (Schema::hasTable('location_cities') && trim($value) !== '') {
            $cityRecord = LocationCity::where('name', 'like', trim($value))->first();
            if ($cityRecord) {
                $this->branchCity = $cityRecord->name;
                if (! empty($cityRecord->province_name)) {
                    $this->branchProvince = $cityRecord->province_name;
                }
                if (! empty($cityRecord->zip_code)) {
                    $this->branchZipCode = $cityRecord->zip_code;
                }
            }
        }
    }

    public function saveBranch(): void
    {
        $user = Auth::user();
        $profile = $user?->employerProfile;
        if (! $profile) {
            return;
        }

        $this->validate([
            'branchName' => 'required|string|max:150',
            'branchStreet' => 'nullable|string|max:255',
            'branchCountry' => 'nullable|string|max:100',
            'branchProvince' => 'nullable|string|max:120',
            'branchCity' => 'required|string|max:120',
            'branchBarangay' => 'nullable|string|max:120',
            'branchZipCode' => 'nullable|string|max:20',
            'branchPhone' => 'nullable|string|max:30',
        ]);

        if ($this->branchIsHq) {
            EmployerBranch::where('employer_profile_id', $profile->id)->update(['is_headquarters' => false]);
        }

        $attrs = [
            'name' => trim($this->branchName),
            'street_address' => trim($this->branchStreet) ?: null,
            'country' => trim($this->branchCountry) ?: 'Philippines',
            'province' => trim($this->branchProvince) ?: null,
            'city' => trim($this->branchCity),
            'barangay' => trim($this->branchBarangay) ?: null,
            'zip_code' => trim($this->branchZipCode) ?: null,
            'contact_phone' => trim($this->branchPhone) ?: null,
            'is_headquarters' => $this->branchIsHq || $profile->branches()->count() === 0,
        ];

        if ($this->editingBranchId) {
            $branch = EmployerBranch::where('employer_profile_id', $profile->id)->findOrFail($this->editingBranchId);
            $branch->update($attrs);
        } else {
            EmployerBranch::create(array_merge($attrs, ['employer_profile_id' => $profile->id]));
        }

        $this->resetBranchForm();
        session()->flash('profile_updated_banner', __('Company branch saved successfully!'));
    }

    public function editBranch(int $id): void
    {
        $profile = Auth::user()?->employerProfile;
        if (! $profile) {
            return;
        }

        $branch = EmployerBranch::where('employer_profile_id', $profile->id)->findOrFail($id);
        $this->editingBranchId = $branch->id;
        $this->branchName = $branch->name;
        $this->branchStreet = (string) ($branch->street_address ?? '');
        $this->branchCountry = (string) ($branch->country ?: 'Philippines');
        $this->branchProvince = (string) ($branch->province ?? '');
        $this->branchCity = (string) ($branch->city ?? '');
        $this->branchBarangay = (string) ($branch->barangay ?? '');
        $this->branchZipCode = (string) ($branch->zip_code ?? '');
        $this->branchPhone = (string) ($branch->contact_phone ?? '');
        $this->branchIsHq = (bool) $branch->is_headquarters;
    }

    public function deleteBranch(int $id): void
    {
        $profile = Auth::user()?->employerProfile;
        if (! $profile) {
            return;
        }

        EmployerBranch::where('employer_profile_id', $profile->id)->where('id', $id)->delete();
        if ($this->editingBranchId === $id) {
            $this->resetBranchForm();
        }
    }

    public function resetBranchForm(): void
    {
        $this->editingBranchId = null;
        $this->branchName = '';
        $this->branchStreet = '';
        $this->branchCountry = 'Philippines';
        $this->branchProvince = '';
        $this->branchCity = '';
        $this->branchBarangay = '';
        $this->branchZipCode = '';
        $this->branchPhone = '';
        $this->branchIsHq = false;
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
            'company_type' => 'nullable|string|max:100',
            'industry' => 'nullable|string|max:100',
            'contact_phone' => 'nullable|string|max:30',
            'location' => 'nullable|string|max:255',
            'street_address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:120',
            'city' => 'nullable|string|max:120',
            'barangay' => 'nullable|string|max:120',
            'zip_code' => 'nullable|string|max:20',
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

        $hasChopped = trim($this->city) !== '' || trim($this->province) !== '' || trim($this->barangay) !== '';
        $resolvedLocation = $hasChopped
            ? implode(', ', array_filter([trim($this->barangay), trim($this->city), trim($this->province), trim($this->country) ?: 'Philippines']))
            : (trim($this->location) ?: null);

        $profile->update([
            'company_name' => $this->company_name,
            'company_type' => trim($this->company_type) ?: null,
            'industry' => $this->industry ?: null,
            'contact_phone' => trim($this->contact_phone) ?: null,
            'location' => $resolvedLocation,
            'street_address' => trim($this->street_address) ?: null,
            'country' => trim($this->country) ?: 'Philippines',
            'province' => trim($this->province) ?: null,
            'city' => trim($this->city) ?: null,
            'barangay' => trim($this->barangay) ?: null,
            'zip_code' => trim($this->zip_code) ?: null,
            'website' => $this->website ?: null,
            'about' => $this->about ?: null,
            'accessibility_highlights' => ! empty($this->accessibility_highlights) ? array_values($this->accessibility_highlights) : null,
            'matrix_completed_at' => ! empty($this->selectedAccommodations) ? ($profile->matrix_completed_at ?? now()) : null,
        ]);

        $profile->accommodations()->sync($this->selectedAccommodations);

        $this->location = (string) ($resolvedLocation ?? '');
        $this->activeTab = 'overview';
        session()->flash('profile_updated_banner', __('Company profile and workplace accessibility matrix saved successfully!'));
    }

    public function render(): View
    {
        $user = Auth::user();
        $profile = $user->employerProfile->load([
            'accommodations',
            'branches',
            'jobPostings' => fn ($q) => $q->latest(),
        ]);

        $allAccommodationsByCategory = Accommodation::all()->groupBy('category');
        $activeJobCount = $profile->jobPostings->where('status', 'published')->count();
        $totalJobCount = $profile->jobPostings->count();

        $psgcProvinces = collect();
        $psgcCities = collect();
        if (Schema::hasTable('location_cities')) {
            $psgcCities = LocationCity::orderBy('name')->get(['name', 'province_name', 'zip_code']);
            $psgcProvinces = $psgcCities->pluck('province_name')->filter()->unique()->sort()->values();
        }

        return view('livewire.employer.profile', [
            'profile' => $profile,
            'branches' => $profile->branches,
            'psgcProvinces' => $psgcProvinces,
            'psgcCities' => $psgcCities,
            'allAccommodationsByCategory' => $allAccommodationsByCategory,
            'activeJobCount' => $activeJobCount,
            'totalJobCount' => $totalJobCount,
        ]);
    }
}
