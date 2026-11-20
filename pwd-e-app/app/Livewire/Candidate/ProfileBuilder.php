<?php

namespace App\Livewire\Candidate;

use App\Models\AccessibilityNeed;
use App\Models\Accommodation;
use App\Models\CandidateCertification;
use App\Models\CandidateEducation;
use App\Models\CandidateProfile;
use App\Models\CandidateWorkExperience;
use App\Models\EducationLevel;
use App\Models\LanguageOption;
use App\Models\LocationBarangay;
use App\Models\LocationCity;
use App\Models\SkillOption;
use Carbon\Carbon;
use Database\Seeders\PsgcLocationSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.candidate')]
class ProfileBuilder extends Component
{
    use WithFileUploads;

    public bool $isEmbedded = false;

    public int $currentStep = 1;

    // Step 1: Input Mode
    public string $inputMode = 'voice'; // 'voice' or 'standard'

    public bool $isListening = true;

    // Step 2: Personal Identification
    public $avatarUpload = null;

    public ?string $avatarPath = null;

    public string $firstName = '';

    public string $middleName = '';

    public string $lastName = '';

    public string $suffix = '';

    public string $email = '';

    public string $phone = '';

    public string $pwdIdNumber = '';

    // Searchable Location Comboboxes (PSGC-backed)
    public string $country = '';

    public ?int $locationCityId = null;

    public string $city = '';

    public ?int $locationBarangayId = null;

    public string $barangay = '';

    public string $zipCode = '';

    // Spoken Languages
    public array $spokenLanguages = [];

    public bool $hasOtherLanguage = false;

    public string $otherLanguage = '';

    // Professional Summary
    public string $headline = '';

    public string $bio = '';

    // Work Experience (Start Year & End Year + Present toggle)
    public array $experiences = [];

    // Education (Highest Educational Attainment + strictly optional certificate)
    public string $highestEducation = 'Tertiary (College / University)';

    public string $institutionName = '';

    public string $yearGraduated = '';

    public $educationCertificateUpload = null;

    public ?string $educationCertificatePath = null;

    public string $educationCertificateFilename = '';

    // Skills (Toggleable pre-populated tags + Custom Skill input)
    public array $selectedSkills = [];

    public string $customSkillInput = '';

    // Step 3: Accommodations
    public array $selectedAccommodations = [];

    public string $accommodationNotes = '';

    // Step 4: Attachments (Auto-generated Resume, Optional FSL Video, Dedicated PWD ID Document)
    public $customResumeUpload = null;

    public $fslVideoUpload = null;

    public $pwdIdUpload = null;

    public string $resumeFilename = '';

    public string $resumeFileSize = '4 KB';

    public ?string $resumePdfPath = null;

    public string $fslVideoFilename = '';

    public string $fslVideoSize = '';

    public ?string $fslVideoPath = null;

    public string $pwdIdFilename = '';

    public string $pwdIdFileSize = '';

    public ?string $pwdIdPath = null;

    public bool $showResumePreviewModal = false;

    public bool $showVideoPreviewModal = false;

    public ?string $draftMessage = null;

    protected function ensureLocationTablesAndData(): void
    {
        if (! Schema::hasTable('location_cities') || ! Schema::hasColumn('location_cities', 'psgc_code')) {
            Artisan::call('migrate', ['--force' => true]);
        }

        if (Schema::hasTable('location_cities') && LocationCity::count() === 0) {
            app(PsgcLocationSeeder::class)->run();
        }

        if (Schema::hasTable('education_levels') && ! EducationLevel::where('name', 'Primary (Elementary)')->exists()) {
            $tieredEducation = [
                'Primary (Elementary)',
                'Secondary (High School)',
                'Tertiary (College / University)',
                'Vocational / Technical Certification',
            ];
            EducationLevel::whereNotIn('name', $tieredEducation)->delete();
            foreach ($tieredEducation as $idx => $eduName) {
                EducationLevel::updateOrCreate(['name' => $eduName], ['sort_order' => $idx + 1]);
            }
        }

        if (Schema::hasTable('skill_options') && ! SkillOption::where('name', 'Administrative Support')->exists()) {
            $inclusiveSkills = [
                ['name' => 'Data Entry', 'category' => 'general'],
                ['name' => 'Customer Service', 'category' => 'general'],
                ['name' => 'Administrative Support', 'category' => 'general'],
                ['name' => 'Cleaning', 'category' => 'general'],
                ['name' => 'Packaging & Sorting', 'category' => 'general'],
                ['name' => 'Bookkeeping', 'category' => 'general'],
                ['name' => 'QA Testing', 'category' => 'general'],
                ['name' => 'Office Tools', 'category' => 'general'],
                ['name' => 'Record Keeping', 'category' => 'general'],
                ['name' => 'Communication', 'category' => 'soft'],
                ['name' => 'Teamwork', 'category' => 'soft'],
                ['name' => 'Inventory Management', 'category' => 'general'],
            ];
            SkillOption::where('category', '!=', 'custom')
                ->whereNotIn('name', array_column($inclusiveSkills, 'name'))
                ->delete();
            foreach ($inclusiveSkills as $idx => $skillItem) {
                SkillOption::updateOrCreate(
                    ['name' => $skillItem['name']],
                    ['category' => $skillItem['category'], 'sort_order' => $idx + 1]
                );
            }
        }
    }

    public function mount(?int $step = null, bool $isEmbedded = false): void
    {
        $this->isEmbedded = $isEmbedded;
        $this->ensureLocationTablesAndData();

        $user = Auth::user();
        $profile = $user?->candidateProfile ?? CandidateProfile::firstOrCreate(['user_id' => $user->id]);

        if ($step && $step >= 1 && $step <= 5) {
            $this->currentStep = $step;
        }

        $this->inputMode = $profile->input_mode ?? 'voice';
        $this->avatarPath = $profile->avatar_path;

        $this->firstName = trim((string) ($user->first_name ?? explode(' ', (string) $user->name)[0] ?? ''));
        $this->middleName = trim((string) ($user->middle_name ?? ''));
        $this->lastName = trim((string) ($user->last_name ?? (explode(' ', (string) $user->name)[1] ?? '')));
        $this->suffix = trim((string) ($profile->suffix ?? ''));
        $this->email = trim((string) ($user->email ?? ''));
        $this->phone = trim((string) ($profile->phone ?? ''));

        // PWD ID Number: Auto-filled & read-only from authenticated user's account record
        $this->pwdIdNumber = trim((string) ($user->pwd_id_number ?? ''));

        // Location fields (dynamically resolved from PSGC database models)
        $cityRecord = $profile->locationCity
            ?? ($profile->city ? LocationCity::where('name', $profile->city)->first() : null)
            ?? LocationCity::orderBy('name')->first();

        $this->country = $profile->country ?: ($cityRecord?->country ?? '');
        $this->locationCityId = $cityRecord?->id;
        $this->city = $cityRecord?->name ?? ($profile->city ?? '');
        $this->zipCode = $cityRecord?->zip_code ?? ($profile->zip_code ?? '');

        $brgyRecord = $profile->locationBarangay
            ?? ($cityRecord && $profile->barangay
                ? LocationBarangay::where('location_city_id', $cityRecord->id)->where('name', $profile->barangay)->first()
                : null)
            ?? ($cityRecord ? LocationBarangay::where('location_city_id', $cityRecord->id)->orderBy('name')->first() : null);

        $this->locationBarangayId = $brgyRecord?->id;
        $this->barangay = $brgyRecord?->name ?? ($profile->barangay ?? '');

        // Spoken languages
        $this->spokenLanguages = is_array($profile->spoken_languages) ? array_values(array_filter($profile->spoken_languages)) : [];
        $this->otherLanguage = trim((string) ($profile->other_language ?? ''));
        $this->hasOtherLanguage = $this->otherLanguage !== '';

        // Headline & Bio
        $this->headline = trim((string) ($profile->headline ?? ''));
        $this->bio = trim((string) ($profile->bio ?? ''));

        // Work Experiences (Start Year & End Year + Present toggle)
        $existingExperiences = $profile->workExperiences()->get();
        if ($existingExperiences->isNotEmpty()) {
            $this->experiences = $existingExperiences->map(fn ($exp) => [
                'job_title' => trim((string) $exp->job_title),
                'company_name' => trim((string) $exp->company_name),
                'start_year' => $exp->start_date ? $exp->start_date->format('Y') : '',
                'end_year' => $exp->is_current ? '' : ($exp->end_date ? $exp->end_date->format('Y') : ''),
                'is_current' => (bool) $exp->is_current,
                'description' => trim((string) ($exp->description ?? '')),
            ])->toArray();
        } else {
            $this->experiences = [
                [
                    'job_title' => '',
                    'company_name' => '',
                    'start_year' => '',
                    'end_year' => '',
                    'is_current' => false,
                    'description' => '',
                ],
            ];
        }

        // Education (Certificate is strictly optional)
        $eduRecord = $profile->education()->first();
        $firstEducationLevel = EducationLevel::orderBy('sort_order')->first()?->name ?? 'Tertiary (College / University)';
        $this->highestEducation = $profile->highest_education ?: ($eduRecord?->degree ?? $firstEducationLevel);
        $this->institutionName = trim((string) ($eduRecord?->institution_name ?? ''));
        $this->yearGraduated = $eduRecord?->end_date ? $eduRecord->end_date->format('Y') : '';

        $certRecord = $profile->certifications()->first();
        if ($certRecord && ! empty($certRecord->file_path) && Storage::disk('public')->exists($certRecord->file_path)) {
            $this->educationCertificatePath = $certRecord->file_path;
            $this->educationCertificateFilename = basename($certRecord->file_path);
        }

        // Skills (ensure any saved custom skills are also present in SkillOption)
        $this->selectedSkills = is_array($profile->skills) ? array_values(array_filter(array_map('trim', $profile->skills))) : [];
        if (Schema::hasTable('skill_options')) {
            foreach ($this->selectedSkills as $savedSkill) {
                SkillOption::firstOrCreate(
                    ['name' => $savedSkill],
                    ['category' => 'custom', 'sort_order' => 99]
                );
            }
        }

        // Accommodations
        $savedNeeds = $profile->accessibilityNeeds()->pluck('accommodation_id')->map(fn ($id) => (int) $id)->toArray();
        $this->selectedAccommodations = $savedNeeds;
        $this->accommodationNotes = trim((string) ($profile->accommodation_notes ?? ''));

        // Attachments: Only mark optional FSL video or PWD ID file as attached if the file genuinely exists on storage
        $slugName = Str::slug($this->firstName.' '.$this->lastName, '_');
        $this->resumeFilename = ($slugName ?: 'Candidate').'_Resume.pdf';
        $this->resumePdfPath = $profile->resume_pdf_path;

        if (! empty($profile->fsl_video_path) && Storage::disk('public')->exists($profile->fsl_video_path)) {
            $this->fslVideoPath = $profile->fsl_video_path;
            $this->fslVideoFilename = basename($profile->fsl_video_path);
            $bytes = Storage::disk('public')->size($profile->fsl_video_path);
            $this->fslVideoSize = max(0.1, round($bytes / 1048576, 1)).' MB';
        } else {
            $this->fslVideoPath = null;
            $this->fslVideoFilename = '';
            $this->fslVideoSize = '';
        }

        if (! empty($user->pwd_id_path)) {
            $this->pwdIdPath = $user->pwd_id_path;
            $this->pwdIdFilename = basename($user->pwd_id_path);
            $bytes = 0;
            if (Storage::disk('private')->exists($user->pwd_id_path)) {
                $bytes = Storage::disk('private')->size($user->pwd_id_path);
            } elseif (Storage::disk('public')->exists($user->pwd_id_path)) {
                $bytes = Storage::disk('public')->size($user->pwd_id_path);
            }
            $this->pwdIdFileSize = $bytes > 0 ? max(1, (int) round($bytes / 1024)).' KB' : 'On File';
        } elseif (! empty($user->pwd_id_number)) {
            $this->pwdIdPath = null;
            $this->pwdIdFilename = 'Verified PWD ID (' . $user->pwd_id_number . ')';
            $this->pwdIdFileSize = 'Verified on Registration';
        } else {
            $this->pwdIdPath = null;
            $this->pwdIdFilename = '';
            $this->pwdIdFileSize = '';
        }

        if ($this->currentStep === 4) {
            $this->generateResumePdf();
        }
    }

    public function selectInputMode(string $mode): void
    {
        $this->inputMode = in_array($mode, ['voice', 'standard']) ? $mode : 'standard';
        $this->isListening = $this->inputMode === 'voice';
    }

    public function toggleListening(): void
    {
        $this->isListening = ! $this->isListening;
    }

    public function updatedCity(string $value): void
    {
        $cityRecord = LocationCity::where('name', 'like', trim($value))->first();
        if ($cityRecord) {
            $this->locationCityId = $cityRecord->id;
            $this->city = $cityRecord->name;
            $this->zipCode = $cityRecord->zip_code;

            $validBrgy = LocationBarangay::where('location_city_id', $cityRecord->id)
                ->where('name', $this->barangay)
                ->first();

            if ($validBrgy) {
                $this->locationBarangayId = $validBrgy->id;
            } else {
                $firstBrgy = LocationBarangay::where('location_city_id', $cityRecord->id)->orderBy('name')->first();
                $this->locationBarangayId = $firstBrgy?->id;
                $this->barangay = $firstBrgy?->name ?? '';
            }
        }
    }

    public function updatedBarangay(string $value): void
    {
        $query = LocationBarangay::where('name', 'like', trim($value));
        if ($this->locationCityId) {
            $query->where('location_city_id', $this->locationCityId);
        }
        $brgyRecord = $query->first();
        if ($brgyRecord) {
            $this->locationBarangayId = $brgyRecord->id;
            $this->barangay = $brgyRecord->name;
        }
    }

    public function selectCityOption(string $cityName): void
    {
        $this->city = $cityName;
        $this->updatedCity($cityName);
    }

    public function selectBarangayOption(string $barangayName): void
    {
        $this->barangay = $barangayName;
        $this->updatedBarangay($barangayName);
    }

    public function addExperience(): void
    {
        $this->experiences[] = [
            'job_title' => '',
            'company_name' => '',
            'start_year' => '',
            'end_year' => '',
            'is_current' => false,
            'description' => '',
        ];
    }

    public function removeExperience(int $index): void
    {
        if (count($this->experiences) > 1 && isset($this->experiences[$index])) {
            unset($this->experiences[$index]);
            $this->experiences = array_values($this->experiences);
        }
    }

    public function toggleExperienceCurrent(int $index): void
    {
        if (isset($this->experiences[$index])) {
            $this->experiences[$index]['is_current'] = ! ($this->experiences[$index]['is_current'] ?? false);
            if ($this->experiences[$index]['is_current']) {
                $this->experiences[$index]['end_year'] = '';
            }
        }
    }

    public function toggleSkill(string $skillName): void
    {
        if (in_array($skillName, $this->selectedSkills, true)) {
            $this->selectedSkills = array_values(array_diff($this->selectedSkills, [$skillName]));
        } else {
            $this->selectedSkills[] = $skillName;
        }
    }

    public function addCustomSkill(): void
    {
        $cleanSkill = trim((string) preg_replace('/\s+/', ' ', $this->customSkillInput));
        if ($cleanSkill === '') {
            return;
        }

        if (Schema::hasTable('skill_options')) {
            $maxOrder = (int) SkillOption::max('sort_order');
            SkillOption::firstOrCreate(
                ['name' => $cleanSkill],
                ['category' => 'custom', 'sort_order' => $maxOrder + 1]
            );
        }

        if (! in_array($cleanSkill, $this->selectedSkills, true)) {
            $this->selectedSkills[] = $cleanSkill;
        }

        $this->customSkillInput = '';
    }

    public function toggleAccommodation(int $accommodationId): void
    {
        if (in_array($accommodationId, $this->selectedAccommodations, true)) {
            $this->selectedAccommodations = array_values(array_diff($this->selectedAccommodations, [$accommodationId]));
        } else {
            $this->selectedAccommodations[] = $accommodationId;
        }
    }

    public function updatedCustomResumeUpload(): void
    {
        if ($this->customResumeUpload) {
            $filename = $this->customResumeUpload->getClientOriginalName();
            $this->resumePdfPath = $this->customResumeUpload->storeAs('resumes', $filename, 'public');
            $this->resumeFilename = $filename;
            $sizeKb = max(1, (int) round($this->customResumeUpload->getSize() / 1024));
            $this->resumeFileSize = $sizeKb.' KB';
        }
    }

    public function updatedFslVideoUpload(): void
    {
        if ($this->fslVideoUpload) {
            $filename = $this->fslVideoUpload->getClientOriginalName();
            $this->fslVideoPath = $this->fslVideoUpload->storeAs('videos/fsl', $filename, 'public');
            $this->fslVideoFilename = $filename;
            $sizeMb = max(0.1, round($this->fslVideoUpload->getSize() / 1048576, 1));
            $this->fslVideoSize = $sizeMb.' MB';
        }
    }

    public function removeAvatar(): void
    {
        if ($this->avatarPath && Storage::disk('public')->exists($this->avatarPath)) {
            Storage::disk('public')->delete($this->avatarPath);
        }
        $this->avatarUpload = null;
        $this->avatarPath = null;
    }

    public function removeFslVideo(): void
    {
        if ($this->fslVideoPath && Storage::disk('public')->exists($this->fslVideoPath)) {
            Storage::disk('public')->delete($this->fslVideoPath);
        }
        $this->fslVideoUpload = null;
        $this->fslVideoPath = null;
        $this->fslVideoFilename = '';
        $this->fslVideoSize = '';
    }

    public function updatedPwdIdUpload(): void
    {
        if ($this->pwdIdUpload) {
            $filename = $this->pwdIdUpload->getClientOriginalName();
            $this->pwdIdPath = $this->pwdIdUpload->storeAs('documents/pwd_ids', $filename, 'private');
            $this->pwdIdFilename = $filename;
            $sizeKb = max(1, (int) round($this->pwdIdUpload->getSize() / 1024));
            $this->pwdIdFileSize = $sizeKb.' KB';
        }
    }

    public function removePwdIdDocument(): void
    {
        if ($this->pwdIdPath) {
            if (Storage::disk('private')->exists($this->pwdIdPath)) {
                Storage::disk('private')->delete($this->pwdIdPath);
            } elseif (Storage::disk('public')->exists($this->pwdIdPath)) {
                Storage::disk('public')->delete($this->pwdIdPath);
            }
        }
        $this->pwdIdUpload = null;
        $this->pwdIdPath = null;
        $this->pwdIdFilename = '';
        $this->pwdIdFileSize = '';
    }

    public function updatedEducationCertificateUpload(): void
    {
        if ($this->educationCertificateUpload) {
            $filename = $this->educationCertificateUpload->getClientOriginalName();
            $this->educationCertificatePath = $this->educationCertificateUpload->storeAs('documents/certificates', $filename, 'public');
            $this->educationCertificateFilename = $filename;
        }
    }

    public function hasFslVideoAttached(): bool
    {
        return $this->fslVideoUpload !== null
            || (! empty($this->fslVideoPath) && (Storage::disk('private')->exists($this->fslVideoPath) || Storage::disk('public')->exists($this->fslVideoPath)));
    }

    public function hasPwdIdAttached(): bool
    {
        $user = Auth::user();

        return $this->pwdIdUpload !== null
            || ! empty($this->pwdIdPath)
            || ! empty($user?->pwd_id_path)
            || ! empty($user?->pwd_id_number);
    }

    public function getValidExperiences(): array
    {
        $valid = [];
        foreach ($this->experiences as $exp) {
            $title = trim((string) ($exp['job_title'] ?? ''));
            $company = trim((string) ($exp['company_name'] ?? ''));
            if ($title === '' && $company === '') {
                continue;
            }

            $startYear = preg_replace('/[^0-9]/', '', (string) ($exp['start_year'] ?? ''));
            $endYear = preg_replace('/[^0-9]/', '', (string) ($exp['end_year'] ?? ''));
            $isCurrent = ! empty($exp['is_current']);

            $valid[] = [
                'job_title' => $title,
                'company_name' => $company,
                'start_year' => strlen($startYear) === 4 ? $startYear : '',
                'end_year' => $isCurrent ? 'Present' : (strlen($endYear) === 4 ? $endYear : ''),
                'is_current' => $isCurrent,
                'description' => trim((string) ($exp['description'] ?? '')),
            ];
        }

        return $valid;
    }

    public function getEffectiveHeadline(): string
    {
        if (trim($this->headline) !== '') {
            return trim($this->headline);
        }

        foreach ($this->getValidExperiences() as $exp) {
            if ($exp['job_title'] !== '') {
                return $exp['job_title'];
            }
        }

        return 'Candidate Professional';
    }

    public function applyHeadlineFallback(): void
    {
        if (trim($this->headline) === '') {
            $this->headline = $this->getEffectiveHeadline();
        }
    }

    public function validateStep(int $step): void
    {
        if ($step === 1) {
            $this->validate([
                'inputMode' => ['required', 'in:voice,standard'],
            ], [
                'inputMode.required' => 'Please choose an input mode to continue.',
                'inputMode.in' => 'Please select a valid input mode.',
            ]);
        } elseif ($step === 2) {
            $this->applyHeadlineFallback();
            $this->validate([
                'firstName' => ['required', 'string', 'max:100'],
                'lastName' => ['required', 'string', 'max:100'],
                'email' => ['required', 'email', 'max:255'],
                'phone' => ['required', 'string', 'min:7', 'max:50'],
                'city' => ['required', 'string', 'max:100'],
                'middleName' => ['nullable', 'string', 'max:100'],
                'suffix' => ['nullable', 'string', 'max:50'],
                'country' => ['nullable', 'string', 'max:100'],
                'barangay' => ['nullable', 'string', 'max:100'],
                'headline' => ['nullable', 'string', 'max:255'],
                'bio' => ['nullable', 'string', 'max:2000'],
                'highestEducation' => ['required', 'string', 'max:255'],
            ], [
                'firstName.required' => 'Please enter your first name.',
                'lastName.required' => 'Please enter your last name.',
                'email.required' => 'Please enter your email address.',
                'email.email' => 'Please enter a valid email address.',
                'phone.required' => 'Please enter your contact phone number.',
                'phone.min' => 'Please enter a valid phone number (at least 7 digits).',
                'city.required' => 'Please select or enter your city or municipality.',
                'highestEducation.required' => 'Please select your educational attainment.',
            ]);
        }
    }

    public function goToStep(int $step): void
    {
        if ($step < 1 || $step > 5) {
            return;
        }

        if ($step > $this->currentStep) {
            try {
                for ($s = $this->currentStep; $s < $step; $s++) {
                    $this->validateStep($s);
                }
            } catch (\Illuminate\Validation\ValidationException $e) {
                $this->dispatch('profile-step-changed');
                throw $e;
            }
        }

        if ($this->currentStep === 2 || $step > 2) {
            $this->applyHeadlineFallback();
        }

        $this->currentStep = $step;

        if ($this->currentStep === 4) {
            $this->generateResumePdf();
        }

        $this->dispatch('profile-step-changed');
    }

    public function nextStep(): void
    {
        try {
            $this->validateStep($this->currentStep);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('profile-step-changed');
            throw $e;
        }

        if ($this->currentStep === 2) {
            $this->applyHeadlineFallback();
        }

        if ($this->currentStep < 5) {
            $this->currentStep++;
        }

        if ($this->currentStep === 4) {
            $this->generateResumePdf();
        }

        $this->dispatch('profile-step-changed');
    }

    public function prevStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }

        $this->dispatch('profile-step-changed');
    }

    public function generateResumePdf(): void
    {
        $this->applyHeadlineFallback();

        $fullName = trim(implode(' ', array_filter([$this->firstName, $this->middleName, $this->lastName, $this->suffix])));
        $slugName = Str::slug($this->firstName.'_'.$this->lastName, '_') ?: 'Candidate';
        $this->resumeFilename = $slugName.'_Resume.pdf';
        $relativePath = 'resumes/'.$this->resumeFilename;

        $accommodationsNames = Accommodation::whereIn('id', $this->selectedAccommodations)
            ->pluck('name')
            ->toArray();

        $languagesList = array_values(array_filter($this->spokenLanguages));
        if ($this->hasOtherLanguage && trim($this->otherLanguage) !== '') {
            $languagesList[] = trim($this->otherLanguage);
        }

        $locationParts = array_filter([
            $this->barangay ? 'Brgy. '.$this->barangay : null,
            $this->city,
            $this->country,
            $this->zipCode ? 'ZIP '.$this->zipCode : null,
        ]);

        $sections = [
            'name' => $fullName ?: 'Candidate Profile',
            'headline' => $this->getEffectiveHeadline(),
            'pwd_id' => $this->pwdIdNumber ? 'PWD ID: '.$this->pwdIdNumber.' (Verified)' : 'PWD Candidate Profile',
            'contact' => implode('  |  ', array_filter([$this->email, $this->phone, implode(', ', $locationParts)])),
            'languages' => ! empty($languagesList) ? 'Spoken Languages: '.implode(', ', $languagesList) : '',
            'summary' => trim($this->bio),
            'experiences' => $this->getValidExperiences(),
            'education' => [
                'level' => $this->highestEducation,
                'institution' => trim($this->institutionName),
                'year' => trim($this->yearGraduated),
            ],
            'skills' => array_values(array_filter($this->selectedSkills)),
            'accommodations' => $accommodationsNames,
        ];

        $pdfContent = $this->buildModernPdfDocument($sections);

        Storage::disk('public')->put($relativePath, $pdfContent);

        $this->resumePdfPath = $relativePath;
        $sizeKb = max(1, (int) round(strlen($pdfContent) / 1024));
        $this->resumeFileSize = $sizeKb.' KB';
    }

    protected function escapePdfText(string $text): string
    {
        $clean = preg_replace('/[^\x20-\x7E]/', ' ', $text);

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], (string) $clean);
    }

    protected function buildModernPdfDocument(array $sections): string
    {
        $ops = [];

        // 1. Modern Dark Navy Header Banner (x=40, y=685, w=532, h=70)
        $ops[] = '0.102 0.157 0.251 rg';
        $ops[] = '40 685 532 70 re f';

        // Candidate Name (White, Bold 18pt)
        $nameEsc = $this->escapePdfText(strtoupper($sections['name']));
        $ops[] = 'BT 1 1 1 rg /F2 17 Tf 56 728 Td ('.$nameEsc.') Tj ET';

        // Headline & PWD ID Badge (Light Sky Blue, Regular 10.5pt)
        $subHeader = $sections['headline'].'   |   '.$sections['pwd_id'];
        $subEsc = $this->escapePdfText($subHeader);
        $ops[] = 'BT 0.86 0.92 0.96 rg /F1 10 Tf 56 708 Td ('.$subEsc.') Tj ET';

        // 2. Contact Pill Bar (Light Tint Box x=40, y=652, w=532, h=30)
        $ops[] = '0.922 0.953 0.980 rg';
        $ops[] = '40 652 532 30 re f';

        $contactEsc = $this->escapePdfText($sections['contact']);
        $ops[] = 'BT 0.102 0.157 0.251 rg /F1 9 Tf 56 668 Td ('.$contactEsc.') Tj ET';

        if (! empty($sections['languages'])) {
            $langEsc = $this->escapePdfText($sections['languages']);
            $ops[] = 'BT 0.306 0.373 0.431 rg /F1 8.5 Tf 56 657 Td ('.$langEsc.') Tj ET';
        }

        $y = 628;

        $drawSectionHeader = function (string $title) use (&$ops, &$y) {
            $y -= 8;
            $titleEsc = $this->escapePdfText(strtoupper($title));
            $ops[] = "BT 0.141 0.310 0.451 rg /F2 10.5 Tf 44 {$y} Td ({$titleEsc}) Tj ET";
            $y -= 5;
            $ops[] = "0.82 0.86 0.90 RG 0.8 w 44 {$y} m 568 {$y} l S";
            $y -= 14;
        };

        $drawWrappedText = function (string $text, string $font = '/F1', float $size = 9.5, int $wrapWidth = 92) use (&$ops, &$y) {
            foreach (explode("\n", wordwrap($text, $wrapWidth, "\n")) as $line) {
                if ($y < 55) {
                    break;
                }
                $lineEsc = $this->escapePdfText(trim($line));
                if ($lineEsc === '') {
                    continue;
                }
                $ops[] = "BT 0.145 0.192 0.231 rg {$font} {$size} Tf 44 {$y} Td ({$lineEsc}) Tj ET";
                $y -= 13;
            }
        };

        // 3. Professional Summary
        if (! empty($sections['summary'])) {
            $drawSectionHeader('Professional Summary');
            $drawWrappedText($sections['summary']);
        }

        // 4. Work Experience
        if (! empty($sections['experiences'])) {
            $drawSectionHeader('Work Experience');
            foreach ($sections['experiences'] as $exp) {
                if ($y < 70) {
                    break;
                }
                $roleLine = trim($exp['job_title'].($exp['company_name'] ? '  —  '.$exp['company_name'] : ''));
                $period = trim(($exp['start_year'] ?: '').(($exp['start_year'] && $exp['end_year']) ? ' - ' : '').($exp['end_year'] ?: ''));
                if ($period !== '') {
                    $roleLine .= '   ('.$period.')';
                }
                $drawWrappedText($roleLine, '/F2', 10, 88);
                if (! empty($exp['description'])) {
                    $drawWrappedText($exp['description'], '/F1', 9.2, 92);
                }
                $y -= 4;
            }
        }

        // 5. Education
        $drawSectionHeader('Education');
        $eduLine = $sections['education']['level'];
        if (! empty($sections['education']['institution'])) {
            $eduLine .= '  —  '.$sections['education']['institution'];
        }
        if (! empty($sections['education']['year'])) {
            $eduLine .= ' (Graduated '.$sections['education']['year'].')';
        }
        $drawWrappedText($eduLine, '/F2', 9.8, 90);

        // 6. Skills & Strengths
        if (! empty($sections['skills'])) {
            $drawSectionHeader('Skills & Strengths');
            $drawWrappedText(implode('   •   ', $sections['skills']), '/F1', 9.5, 90);
        }

        // 7. Workplace Accommodations
        if (! empty($sections['accommodations'])) {
            $drawSectionHeader('Workplace Accommodations');
            $drawWrappedText(implode('   •   ', $sections['accommodations']), '/F1', 9.5, 90);
        }

        $streamData = implode("\n", $ops);
        $streamLength = strlen($streamData);

        $objects = [];
        $objects[1] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $objects[2] = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $objects[3] = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R /F2 6 0 R >> >> >>\nendobj\n";
        $objects[4] = "4 0 obj\n<< /Length {$streamLength} >>\nstream\n{$streamData}\nendstream\nendobj\n";
        $objects[5] = "5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";
        $objects[6] = "6 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>\nendobj\n";

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $num => $objContent) {
            $offsets[$num] = strlen($pdf);
            $pdf .= $objContent;
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 7\n0000000000 65535 f \n";
        for ($i = 1; $i <= 6; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= "trailer\n<< /Size 7 /Root 1 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF";

        return $pdf;
    }

    public function downloadResume()
    {
        $this->generateResumePdf();

        return Storage::disk('public')->download($this->resumePdfPath, $this->resumeFilename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function previewResume(): void
    {
        $this->generateResumePdf();
        $this->showResumePreviewModal = true;
    }

    public function closeResumePreviewModal(): void
    {
        $this->showResumePreviewModal = false;
    }

    public function previewVideo(): void
    {
        if ($this->hasFslVideoAttached()) {
            $this->showVideoPreviewModal = true;
        }
    }

    public function closeVideoPreviewModal(): void
    {
        $this->showVideoPreviewModal = false;
    }

    public function persistProfileData(): CandidateProfile
    {
        $this->applyHeadlineFallback();
        $this->generateResumePdf();

        $user = Auth::user();

        if ($this->avatarUpload) {
            $this->avatarPath = $this->avatarUpload->store('avatars', 'public');
        }

        if ($this->customResumeUpload) {
            $this->updatedCustomResumeUpload();
        }

        if ($this->fslVideoUpload) {
            $this->updatedFslVideoUpload();
        }

        if ($this->pwdIdUpload) {
            $this->updatedPwdIdUpload();
        }

        if ($this->educationCertificateUpload) {
            $this->updatedEducationCertificateUpload();
        }

        $fullName = trim(implode(' ', array_filter([$this->firstName, $this->middleName, $this->lastName])));
        $userUpdates = [
            'name' => $fullName ?: $user->name,
            'first_name' => $this->firstName,
            'middle_name' => $this->middleName ?: null,
            'last_name' => $this->lastName,
            'email' => $this->email ?: $user->email,
        ];
        if ($this->pwdIdPath) {
            $userUpdates['pwd_id_path'] = $this->pwdIdPath;
        }
        $user->update($userUpdates);

        $resolvedCity = ($this->locationCityId ? LocationCity::find($this->locationCityId) : null)
            ?? LocationCity::where('name', $this->city)->first();
        $resolvedBarangay = ($this->locationBarangayId ? LocationBarangay::find($this->locationBarangayId) : null)
            ?? ($resolvedCity ? LocationBarangay::where('location_city_id', $resolvedCity->id)->where('name', $this->barangay)->first() : null);

        $profile = CandidateProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'avatar_path' => $this->avatarPath,
                'suffix' => $this->suffix ?: null,
                'headline' => $this->getEffectiveHeadline(),
                'highest_education' => $this->highestEducation,
                'input_mode' => $this->inputMode,
                'bio' => trim($this->bio),
                'location' => trim(implode(', ', array_filter([$resolvedCity?->name ?? $this->city, $this->country]))),
                'country' => $this->country,
                'location_city_id' => $resolvedCity?->id,
                'city' => $resolvedCity?->name ?? $this->city,
                'location_barangay_id' => $resolvedBarangay?->id,
                'barangay' => $resolvedBarangay?->name ?? $this->barangay,
                'zip_code' => $resolvedCity?->zip_code ?? $this->zipCode,
                'spoken_languages' => array_values(array_filter($this->spokenLanguages)),
                'other_language' => $this->hasOtherLanguage && trim($this->otherLanguage) !== '' ? trim($this->otherLanguage) : null,
                'phone' => trim($this->phone),
                'skills' => array_values(array_filter($this->selectedSkills)),
                'resume_pdf_path' => $this->resumePdfPath,
                'fsl_video_path' => $this->hasFslVideoAttached() ? $this->fslVideoPath : null,
                'accommodation_notes' => trim($this->accommodationNotes),
            ]
        );

        // Sync Validated Work Experiences
        $profile->workExperiences()->delete();
        foreach ($this->getValidExperiences() as $exp) {
            $startYear = (int) ($exp['start_year'] ?: date('Y'));
            $isCurrent = (bool) $exp['is_current'];
            $endYear = ! $isCurrent && ! empty($exp['end_year']) && is_numeric($exp['end_year'])
                ? (int) $exp['end_year']
                : null;

            CandidateWorkExperience::create([
                'candidate_profile_id' => $profile->id,
                'job_title' => $exp['job_title'] ?: 'Professional Role',
                'company_name' => $exp['company_name'] ?: 'Employer',
                'location' => $this->city,
                'start_date' => Carbon::createFromDate(max(1970, $startYear), 1, 1),
                'end_date' => $endYear ? Carbon::createFromDate(max(1970, $endYear), 1, 1) : null,
                'is_current' => $isCurrent,
                'description' => $exp['description'] ?: null,
            ]);
        }

        // Sync Education (Strictly optional certificate/institution)
        $profile->education()->delete();
        $gradYear = is_numeric($this->yearGraduated) && strlen($this->yearGraduated) === 4
            ? (int) $this->yearGraduated
            : null;

        CandidateEducation::create([
            'candidate_profile_id' => $profile->id,
            'institution_name' => trim($this->institutionName) ?: $this->highestEducation,
            'degree' => $this->highestEducation,
            'field_of_study' => $this->highestEducation,
            'start_date' => $gradYear ? Carbon::createFromDate(max(1970, $gradYear - 4), 1, 1) : null,
            'end_date' => $gradYear ? Carbon::createFromDate(max(1970, $gradYear), 1, 1) : null,
            'is_current' => false,
        ]);

        if ($this->educationCertificatePath) {
            $profile->certifications()->delete();
            CandidateCertification::create([
                'candidate_profile_id' => $profile->id,
                'name' => $this->highestEducation.' Certificate',
                'issuing_organization' => trim($this->institutionName) ?: 'Educational Institution',
                'issued_at' => $gradYear ? Carbon::createFromDate(max(1970, $gradYear), 1, 1) : Carbon::now(),
                'file_path' => $this->educationCertificatePath,
            ]);
        }

        // Sync Accessibility Needs
        $profile->accessibilityNeeds()->delete();
        foreach ($this->selectedAccommodations as $accId) {
            AccessibilityNeed::create([
                'candidate_profile_id' => $profile->id,
                'accommodation_id' => (int) $accId,
                'is_required' => true,
                'visible_to_employers' => true,
            ]);
        }

        return $profile;
    }

    public function saveDraftAndExit()
    {
        $this->persistProfileData();
        $this->draftMessage = 'Your profile draft has been saved.';

        if ($this->isEmbedded) {
            session()->flash('profile_saved_banner', 'Draft saved successfully.');

            return $this->redirect(route('candidate.profile.show'));
        }

        return $this->redirect(route('dashboard'));
    }

    public function saveProfileAndRedirect()
    {
        $this->validateStep(1);
        $this->validateStep(2);

        $this->persistProfileData();

        session()->flash(
            'profile_saved_banner',
            'Profile saved successfully! Your details, accommodation needs, and both attachments are saved. Your job matches are ready.'
        );

        if ($this->isEmbedded) {
            return $this->redirect(route('candidate.profile.show'));
        }

        return $this->redirect(route('dashboard'));
    }

    public function render()
    {
        $this->ensureLocationTablesAndData();

        $countries = LocationCity::query()->select('country')->distinct()->orderBy('country')->pluck('country');
        $citiesQuery = LocationCity::orderBy('name');
        if (! empty($this->country) && LocationCity::where('country', $this->country)->exists()) {
            $citiesQuery->where('country', $this->country);
        }
        $cities = $citiesQuery->get();

        $selectedCityRecord = ($this->locationCityId ? LocationCity::find($this->locationCityId) : null)
            ?? LocationCity::where('name', $this->city)->first();

        $barangays = $selectedCityRecord
            ? $selectedCityRecord->barangays()->get()
            : LocationBarangay::orderBy('name')->get();

        $languageOptions = LanguageOption::orderBy('sort_order')->get();
        $educationLevels = EducationLevel::orderBy('sort_order')->get();
        $skillOptions = SkillOption::orderBy('sort_order')->get();

        $physicalAccommodations = Accommodation::where('category', 'mobility')
            ->whereIn('name', ['Wheelchair Ramp Access', 'Accessible Restroom', 'Adjustable Workstation'])
            ->get();
        if ($physicalAccommodations->isEmpty()) {
            $physicalAccommodations = Accommodation::where('category', 'mobility')->take(3)->get();
        }

        $visualAccommodations = Accommodation::where('category', 'visual')
            ->whereIn('name', ['Screen Reader Compatibility', 'Large Print Materials', 'High-Contrast Displays'])
            ->get();
        if ($visualAccommodations->isEmpty()) {
            $visualAccommodations = Accommodation::where('category', 'visual')->take(3)->get();
        }

        $auditoryAccommodations = Accommodation::where('category', 'hearing')
            ->whereIn('name', ['FSL Bilingual Staff', 'Live Captions', 'Visual Alerts'])
            ->get();
        if ($auditoryAccommodations->isEmpty()) {
            $auditoryAccommodations = Accommodation::where('category', 'hearing')->take(3)->get();
        }

        $selectedAccommodationObjects = Accommodation::whereIn('id', $this->selectedAccommodations)->get();

        $hasFslAttached = $this->hasFslVideoAttached();
        $hasPwdIdAttached = $this->hasPwdIdAttached();
        $attachedFilesCount = 1 + ($hasFslAttached ? 1 : 0) + ($hasPwdIdAttached ? 1 : 0);

        return view('livewire.candidate.profile-builder', [
            'countries' => $countries,
            'cities' => $cities,
            'selectedCityRecord' => $selectedCityRecord,
            'barangays' => $barangays,
            'languageOptions' => $languageOptions,
            'educationLevels' => $educationLevels,
            'skillOptions' => $skillOptions,
            'physicalAccommodations' => $physicalAccommodations,
            'visualAccommodations' => $visualAccommodations,
            'auditoryAccommodations' => $auditoryAccommodations,
            'selectedAccommodationObjects' => $selectedAccommodationObjects,
            'effectiveHeadline' => $this->getEffectiveHeadline(),
            'validExperiences' => $this->getValidExperiences(),
            'hasFslAttached' => $hasFslAttached,
            'hasPwdIdAttached' => $hasPwdIdAttached,
            'attachedFilesCount' => $attachedFilesCount,
        ]);
    }
}
