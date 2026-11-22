<?php

use App\Livewire\Admin\VerificationQueue;
use App\Livewire\Auth\ResubmitVerification;
use App\Livewire\Candidate\ApplicationsTracker;
use App\Livewire\Employer\Applicants;
use App\Livewire\Employer\Workforce;
use App\Models\Accommodation;
use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\EmployerProfile;
use App\Models\JobPosting;
use App\Models\User;
use App\Models\WorkforceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Accommodation::firstOrCreate(['name' => 'Screen Reader Compatible', 'category' => 'visual']);
});

test('admin rejection does not lock out user and keeps status pending for resubmission', function () {
    $admin = User::factory()->verified()->create(['role' => 'admin']);
    $candidate = User::factory()->create([
        'role' => 'candidate',
        'status' => 'pending',
        'pwd_id_path' => 'verifications/pwd_ids/old.jpg',
    ]);

    $vr = \App\Models\VerificationRequest::create([
        'user_id' => $candidate->id,
        'type' => 'pwd_id',
        'id_number' => 'PWD-OLD-01',
        'document_path' => 'verifications/pwd_ids/old.jpg',
        'status' => 'pending',
    ]);

    Livewire::actingAs($admin)
        ->test(VerificationQueue::class)
        ->set('reviewNote', 'ID card photo is blurry. Please upload a clear copy.')
        ->call('reject', $vr->id);

    $candidate->refresh();
    $vr->refresh();

    expect($candidate->status)->toBe('rejected')
        ->and($vr->status)->toBe('rejected')
        ->and($vr->rejection_reason)->toBe('ID card photo is blurry. Please upload a clear copy.');
});

test('rejected candidate can resubmit verification documents and notify admin', function () {
    Storage::fake('private');

    $candidate = User::factory()->create([
        'role' => 'candidate',
        'status' => 'pending',
        'pwd_id_path' => 'verifications/pwd_ids/old.jpg',
    ]);

    $file = UploadedFile::fake()->create('id_new.jpg', 500, 'image/jpeg');

    Livewire::actingAs($candidate)
        ->test(ResubmitVerification::class)
        ->set('documentFile', $file)
        ->set('idNumber', 'PWD-RESUBMIT-9999')
        ->call('submit')
        ->assertHasNoErrors();

    $candidate->refresh();
    expect($candidate->status)->toBe('pending')
        ->and($candidate->pwd_id_number)->toBe('PWD-RESUBMIT-9999')
        ->and($candidate->pwd_id_path)->not->toBe('verifications/pwd_ids/old.jpg');

    $latestVr = \App\Models\VerificationRequest::where('user_id', $candidate->id)->latest()->first();
    expect($latestVr)->not->toBeNull()
        ->and($latestVr->status)->toBe('pending')
        ->and($latestVr->id_number)->toBe('PWD-RESUBMIT-9999');
});

test('workforce pwd verification derives strictly from candidate profile and logs to activity_log', function () {
    $employerUser = User::factory()->verified()->create(['role' => 'employer']);
    $employerProfile = EmployerProfile::create([
        'user_id' => $employerUser->id,
        'company_name' => 'Global Logistics Inc',
        'business_permit_number' => 'BP-9988',
    ]);

    $candidateUser = User::factory()->verified()->create([
        'role' => 'candidate',
        'pwd_id_number' => 'PWD-VERIFIED-123',
    ]);
    $candidateProfile = CandidateProfile::create(['user_id' => $candidateUser->id]);

    $record = WorkforceRecord::create([
        'employer_profile_id' => $employerProfile->id,
        'candidate_profile_id' => $candidateProfile->id,
        'hired_at' => now(),
        'pwd_verified' => false,
        'annual_wage' => 480000,
    ]);

    Livewire::actingAs($employerUser)
        ->test(Workforce::class)
        ->call('syncPwdVerification', $record->id);

    $record->refresh();
    expect($record->pwd_verified)->toBeTrue();

    // Verify activity_log entry
    $activity = DB::table('activity_log')
        ->where('log_name', 'workforce')
        ->where('subject_id', $record->id)
        ->first();

    expect($activity)->not->toBeNull()
        ->and($activity->description)->toContain('synchronized');
});

test('withdrawing application automatically sets ended_at on active workforce record', function () {
    $employerUser = User::factory()->verified()->create(['role' => 'employer']);
    $employerProfile = EmployerProfile::create([
        'user_id' => $employerUser->id,
        'company_name' => 'Acme Corp',
        'business_permit_number' => 'BP-ACME-01',
    ]);

    $candidateUser = User::factory()->verified()->create([
        'role' => 'candidate',
        'pwd_id_number' => 'PWD-TEST-4444',
    ]);
    $candidateProfile = CandidateProfile::create(['user_id' => $candidateUser->id]);

    $job = JobPosting::create([
        'employer_profile_id' => $employerProfile->id,
        'title' => 'Backend Developer',
        'description' => 'Test Job',
        'employment_type' => 'full_time',
        'work_arrangement' => 'remote',
        'status' => 'published',
    ]);

    $application = Application::create([
        'job_posting_id' => $job->id,
        'candidate_profile_id' => $candidateProfile->id,
        'status' => Application::STATUS_OFFERED,
    ]);

    $workforce = WorkforceRecord::create([
        'application_id' => $application->id,
        'employer_profile_id' => $employerProfile->id,
        'candidate_profile_id' => $candidateProfile->id,
        'hired_at' => now(),
        'ended_at' => null,
        'pwd_verified' => true,
        'annual_wage' => 500000,
    ]);

    Livewire::actingAs($candidateUser)
        ->test(ApplicationsTracker::class)
        ->call('promptWithdraw', $application->id)
        ->call('confirmWithdraw');

    $application->refresh();
    $workforce->refresh();

    expect($application->status)->toBe('closed')
        ->and($workforce->ended_at)->not->toBeNull();
});

test('self-hiring fraud attempt is blocked and logged', function () {
    $dualUser = User::factory()->verified()->create([
        'role' => 'employer',
        'pwd_id_number' => 'PWD-DUAL-001',
    ]);
    $employerProfile = EmployerProfile::create([
        'user_id' => $dualUser->id,
        'company_name' => 'Self Hire Ltd',
        'business_permit_number' => 'BP-SELF-01',
    ]);
    $candidateProfile = CandidateProfile::create(['user_id' => $dualUser->id]);

    $job = JobPosting::create([
        'employer_profile_id' => $employerProfile->id,
        'title' => 'Lead Architect',
        'description' => 'Test Job',
        'employment_type' => 'full_time',
        'work_arrangement' => 'remote',
        'status' => 'published',
    ]);

    $application = Application::create([
        'job_posting_id' => $job->id,
        'candidate_profile_id' => $candidateProfile->id,
        'status' => Application::STATUS_INTERVIEW,
    ]);

    Livewire::actingAs($dualUser)
        ->test(Applicants::class, ['job' => $job])
        ->call('openOfferModal', $application->id)
        ->call('sendOffer');

    $application->refresh();
    expect($application->status)->toBe(Application::STATUS_INTERVIEW);

    $fraudLog = DB::table('activity_log')
        ->where('log_name', 'fraud_alert')
        ->first();

    expect($fraudLog)->not->toBeNull()
        ->and($fraudLog->description)->toBe('Self-hiring attempt blocked');
});
