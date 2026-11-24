<?php

use App\Livewire\Candidate\ApplicationsTracker;
use App\Livewire\Employer\Applicants;
use App\Models\Accommodation;
use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\EmployerProfile;
use App\Models\JobPosting;
use App\Models\User;
use App\Models\WorkforceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Accommodation::firstOrCreate(['name' => 'Wheelchair Ramp', 'category' => 'mobility']);
});

test('state machine blocks illegal transitions such as applied directly to hired', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    $employerProfile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Inclusive Inc',
        'business_permit_number' => 'BP-1234',
    ]);

    $candidate = User::factory()->verified()->create(['role' => 'candidate']);
    $candidateProfile = CandidateProfile::create(['user_id' => $candidate->id]);

    $job = JobPosting::create([
        'employer_profile_id' => $employerProfile->id,
        'title' => 'Accessibility Specialist',
        'description' => 'Detailed job description for accessibility testing',
        'employment_type' => 'full_time',
        'work_arrangement' => 'remote',
        'status' => 'published',
    ]);

    $app = Application::create([
        'candidate_profile_id' => $candidateProfile->id,
        'job_posting_id' => $job->id,
        'status' => Application::STATUS_APPLIED,
    ]);

    Livewire::actingAs($employer)
        ->test(Applicants::class, ['job' => $job])
        ->call('updateStatus', $app->id, Application::STATUS_HIRED);

    expect($app->fresh()->status)->toBe(Application::STATUS_APPLIED);
});

test('employer can extend formal offer to interviewed candidate', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    $employerProfile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Inclusive Inc',
        'business_permit_number' => 'BP-1234',
    ]);

    $candidate = User::factory()->verified()->create(['role' => 'candidate']);
    $candidateProfile = CandidateProfile::create(['user_id' => $candidate->id]);

    $job = JobPosting::create([
        'employer_profile_id' => $employerProfile->id,
        'title' => 'QA Engineer',
        'description' => 'Detailed job description for quality assurance testing',
        'employment_type' => 'full_time',
        'work_arrangement' => 'remote',
        'status' => 'published',
    ]);

    $app = Application::create([
        'candidate_profile_id' => $candidateProfile->id,
        'job_posting_id' => $job->id,
        'status' => Application::STATUS_INTERVIEW,
    ]);

    Livewire::actingAs($employer)
        ->test(Applicants::class, ['job' => $job])
        ->call('openOfferModal', $app->id)
        ->set('offerSalary', 45000)
        ->set('offerStartDate', now()->addWeek()->format('Y-m-d'))
        ->set('offerAccommodations', 'Screen reader license + ergonomic desk')
        ->set('offerNotes', 'Welcome to the team!')
        ->call('sendOffer');

    $app->refresh();
    expect($app->status)->toBe(Application::STATUS_OFFERED)
        ->and($app->offer_extended_at)->not->toBeNull()
        ->and((float) $app->offer_details['salary'])->toBe(45000.0)
        ->and($app->offer_details['accommodations'])->toBe('Screen reader license + ergonomic desk');
});

test('candidate can accept offer which transitions status to hired and creates workforce record', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    $employerProfile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Inclusive Inc',
        'business_permit_number' => 'BP-1234',
    ]);

    $candidate = User::factory()->verified()->create([
        'role' => 'candidate',
        'pwd_id_number' => 'PWD-2026-9999',
    ]);
    $candidateProfile = CandidateProfile::create(['user_id' => $candidate->id]);

    $job = JobPosting::create([
        'employer_profile_id' => $employerProfile->id,
        'title' => 'DevOps Specialist',
        'description' => 'Detailed job description for devops engineer',
        'employment_type' => 'full_time',
        'work_arrangement' => 'remote',
        'status' => 'published',
    ]);

    $app = Application::create([
        'candidate_profile_id' => $candidateProfile->id,
        'job_posting_id' => $job->id,
        'status' => Application::STATUS_OFFERED,
        'offer_details' => [
            'salary' => 55000.0,
            'start_date' => now()->addWeeks(2)->format('Y-m-d'),
        ],
    ]);

    Livewire::actingAs($candidate)
        ->test(ApplicationsTracker::class)
        ->call('acceptOffer', $app->id);

    $app->refresh();
    expect($app->status)->toBe(Application::STATUS_HIRED)
        ->and($app->offer_accepted_at)->not->toBeNull();

    $workforce = WorkforceRecord::where('application_id', $app->id)->first();
    expect($workforce)->not->toBeNull()
        ->and($workforce->candidate_profile_id)->toBe($candidateProfile->id)
        ->and($workforce->employer_profile_id)->toBe($employerProfile->id)
        ->and($workforce->pwd_verified)->toBeTrue()
        ->and((float) $workforce->annual_wage)->toBe(55000.0);
});

test('candidate can decline offer without creating workforce record', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    $employerProfile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Inclusive Inc',
        'business_permit_number' => 'BP-1234',
    ]);

    $candidate = User::factory()->verified()->create(['role' => 'candidate']);
    $candidateProfile = CandidateProfile::create(['user_id' => $candidate->id]);

    $job = JobPosting::create([
        'employer_profile_id' => $employerProfile->id,
        'title' => 'Cloud Architect',
        'description' => 'Detailed job description for cloud architecture',
        'employment_type' => 'full_time',
        'work_arrangement' => 'remote',
        'status' => 'published',
    ]);

    $app = Application::create([
        'candidate_profile_id' => $candidateProfile->id,
        'job_posting_id' => $job->id,
        'status' => Application::STATUS_OFFERED,
        'offer_details' => ['salary' => 60000.0],
    ]);

    Livewire::actingAs($candidate)
        ->test(ApplicationsTracker::class)
        ->call('openDeclineModal', $app->id)
        ->set('declineReason', 'Accepted another opportunity')
        ->call('confirmDeclineOffer');

    $app->refresh();
    expect($app->status)->toBe(Application::STATUS_OFFER_DECLINED)
        ->and($app->offer_declined_at)->not->toBeNull()
        ->and($app->offer_decline_reason)->toBe('Accepted another opportunity')
        ->and(WorkforceRecord::where('application_id', $app->id)->count())->toBe(0);
});

test('candidate cannot withdraw an application in a terminal state', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    $employerProfile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Inclusive Inc',
        'business_permit_number' => 'BP-1234',
    ]);

    $candidate = User::factory()->verified()->create(['role' => 'candidate']);
    $candidateProfile = CandidateProfile::create(['user_id' => $candidate->id]);

    $job = JobPosting::create([
        'employer_profile_id' => $employerProfile->id,
        'title' => 'Security Analyst',
        'description' => 'Detailed job description for security specialist',
        'employment_type' => 'full_time',
        'work_arrangement' => 'remote',
        'status' => 'published',
    ]);

    $hiredApp = Application::create([
        'candidate_profile_id' => $candidateProfile->id,
        'job_posting_id' => $job->id,
        'status' => Application::STATUS_HIRED,
    ]);

    Livewire::actingAs($candidate)
        ->test(ApplicationsTracker::class)
        ->call('promptWithdraw', $hiredApp->id)
        ->call('confirmWithdraw');

    expect($hiredApp->fresh()->status)->toBe(Application::STATUS_HIRED);
});

test('interview completion state updates application subtext correctly', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    $employerProfile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Inclusive Inc',
        'business_permit_number' => 'BP-1234',
    ]);

    $candidate = User::factory()->verified()->create(['role' => 'candidate']);
    $candidateProfile = CandidateProfile::create(['user_id' => $candidate->id]);

    $job = JobPosting::create([
        'employer_profile_id' => $employerProfile->id,
        'title' => 'UX Researcher',
        'description' => 'Detailed job description for user experience researcher',
        'employment_type' => 'full_time',
        'work_arrangement' => 'remote',
        'status' => 'published',
    ]);

    $app = Application::create([
        'candidate_profile_id' => $candidateProfile->id,
        'job_posting_id' => $job->id,
        'status' => Application::STATUS_INTERVIEW,
    ]);

    Livewire::actingAs($employer)
        ->test(Applicants::class, ['job' => $job])
        ->call('markInterviewCompleted', $app->id);

    expect($app->fresh()->status_subtext)->toContain('Interview completed');
});
