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
        ->assertSee('Bio-Data');
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
        ->assertSee('Bio-Data')
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

test('employer can view workforce page', function () {
    [$user] = makeEmployerWithJob();
    $this->actingAs($user)->get(route('employer.workforce'))->assertOk();
});

test('workforce page shows empty state', function () {
    [$user] = makeEmployerWithJob();
    $this->actingAs($user);
    Livewire::test(Workforce::class)->assertSee('No workforce records yet');
});