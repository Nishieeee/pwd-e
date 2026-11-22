<?php

use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\EmployerProfile;
use App\Models\JobPosting;
use App\Models\User;
use App\Models\WorkforceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('recruitment configuration exists with expected defaults', function () {
    expect(config('recruitment.max_active_job_postings'))->toBe(5)
        ->and(config('recruitment.max_daily_invitations'))->toBe(25);
});

test('application model defines all lifecycle statuses and transitions correctly', function () {
    $app = new Application(['status' => Application::STATUS_APPLIED]);

    expect($app->canTransitionTo(Application::STATUS_UNDER_REVIEW))->toBeTrue()
        ->and($app->canTransitionTo(Application::STATUS_SHORTLISTED))->toBeTrue()
        ->and($app->canTransitionTo(Application::STATUS_WITHDRAWN))->toBeTrue()
        ->and($app->canTransitionTo(Application::STATUS_HIRED))->toBeFalse(); // Cannot skip to hired

    $interviewApp = new Application(['status' => Application::STATUS_INTERVIEW]);
    expect($interviewApp->canTransitionTo(Application::STATUS_OFFERED))->toBeTrue()
        ->and($interviewApp->canTransitionTo(Application::STATUS_REJECTED))->toBeTrue()
        ->and($interviewApp->canTransitionTo(Application::STATUS_APPLIED))->toBeFalse();

    $hiredApp = new Application(['status' => Application::STATUS_HIRED]);
    expect($hiredApp->isTerminal())->toBeTrue()
        ->and($hiredApp->canTransitionTo(Application::STATUS_WITHDRAWN))->toBeFalse();
});

test('application supports reapplied_from foreign relationship and multiple applications for reapply', function () {
    $employer = User::factory()->create(['role' => 'employer']);
    $employerProfile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Acme Corp',
    ]);

    $candidate = User::factory()->create(['role' => 'candidate']);
    $candidateProfile = CandidateProfile::create([
        'user_id' => $candidate->id,
    ]);

    $job = JobPosting::create([
        'employer_profile_id' => $employerProfile->id,
        'title' => 'Software Engineer',
        'description' => 'Test job description with enough characters',
        'employment_type' => 'full_time',
        'work_arrangement' => 'remote',
        'status' => 'published',
    ]);

    $originalApp = Application::create([
        'candidate_profile_id' => $candidateProfile->id,
        'job_posting_id' => $job->id,
        'status' => Application::STATUS_WITHDRAWN,
    ]);

    $reappliedApp = Application::create([
        'candidate_profile_id' => $candidateProfile->id,
        'job_posting_id' => $job->id,
        'status' => Application::STATUS_APPLIED,
        'reapplied_from_id' => $originalApp->id,
        'offer_extended_at' => now(),
    ]);

    expect($reappliedApp->reappliedFrom->id)->toBe($originalApp->id)
        ->and($originalApp->reapplications->first()->id)->toBe($reappliedApp->id)
        ->and($reappliedApp->offer_extended_at)->not->toBeNull();
});

test('workforce records support soft deletes without permanent data loss', function () {
    $employer = User::factory()->create(['role' => 'employer']);
    $employerProfile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Acme Corp',
    ]);

    $record = WorkforceRecord::create([
        'employer_profile_id' => $employerProfile->id,
        'hired_at' => now(),
        'pwd_verified' => true,
        'annual_wage' => 450000.00,
    ]);

    $record->delete();

    expect(WorkforceRecord::count())->toBe(0)
        ->and(WorkforceRecord::withTrashed()->count())->toBe(1);

    $record->restore();
    expect(WorkforceRecord::count())->toBe(1);
});
