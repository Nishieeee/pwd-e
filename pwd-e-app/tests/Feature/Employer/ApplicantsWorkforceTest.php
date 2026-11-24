<?php

use App\Livewire\Employer\Applicants;
use App\Livewire\Employer\Workforce;
use App\Models\Application;
use App\Models\EmployerProfile;
use App\Models\JobPosting;
use App\Models\User;
use App\Models\WorkforceRecord;
use Livewire\Livewire;

beforeEach(function () {
    foreach (['candidate', 'employer', 'admin'] as $r) {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => $r, 'guard_name' => 'web']);
    }
});

function makeEmployerWithJob(): array
{
    $user = User::factory()->verified()->create(['role' => 'employer']);
    $user->assignRole('employer');
    $profile = EmployerProfile::create([
        'user_id' => $user->id,
        'company_name' => 'Acme',
        'business_permit_number' => 'BP-1',
    ]);
    $job = JobPosting::create([
        'employer_profile_id' => $profile->id,
        'title' => 'Open Role',
        'description' => 'Desc',
        'status' => 'published',
    ]);

    return [$user, $profile, $job];
}

test('guests are redirected from applicants page', function () {
    [, , $job] = makeEmployerWithJob();
    $this->get(route('employer.jobs.applicants', $job))->assertRedirect(route('login'));
});

test('employer can view applicants page', function () {
    [$user, , $job] = makeEmployerWithJob();
    $this->actingAs($user)->get(route('employer.jobs.applicants', $job))->assertOk();
});

test('employer can view centralized applicants page across all jobs', function () {
    [$user, $profile, $job] = makeEmployerWithJob();
    $candidate = User::factory()->verified()->create(['role' => 'candidate']);
    $candidate->assignRole('candidate');
    $cp = \App\Models\CandidateProfile::firstOrCreate(['user_id' => $candidate->id]);
    Application::create([
        'candidate_profile_id' => $cp->id,
        'job_posting_id' => $job->id,
        'status' => 'applied',
        'applied_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('employer.applicants'))
        ->assertOk()
        ->assertSee('Applicants')
        ->assertSee('All Positions')
        ->assertSee($candidate->name)
        ->assertSee('Profile');
});

test('employer dashboard renders live metrics and recent applicants', function () {
    [$user, $profile, $job] = makeEmployerWithJob();
    $candidate = User::factory()->verified()->create(['role' => 'candidate']);
    $candidate->assignRole('candidate');
    $cp = \App\Models\CandidateProfile::firstOrCreate(['user_id' => $candidate->id]);
    Application::create([
        'candidate_profile_id' => $cp->id,
        'job_posting_id' => $job->id,
        'status' => 'applied',
        'applied_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Welcome back,')
        ->assertSee('Active Postings')
        ->assertSee('Total Applicants')
        ->assertSee('Recent Applicants')
        ->assertSee($candidate->name)
        ->assertSee('Profile')
        ->assertSee('RA 10524');
});

test('employer can update applicant status', function () {
    [$user, $profile, $job] = makeEmployerWithJob();
    $candidate = User::factory()->verified()->create(['role' => 'candidate']);
    $candidate->assignRole('candidate');
    $cp = \App\Models\CandidateProfile::firstOrCreate(['user_id' => $candidate->id]);
    $app = Application::create([
        'candidate_profile_id' => $cp->id,
        'job_posting_id' => $job->id,
        'status' => 'applied',
        'applied_at' => now(),
    ]);

    $this->actingAs($user);
    Livewire::test(Applicants::class, ['job' => $job])
        ->call('updateStatus', $app->id, 'interview')
        ->assertHasNoErrors();

    expect($app->fresh()->status)->toBe('interview');
});

test('employer can hire applicant and creates workforce record', function () {
    [$user, $profile, $job] = makeEmployerWithJob();
    $candidate = User::factory()->verified()->create(['role' => 'candidate']);
    $candidate->assignRole('candidate');
    $cp = \App\Models\CandidateProfile::firstOrCreate(['user_id' => $candidate->id]);
    $app = Application::create([
        'candidate_profile_id' => $cp->id,
        'job_posting_id' => $job->id,
        'status' => 'interview',
        'applied_at' => now(),
    ]);

    $this->actingAs($user);
    Livewire::test(Applicants::class, ['job' => $job])
        ->call('hire', $app->id)
        ->assertHasNoErrors();

    expect($app->fresh()->status)->toBe('hired');
    expect(WorkforceRecord::where('application_id', $app->id)->exists())->toBeTrue();
});

test('employer can schedule interview with accommodations and meeting link', function () {
    [$user, $profile, $job] = makeEmployerWithJob();
    $candidate = User::factory()->verified()->create(['role' => 'candidate']);
    $candidate->assignRole('candidate');
    $cp = \App\Models\CandidateProfile::firstOrCreate(['user_id' => $candidate->id]);
    $app = Application::create([
        'candidate_profile_id' => $cp->id,
        'job_posting_id' => $job->id,
        'status' => 'applied',
        'applied_at' => now(),
    ]);

    $this->actingAs($user);
    Livewire::test(Applicants::class, ['job' => $job])
        ->call('openInterviewModal', $app->id)
        ->assertSet('showInterviewModal', true)
        ->set('interviewUrl', 'https://meet.google.com/test-meet')
        ->set('interviewAccommodations', 'FSL Interpreter provided')
        ->call('saveInterview')
        ->assertHasNoErrors()
        ->assertSet('showInterviewModal', false);

    $freshApp = $app->fresh();
    expect($freshApp->status)->toBe('interview');
    expect($freshApp->interview_url)->toBe('https://meet.google.com/test-meet');
    expect($freshApp->interview_accommodations)->toBe('FSL Interpreter provided');
});

test('employer can filter applicants by status tabs', function () {
    [$user, $profile, $job] = makeEmployerWithJob();
    $this->actingAs($user);

    Livewire::test(Applicants::class, ['job' => $job])
        ->assertSee('All Applicants')
        ->call('setStatusFilter', 'interview')
        ->assertSet('statusFilter', 'interview');
});

test('employer can decline applicant with remark and notify candidate', function () {
    \Illuminate\Support\Facades\Notification::fake();

    [$user, $profile, $job] = makeEmployerWithJob();
    $candidate = User::factory()->verified()->create(['role' => 'candidate']);
    $candidate->assignRole('candidate');
    $cp = \App\Models\CandidateProfile::firstOrCreate(['user_id' => $candidate->id]);
    $app = Application::create([
        'candidate_profile_id' => $cp->id,
        'job_posting_id' => $job->id,
        'status' => 'applied',
        'applied_at' => now(),
    ]);

    $this->actingAs($user);
    Livewire::test(Applicants::class, ['job' => $job])
        ->assertSee('Decline')
        ->call('openRejectModal', $app->id)
        ->assertSet('showRejectModal', true)
        ->assertSet('rejectingAppId', $app->id)
        ->set('rejectionReason', 'Position filled by another applicant')
        ->call('confirmReject')
        ->assertHasNoErrors()
        ->assertSet('showRejectModal', false);

    $freshApp = $app->fresh();
    expect($freshApp->status)->toBe('rejected');
    expect($freshApp->rejection_reason)->toBe('Position filled by another applicant');
    expect($freshApp->rejected_at)->not->toBeNull();

    \Illuminate\Support\Facades\Notification::assertSentTo(
        $candidate,
        \App\Notifications\ApplicationStatusChanged::class,
        function ($notification) use ($job) {
            return $notification->status === 'rejected'
                && $notification->note === 'Position filled by another applicant'
                && $notification->jobTitle === $job->title;
        }
    );
});

test('employer can view workforce page', function () {
    [$user] = makeEmployerWithJob();
    $this->actingAs($user)->get(route('employer.workforce'))->assertOk();
});

test('workforce page shows empty state', function () {
    [$user] = makeEmployerWithJob();
    $this->actingAs($user);
    Livewire::test(Workforce::class)->assertSee('No active workforce records yet');
});

test('applicant details modal shows extend offer and hides options when hired', function () {
    [$user, $profile, $job] = makeEmployerWithJob();
    $candidate = User::factory()->verified()->create(['role' => 'candidate']);
    $candidate->assignRole('candidate');
    $cp = \App\Models\CandidateProfile::firstOrCreate(['user_id' => $candidate->id]);
    $app = Application::create([
        'candidate_profile_id' => $cp->id,
        'job_posting_id' => $job->id,
        'status' => 'applied',
        'applied_at' => now(),
    ]);

    $this->actingAs($user);

    // When applied, details modal shows "Extend Offer" option
    Livewire::test(Applicants::class, ['job' => $job])
        ->call('viewDetails', $app->id)
        ->assertSee('Update application stage:')
        ->assertSee('Extend Offer')
        ->assertDontSee('value="hired"', false);

    // When hired, details modal shows immutable "Hired" badge and NO stage dropdown
    $app->update(['status' => 'hired']);

    Livewire::test(Applicants::class, ['job' => $job])
        ->call('viewDetails', $app->id)
        ->assertSee('Application stage:')
        ->assertSee('Hired')
        ->assertSee('View in Workforce & Tax')
        ->assertDontSee('Update application stage:')
        ->assertDontSee('Decline Candidate');
});

test('employer can end employment with reason and move to separated staff', function () {
    [$user, $profile, $job] = makeEmployerWithJob();
    $candidate = User::factory()->verified()->create(['role' => 'candidate', 'pwd_id_number' => 'PWD-1234']);
    $candidate->assignRole('candidate');
    $cp = \App\Models\CandidateProfile::firstOrCreate(['user_id' => $candidate->id]);

    $record = WorkforceRecord::create([
        'employer_profile_id' => $profile->id,
        'candidate_profile_id' => $cp->id,
        'hired_at' => now()->subMonths(6),
        'annual_wage' => 450000.00,
        'pwd_verified' => true,
    ]);

    $this->actingAs($user);

    // Active tab initially shows employee
    $component = Livewire::test(Workforce::class)
        ->assertSee('Active Workforce')
        ->assertSee($candidate->name)
        ->call('openEndEmploymentModal', $record->id)
        ->assertSet('showEndEmploymentModal', true)
        ->set('endedReason', 'resigned')
        ->set('endedAt', now()->format('Y-m-d'))
        ->set('endedNotes', 'Pursuing further studies')
        ->call('confirmEndEmployment')
        ->assertSet('showEndEmploymentModal', false);

    $freshRecord = $record->fresh();
    expect($freshRecord->ended_at)->not->toBeNull();
    expect($freshRecord->ended_reason)->toBe('resigned');
    expect($freshRecord->ended_notes)->toBe('Pursuing further studies');

    // Notice Pending Review shows in Active Workforce
    $component->assertSee('Notice Pending Review')
        ->assertSee($candidate->name);

    // If candidate acknowledges separation, moves to separated staff
    $record->fresh()->update([
        'separation_status' => 'acknowledged',
        'candidate_response_at' => now(),
    ]);

    // Separated staff tab shows employee with Resigned / Left badge
    $component->call('setWorkforceTab', 'separated')
        ->assertSet('workforceTab', 'separated')
        ->assertSee('Resigned / Left')
        ->assertSee($candidate->name);

    // Can reactivate
    $component->call('reactivateRecord', $record->id);
    expect($record->fresh()->ended_at)->toBeNull();
    expect($record->fresh()->separation_status)->toBe('active');
});

test('statutory separation due process allows candidate to acknowledge or contest separation', function () {
    \Illuminate\Support\Facades\Notification::fake();

    [$employerUser, $profile, $job] = makeEmployerWithJob();
    $candidateUser = User::factory()->verified()->create(['role' => 'candidate', 'pwd_id_number' => 'PWD-4455']);
    $candidateUser->assignRole('candidate');
    $candidateProfile = \App\Models\CandidateProfile::firstOrCreate(['user_id' => $candidateUser->id]);

    $app = Application::create([
        'candidate_profile_id' => $candidateProfile->id,
        'job_posting_id' => $job->id,
        'status' => 'hired',
        'applied_at' => now()->subMonths(3),
    ]);

    $record = WorkforceRecord::create([
        'employer_profile_id' => $profile->id,
        'candidate_profile_id' => $candidateProfile->id,
        'application_id' => $app->id,
        'hired_at' => now()->subMonths(3),
        'annual_wage' => 360000.00,
        'pwd_verified' => true,
        'separation_status' => 'active',
    ]);

    // 1. Employer issues notice of separation
    $this->actingAs($employerUser);
    Livewire::test(Workforce::class)
        ->call('openEndEmploymentModal', $record->id)
        ->set('endedReason', 'relieved')
        ->set('endedAt', now()->addDays(30)->format('Y-m-d'))
        ->set('endedNotes', 'Restructuring of department')
        ->call('confirmEndEmployment');

    $record->refresh();
    expect($record->separation_status)->toBe('pending_review');
    expect($record->ended_reason)->toBe('relieved');

    \Illuminate\Support\Facades\Notification::assertSentTo(
        $candidateUser,
        \App\Notifications\EmploymentSeparationNotice::class
    );

    // 2. Candidate sees notice on dashboard and can contest / dispute it
    $this->actingAs($candidateUser);
    Livewire::test(\App\Livewire\Candidate\Dashboard::class)
        ->assertSee('Notice of Employment Separation')
        ->assertSee('Restructuring of department')
        ->call('openDisputeModal', $record->id)
        ->assertSet('showDisputeModal', true)
        ->set('candidateDisputeReason', 'I was terminated without reasonable accommodation review or performance justification.')
        ->call('submitDispute')
        ->assertSet('showDisputeModal', false);

    $record->refresh();
    expect($record->separation_status)->toBe('disputed');
    expect($record->candidate_dispute_reason)->toBe('I was terminated without reasonable accommodation review or performance justification.');

    \Illuminate\Support\Facades\Notification::assertSentTo(
        $employerUser,
        \App\Notifications\EmploymentSeparationResponse::class
    );

    // 3. Employer sees dispute, views details modal, and can withdraw notice to retain employee
    $this->actingAs($employerUser);
    Livewire::test(Workforce::class)
        ->call('setWorkforceTab', 'disputed')
        ->assertSee('Disputed by Candidate')
        ->assertSee('I was terminated without reasonable accommodation review')
        ->call('viewDispute', $record->id)
        ->assertSee('Contested Employment Separation')
        ->call('cancelNotice', $record->id);

    $record->refresh();
    expect($record->separation_status)->toBe('active');
    expect($record->ended_at)->toBeNull();
});

test('candidate can acknowledge separation and record is confirmed in separated staff', function () {
    \Illuminate\Support\Facades\Notification::fake();

    [$employerUser, $profile, $job] = makeEmployerWithJob();
    $candidateUser = User::factory()->verified()->create(['role' => 'candidate', 'pwd_id_number' => 'PWD-8899']);
    $candidateUser->assignRole('candidate');
    $candidateProfile = \App\Models\CandidateProfile::firstOrCreate(['user_id' => $candidateUser->id]);

    $app = Application::create([
        'candidate_profile_id' => $candidateProfile->id,
        'job_posting_id' => $job->id,
        'status' => 'hired',
        'applied_at' => now()->subMonths(6),
    ]);

    $record = WorkforceRecord::create([
        'employer_profile_id' => $profile->id,
        'candidate_profile_id' => $candidateProfile->id,
        'application_id' => $app->id,
        'hired_at' => now()->subMonths(6),
        'ended_at' => now()->addDays(15),
        'ended_reason' => 'resigned',
        'annual_wage' => 400000.00,
        'pwd_verified' => true,
        'separation_status' => 'pending_review',
        'separation_notice_at' => now(),
    ]);

    $this->actingAs($candidateUser);
    Livewire::test(\App\Livewire\Candidate\Dashboard::class)
        ->assertSee('Notice of Employment Separation')
        ->call('openAcknowledgeModal', $record->id)
        ->assertSet('showAcknowledgeModal', true)
        ->assertSee('Acknowledge Employment Separation')
        ->assertSee('Confirm & Acknowledge Separation')
        ->call('acknowledgeSeparation', $record->id)
        ->assertSet('showAcknowledgeModal', false);

    $record->refresh();
    expect($record->separation_status)->toBe('acknowledged');
    expect($record->candidate_response_at)->not->toBeNull();

    \Illuminate\Support\Facades\Notification::assertSentTo(
        $employerUser,
        \App\Notifications\EmploymentSeparationResponse::class
    );

    // Employer sees candidate in separated roster
    $this->actingAs($employerUser);
    Livewire::test(Workforce::class)
        ->call('setWorkforceTab', 'separated')
        ->assertSee($candidateUser->name);
});

test('separated candidate can reapply to open published job and receives rehire badge in employer view', function () {
    [$employerUser, $profile, $job] = makeEmployerWithJob();
    $candidateUser = User::factory()->verified()->create(['role' => 'candidate', 'pwd_id_number' => 'PWD-7777']);
    $candidateUser->assignRole('candidate');
    $candidateProfile = \App\Models\CandidateProfile::firstOrCreate(['user_id' => $candidateUser->id]);

    // Candidate previously hired and acknowledged separation
    $prevApp = Application::create([
        'candidate_profile_id' => $candidateProfile->id,
        'job_posting_id' => $job->id,
        'status' => 'hired',
        'applied_at' => now()->subMonths(10),
    ]);

    WorkforceRecord::create([
        'employer_profile_id' => $profile->id,
        'candidate_profile_id' => $candidateProfile->id,
        'application_id' => $prevApp->id,
        'hired_at' => now()->subMonths(10),
        'ended_at' => now()->subMonths(2),
        'ended_reason' => 'contract_ended',
        'annual_wage' => 380000.00,
        'pwd_verified' => true,
        'separation_status' => 'acknowledged',
        'candidate_response_at' => now()->subMonths(2),
    ]);

    // Candidate reapplies via Apply component
    $this->actingAs($candidateUser);
    Livewire::test(\App\Livewire\Candidate\Apply::class, ['job' => $job])
        ->assertSet('alreadyApplied', false)
        ->set('includeBiodata', true)
        ->call('submit')
        ->assertHasNoErrors();

    // Verify new application created and linked to previous application
    $newApp = Application::where('candidate_profile_id', $candidateProfile->id)
        ->where('job_posting_id', $job->id)
        ->where('status', 'applied')
        ->first();

    expect($newApp)->not->toBeNull();
    expect($newApp->reapplied_from_id)->toBe($prevApp->id);

    // Employer sees candidate in applicants table with "Former Staff / Rehire" badge
    // and deduplication ensures only the latest active application is shown (1 row, not 2)
    $this->actingAs($employerUser);
    $component = Livewire::test(Applicants::class, ['job' => $job]);
    $displayedApps = $component->viewData('applications');
    expect($displayedApps->count())->toBe(1);
    expect($displayedApps->first()->id)->toBe($newApp->id);
    expect($displayedApps->first()->status)->toBe('applied');

    $component->assertSee('Former Staff / Rehire')
        ->assertSee('Re-application')
        ->call('viewDetails', $newApp->id)
        ->assertSee('Former Employee / Rehire Applicant')
        ->assertSee('Contract_ended');
});

test('currently employed candidate is blocked from reapplying to the same job', function () {
    [$employerUser, $profile, $job] = makeEmployerWithJob();
    $candidateUser = User::factory()->verified()->create(['role' => 'candidate', 'pwd_id_number' => 'PWD-9999']);
    $candidateUser->assignRole('candidate');
    $candidateProfile = \App\Models\CandidateProfile::firstOrCreate(['user_id' => $candidateUser->id]);

    $activeApp = Application::create([
        'candidate_profile_id' => $candidateProfile->id,
        'job_posting_id' => $job->id,
        'status' => 'hired',
        'applied_at' => now()->subMonths(4),
    ]);

    WorkforceRecord::create([
        'employer_profile_id' => $profile->id,
        'candidate_profile_id' => $candidateProfile->id,
        'application_id' => $activeApp->id,
        'hired_at' => now()->subMonths(4),
        'annual_wage' => 420000.00,
        'pwd_verified' => true,
        'separation_status' => 'active',
    ]);

    // Active employee tries to reapply
    $this->actingAs($candidateUser);
    Livewire::test(\App\Livewire\Candidate\Apply::class, ['job' => $job])
        ->assertSet('alreadyApplied', true)
        ->call('submit');

    // No duplicate application created
    expect(Application::where('candidate_profile_id', $candidateProfile->id)->where('job_posting_id', $job->id)->count())->toBe(1);
});

test('employer is prompted to close filled job upon hiring and can close listing', function () {
    [$employerUser, $profile, $job] = makeEmployerWithJob();
    $candidateUser = User::factory()->verified()->create(['role' => 'candidate', 'pwd_id_number' => 'PWD-2222']);
    $candidateUser->assignRole('candidate');
    $candidateProfile = \App\Models\CandidateProfile::firstOrCreate(['user_id' => $candidateUser->id]);

    $app = Application::create([
        'candidate_profile_id' => $candidateProfile->id,
        'job_posting_id' => $job->id,
        'status' => 'interview',
        'applied_at' => now()->subDays(5),
    ]);

    $this->actingAs($employerUser);
    Livewire::test(Applicants::class, ['job' => $job])
        ->call('hire', $app->id)
        ->assertSet('showCloseJobPromptModal', true)
        ->assertSee('Close Filled Job Listing?')
        ->call('closeJobListing', $job->id)
        ->assertSet('showCloseJobPromptModal', false);

    expect($job->fresh()->status)->toBe('closed');
});