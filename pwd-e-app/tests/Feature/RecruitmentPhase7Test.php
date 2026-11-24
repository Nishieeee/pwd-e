<?php

use App\Console\Commands\CloseExpiredJobPostings;
use App\Livewire\Candidate\ApplicationsTracker;
use App\Livewire\Employer\Applicants;
use App\Models\Accommodation;
use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\EmployerProfile;
use App\Models\JobPosting;
use App\Models\NotificationPreference;
use App\Models\User;
use App\Notifications\ApplicationStatusChanged;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Accommodation::firstOrCreate(['name' => 'Wheelchair Ramp', 'category' => 'mobility']);
});

test('jobs:close-expired command auto-closes expired jobs and leaves active jobs untouched', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    $profile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Expiring Jobs Co',
        'business_permit_number' => 'BP-EXP-01',
    ]);

    $expiredJob = JobPosting::create([
        'employer_profile_id' => $profile->id,
        'title' => 'Expired Post',
        'description' => 'Test Job',
        'employment_type' => 'full_time',
        'work_arrangement' => 'remote',
        'status' => 'published',
        'closes_at' => Carbon::yesterday(),
    ]);

    $activeJob = JobPosting::create([
        'employer_profile_id' => $profile->id,
        'title' => 'Active Post',
        'description' => 'Test Job',
        'employment_type' => 'full_time',
        'work_arrangement' => 'remote',
        'status' => 'published',
        'closes_at' => Carbon::tomorrow(),
    ]);

    Artisan::call('jobs:close-expired');

    $expiredJob->refresh();
    $activeJob->refresh();

    expect($expiredJob->status)->toBe('closed')
        ->and($activeJob->status)->toBe('published');
});

test('jobs:close-expired is registered in schedule', function () {
    $this->artisan('schedule:list')
        ->assertSuccessful()
        ->expectsOutputToContain('jobs:close-expired');
});

test('extending formal offer notifies candidate via ApplicationStatusChanged', function () {
    Notification::fake();

    $employerUser = User::factory()->verified()->create(['role' => 'employer']);
    $employerProfile = EmployerProfile::create([
        'user_id' => $employerUser->id,
        'company_name' => 'Offer Tech Corp',
        'business_permit_number' => 'BP-OFF-01',
    ]);

    $candidateUser = User::factory()->verified()->create([
        'role' => 'candidate',
        'pwd_id_number' => 'PWD-NOTIF-01',
    ]);
    $candidateProfile = CandidateProfile::create(['user_id' => $candidateUser->id]);

    $job = JobPosting::create([
        'employer_profile_id' => $employerProfile->id,
        'title' => 'Software Engineer',
        'description' => 'Job Details',
        'employment_type' => 'full_time',
        'work_arrangement' => 'remote',
        'status' => 'published',
    ]);

    $app = Application::create([
        'job_posting_id' => $job->id,
        'candidate_profile_id' => $candidateProfile->id,
        'status' => Application::STATUS_INTERVIEW,
    ]);

    Livewire::actingAs($employerUser)
        ->test(Applicants::class, ['job' => $job])
        ->call('openOfferModal', $app->id)
        ->set('offerSalary', 75000)
        ->set('offerStartDate', '2026-12-01')
        ->call('sendOffer');

    Notification::assertSentTo(
        $candidateUser,
        ApplicationStatusChanged::class,
        function ($notification) {
            return $notification->status === Application::STATUS_OFFERED
                && str_contains($notification->note, '75,000');
        }
    );
});

test('candidate offer acceptance notifies employer', function () {
    Notification::fake();

    $employerUser = User::factory()->verified()->create(['role' => 'employer']);
    $employerProfile = EmployerProfile::create([
        'user_id' => $employerUser->id,
        'company_name' => 'Offer Tech Corp',
        'business_permit_number' => 'BP-OFF-02',
    ]);

    $candidateUser = User::factory()->verified()->create([
        'role' => 'candidate',
        'pwd_id_number' => 'PWD-ACCEPT-01',
    ]);
    $candidateProfile = CandidateProfile::create(['user_id' => $candidateUser->id]);

    $job = JobPosting::create([
        'employer_profile_id' => $employerProfile->id,
        'title' => 'UI Designer',
        'description' => 'Job Details',
        'employment_type' => 'full_time',
        'work_arrangement' => 'remote',
        'status' => 'published',
    ]);

    $app = Application::create([
        'job_posting_id' => $job->id,
        'candidate_profile_id' => $candidateProfile->id,
        'status' => Application::STATUS_OFFERED,
        'offer_details' => ['salary' => 60000, 'start_date' => '2026-12-01'],
    ]);

    Livewire::actingAs($candidateUser)
        ->test(ApplicationsTracker::class)
        ->call('acceptOffer', $app->id);

    Notification::assertSentTo(
        $employerUser,
        ApplicationStatusChanged::class,
        function ($notification) {
            return $notification->status === 'offer_accepted';
        }
    );
});

test('notification preference suppresses email channel when disabled', function () {
    $candidateUser = User::factory()->verified()->create([
        'role' => 'candidate',
        'pwd_id_number' => 'PWD-PREF-01',
    ]);

    NotificationPreference::create([
        'user_id' => $candidateUser->id,
        'event_type' => 'application_status',
        'email_enabled' => false,
    ]);

    $notification = new ApplicationStatusChanged('Test Job', 'interview');
    $channels = $notification->via($candidateUser);

    expect($channels)->toBe(['database'])
        ->and($channels)->not->toContain('mail');
});
