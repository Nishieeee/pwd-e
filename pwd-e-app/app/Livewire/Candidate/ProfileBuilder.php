<?php

namespace App\Livewire\Candidate;

use App\Models\AccessibilityNeed;
use App\Models\Accommodation;
use App\Models\CandidateCertification;
use App\Models\CandidateEducation;
use App\Models\CandidateProfile;
use App\Models\CandidateTraining;
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

    /** @var array<int, string> */
    public array $disabilityTypes = [];

    // Searchable Location Comboboxes (PSGC-backed)
    public string $address = '';

    public string $country = '';

    public string $province = '';

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

    // Trainings & Seminars (REV-13)
    public array $trainings = [];

    // Professional Certifications, TESDA NC & Eligibility (REV-13)
    public array $certificationsList = [];

    public bool $hasCustomUploadedResume = false;

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

    public $medCertUpload = null;

    public string $resumeFilename = '';

    public string $resumeFileSize = '4 KB';

    public ?string $resumePdfPath = null;

    public string $fslVideoFilename = '';

    public string $fslVideoSize = '';

    public ?string $fslVideoPath = null;

    public string $pwdIdFilename = '';

    public string $pwdIdFileSize = '';

    public ?string $pwdIdPath = null;

    public string $medCertNumber = '';

    public string $medCertFilename = '';

    public string $medCertFileSize = '';

    public ?string $medCertPath = null;

    public bool $showResumePreviewModal = false;

    public bool $showVideoPreviewModal = false;

    public ?string $draftMessage = null;

    // Resume Scan & Auto-Build Profile
    public $resumeScanUpload = null;

    public bool $isScanningResume = false;

    public bool $resumeScanSuccess = false;

    public bool $showScanReviewModal = false;

    public ?array $scanSummary = null;

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
        $this->disabilityTypes = $profile->resolvedDisabilityTypes();

        // Location fields (dynamically resolved from PSGC database models)
        $cityRecord = $profile->locationCity
            ?? ($profile->city ? LocationCity::where('name', $profile->city)->first() : null)
            ?? LocationCity::orderBy('name')->first();

        $this->address = trim((string) ($profile->address ?? ''));
        $this->country = $profile->country ?: ($cityRecord?->country ?? 'Philippines');
        $this->province = trim((string) ($profile->province ?: ($cityRecord?->province_name ?? '')));
        $this->locationCityId = $cityRecord?->id;
        $this->city = $cityRecord?->name ?? ($profile->city ?? '');
        $this->zipCode = ($profile->zip_code ?: ($cityRecord?->zip_code ?? ''));

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

        // Trainings & Seminars (REV-13)
        $existingTrainings = Schema::hasTable('candidate_trainings')
            ? $profile->trainings()->get()
            : collect();
        if ($existingTrainings->isNotEmpty()) {
            $this->trainings = $existingTrainings->map(fn ($tr) => [
                'title' => trim((string) $tr->title),
                'organizer' => trim((string) ($tr->organizer ?? '')),
                'year_completed' => trim((string) ($tr->year_completed ?? '')),
                'description' => trim((string) ($tr->description ?? '')),
            ])->toArray();
        } else {
            $this->trainings = [
                [
                    'title' => '',
                    'organizer' => '',
                    'year_completed' => '',
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

        $hasCertCategory = Schema::hasColumn('candidate_certifications', 'category');
        $certRecord = $hasCertCategory
            ? $profile->certifications()->where('category', 'education_diploma')->first()
            : $profile->certifications()->whereNotNull('file_path')->first();
        if ($certRecord && ! empty($certRecord->file_path) && Storage::disk('public')->exists($certRecord->file_path)) {
            $this->educationCertificatePath = $certRecord->file_path;
            $this->educationCertificateFilename = basename($certRecord->file_path);
        }

        $existingProCerts = $hasCertCategory
            ? $profile->certifications()->where('category', '!=', 'education_diploma')->get()
            : $profile->certifications()->get();
        if ($existingProCerts->isNotEmpty()) {
            $this->certificationsList = $existingProCerts->map(fn ($cert) => [
                'name' => trim((string) $cert->name),
                'issuing_organization' => trim((string) ($cert->issuing_organization ?? '')),
                'year_obtained' => trim((string) ($cert->year_obtained ?: ($cert->issued_at ? $cert->issued_at->format('Y') : ''))),
                'credential_id' => trim((string) ($cert->credential_id ?? '')),
            ])->toArray();
        } else {
            $this->certificationsList = [
                [
                    'name' => '',
                    'issuing_organization' => '',
                    'year_obtained' => '',
                    'credential_id' => '',
                ],
            ];
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
        } elseif (! empty($user->pwd_id_number) && $user->pwd_document_type !== 'medical_certificate') {
            $this->pwdIdPath = null;
            $this->pwdIdFilename = 'Verified PWD ID (' . $user->pwd_id_number . ')';
            $this->pwdIdFileSize = 'Verified on Registration';
        } else {
            $this->pwdIdPath = null;
            $this->pwdIdFilename = '';
            $this->pwdIdFileSize = '';
        }

        $this->medCertNumber = trim((string) ($user->medical_certificate_number ?? ($user->pwd_document_type === 'medical_certificate' ? $user->pwd_id_number : '')));
        if (! empty($user->medical_certificate_path)) {
            $this->medCertPath = $user->medical_certificate_path;
            $this->medCertFilename = basename($user->medical_certificate_path);
            $medBytes = 0;
            if (Storage::disk('private')->exists($user->medical_certificate_path)) {
                $medBytes = Storage::disk('private')->size($user->medical_certificate_path);
            } elseif (Storage::disk('public')->exists($user->medical_certificate_path)) {
                $medBytes = Storage::disk('public')->size($user->medical_certificate_path);
            }
            $this->medCertFileSize = $medBytes > 0 ? max(1, (int) round($medBytes / 1024)).' KB' : 'On File';
        } elseif ($user->pwd_document_type === 'medical_certificate' && ! empty($user->pwd_id_number)) {
            $this->medCertPath = null;
            $this->medCertFilename = 'Verified Medical Cert (' . $user->pwd_id_number . ')';
            $this->medCertFileSize = 'Verified on Registration';
        } else {
            $this->medCertPath = null;
            $this->medCertFilename = '';
            $this->medCertFileSize = '';
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

    public function updatedProvince(string $value): void
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return;
        }

        $matchingCities = LocationCity::where('province_name', 'like', $trimmed)->orderBy('name')->get();
        if ($matchingCities->isNotEmpty()) {
            $this->province = (string) $matchingCities->first()->province_name;
            $currentInProvince = $matchingCities->first(fn ($c) => strcasecmp($c->name, $this->city) === 0);
            if (! $currentInProvince) {
                $firstCity = $matchingCities->first();
                $this->locationCityId = $firstCity->id;
                $this->city = $firstCity->name;
                $this->zipCode = (string) ($firstCity->zip_code ?? '');
                $firstBrgy = LocationBarangay::where('location_city_id', $firstCity->id)->orderBy('name')->first();
                $this->locationBarangayId = $firstBrgy?->id;
                $this->barangay = $firstBrgy?->name ?? '';
            }
        }
    }

    public function selectProvinceOption(string $provinceName): void
    {
        $this->province = $provinceName;
        $this->updatedProvince($provinceName);
    }

    public function updatedCity(string $value): void
    {
        $cityRecord = LocationCity::where('name', 'like', trim($value))->first();
        if ($cityRecord) {
            $this->locationCityId = $cityRecord->id;
            $this->city = $cityRecord->name;
            if (! empty($cityRecord->province_name)) {
                $this->province = $cityRecord->province_name;
            }
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

    public function addTraining(): void
    {
        $this->trainings[] = [
            'title' => '',
            'organizer' => '',
            'year_completed' => '',
            'description' => '',
        ];
    }

    public function removeTraining(int $index): void
    {
        if (count($this->trainings) > 1 && isset($this->trainings[$index])) {
            unset($this->trainings[$index]);
            $this->trainings = array_values($this->trainings);
        } elseif (isset($this->trainings[$index])) {
            $this->trainings = [['title' => '', 'organizer' => '', 'year_completed' => '', 'description' => '']];
        }
    }

    public function addCertificationItem(): void
    {
        $this->certificationsList[] = [
            'name' => '',
            'issuing_organization' => '',
            'year_obtained' => '',
            'credential_id' => '',
        ];
    }

    public function removeCertificationItem(int $index): void
    {
        if (count($this->certificationsList) > 1 && isset($this->certificationsList[$index])) {
            unset($this->certificationsList[$index]);
            $this->certificationsList = array_values($this->certificationsList);
        } elseif (isset($this->certificationsList[$index])) {
            $this->certificationsList = [['name' => '', 'issuing_organization' => '', 'year_obtained' => '', 'credential_id' => '']];
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

    public function isStandardTypingUnpopulated(): bool
    {
        return empty($this->selectedSkills)
            && empty($this->getValidExperiences())
            && empty($this->getValidTrainings())
            && empty($this->getValidCertifications())
            && trim($this->bio) === '';
    }

    public function updatedCustomResumeUpload(): void
    {
        if ($this->customResumeUpload) {
            $filename = $this->customResumeUpload->getClientOriginalName();
            $storedPath = $this->customResumeUpload->storeAs('resumes', $filename, 'public');
            $this->resumePdfPath = $storedPath;
            $this->resumeFilename = $filename;
            $sizeKb = max(1, (int) round($this->customResumeUpload->getSize() / 1024));
            $this->resumeFileSize = $sizeKb.' KB';
            $this->hasCustomUploadedResume = true;

            // REV-11: If Standard Typing was not used / profile fields are empty, auto-read the uploaded résumé!
            if ($this->inputMode !== 'standard' || $this->isStandardTypingUnpopulated()) {
                $parser = app(\App\Services\Resume\ResumeParserService::class);
                $parsed = $parser->parse($this->customResumeUpload, $filename);
                $this->applyParsedResumeData($parsed, $filename, $storedPath, $this->resumeFileSize, false, true);
            }
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

    public function updatedAvatarUpload(): void
    {
        $this->validate([
            'avatarUpload' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ], [
            'avatarUpload.image' => 'The 2x2 photo must be a valid image file.',
            'avatarUpload.mimes' => 'The 2x2 photo must be in JPG, PNG, or WebP format.',
            'avatarUpload.max' => 'The 2x2 photo must not exceed 5 MB.',
        ]);

        if ($this->avatarUpload) {
            if ($this->avatarPath && Storage::disk('public')->exists($this->avatarPath)) {
                Storage::disk('public')->delete($this->avatarPath);
            }
            $this->avatarPath = $this->avatarUpload->store('avatars', 'public');
            $this->avatarUpload = null;
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
        $this->validate([
            'pwdIdUpload' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
        ]);

        if ($this->pwdIdUpload) {
            $filename = $this->pwdIdUpload->getClientOriginalName();
            $this->pwdIdPath = $this->pwdIdUpload->storeAs('documents/pwd_ids', $filename, 'private');
            $this->pwdIdFilename = $filename;
            $sizeKb = max(1, (int) round($this->pwdIdUpload->getSize() / 1024));
            $this->pwdIdFileSize = $sizeKb.' KB';

            $user = Auth::user();
            if ($user) {
                $newDocType = ($this->medCertPath || $user->medical_certificate_path || $user->pwd_document_type === 'medical_certificate')
                    ? 'both'
                    : ($user->pwd_document_type ?: 'pwd_id');
                $user->update([
                    'pwd_id_path' => $this->pwdIdPath,
                    'pwd_document_type' => $newDocType,
                ]);
            }
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

    public function updatedMedCertUpload(): void
    {
        $this->validate([
            'medCertUpload' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
        ]);

        if ($this->medCertUpload) {
            $filename = $this->medCertUpload->getClientOriginalName();
            $this->medCertPath = $this->medCertUpload->storeAs('documents/medical_certificates', $filename, 'private');
            $this->medCertFilename = $filename;
            $sizeKb = max(1, (int) round($this->medCertUpload->getSize() / 1024));
            $this->medCertFileSize = $sizeKb.' KB';

            $user = Auth::user();
            if ($user) {
                $newDocType = ($this->pwdIdPath || $user->pwd_id_path || $user->pwd_document_type === 'pwd_id')
                    ? 'both'
                    : ($user->pwd_document_type ?: 'medical_certificate');
                $user->update([
                    'medical_certificate_path' => $this->medCertPath,
                    'medical_certificate_number' => trim($this->medCertNumber) !== '' ? trim($this->medCertNumber) : $user->medical_certificate_number,
                    'pwd_document_type' => $newDocType,
                ]);
            }
        }
    }

    public function removeMedCertDocument(): void
    {
        if ($this->medCertPath) {
            if (Storage::disk('private')->exists($this->medCertPath)) {
                Storage::disk('private')->delete($this->medCertPath);
            } elseif (Storage::disk('public')->exists($this->medCertPath)) {
                Storage::disk('public')->delete($this->medCertPath);
            }
        }
        $this->medCertUpload = null;
        $this->medCertPath = null;
        $this->medCertFilename = '';
        $this->medCertFileSize = '';
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
            || (! empty($user?->pwd_id_number) && $user?->pwd_document_type !== 'medical_certificate');
    }

    public function hasMedCertAttached(): bool
    {
        $user = Auth::user();

        return $this->medCertUpload !== null
            || ! empty($this->medCertPath)
            || ! empty($user?->medical_certificate_path)
            || ($user?->pwd_document_type === 'medical_certificate' && ! empty($user?->pwd_id_number));
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

    public function getValidTrainings(): array
    {
        $valid = [];
        foreach ($this->trainings as $tr) {
            $title = trim((string) ($tr['title'] ?? ''));
            $organizer = trim((string) ($tr['organizer'] ?? ''));
            if ($title === '' && $organizer === '') {
                continue;
            }
            $year = preg_replace('/[^0-9]/', '', (string) ($tr['year_completed'] ?? ''));
            $valid[] = [
                'title' => $title ?: 'Training / Seminar',
                'organizer' => $organizer,
                'year_completed' => strlen($year) === 4 ? $year : '',
                'description' => trim((string) ($tr['description'] ?? '')),
            ];
        }

        return $valid;
    }

    public function getValidCertifications(): array
    {
        $valid = [];
        foreach ($this->certificationsList as $cert) {
            $name = trim((string) ($cert['name'] ?? ''));
            $org = trim((string) ($cert['issuing_organization'] ?? ''));
            if ($name === '' && $org === '') {
                continue;
            }
            $year = preg_replace('/[^0-9]/', '', (string) ($cert['year_obtained'] ?? ''));
            $valid[] = [
                'name' => $name ?: 'Professional Certification',
                'issuing_organization' => $org ?: 'Certifying Authority',
                'year_obtained' => strlen($year) === 4 ? $year : '',
                'credential_id' => trim((string) ($cert['credential_id'] ?? '')),
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
                'address' => ['nullable', 'string', 'max:255'],
                'country' => ['nullable', 'string', 'max:100'],
                'province' => ['nullable', 'string', 'max:120'],
                'barangay' => ['nullable', 'string', 'max:100'],
                'zipCode' => ['nullable', 'string', 'max:20'],
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
            for ($s = $this->currentStep; $s < $step; $s++) {
                try {
                    $this->validateStep($s);
                } catch (\Illuminate\Validation\ValidationException $e) {
                    $this->currentStep = $s;
                    $this->dispatch('profile-step-changed');
                    throw $e;
                }
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

        $user = Auth::user();
        $profile = $user?->candidateProfile;

        $fullName = trim(implode(' ', array_filter([$this->firstName, $this->middleName, $this->lastName, $this->suffix])));
        $printName = trim(implode(' ', array_filter([$this->firstName, $this->middleName, $this->lastName])));
        $initials = strtoupper(substr($this->firstName, 0, 1).substr($this->lastName, 0, 1)) ?: ($user?->initials() ?? 'MS');
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
        if (empty($languagesList)) {
            $languagesList = ['Filipino', 'English'];
        }

        $locationParts = array_filter([
            trim($this->address),
            $this->barangay,
            $this->city,
            trim($this->province),
            $this->country,
        ]);

        $maskedId = 'PWD-'.str_pad((string) ($user?->id ?? 0), 5, '0', STR_PAD_LEFT);
        $updatedDate = $profile?->updated_at ? $profile->updated_at->format('M d, Y') : now()->format('M d, Y');

        $sections = [
            'name' => $fullName ?: ($user?->name ?? 'Candidate Profile'),
            'print_name' => $printName ?: ($user?->name ?? 'Candidate Profile'),
            'initials' => $initials,
            'disability_type' => trim((string) ($profile?->disability_type ?? '')),
            'headline' => $this->getEffectiveHeadline(),
            'masked_id' => $maskedId,
            'updated_at' => $updatedDate,
            'contact' => implode('  |  ', array_filter([
                implode(', ', $locationParts),
                $this->email ?: ($user?->email ?? ''),
                $this->phone,
            ])),
            'languages' => $languagesList,
            'summary' => trim($this->bio),
            'experiences' => $this->getValidExperiences(),
            'education' => [
                'level' => $this->highestEducation,
                'institution' => trim($this->institutionName),
                'year' => trim($this->yearGraduated),
            ],
            'skills' => array_values(array_filter($this->selectedSkills)),
            'accommodations' => $accommodationsNames,
            'accommodation_notes' => trim($this->accommodationNotes),
            'signed_date' => now()->format('F d, Y'),
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
        $pages = [[]];
        $pageIdx = 0;
        $y = 752.0;

        $addOp = function (string $op) use (&$pages, &$pageIdx): void {
            $pages[$pageIdx][] = $op;
        };

        $ensureSpace = function (float $neededHeight) use (&$pages, &$pageIdx, &$y): void {
            if ($y - $neededHeight < 50.0) {
                $pages[] = [];
                $pageIdx++;
                $y = 752.0;
            }
        };

        $drawText = function (string $text, float $x, float $yPos, string $font = '/F1', float $size = 9.5, string $color = '0.102 0.157 0.251') use ($addOp): void {
            $esc = $this->escapePdfText($text);
            if ($esc === '') {
                return;
            }
            $addOp(sprintf('BT %s rg %s %.2F Tf %.2F %.2F Td (%s) Tj ET', $color, $font, $size, $x, $yPos, $esc));
        };

        $approxWidth = function (string $text, float $size): float {
            return strlen($text) * $size * 0.52;
        };

        // ---------------------------------------------------------------------
        // 1. Clean White Digital Profile Header
        // ---------------------------------------------------------------------
        // 2x2 Photo Box (Left: x=40, y=674, w=74, h=74)
        $addOp('0.945 0.969 0.984 rg 40 674 74 74 re f');
        $addOp('0.722 0.816 0.886 RG 1.2 w 40 674 74 74 re S');
        $initials = (string) ($sections['initials'] ?: 'MS');
        $initX = 40.0 + max(8.0, (74.0 - $approxWidth($initials, 18.0)) / 2.0);
        $drawText($initials, $initX, 710.0, '/F2', 18.0, '0.141 0.310 0.451');
        $drawText('2x2 PHOTO', 55.0, 691.0, '/F2', 7.0, '0.306 0.373 0.431');

        // Top-right Document Stamp (DIGITAL PROFILE + Masked ID + Updated Date)
        $addOp('0.863 0.918 0.957 rg 482 732 90 16 re f');
        $drawText('DIGITAL PROFILE', 491.0, 737.0, '/F2', 7.5, '0.141 0.310 0.451');
        $drawText('ID: '.$sections['masked_id'], 482.0, 719.0, '/F1', 8.0, '0.306 0.373 0.431');
        $drawText('Updated: '.$sections['updated_at'], 482.0, 707.0, '/F1', 8.0, '0.306 0.373 0.431');

        // Middle Identity Column (x=128..472)
        // Badges row: RA 7277 Verified PWD + Optional Category
        $badgeX = 128.0;
        $verifiedText = 'RA 7277 Verified PWD';
        $verifiedW = max(106.0, $approxWidth($verifiedText, 8.0) + 16.0);
        $addOp(sprintf('0.910 0.961 0.914 rg %.2F 732 %.2F 16 re f', $badgeX, $verifiedW));
        $addOp(sprintf('0.647 0.839 0.655 RG 0.6 w %.2F 732 %.2F 16 re S', $badgeX, $verifiedW));
        $drawText($verifiedText, $badgeX + 8.0, 737.0, '/F2', 8.0, '0.090 0.420 0.278');

        if (! empty($sections['disability_type'])) {
            $catX = $badgeX + $verifiedW + 6.0;
            $catText = 'Category: '.$sections['disability_type'];
            $catW = min(472.0 - $catX, max(84.0, $approxWidth($catText, 8.0) + 14.0));
            if ($catW > 45.0) {
                $addOp(sprintf('0.863 0.918 0.957 rg %.2F 732 %.2F 16 re f', $catX, $catW));
                $addOp(sprintf('0.722 0.816 0.886 RG 0.6 w %.2F 732 %.2F 16 re S', $catX, $catW));
                $drawText($catText, $catX + 7.0, 737.0, '/F2', 8.0, '0.102 0.157 0.251');
            }
        }

        // Full Name & Headline
        $drawText($sections['name'], 128.0, 711.0, '/F2', 16.0, '0.102 0.157 0.251');
        $drawText($sections['headline'], 128.0, 696.0, '/F2', 10.0, '0.141 0.310 0.451');

        // Contact Row (wrapped cleanly inside x=128..472)
        $contactLines = explode("\n", wordwrap((string) $sections['contact'], 72, "\n"));
        $contactY = 682.0;
        foreach (array_slice($contactLines, 0, 2) as $cLine) {
            $drawText(trim($cLine), 128.0, $contactY, '/F1', 8.2, '0.306 0.373 0.431');
            $contactY -= 11.0;
        }

        // Header bottom rule
        $y = min(660.0, $contactY - 6.0);
        $addOp(sprintf('0.780 0.820 0.860 RG 1.4 w 40 %.2F m 572 %.2F l S', $y, $y));
        $y -= 18.0;

        $drawSectionHeader = function (string $title, float $x = 40.0, float $width = 532.0) use ($addOp, $drawText, &$y): void {
            $drawText(strtoupper($title), $x, $y, '/F2', 9.0, '0.102 0.157 0.251');
            $y -= 5.0;
            $addOp(sprintf('0.898 0.906 0.922 RG 0.7 w %.2F %.2F m %.2F %.2F l S', $x, $y, $x + $width, $y));
            $y -= 14.0;
        };

        $drawWrappedParagraph = function (string $text, float $x, float $size, int $wrapWidth, string $font = '/F1', string $color = '0.102 0.157 0.251', float $lineHeight = 13.0) use ($drawText, $ensureSpace, &$y): void {
            foreach (explode("\n", wordwrap(trim($text), $wrapWidth, "\n")) as $line) {
                $trimmed = trim($line);
                if ($trimmed === '') {
                    continue;
                }
                $ensureSpace($lineHeight + 4.0);
                $drawText($trimmed, $x, $y, $font, $size, $color);
                $y -= $lineHeight;
            }
        };

        // ---------------------------------------------------------------------
        // Section 1: PERSONAL SUMMARY & CAREER OBJECTIVE
        // ---------------------------------------------------------------------
        if (! empty($sections['summary'])) {
            $summaryLines = count(explode("\n", wordwrap($sections['summary'], 96, "\n")));
            $ensureSpace(30.0 + ($summaryLines * 13.0));
            $drawSectionHeader('Personal Summary & Career Objective');
            $drawWrappedParagraph($sections['summary'], 40.0, 9.2, 96, '/F1', '0.102 0.157 0.251', 13.0);
            $y -= 10.0;
        }

        // ---------------------------------------------------------------------
        // Section 2: REQUIRED WORKPLACE ACCOMMODATIONS (RA 7277 / RA 10524)
        // ---------------------------------------------------------------------
        $accItems = (array) ($sections['accommodations'] ?? []);
        $accNotes = trim((string) ($sections['accommodation_notes'] ?? ''));

        // Pre-measure pill rows for the bordered card so break-inside: avoid is exact
        $pillRows = [];
        $currentRow = [];
        $rowWidth = 0.0;
        $maxInnerWidth = 504.0;
        foreach ($accItems as $accName) {
            $pillW = max(56.0, $approxWidth($accName, 8.2) + 22.0);
            if (! empty($currentRow) && ($rowWidth + $pillW + 6.0) > $maxInnerWidth) {
                $pillRows[] = $currentRow;
                $currentRow = [];
                $rowWidth = 0.0;
            }
            $currentRow[] = ['label' => $accName, 'w' => $pillW];
            $rowWidth += $pillW + 6.0;
        }
        if (! empty($currentRow)) {
            $pillRows[] = $currentRow;
        }

        $notesLines = $accNotes !== '' ? explode("\n", wordwrap('Candidate notes: '.$accNotes, 90, "\n")) : [];
        $accCardHeight = 46.0
            + (empty($pillRows) ? 16.0 : (count($pillRows) * 23.0))
            + (! empty($pillRows) && ! empty($notesLines) ? (count($notesLines) * 12.0 + 6.0) : 0.0)
            + 12.0;

        $ensureSpace($accCardHeight + 12.0);
        $cardTop = $y + 4.0;
        $cardBottom = $cardTop - $accCardHeight;

        $addOp(sprintf('0.945 0.969 0.984 rg 40 %.2F 532 %.2F re f', $cardBottom, $accCardHeight));
        $addOp(sprintf('0.722 0.816 0.886 RG 0.9 w 40 %.2F 532 %.2F re S', $cardBottom, $accCardHeight));

        $innerY = $cardTop - 16.0;
        $drawText('REQUIRED WORKPLACE ACCOMMODATIONS (RA 7277 / RA 10524)', 54.0, $innerY, '/F2', 8.8, '0.102 0.157 0.251');
        $drawText('Incentive Eligible', 494.0, $innerY, '/F2', 8.0, '0.141 0.310 0.451');

        $innerY -= 14.0;
        $drawText('Accommodations required by candidate to perform essential job duties efficiently without barriers:', 54.0, $innerY, '/F1', 8.0, '0.306 0.373 0.431');

        $innerY -= 20.0;
        if (! empty($pillRows)) {
            foreach ($pillRows as $row) {
                $px = 54.0;
                foreach ($row as $pill) {
                    $addOp(sprintf('1 1 1 rg %.2F %.2F %.2F 17 re f', $px, $innerY - 4.0, $pill['w']));
                    $addOp(sprintf('0.722 0.816 0.886 RG 0.6 w %.2F %.2F %.2F 17 re S', $px, $innerY - 4.0, $pill['w']));
                    $drawText('+ '.$pill['label'], $px + 7.0, $innerY + 1.0, '/F2', 8.0, '0.102 0.157 0.251');
                    $px += $pill['w'] + 6.0;
                }
                $innerY -= 23.0;
            }
            if (! empty($notesLines)) {
                $innerY -= 2.0;
                foreach ($notesLines as $nLine) {
                    $drawText(trim($nLine), 54.0, $innerY, '/F1', 8.2, '0.102 0.157 0.251');
                    $innerY -= 12.0;
                }
            }
        } else {
            $fallbackAcc = $accNotes !== '' ? $accNotes : 'Standard workplace accommodations indicated upon application.';
            $drawText($fallbackAcc, 54.0, $innerY + 2.0, '/F3', 8.2, '0.306 0.373 0.431');
        }

        $y = $cardBottom - 18.0;

        // ---------------------------------------------------------------------
        // Section 3: WORK EXPERIENCE & EMPLOYMENT HISTORY
        // ---------------------------------------------------------------------
        $ensureSpace(48.0);
        $drawSectionHeader('Work Experience & Employment History');

        if (! empty($sections['experiences'])) {
            foreach ($sections['experiences'] as $exp) {
                $descLines = ! empty($exp['description']) ? explode("\n", wordwrap($exp['description'], 92, "\n")) : [];
                $blockHeight = 32.0 + (count($descLines) * 12.0) + 10.0;
                $ensureSpace($blockHeight);

                $blockTop = $y + 8.0;
                $roleTitle = $exp['job_title'] ?: 'Professional Role';
                $period = trim(($exp['start_year'] ?: '').(($exp['start_year'] && $exp['end_year']) ? ' - ' : '').($exp['end_year'] ?: ''));

                $drawText($roleTitle, 52.0, $y, '/F2', 10.0, '0.102 0.157 0.251');
                if ($period !== '') {
                    $periodX = max(420.0, 570.0 - $approxWidth($period, 8.2));
                    $drawText($period, $periodX, $y, '/F1', 8.2, '0.306 0.373 0.431');
                }
                $y -= 13.0;

                if (! empty($exp['company_name'])) {
                    $drawText($exp['company_name'], 52.0, $y, '/F2', 8.8, '0.141 0.310 0.451');
                    $y -= 13.0;
                }

                foreach ($descLines as $dLine) {
                    $drawText(trim($dLine), 52.0, $y, '/F1', 8.5, '0.306 0.373 0.431');
                    $y -= 12.0;
                }

                $blockBottom = $y + 8.0;
                $addOp(sprintf('0.550 0.670 0.770 RG 1.8 w 43 %.2F m 43 %.2F l S', $blockTop, $blockBottom));
                $y -= 8.0;
            }
        } else {
            $drawText('No formal work experience added yet. Entry-level & vocational training candidate.', 40.0, $y, '/F3', 8.5, '0.306 0.373 0.431');
            $y -= 16.0;
        }

        $y -= 6.0;

        // ---------------------------------------------------------------------
        // Section 4: EDUCATIONAL ATTAINMENT
        // ---------------------------------------------------------------------
        $ensureSpace(58.0);
        $drawSectionHeader('Educational Attainment');

        $eduLevel = (string) ($sections['education']['level'] ?? '');
        $eduInst = (string) ($sections['education']['institution'] ?? '');
        $eduYear = (string) ($sections['education']['year'] ?? '');

        if ($eduLevel !== '' || $eduInst !== '') {
            $eduTop = $y + 8.0;
            $drawText($eduLevel ?: 'Educational Background', 52.0, $y, '/F2', 9.8, '0.102 0.157 0.251');
            if ($eduYear !== '') {
                $yrLabel = 'Graduated '.$eduYear;
                $yrX = max(430.0, 570.0 - $approxWidth($yrLabel, 8.2));
                $drawText($yrLabel, $yrX, $y, '/F1', 8.2, '0.306 0.373 0.431');
            }
            $y -= 13.0;
            if ($eduInst !== '') {
                $drawText($eduInst, 52.0, $y, '/F2', 8.8, '0.090 0.420 0.278');
                $y -= 13.0;
            }
            $eduBottom = $y + 8.0;
            $addOp(sprintf('0.550 0.760 0.660 RG 1.8 w 43 %.2F m 43 %.2F l S', $eduTop, $eduBottom));
        } else {
            $drawText('Educational background details available upon request.', 40.0, $y, '/F3', 8.5, '0.306 0.373 0.431');
            $y -= 14.0;
        }

        $y -= 12.0;

        // ---------------------------------------------------------------------
        // Section 5: Two-Column Grid (Skills & Core Competencies | Communication & Languages)
        // ---------------------------------------------------------------------
        $buildColumnRows = function (array $items, float $colWidth) use ($approxWidth): array {
            $rows = [];
            $row = [];
            $w = 0.0;
            foreach ($items as $item) {
                $pillW = min($colWidth, max(44.0, $approxWidth($item, 8.0) + 14.0));
                if (! empty($row) && ($w + $pillW + 5.0) > $colWidth) {
                    $rows[] = $row;
                    $row = [];
                    $w = 0.0;
                }
                $row[] = ['label' => $item, 'w' => $pillW];
                $w += $pillW + 5.0;
            }
            if (! empty($row)) {
                $rows[] = $row;
            }

            return $rows;
        };

        $skillRows = $buildColumnRows((array) ($sections['skills'] ?? []), 254.0);
        $langRows = $buildColumnRows((array) ($sections['languages'] ?? []), 254.0);
        $maxRows = max(1, count($skillRows), count($langRows));
        $twoColHeight = 28.0 + ($maxRows * 21.0) + 10.0;

        $ensureSpace($twoColHeight);
        $colsStartY = $y;

        // Left Column Header
        $drawText('SKILLS & CORE COMPETENCIES', 40.0, $colsStartY, '/F2', 8.8, '0.102 0.157 0.251');
        $addOp(sprintf('0.898 0.906 0.922 RG 0.7 w 40 %.2F m 294 %.2F l S', $colsStartY - 5.0, $colsStartY - 5.0));

        // Right Column Header
        $drawText('COMMUNICATION & LANGUAGES', 318.0, $colsStartY, '/F2', 8.8, '0.102 0.157 0.251');
        $addOp(sprintf('0.898 0.906 0.922 RG 0.7 w 318 %.2F m 572 %.2F l S', $colsStartY - 5.0, $colsStartY - 5.0));

        $leftY = $colsStartY - 22.0;
        if (! empty($skillRows)) {
            foreach ($skillRows as $row) {
                $px = 40.0;
                foreach ($row as $pill) {
                    $addOp(sprintf('0.953 0.957 0.965 rg %.2F %.2F %.2F 16 re f', $px, $leftY - 4.0, $pill['w']));
                    $drawText($pill['label'], $px + 7.0, $leftY + 1.0, '/F1', 8.0, '0.102 0.157 0.251');
                    $px += $pill['w'] + 5.0;
                }
                $leftY -= 21.0;
            }
        } else {
            $drawText('Skills documented during onboarding.', 40.0, $leftY + 2.0, '/F3', 8.2, '0.306 0.373 0.431');
            $leftY -= 16.0;
        }

        $rightY = $colsStartY - 22.0;
        foreach ($langRows as $row) {
            $px = 318.0;
            foreach ($row as $pill) {
                $addOp(sprintf('0.898 0.941 0.973 rg %.2F %.2F %.2F 16 re f', $px, $rightY - 4.0, $pill['w']));
                $drawText($pill['label'], $px + 7.0, $rightY + 1.0, '/F1', 8.0, '0.102 0.157 0.251');
                $px += $pill['w'] + 5.0;
            }
            $rightY -= 21.0;
        }

        $y = min($leftY, $rightY) - 12.0;

        // ---------------------------------------------------------------------
        // Section 6: Legal Certification & Bottom Signature / Accreditation Footer
        // ---------------------------------------------------------------------
        $ensureSpace(110.0);
        $addOp(sprintf('0.780 0.820 0.860 RG 1.4 w 40 %.2F m 572 %.2F l S', $y, $y));
        $y -= 16.0;

        $certQuote = '"I hereby certify that all information stated herein is true, correct, and complete to the best of my knowledge and belief. Prepared in compliance with Republic Act No. 7277, RA 10524, and the Data Privacy Act of 2012 (RA 10173)."';
        foreach (explode("\n", wordwrap($certQuote, 102, "\n")) as $qLine) {
            $drawText(trim($qLine), 40.0, $y, '/F3', 8.2, '0.306 0.373 0.431');
            $y -= 11.5;
        }

        $y -= 18.0;
        $footerY = $y;

        // Left Accreditation Block
        $drawText('Accredited by PESO & Republic Act No. 10524 Partner', 40.0, $footerY, '/F1', 8.0, '0.306 0.373 0.431');
        $drawText('PWD-E Multimodal Employment Platform', 40.0, $footerY - 12.0, '/F2', 8.8, '0.102 0.157 0.251');
        $drawText('Western Mindanao State University - College of Computing Studies', 40.0, $footerY - 23.0, '/F1', 7.5, '0.306 0.373 0.431');

        // Right Signature Block
        $printedName = strtoupper((string) ($sections['print_name'] ?: $sections['name']));
        $sigLineStart = 392.0;
        $sigLineEnd = 572.0;
        $nameX = max($sigLineStart, $sigLineEnd - $approxWidth($printedName, 8.5));
        $drawText($printedName, $nameX, $footerY, '/F2', 8.5, '0.102 0.157 0.251');
        $addOp(sprintf('0.102 0.157 0.251 RG 0.8 w %.2F %.2F m %.2F %.2F l S', $sigLineStart, $footerY - 4.0, $sigLineEnd, $footerY - 4.0));
        $sigCaption = 'CANDIDATE SIGNATURE OVER PRINTED NAME';
        $sigCapX = max($sigLineStart - 10.0, $sigLineEnd - $approxWidth($sigCaption, 7.0));
        $drawText($sigCaption, $sigCapX, $footerY - 14.0, '/F1', 7.0, '0.306 0.373 0.431');
        $dateLine = 'Date: '.$sections['signed_date'];
        $dateX = max($sigLineStart, $sigLineEnd - $approxWidth($dateLine, 7.2));
        $drawText($dateLine, $dateX, $footerY - 24.0, '/F1', 7.2, '0.306 0.373 0.431');

        // ---------------------------------------------------------------------
        // Assemble Multi-Page PDF 1.4 Objects
        // ---------------------------------------------------------------------
        // Object layout:
        // 1: Catalog
        // 2: Pages
        // 3: Font /F1 Helvetica
        // 4: Font /F2 Helvetica-Bold
        // 5: Font /F3 Helvetica-Oblique
        // 6..: Pairs of (Page obj, Content stream obj)
        $objects = [];
        $pageObjectIds = [];
        $nextObjId = 6;

        foreach ($pages as $pageOps) {
            $pageObjId = $nextObjId++;
            $contentObjId = $nextObjId++;
            $pageObjectIds[] = $pageObjId;

            $streamData = implode("\n", $pageOps);
            $streamLength = strlen($streamData);

            $objects[$pageObjId] = "{$pageObjId} 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents {$contentObjId} 0 R /Resources << /Font << /F1 3 0 R /F2 4 0 R /F3 5 0 R >> >> >>\nendobj\n";
            $objects[$contentObjId] = "{$contentObjId} 0 obj\n<< /Length {$streamLength} >>\nstream\n{$streamData}\nendstream\nendobj\n";
        }

        $kidsStr = implode(' ', array_map(fn ($id) => "{$id} 0 R", $pageObjectIds));
        $pageCount = count($pageObjectIds);

        $allObjects = [
            1 => "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n",
            2 => "2 0 obj\n<< /Type /Pages /Kids [{$kidsStr}] /Count {$pageCount} >>\nendobj\n",
            3 => "3 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n",
            4 => "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>\nendobj\n",
            5 => "5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Oblique >>\nendobj\n",
        ] + $objects;

        ksort($allObjects);

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($allObjects as $num => $objContent) {
            $offsets[$num] = strlen($pdf);
            $pdf .= $objContent;
        }

        $totalObjs = count($allObjects) + 1;
        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 {$totalObjs}\n0000000000 65535 f \n";
        for ($i = 1; $i < $totalObjs; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= "trailer\n<< /Size {$totalObjs} /Root 1 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF";

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
        if ($this->customResumeUpload) {
            $this->updatedCustomResumeUpload();
        }

        $this->applyHeadlineFallback();
        if (! $this->hasCustomUploadedResume) {
            $this->generateResumePdf();
        }

        $user = Auth::user();

        if ($this->avatarUpload) {
            $this->updatedAvatarUpload();
        }

        if ($this->fslVideoUpload) {
            $this->updatedFslVideoUpload();
        }

        if ($this->pwdIdUpload) {
            $this->updatedPwdIdUpload();
        }

        if ($this->medCertUpload) {
            $this->updatedMedCertUpload();
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
        if ($this->medCertPath) {
            $userUpdates['medical_certificate_path'] = $this->medCertPath;
        }
        if (trim($this->medCertNumber) !== '') {
            $userUpdates['medical_certificate_number'] = trim($this->medCertNumber);
        }
        if (($this->pwdIdPath || $user->pwd_id_path) && ($this->medCertPath || $user->medical_certificate_path)) {
            $userUpdates['pwd_document_type'] = 'both';
        }
        $user->update($userUpdates);

        $resolvedCity = ($this->locationCityId ? LocationCity::find($this->locationCityId) : null)
            ?? LocationCity::where('name', $this->city)->first();
        $resolvedBarangay = ($this->locationBarangayId ? LocationBarangay::find($this->locationBarangayId) : null)
            ?? ($resolvedCity ? LocationBarangay::where('location_city_id', $resolvedCity->id)->where('name', $this->barangay)->first() : null);

        $resolvedProvince = trim($this->province) !== ''
            ? trim($this->province)
            : ($resolvedCity?->province_name ?? null);

        $cleanDisabilities = array_values(array_unique(array_filter(array_map('trim', $this->disabilityTypes))));
        $existingProfile = $user->candidateProfile;
        $resolvedDisabilities = ! empty($cleanDisabilities)
            ? $cleanDisabilities
            : ($existingProfile ? $existingProfile->resolvedDisabilityTypes() : []);

        $profile = CandidateProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'avatar_path' => $this->avatarPath,
                'suffix' => $this->suffix ?: null,
                'headline' => $this->getEffectiveHeadline(),
                'highest_education' => $this->highestEducation,
                'input_mode' => $this->inputMode,
                'bio' => trim($this->bio),
                'disability_type' => ! empty($resolvedDisabilities) ? implode(', ', $resolvedDisabilities) : ($existingProfile?->disability_type ?? null),
                'disability_types' => ! empty($resolvedDisabilities) ? $resolvedDisabilities : null,
                'location' => trim(implode(', ', array_filter([$resolvedCity?->name ?? $this->city, $resolvedProvince, $this->country]))),
                'address' => trim($this->address) !== '' ? trim($this->address) : null,
                'country' => $this->country,
                'province' => $resolvedProvince,
                'location_city_id' => $resolvedCity?->id,
                'city' => $resolvedCity?->name ?? $this->city,
                'location_barangay_id' => $resolvedBarangay?->id,
                'barangay' => $resolvedBarangay?->name ?? $this->barangay,
                'zip_code' => trim($this->zipCode) !== '' ? trim($this->zipCode) : ($resolvedCity?->zip_code ?? ''),
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

        // Sync Trainings & Seminars (REV-13)
        if (Schema::hasTable('candidate_trainings')) {
            $profile->trainings()->delete();
            foreach ($this->getValidTrainings() as $tr) {
                CandidateTraining::create([
                    'candidate_profile_id' => $profile->id,
                    'title' => $tr['title'],
                    'organizer' => $tr['organizer'] ?: null,
                    'year_completed' => $tr['year_completed'] ?: null,
                    'description' => $tr['description'] ?: null,
                ]);
            }
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

        // Sync Certifications, TESDA & Education Diploma
        $profile->certifications()->delete();
        if ($this->educationCertificatePath) {
            CandidateCertification::create([
                'candidate_profile_id' => $profile->id,
                'category' => 'education_diploma',
                'name' => $this->highestEducation.' Certificate',
                'issuing_organization' => trim($this->institutionName) ?: 'Educational Institution',
                'year_obtained' => $gradYear ? (string) $gradYear : null,
                'issued_at' => $gradYear ? Carbon::createFromDate(max(1970, $gradYear), 1, 1) : Carbon::now(),
                'file_path' => $this->educationCertificatePath,
            ]);
        }

        foreach ($this->getValidCertifications() as $cert) {
            $certYear = ! empty($cert['year_obtained']) && is_numeric($cert['year_obtained'])
                ? (int) $cert['year_obtained']
                : (int) date('Y');
            CandidateCertification::create([
                'candidate_profile_id' => $profile->id,
                'category' => 'certification',
                'name' => $cert['name'],
                'issuing_organization' => $cert['issuing_organization'],
                'year_obtained' => $cert['year_obtained'] ?: null,
                'issued_at' => Carbon::createFromDate(max(1970, $certYear), 1, 1),
                'credential_id' => $cert['credential_id'] ?: null,
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

    public function updatedResumeScanUpload(): void
    {
        $this->scanAndBuildProfile();
    }

    public function scanAndBuildProfile(): void
    {
        $this->validate([
            'resumeScanUpload' => ['required', 'file', 'mimes:pdf,docx,txt,text,rtf', 'max:10240'],
        ]);

        $this->isScanningResume = true;
        $parser = app(\App\Services\Resume\ResumeParserService::class);
        $parsed = $parser->parse($this->resumeScanUpload);

        $storedPath = $this->resumeScanUpload->store('resumes', 'public');
        $filename = $this->resumeScanUpload->getClientOriginalName();
        $fileSize = round($this->resumeScanUpload->getSize() / 1024, 1).' KB';

        $this->applyParsedResumeData($parsed, $filename, $storedPath, $fileSize);
    }

    public function loadSampleResume(): void
    {
        $sampleText = "Jhon Clein Pagarogan\nSoftware Engineer & Web Developer\nAyala, Zamboanga City, Philippines\nEmail: pagaroganjhonclein@gmail.com\nPhone: +63 96 9468 5197\n\nProfessional Summary:\nA software engineer specializing in accessible web development, dashboard design, and inclusive user experiences.\n\nSkills:\nHTML, CSS, JavaScript, Data Entry, Customer Service, Accessibility\n\nEducation:\nTertiary (College / University)\nWestern Mindanao State University\nBachelor of Science in Computer Science\nClass of 2025\n\nWork Experience:\nSoftware Engineer Intern at AKA (2024 - Present)\nDesigned and developed ERP systems and dashboards with WCAG 2.1 AA accessibility.\n\nAccommodations:\nWheelchair Ramp Access, Accessible Restroom, Screen Reader Compatibility";

        $parser = app(\App\Services\Resume\ResumeParserService::class);
        $parsed = $parser->extractProfileData($sampleText, 'Sample_Resume_Jhon_Clein.pdf');

        $this->applyParsedResumeData($parsed, 'Sample_Resume_Jhon_Clein.pdf', 'resumes/sample_resume.pdf', '12.5 KB');
    }

    protected function applyParsedResumeData(
        array $parsed,
        string $filename,
        ?string $storedPath = null,
        ?string $fileSize = null,
        bool $jumpToStep2 = true,
        bool $onlyFillEmpty = false
    ): void {
        if (! empty($parsed['first_name']) && (! $onlyFillEmpty || trim($this->firstName) === '')) {
            $this->firstName = $parsed['first_name'];
        }
        if (! empty($parsed['middle_name']) && (! $onlyFillEmpty || trim($this->middleName) === '')) {
            $this->middleName = $parsed['middle_name'];
        }
        if (! empty($parsed['last_name']) && (! $onlyFillEmpty || trim($this->lastName) === '')) {
            $this->lastName = $parsed['last_name'];
        }
        if (! empty($parsed['phone']) && (! $onlyFillEmpty || trim($this->phone) === '')) {
            $this->phone = $parsed['phone'];
        }
        if (! empty($parsed['city']) && (! $onlyFillEmpty || trim($this->city) === '')) {
            $this->city = $parsed['city'];
            $this->locationCityId = $parsed['location_city_id'];
            if ($parsed['location_city_id']) {
                $parsedCityRec = LocationCity::find($parsed['location_city_id']);
                if ($parsedCityRec?->province_name) {
                    $this->province = $parsedCityRec->province_name;
                }
            }
        }
        if (! empty($parsed['barangay']) && (! $onlyFillEmpty || trim($this->barangay) === '')) {
            $this->barangay = $parsed['barangay'];
            $this->locationBarangayId = $parsed['location_barangay_id'];
        }
        if (! empty($parsed['zip_code']) && (! $onlyFillEmpty || trim($this->zipCode) === '')) {
            $this->zipCode = $parsed['zip_code'];
        }
        if (! empty($parsed['headline']) && (! $onlyFillEmpty || trim($this->headline) === '')) {
            $this->headline = $parsed['headline'];
        }
        if (! empty($parsed['bio']) && (! $onlyFillEmpty || trim($this->bio) === '')) {
            $this->bio = $parsed['bio'];
        }
        if (! empty($parsed['skills'])) {
            $this->selectedSkills = array_values(array_unique(array_merge($this->selectedSkills, $parsed['skills'])));
        }
        if (! empty($parsed['spoken_languages'])) {
            $this->spokenLanguages = array_values(array_unique(array_merge($this->spokenLanguages, $parsed['spoken_languages'])));
        }
        if (! empty($parsed['highest_education'])) {
            $this->highestEducation = $parsed['highest_education'];
        }
        if (! empty($parsed['institution_name']) && (! $onlyFillEmpty || trim($this->institutionName) === '')) {
            $this->institutionName = $parsed['institution_name'];
        }
        if (! empty($parsed['year_graduated']) && (! $onlyFillEmpty || trim($this->yearGraduated) === '')) {
            $this->yearGraduated = $parsed['year_graduated'];
        }
        if (! empty($parsed['experiences']) && (! $onlyFillEmpty || empty($this->getValidExperiences()))) {
            $this->experiences = $parsed['experiences'];
        }
        if (! empty($parsed['trainings']) && (! $onlyFillEmpty || empty($this->getValidTrainings()))) {
            $this->trainings = $parsed['trainings'];
        }
        if (! empty($parsed['certifications']) && (! $onlyFillEmpty || empty($this->getValidCertifications()))) {
            $this->certificationsList = $parsed['certifications'];
        }
        if (! empty($parsed['suggested_accommodations'])) {
            $this->selectedAccommodations = array_values(array_unique(array_merge($this->selectedAccommodations, $parsed['suggested_accommodations'])));
        }

        if ($storedPath) {
            $this->resumePdfPath = $storedPath;
            $this->hasCustomUploadedResume = true;
        }
        $this->resumeFilename = $filename;
        if ($fileSize) {
            $this->resumeFileSize = $fileSize;
        }

        $this->scanSummary = [
            'filename' => $filename,
            'skills_count' => count($parsed['skills'] ?? []),
            'skills' => $parsed['skills'] ?? [],
            'experiences_count' => count($parsed['experiences'] ?? []),
            'experiences' => $parsed['experiences'] ?? [],
            'trainings_count' => count($parsed['trainings'] ?? []),
            'trainings' => $parsed['trainings'] ?? [],
            'certifications_count' => count($parsed['certifications'] ?? []),
            'certifications' => $parsed['certifications'] ?? [],
            'education' => $parsed['institution_name'] ? (($parsed['degree'] ?? '') ? "{$parsed['degree']}, {$parsed['institution_name']}" : $parsed['institution_name']) : null,
            'headline' => $parsed['headline'] ?? null,
            'contact' => array_filter([$parsed['full_name'] ?? null, $parsed['phone'] ?? null, $parsed['city'] ?? null]),
            'accommodations_count' => count($parsed['suggested_accommodations'] ?? []),
        ];

        $this->resumeScanSuccess = true;
        $this->isScanningResume = false;

        if ($jumpToStep2) {
            $this->showScanReviewModal = true;
            $this->currentStep = 2;
            $this->dispatch('profile-step-changed');
        }
    }

    public function confirmAndSaveFromScan()
    {
        $this->showScanReviewModal = false;
        return $this->saveProfileAndRedirect();
    }

    public function closeScanReviewModal(): void
    {
        $this->showScanReviewModal = false;
    }

    public function dismissScanSummary(): void
    {
        $this->resumeScanSuccess = false;
        $this->showScanReviewModal = false;
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
        try {
            $this->validateStep(1);
            $this->validateStep(2);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->currentStep = 2;
            $this->dispatch('profile-step-changed');
            throw $e;
        }

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

        $provincesQuery = LocationCity::query()
            ->whereNotNull('province_name')
            ->where('province_name', '!=', '');
        if (! empty($this->country) && LocationCity::where('country', $this->country)->exists()) {
            $provincesQuery->where('country', $this->country);
        }
        $provinces = $provincesQuery->select('province_name')->distinct()->orderBy('province_name')->pluck('province_name');

        $citiesQuery = LocationCity::orderBy('name');
        if (! empty($this->country) && LocationCity::where('country', $this->country)->exists()) {
            $citiesQuery->where('country', $this->country);
        }
        if (! empty($this->province) && LocationCity::where('province_name', $this->province)->exists()) {
            $citiesQuery->where('province_name', $this->province);
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
        $hasMedCertAttached = $this->hasMedCertAttached();
        $attachedFilesCount = 1 + ($hasFslAttached ? 1 : 0) + ($hasPwdIdAttached ? 1 : 0) + ($hasMedCertAttached ? 1 : 0);

        return view('livewire.candidate.profile-builder', [
            'countries' => $countries,
            'provinces' => $provinces,
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
            'validTrainings' => $this->getValidTrainings(),
            'validCertifications' => $this->getValidCertifications(),
            'hasFslAttached' => $hasFslAttached,
            'hasPwdIdAttached' => $hasPwdIdAttached,
            'hasMedCertAttached' => $hasMedCertAttached,
            'attachedFilesCount' => $attachedFilesCount,
        ]);
    }
}
