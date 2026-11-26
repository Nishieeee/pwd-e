<?php

use App\Actions\Fortify\CreateNewUser;
use App\Livewire\Candidate\ApplicationsTracker;
use App\Livewire\Employer\Applicants;
use App\Livewire\Employer\Workforce;
use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\EmployerProfile;
use App\Models\JobPosting;
use App\Models\User;
use App\Models\VerificationRequest;
use App\Models\WorkforceRecord;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    foreach (['candidate', 'employer', 'admin'] as $r) {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => $r, 'guard_name' => 'web']);
    }
    Storage::fake('private');
});

test('candidate can register with medical_certificate document type', function () {
    $action = new CreateNewUser();
    $user = $action->create([
        'first_name' => 'Maria',
        'last_name' => 'Santos',
        'email' => 'maria.medcert@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'role' => 'candidate',
        'disability_type' => 'Visual',
        'pwd_document_type' => 'medical_certificate',
        'pwd_id_number' => 'MED-CERT-2026-009',
        'date_of_birth' => '1995-05-15',
        'consent' => '1',
        'terms' => '1',
    ]);

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->pwd_document_type)->toBe('medical_certificate');

    $vr = VerificationRequest::where('user_id', $user->id)->first();
    expect($vr)->not->toBeNull()
        ->and($vr->type)->toBe('medical_certificate');
});

test('employer can request additional documents from an applicant', function () {
    $candidateUser = User::factory()->verified()->create(['role' => 'candidate', 'name' => 'Juan Candidate']);
    $candidateUser->assignRole('candidate');
    $candidateProfile = CandidateProfile::create(['user_id' => $candidateUser->id]);

    $employerUser = User::factory()->verified()->create(['role' => 'employer', 'name' => 'Tech Corp Employer']);
    $employerUser->assignRole('employer');
    $employerProfile = EmployerProfile::create([
        'user_id' => $employerUser->id,
        'company_name' => 'Inclusion Tech Corp',
    ]);

    $job = JobPosting::create([
        'employer_profile_id' => $employerProfile->id,
        'title' => 'Software Engineer',
        'description' => 'Software engineering role',
        'status' => 'published',
    ]);

    $app = Application::create([
        'candidate_profile_id' => $candidateProfile->id,
        'job_posting_id' => $job->id,
        'status' => 'applied',
        'applied_at' => now(),
    ]);

    Livewire::actingAs($employerUser)
        ->test(Applicants::class, ['job' => $job])
        ->call('openRequestDocsModal', $app->id)
        ->set('selectedDocTypes', ['NBI Clearance', 'Barangay Clearance'])
        ->set('customDocName', 'Portfolio Samples')
        ->set('requestDocsNotes', 'Please submit within 5 business days.')
        ->call('sendDocumentRequest')
        ->assertSet('showRequestDocsModal', false);

    $app->refresh();
    expect($app->requested_documents)->toBeArray()
        ->and(count($app->requested_documents))->toBe(3)
        ->and(collect($app->requested_documents)->pluck('name')->toArray())
        ->toContain('NBI Clearance', 'Barangay Clearance', 'Portfolio Samples');

    expect($app->pendingDocumentRequestsCount())->toBe(3)
        ->and($app->hasPendingDocumentRequests())->toBeTrue();
});

test('candidate can view and upload requested document', function () {
    $candidateUser = User::factory()->verified()->create(['role' => 'candidate', 'name' => 'Ana Candidate']);
    $candidateUser->assignRole('candidate');
    $candidateProfile = CandidateProfile::create(['user_id' => $candidateUser->id]);

    $employerUser = User::factory()->verified()->create(['role' => 'employer', 'name' => 'Hiring Employer']);
    $employerUser->assignRole('employer');
    $employerProfile = EmployerProfile::create([
        'user_id' => $employerUser->id,
        'company_name' => 'All Inclusive Corp',
    ]);

    $job = JobPosting::create([
        'employer_profile_id' => $employerProfile->id,
        'title' => 'Graphic Designer',
        'description' => 'Graphic design role',
        'status' => 'published',
    ]);

    $docId = 'test-doc-uuid-1';
    $app = Application::create([
        'candidate_profile_id' => $candidateProfile->id,
        'job_posting_id' => $job->id,
        'status' => 'applied',
        'applied_at' => now(),
        'requested_documents' => [
            [
                'id' => $docId,
                'name' => 'NBI Clearance',
                'status' => 'pending',
                'requested_at' => now()->toIso8601String(),
                'file_path' => null,
                'file_name' => null,
                'uploaded_at' => null,
            ],
        ],
    ]);

    $fakeFile = UploadedFile::fake()->create('nbi_clearance.pdf', 500, 'application/pdf');

    Livewire::actingAs($candidateUser)
        ->test(ApplicationsTracker::class)
        ->call('openUploadDocModal', $app->id, $docId)
        ->assertSet('showUploadDocModal', true)
        ->assertSet('uploadingDocName', 'NBI Clearance')
        ->set('uploadedDocumentFile', $fakeFile)
        ->call('submitRequestedDocument')
        ->assertSet('showUploadDocModal', false);

    $app->refresh();
    $doc = collect($app->requested_documents)->firstWhere('id', $docId);
    expect($doc['status'])->toBe('uploaded')
        ->and($doc['file_name'])->toBe('nbi_clearance.pdf')
        ->and($doc['file_path'])->not->toBeNull();

    expect($app->pendingDocumentRequestsCount())->toBe(0);
});

test('candidate with medical_certificate can upgrade to official PWD ID card', function () {
    $candidateUser = User::factory()->verified()->create([
        'role' => 'candidate',
        'name' => 'Jose MedCert',
        'pwd_document_type' => 'medical_certificate',
        'pwd_id_number' => 'MED-CERT-2026-999',
    ]);
    $candidateUser->assignRole('candidate');
    CandidateProfile::create(['user_id' => $candidateUser->id]);

    $fakeIdCard = UploadedFile::fake()->create('pwd_id_card.png', 500, 'image/png');

    Livewire::actingAs($candidateUser)
        ->test(ApplicationsTracker::class)
        ->call('openPwdUpgradeModal')
        ->assertSet('showPwdUpgradeModal', true)
        ->set('upgradePwdIdNumber', 'PWD-NCR-2026-8888')
        ->set('upgradePwdIdFile', $fakeIdCard)
        ->call('submitPwdUpgrade')
        ->assertSet('showPwdUpgradeModal', false);

    $candidateUser->refresh();
    expect($candidateUser->pwd_document_type)->toBe('pwd_id')
        ->and($candidateUser->pwd_id_number)->toBe('PWD-NCR-2026-8888');

    $vr = VerificationRequest::where('user_id', $candidateUser->id)->where('type', 'pwd_id')->first();
    expect($vr)->not->toBeNull()
        ->and($vr->id_number)->toBe('PWD-NCR-2026-8888');
});

test('workforce calculates tax deduction strictly from official PWD IDs', function () {
    $employerUser = User::factory()->verified()->create(['role' => 'employer', 'name' => 'Workforce Boss']);
    $employerUser->assignRole('employer');
    $employerProfile = EmployerProfile::create([
        'user_id' => $employerUser->id,
        'company_name' => 'Enterprise Inclusive',
    ]);

    // Employee 1: Official PWD ID (Annual Wage 400,000) -> Eligible for 25% BIR credit
    $u1 = User::factory()->verified()->create([
        'role' => 'candidate',
        'pwd_document_type' => 'pwd_id',
        'pwd_id_number' => 'PWD-001',
    ]);
    $u1->assignRole('candidate');
    $cp1 = CandidateProfile::create(['user_id' => $u1->id]);
    WorkforceRecord::create([
        'employer_profile_id' => $employerProfile->id,
        'candidate_profile_id' => $cp1->id,
        'annual_wage' => 400000,
        'pwd_verified' => true,
        'separation_status' => 'active',
        'hired_at' => now()->subMonths(6),
    ]);

    // Employee 2: Medical Certificate only (Annual Wage 300,000) -> Fit to work, but excluded from BIR 25% credit until PWD ID is submitted
    $u2 = User::factory()->verified()->create([
        'role' => 'candidate',
        'pwd_document_type' => 'medical_certificate',
        'pwd_id_number' => 'MED-002',
    ]);
    $u2->assignRole('candidate');
    $cp2 = CandidateProfile::create(['user_id' => $u2->id]);
    WorkforceRecord::create([
        'employer_profile_id' => $employerProfile->id,
        'candidate_profile_id' => $cp2->id,
        'annual_wage' => 300000,
        'pwd_verified' => true,
        'separation_status' => 'active',
        'hired_at' => now()->subMonths(3),
    ]);

    Livewire::actingAs($employerUser)
        ->test(Workforce::class)
        ->assertViewHas('totalEmployees', 2)
        ->assertViewHas('pwdCount', 2)
        ->assertViewHas('medCertOnlyCount', 1)
        ->assertViewHas('totalWages', 400000.0) // Only the official PWD ID wage is counted
        ->assertViewHas('deduction', 100000.0); // 25% of 400,000
});

test('admin can view both primary pwd id and secondary medical certificate documents and workforce counts both as tax eligible', function () {
    $admin = User::factory()->verified()->create(['role' => 'admin']);
    $admin->assignRole('admin');

    $pwdFile = UploadedFile::fake()->create('official_pwd.png', 200, 'image/png');
    $medFile = UploadedFile::fake()->create('clinical_med.pdf', 300, 'application/pdf');

    $action = new CreateNewUser();
    $candidate = $action->create([
        'first_name' => 'Carlo',
        'last_name' => 'Dual',
        'email' => 'carlo.dual@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'role' => 'candidate',
        'disability_type' => 'Motor/Physical',
        'pwd_document_type' => 'both',
        'pwd_id_number' => 'PWD-DUAL-001',
        'medical_certificate_number' => 'MED-DUAL-001',
        'pwd_id_file' => $pwdFile,
        'medical_certificate_file' => $medFile,
        'date_of_birth' => '1994-03-10',
        'consent' => '1',
        'terms' => '1',
    ]);

    $vr = VerificationRequest::where('user_id', $candidate->id)->firstOrFail();

    $this->actingAs($admin)
        ->get(route('admin.verifications.document', $vr))
        ->assertOk();

    $this->actingAs($admin)
        ->get(route('admin.verifications.document', ['verificationRequest' => $vr, 'doc' => 'secondary']))
        ->assertOk();

    $candidate->update(['status' => 'active', 'verified_at' => now()]);
    expect($candidate->fresh()->isPwdIdVerified())->toBeTrue()
        ->and($candidate->fresh()->isMedCertVerified())->toBeTrue();

    $employerUser = User::factory()->verified()->create(['role' => 'employer']);
    $employerUser->assignRole('employer');
    $employerProfile = EmployerProfile::create([
        'user_id' => $employerUser->id,
        'company_name' => 'Dual Inclusive Inc',
    ]);

    WorkforceRecord::create([
        'employer_profile_id' => $employerProfile->id,
        'candidate_profile_id' => $candidate->candidateProfile->id,
        'annual_wage' => 500000,
        'pwd_verified' => true,
        'separation_status' => 'active',
        'hired_at' => now()->subMonths(2),
    ]);

    Livewire::actingAs($employerUser)
        ->test(Workforce::class)
        ->assertViewHas('medCertOnlyCount', 0)
        ->assertViewHas('totalWages', 500000.0)
        ->assertViewHas('deduction', 125000.0);
});

test('candidate can attach med cert in profile builder or apply page and employer sees both pwd id and med cert in applicant details', function () {
    $candidateUser = User::factory()->verified()->create([
        'role' => 'candidate',
        'name' => 'Marco Credential',
        'pwd_document_type' => 'pwd_id',
        'pwd_id_number' => 'PWD-09-2026-444',
    ]);
    $candidateUser->assignRole('candidate');
    $candidateProfile = CandidateProfile::create([
        'user_id' => $candidateUser->id,
        'phone' => '+639171112233',
        'city' => 'Zamboanga City',
    ]);

    $medFileProfile = UploadedFile::fake()->create('profile_medcert.pdf', 250, 'application/pdf');

    Livewire::actingAs($candidateUser)
        ->test(\App\Livewire\Candidate\ProfileBuilder::class)
        ->set('medCertNumber', 'PRC-MED-8899')
        ->set('medCertUpload', $medFileProfile)
        ->assertSet('medCertFilename', 'profile_medcert.pdf');

    $candidateUser->refresh();
    expect($candidateUser->medical_certificate_path)->not->toBeNull()
        ->and($candidateUser->medical_certificate_number)->toBe('PRC-MED-8899')
        ->and($candidateUser->pwd_document_type)->toBe('both');

    $employerUser = User::factory()->verified()->create(['role' => 'employer', 'name' => 'Hiring Manager']);
    $employerUser->assignRole('employer');
    $employerProfile = EmployerProfile::create([
        'user_id' => $employerUser->id,
        'company_name' => 'Inclusive Careers PH',
    ]);

    $job = JobPosting::create([
        'employer_profile_id' => $employerProfile->id,
        'title' => 'Operations Coordinator',
        'description' => 'Accessible operations coordinator role',
        'status' => 'published',
    ]);

    $pwdScan = UploadedFile::fake()->create('apply_pwd_card.png', 200, 'image/png');
    $medScan = UploadedFile::fake()->create('apply_med_cert.pdf', 300, 'application/pdf');

    Livewire::actingAs($candidateUser)
        ->test(\App\Livewire\Candidate\Apply::class, ['job' => $job])
        ->assertSee('Clinical Medical Certificate:')
        ->set('pwdIdFile', $pwdScan)
        ->set('medicalCertificateFile', $medScan)
        ->call('submit')
        ->assertSet('submitted', true);

    $app = Application::where('candidate_profile_id', $candidateProfile->id)
        ->where('job_posting_id', $job->id)
        ->firstOrFail();

    Livewire::actingAs($employerUser)
        ->test(Applicants::class, ['job' => $job])
        ->call('viewDetails', $app->id)
        ->assertSee('Government PWD ID Card')
        ->assertSee('Clinical Medical Certificate');

    $this->actingAs($employerUser)
        ->get(route('employer.applicants.document', ['application' => $app->id, 'type' => 'pwd_id']))
        ->assertOk();

    $this->actingAs($employerUser)
        ->get(route('employer.applicants.document', ['application' => $app->id, 'type' => 'medical_certificate']))
        ->assertOk();

    // Request Documents modal disables already-attached PWD ID and Medical Certificate and blocks duplicate requests
    Livewire::actingAs($employerUser)
        ->test(Applicants::class, ['job' => $job])
        ->call('openRequestDocsModal', $app->id)
        ->assertViewHas('alreadyAttachedDocTypes', function (array $attached) {
            return in_array('Official PWD ID Card (BIR Tax Credit)', $attached, true)
                && in_array('Medical Certificate (Fit to Work)', $attached, true);
        })
        ->assertSee('Already attached')
        ->set('selectedDocTypes', [
            'Official PWD ID Card (BIR Tax Credit)',
            'Medical Certificate (Fit to Work)',
            'NBI Clearance',
        ])
        ->call('sendDocumentRequest');

    $app->refresh();
    expect(collect($app->requested_documents)->pluck('name')->all())->toBe(['NBI Clearance']);
});
