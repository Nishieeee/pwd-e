<?php

use App\Livewire\Candidate\Apply;
use App\Livewire\Candidate\JobMatches;
use App\Models\Accommodation;
use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\EmployerProfile;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Accommodation::firstOrCreate(['name' => 'Wheelchair Ramp', 'category' => 'mobility']);
});

test('candidate cannot apply to a draft or closed job posting', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    $employerProfile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Tech Corp',
        'business_permit_number' => 'BP-1234',
    ]);

    $candidate = User::factory()->verified()->create([
        'role' => 'candidate',
        'pwd_id_number' => 'PWD-2026-1111',
    ]);
    CandidateProfile::create(['user_id' => $candidate->id]);

    $draftJob = JobPosting::create([
        'employer_profile_id' => $employerProfile->id,
        'title' => 'Draft Position',
        'description' => 'Detailed job description for test role',
        'employment_type' => 'full_time',
        'work_arrangement' => 'remote',
        'status' => 'draft',
    ]);

    $this->actingAs($candidate)
        ->get(route('candidate.apply', ['job' => $draftJob->id]))
        ->assertForbidden();
});

test('candidate cannot apply to an expired job posting past closes_at date', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    $employerProfile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Tech Corp',
        'business_permit_number' => 'BP-1234',
    ]);

    $candidate = User::factory()->verified()->create([
        'role' => 'candidate',
        'pwd_id_number' => 'PWD-2026-1111',
    ]);
    CandidateProfile::create(['user_id' => $candidate->id]);

    $expiredJob = JobPosting::create([
        'employer_profile_id' => $employerProfile->id,
        'title' => 'Expired Position',
        'description' => 'Detailed job description for test role',
        'employment_type' => 'full_time',
        'work_arrangement' => 'remote',
        'status' => 'published',
        'closes_at' => now()->subDay(),
    ]);

    $this->actingAs($candidate)
        ->get(route('candidate.apply', ['job' => $expiredJob->id]))
        ->assertForbidden();
});

test('candidate who previously withdrew can re-apply and new record links reapplied_from_id', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    $employerProfile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Tech Corp',
        'business_permit_number' => 'BP-1234',
    ]);

    $candidate = User::factory()->verified()->create([
        'role' => 'candidate',
        'pwd_id_number' => 'PWD-2026-1111',
    ]);
    $cp = CandidateProfile::create([
        'user_id' => $candidate->id,
        'resume_pdf_path' => 'resumes/sample.pdf',
    ]);

    $job = JobPosting::create([
        'employer_profile_id' => $employerProfile->id,
        'title' => 'Senior Developer',
        'description' => 'Detailed job description for senior developer role',
        'employment_type' => 'full_time',
        'work_arrangement' => 'remote',
        'status' => 'published',
    ]);

    $withdrawnApp = Application::create([
        'candidate_profile_id' => $cp->id,
        'job_posting_id' => $job->id,
        'status' => Application::STATUS_WITHDRAWN,
    ]);

    Livewire::actingAs($candidate)
        ->test(Apply::class, ['job' => $job])
        ->set('includeBiodata', true)
        ->set('includeResume', true)
        ->call('submit')
        ->assertHasNoErrors();

    $reappliedApp = Application::where('candidate_profile_id', $cp->id)
        ->where('job_posting_id', $job->id)
        ->where('status', Application::STATUS_APPLIED)
        ->first();

    expect($reappliedApp)->not->toBeNull()
        ->and($reappliedApp->reapplied_from_id)->toBe($withdrawnApp->id);
});

test('candidate with active application is blocked from submitting a duplicate application', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    $employerProfile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Tech Corp',
        'business_permit_number' => 'BP-1234',
    ]);

    $candidate = User::factory()->verified()->create([
        'role' => 'candidate',
        'pwd_id_number' => 'PWD-2026-1111',
    ]);
    $cp = CandidateProfile::create([
        'user_id' => $candidate->id,
        'resume_pdf_path' => 'resumes/sample.pdf',
    ]);

    $job = JobPosting::create([
        'employer_profile_id' => $employerProfile->id,
        'title' => 'Software Engineer',
        'description' => 'Detailed job description for software engineer',
        'employment_type' => 'full_time',
        'work_arrangement' => 'remote',
        'status' => 'published',
    ]);

    Application::create([
        'candidate_profile_id' => $cp->id,
        'job_posting_id' => $job->id,
        'status' => Application::STATUS_UNDER_REVIEW,
    ]);

    Livewire::actingAs($candidate)
        ->test(Apply::class, ['job' => $job])
        ->set('includeBiodata', true)
        ->call('submit');

    expect(Application::where('candidate_profile_id', $cp->id)->where('job_posting_id', $job->id)->count())->toBe(1);
});

test('quick apply from job matches attaches candidate materials, notifies employer, and dispatches event', function () {
    \Illuminate\Support\Facades\Notification::fake();

    $employer = User::factory()->verified()->create(['role' => 'employer']);
    $employerProfile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Tech Corp',
        'business_permit_number' => 'BP-1234',
    ]);

    $candidate = User::factory()->verified()->create([
        'role' => 'candidate',
        'pwd_id_number' => 'PWD-2026-1111',
    ]);
    $cp = CandidateProfile::create([
        'user_id' => $candidate->id,
        'resume_pdf_path' => 'resumes/sample.pdf',
    ]);

    $job = JobPosting::create([
        'employer_profile_id' => $employerProfile->id,
        'title' => 'Data Analyst',
        'description' => 'Detailed job description for data analyst role',
        'employment_type' => 'full_time',
        'work_arrangement' => 'remote',
        'status' => 'published',
    ]);

    Livewire::actingAs($candidate)
        ->test(JobMatches::class)
        ->call('applyForJob', $job->id)
        ->assertDispatched('application-submitted');

    $app = Application::where('candidate_profile_id', $cp->id)
        ->where('job_posting_id', $job->id)
        ->first();

    expect($app)->not->toBeNull()
        ->and($app->resume_path)->toBe('resumes/sample.pdf')
        ->and($app->application_materials['biodata'])->toBeTrue()
        ->and($app->application_materials['resume'])->toBeTrue();

    \Illuminate\Support\Facades\Notification::assertSentTo($employer, \App\Notifications\ApplicationReceived::class);
});
