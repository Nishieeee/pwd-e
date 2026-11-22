<?php

use App\Livewire\Employer\Jobs\Create;
use App\Livewire\Employer\Jobs\Edit;
use App\Livewire\Employer\Talents;
use App\Models\Accommodation;
use App\Models\CandidateProfile;
use App\Models\EmployerProfile;
use App\Models\JobInvitation;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Accommodation::firstOrCreate(['name' => 'Wheelchair Ramp', 'category' => 'mobility']);
    Accommodation::firstOrCreate(['name' => 'Screen Reader Compatible', 'category' => 'visual']);
});

test('employer cannot send duplicate invitations to the same candidate for the same role', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    $employerProfile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Innovate Corp',
        'business_permit_number' => 'BP-5555',
    ]);

    $candidate = User::factory()->verified()->create(['role' => 'candidate']);
    $cp = CandidateProfile::create(['user_id' => $candidate->id]);

    $job = JobPosting::create([
        'employer_profile_id' => $employerProfile->id,
        'title' => 'Product Designer',
        'description' => 'Detailed product design job description',
        'employment_type' => 'full_time',
        'work_arrangement' => 'remote',
        'status' => 'published',
    ]);

    JobInvitation::create([
        'employer_profile_id' => $employerProfile->id,
        'job_posting_id' => $job->id,
        'candidate_profile_id' => $cp->id,
        'status' => 'pending',
    ]);

    Livewire::actingAs($employer)
        ->test(Talents::class)
        ->set('invitingCandidateProfileId', $cp->id)
        ->set('selectedJobId', $job->id)
        ->call('sendInvitation')
        ->assertHasErrors(['selectedJobId']);

    expect(JobInvitation::where('candidate_profile_id', $cp->id)->where('job_posting_id', $job->id)->count())->toBe(1);
});

test('employer cannot send invitations for closed or expired jobs', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    $employerProfile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Innovate Corp',
        'business_permit_number' => 'BP-5555',
    ]);

    $candidate = User::factory()->verified()->create(['role' => 'candidate']);
    $cp = CandidateProfile::create(['user_id' => $candidate->id]);

    $closedJob = JobPosting::create([
        'employer_profile_id' => $employerProfile->id,
        'title' => 'Closed Position',
        'description' => 'Detailed description for closed position',
        'employment_type' => 'full_time',
        'work_arrangement' => 'remote',
        'status' => 'closed',
    ]);

    Livewire::actingAs($employer)
        ->test(Talents::class)
        ->set('invitingCandidateProfileId', $cp->id)
        ->set('selectedJobId', $closedJob->id)
        ->call('sendInvitation')
        ->assertHasErrors(['selectedJobId']);

    expect(JobInvitation::count())->toBe(0);
});

test('invitation rate limiter blocks sending invitations past daily limit', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    $employerProfile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Innovate Corp',
        'business_permit_number' => 'BP-5555',
    ]);

    $candidate = User::factory()->verified()->create(['role' => 'candidate']);
    $cp = CandidateProfile::create(['user_id' => $candidate->id]);

    $job = JobPosting::create([
        'employer_profile_id' => $employerProfile->id,
        'title' => 'Data Analyst',
        'description' => 'Detailed description for data analyst',
        'employment_type' => 'full_time',
        'work_arrangement' => 'remote',
        'status' => 'published',
    ]);

    // Create 25 existing invitations within last 24h
    for ($i = 0; $i < 25; $i++) {
        $otherCandidate = User::factory()->verified()->create(['role' => 'candidate']);
        $otherCp = CandidateProfile::create(['user_id' => $otherCandidate->id]);
        JobInvitation::create([
            'employer_profile_id' => $employerProfile->id,
            'job_posting_id' => $job->id,
            'candidate_profile_id' => $otherCp->id,
            'status' => 'pending',
            'created_at' => now()->subMinutes(10),
        ]);
    }

    Livewire::actingAs($employer)
        ->test(Talents::class)
        ->set('invitingCandidateProfileId', $cp->id)
        ->set('selectedJobId', $job->id)
        ->call('sendInvitation')
        ->assertHasErrors(['selectedJobId']);
});

test('employer publishing is blocked when active job posting limit is reached', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    $employerProfile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Innovate Corp',
        'business_permit_number' => 'BP-5555',
        'matrix_completed_at' => now(),
    ]);

    $acc = Accommodation::first();

    // Create 5 published jobs
    for ($i = 0; $i < 5; $i++) {
        JobPosting::create([
            'employer_profile_id' => $employerProfile->id,
            'title' => "Active Role {$i}",
            'description' => 'Detailed active role description here',
            'employment_type' => 'full_time',
            'work_arrangement' => 'remote',
            'status' => 'published',
        ]);
    }

    Livewire::actingAs($employer)
        ->test(Create::class)
        ->set('title', 'Sixth Role Attempt')
        ->set('description', 'Detailed description for sixth role attempt with enough length')
        ->set('employment_type', 'full_time')
        ->set('work_arrangement', 'remote')
        ->set('selectedAccommodations', [$acc->id])
        ->call('publish')
        ->assertHasErrors(['title']);

    expect(JobPosting::where('employer_profile_id', $employerProfile->id)->where('status', 'published')->count())->toBe(5);
});

test('editing a published job rejects past closing date or removing all accommodations', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    $employerProfile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Innovate Corp',
        'business_permit_number' => 'BP-5555',
    ]);

    $acc = Accommodation::first();
    $job = JobPosting::create([
        'employer_profile_id' => $employerProfile->id,
        'title' => 'Quality Lead',
        'description' => 'Detailed quality lead description goes here',
        'employment_type' => 'full_time',
        'work_arrangement' => 'remote',
        'status' => 'published',
        'closes_at' => now()->addMonth(),
    ]);
    $job->accommodations()->attach($acc->id);

    Livewire::actingAs($employer)
        ->test(Edit::class, ['job' => $job])
        ->set('closes_at', now()->subDay()->format('Y-m-d'))
        ->set('selectedAccommodations', [])
        ->call('saveChanges')
        ->assertHasErrors(['closes_at', 'selectedAccommodations']);
});
