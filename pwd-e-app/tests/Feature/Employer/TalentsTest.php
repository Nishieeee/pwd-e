<?php

use App\Livewire\Employer\Talents;
use App\Models\Accommodation;
use App\Models\CandidateProfile;
use App\Models\EmployerProfile;
use App\Models\JobPosting;
use App\Models\User;
use App\Notifications\JobInvitationReceived;
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
        ->assertSet('showInviteModal', false)
        ->assertSessionHas('invitation_success');

    Notification::assertSentTo(
        $candidateUser,
        JobInvitationReceived::class,
        function ($notification) use ($job) {
            return $notification->jobPosting->id === $job->id
                && $notification->employerCompanyName === 'Tech Inclusion Corp';
        }
    );
});
