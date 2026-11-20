<?php

use App\Livewire\Employer\Talents;
use App\Models\Accommodation;
use App\Models\CandidateProfile;
use App\Models\EmployerProfile;
use App\Models\JobInvitation;
use App\Models\JobPosting;
use App\Models\User;
use App\Notifications\JobInvitationReceived;
use App\Notifications\JobInvitationWithdrawn;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

beforeEach(function () {
    foreach (['candidate', 'employer', 'admin'] as $r) {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => $r, 'guard_name' => 'web']);
    }
});

function createEmployerUserWithProfile(): array
{
    $user = User::factory()->verified()->create(['role' => 'employer']);
    $user->assignRole('employer');
    $profile = EmployerProfile::create([
        'user_id' => $user->id,
        'company_name' => 'Tech Inclusion Corp',
        'business_permit_number' => 'BP-999',
    ]);
    $job = JobPosting::create([
        'employer_profile_id' => $profile->id,
        'title' => 'Software Engineer',
        'description' => 'Great role for developers',
        'status' => 'published',
    ]);

    return [$user, $profile, $job];
}

test('guest is redirected from talents discovery page', function () {
    $this->get(route('employer.talents'))->assertRedirect(route('login'));
});

test('candidate cannot access employer talents discovery page', function () {
    $candidate = User::factory()->verified()->create(['role' => 'candidate']);
    $candidate->assignRole('candidate');

    $this->actingAs($candidate)
        ->get(route('employer.talents'))
        ->assertForbidden();
});

test('employer can view talents discovery page and list candidates', function () {
    [$employer] = createEmployerUserWithProfile();

    $candidateUser = User::factory()->verified()->create([
        'name' => 'John Developer',
        'role' => 'candidate',
        'pwd_id_number' => 'PWD-00123',
    ]);
    $candidateUser->assignRole('candidate');

    CandidateProfile::create([
        'user_id' => $candidateUser->id,
        'headline' => 'Junior Software Engineer',
        'city' => 'Manila',
        'skills' => ['PHP', 'Laravel', 'Tailwind CSS'],
    ]);

    $this->actingAs($employer)
        ->get(route('employer.talents'))
        ->assertOk()
        ->assertSee('Talent Discovery')
        ->assertSee('John Developer')
        ->assertSee('Junior Software Engineer')
        ->assertSee('PHP')
        ->assertSee('View Profile')
        ->assertSee('Invite to Apply');
});

test('employer can search talents by keyword or skill', function () {
    [$employer] = createEmployerUserWithProfile();

    $candidate1 = User::factory()->verified()->create(['name' => 'Alice Dev', 'role' => 'candidate']);
    $candidate1->assignRole('candidate');
    CandidateProfile::create([
        'user_id' => $candidate1->id,
        'headline' => 'Full Stack Laravel Developer',
        'skills' => ['PHP', 'Livewire'],
    ]);

    $candidate2 = User::factory()->verified()->create(['name' => 'Bob Finance', 'role' => 'candidate']);
    $candidate2->assignRole('candidate');
    CandidateProfile::create([
        'user_id' => $candidate2->id,
        'headline' => 'Senior Accountant',
        'skills' => ['Excel', 'Quickbooks'],
    ]);

    $this->actingAs($employer);

    Livewire::test(Talents::class)
        ->set('search', 'Laravel')
        ->assertSee('Alice Dev')
        ->assertDontSee('Bob Finance');
});

test('employer can filter talents using accommodations checklist', function () {
    [$employer] = createEmployerUserWithProfile();

    $accRamp = Accommodation::firstOrCreate(['name' => 'Wheelchair Ramp Access', 'category' => 'mobility']);
    $accReader = Accommodation::firstOrCreate(['name' => 'Screen Reader Compatibility', 'category' => 'visual']);

    $candidate1 = User::factory()->verified()->create(['name' => 'Wheelchair Candidate', 'role' => 'candidate']);
    $candidate1->assignRole('candidate');
    $cp1 = CandidateProfile::create(['user_id' => $candidate1->id, 'headline' => 'QA Analyst']);
    $cp1->accommodations()->attach($accRamp->id);

    $candidate2 = User::factory()->verified()->create(['name' => 'Screen Reader Candidate', 'role' => 'candidate']);
    $candidate2->assignRole('candidate');
    $cp2 = CandidateProfile::create(['user_id' => $candidate2->id, 'headline' => 'Writer']);
    $cp2->accommodations()->attach($accReader->id);

    $this->actingAs($employer);

    Livewire::test(Talents::class)
        ->set('selectedAccommodations', ['Wheelchair Ramp Access'])
        ->assertSee('Wheelchair Candidate')
        ->assertDontSee('Screen Reader Candidate')
        ->call('removeAccommodation', 'Wheelchair Ramp Access')
        ->assertSee('Screen Reader Candidate')
        ->set('selectedAccommodations', ['Screen Reader Compatibility'])
        ->assertSee('Screen Reader Candidate')
        ->assertDontSee('Wheelchair Candidate')
        ->call('clearAccommodations')
        ->assertSee('Wheelchair Candidate')
        ->assertSee('Screen Reader Candidate');
});

test('employer can invite candidate to apply for active job posting', function () {
    Notification::fake();

    [$employer, $employerProfile, $job] = createEmployerUserWithProfile();

    $candidateUser = User::factory()->verified()->create([
        'name' => 'Jane Candidate',
        'role' => 'candidate',
    ]);
    $candidateUser->assignRole('candidate');

    $candidateProfile = CandidateProfile::create([
        'user_id' => $candidateUser->id,
        'headline' => 'Accessibility Specialist',
        'skills' => ['WCAG', 'Screen Readers'],
    ]);

    $this->actingAs($employer);

    Livewire::test(Talents::class)
        ->call('openInviteModal', $candidateProfile->id)
        ->assertSet('showInviteModal', true)
        ->assertSet('invitingCandidateName', 'Jane Candidate')
        ->set('selectedJobId', $job->id)
        ->set('invitationMessage', 'We think you would be a great fit for our Software Engineer opening!')
        ->call('sendInvitation')
        ->assertHasNoErrors()
        ->assertSet('showInviteModal', false)
        ->assertSee('Invitation successfully sent');

    Notification::assertSentTo(
        $candidateUser,
        JobInvitationReceived::class,
        function ($notification) use ($job) {
            return $notification->jobPosting->id === $job->id
                && $notification->employerCompanyName === 'Tech Inclusion Corp';
        }
    );

    $invitation = JobInvitation::where('candidate_profile_id', $candidateProfile->id)->first();
    expect($invitation)->not->toBeNull();
    expect($invitation->status)->toBe('pending');
});

test('employer can withdraw invitation with a reason remark', function () {
    Notification::fake();

    [$employer, $employerProfile, $job] = createEmployerUserWithProfile();

    $candidateUser = User::factory()->verified()->create([
        'name' => 'Michael Candidate',
        'role' => 'candidate',
    ]);
    $candidateUser->assignRole('candidate');

    $candidateProfile = CandidateProfile::create([
        'user_id' => $candidateUser->id,
        'headline' => 'Database Administrator',
    ]);

    $invitation = JobInvitation::create([
        'employer_profile_id' => $employerProfile->id,
        'job_posting_id' => $job->id,
        'candidate_profile_id' => $candidateProfile->id,
        'status' => 'pending',
    ]);

    $this->actingAs($employer);

    Livewire::test(Talents::class)
        ->assertSee('Invited')
        ->assertSee('Withdraw')
        ->call('openWithdrawModal', $invitation->id)
        ->assertSet('showWithdrawModal', true)
        ->assertSet('withdrawalCandidateName', 'Michael Candidate')
        ->assertSet('withdrawalJobTitle', 'Software Engineer')
        ->set('withdrawalReason', 'Role filled by internal transfer')
        ->call('confirmWithdrawInvitation')
        ->assertHasNoErrors()
        ->assertSet('showWithdrawModal', false)
        ->assertSee('withdrawn successfully');

    $invitation->refresh();
    expect($invitation->status)->toBe('withdrawn');
    expect($invitation->withdrawn_reason)->toBe('Role filled by internal transfer');
    expect($invitation->withdrawn_at)->not->toBeNull();

    Notification::assertSentTo(
        $candidateUser,
        JobInvitationWithdrawn::class,
        function ($notification) {
            return $notification->withdrawnReason === 'Role filled by internal transfer';
        }
    );
});

test('talents page displays applied badge if candidate already applied to employer job', function () {
    [$employer, $employerProfile, $job] = createEmployerUserWithProfile();

    $candidateUser = User::factory()->verified()->create([
        'role' => 'candidate',
        'name' => 'Sarah Applicant',
    ]);
    $candidateUser->assignRole('candidate');

    $candidateProfile = CandidateProfile::create([
        'user_id' => $candidateUser->id,
        'headline' => 'Senior Developer',
    ]);

    \App\Models\Application::create([
        'candidate_profile_id' => $candidateProfile->id,
        'job_posting_id' => $job->id,
        'status' => 'applied',
        'applied_at' => now(),
    ]);

    $this->actingAs($employer);

    Livewire::test(Talents::class)
        ->assertSee('Sarah Applicant')
        ->assertSee('Applied')
        ->assertSee('Applicant');
});
