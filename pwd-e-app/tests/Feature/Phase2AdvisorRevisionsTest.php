<?php

use App\Livewire\Admin\VerificationQueue;
use App\Livewire\RoleSwitcher;
use App\Livewire\Settings\LinkedAccounts;
use App\Models\AccountLink;
use App\Models\CandidateProfile;
use App\Models\EmployerProfile;
use App\Models\User;
use App\Models\VerificationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (['candidate', 'employer', 'admin'] as $role) {
        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
    }
});

function createVerifiedEmployer(string $email = 'hr@acme.test', string $password = 'EmployerPass123!'): User
{
    $user = User::factory()->create([
        'name' => 'Acme HR Manager',
        'email' => $email,
        'password' => Hash::make($password),
        'role' => 'employer',
        'status' => 'active',
        'verified_at' => now(),
    ]);
    $user->assignRole('employer');

    EmployerProfile::create([
        'user_id' => $user->id,
        'company_name' => 'Acme Accessibility Corp',
        'business_permit_number' => 'BP-2026-9001',
    ]);

    return $user;
}

function createVerifiedCandidate(string $email = 'maria@pwd.test', string $password = 'CandidateSecret123!'): User
{
    $user = User::factory()->create([
        'name' => 'Maria Clara Santos',
        'email' => $email,
        'password' => Hash::make($password),
        'role' => 'candidate',
        'status' => 'active',
        'verified_at' => now(),
    ]);
    $user->assignRole('candidate');

    CandidateProfile::create([
        'user_id' => $user->id,
        'disability_type' => 'Visual',
        'disability_types' => ['Visual'],
    ]);

    return $user;
}

test('employer and candidate can link verified accounts in settings and reject invalid or unverified accounts', function () {
    $employer = createVerifiedEmployer();
    $candidate = createVerifiedCandidate();

    $unverifiedCandidate = User::factory()->create([
        'email' => 'pending@pwd.test',
        'password' => Hash::make('PendingPass123!'),
        'role' => 'candidate',
        'status' => 'pending',
        'verified_at' => null,
    ]);
    $unverifiedCandidate->assignRole('candidate');

    // Wrong password rejected
    Livewire::actingAs($employer)
        ->test(LinkedAccounts::class)
        ->set('email', $candidate->email)
        ->set('password', 'WrongPassword!')
        ->call('linkAccount')
        ->assertHasErrors(['email']);

    expect(AccountLink::count())->toBe(0);

    // Unverified candidate rejected
    Livewire::actingAs($employer)
        ->test(LinkedAccounts::class)
        ->set('email', $unverifiedCandidate->email)
        ->set('password', 'PendingPass123!')
        ->call('linkAccount')
        ->assertHasErrors(['email']);

    expect(AccountLink::count())->toBe(0);

    // Valid verified candidate linked
    Livewire::actingAs($employer)
        ->test(LinkedAccounts::class)
        ->set('email', $candidate->email)
        ->set('password', 'CandidateSecret123!')
        ->call('linkAccount')
        ->assertHasNoErrors();

    expect(AccountLink::count())->toBe(1);
    expect($employer->fresh()->linkedSwitchTargets()->pluck('id')->all())->toBe([$candidate->id]);
    expect($candidate->fresh()->linkedSwitchTargets()->pluck('id')->all())->toBe([$employer->id]);
});

test('role switcher requires destination account password and blocks switching once unlinked by either party', function () {
    $employer = createVerifiedEmployer();
    $candidate = createVerifiedCandidate();

    $link = AccountLink::create([
        'employer_user_id' => $employer->id,
        'candidate_user_id' => $candidate->id,
        'linked_by_user_id' => $employer->id,
    ]);

    // Header renders role switcher on employer & candidate dashboards
    $this->actingAs($employer)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Switch to')
        ->assertSee('Candidate');

    // Shared employer user WITHOUT candidate's personal password CANNOT switch into HR's personal candidate profile
    Livewire::actingAs($employer)
        ->test(RoleSwitcher::class)
        ->call('openModal')
        ->set('password', 'EmployerPass123!')
        ->call('confirmSwitch')
        ->assertHasErrors(['password']);

    expect(Auth::id())->toBe($employer->id);

    // Entering candidate's personal password switches session cleanly to candidate
    Livewire::actingAs($employer)
        ->test(RoleSwitcher::class)
        ->call('openModal')
        ->set('password', 'CandidateSecret123!')
        ->call('confirmSwitch')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    expect(Auth::id())->toBe($candidate->id);

    // When HR leaves or gets fired, employer (or candidate) can unlink immediately without needing the other's password
    Livewire::actingAs($employer)
        ->test(LinkedAccounts::class)
        ->call('unlinkAccount', $link->id)
        ->assertHasNoErrors();

    expect(AccountLink::count())->toBe(0);

    // Attempting to switch after unlink is immediately blocked even with the right password
    Livewire::actingAs($candidate)
        ->test(RoleSwitcher::class)
        ->set('selectedTargetId', $employer->id)
        ->set('password', 'EmployerPass123!')
        ->call('confirmSwitch')
        ->assertHasErrors(['password']);
});

test('admin verification queue enforces mandatory checklist for pwd id and medical certificate before approval', function () {
    Notification::fake();

    $admin = User::factory()->create([
        'role' => 'admin',
        'status' => 'active',
        'verified_at' => now(),
    ]);
    $admin->assignRole('admin');

    $candidate = User::factory()->create([
        'role' => 'candidate',
        'status' => 'pending',
        'verified_at' => null,
        'pwd_document_type' => 'both',
    ]);
    $candidate->assignRole('candidate');

    $vr = VerificationRequest::create([
        'user_id' => $candidate->id,
        'type' => 'both',
        'id_number' => 'PWD-09-2026-001',
        'document_path' => 'documents/pwd_ids/sample.png',
        'secondary_document_path' => 'documents/med_certs/sample.pdf',
        'status' => 'pending',
    ]);

    // Both PWD ID (4 items) + Medical Certificate (4 items) = 8 mandatory checklist items
    expect($vr->requiredChecklistKeys())->toHaveCount(8);

    // Attempting to approve with incomplete checklist fails and leaves request pending
    $component = Livewire::actingAs($admin)
        ->test(VerificationQueue::class)
        ->set("checklists.{$vr->id}.pwd_id_format", true)
        ->call('approve', $vr->id)
        ->assertHasErrors(["checklist.{$vr->id}"]);

    expect($vr->fresh()->status)->toBe('pending')
        ->and($candidate->fresh()->isVerified())->toBeFalse();

    // Checking remaining PWD ID + Medical Certificate criteria unlocks approval and saves checklist audit record
    foreach ($vr->requiredChecklistKeys() as $key) {
        $component->set("checklists.{$vr->id}.{$key}", true);
    }

    $component->call('approve', $vr->id)
        ->assertHasNoErrors();

    $vr->refresh();
    $candidate->refresh();

    expect($vr->status)->toBe('approved')
        ->and($candidate->isVerified())->toBeTrue()
        ->and($vr->verification_checklist['checked_keys'])->toEqual($vr->requiredChecklistKeys())
        ->and($vr->verification_checklist['reviewer_id'])->toBe($admin->id);
});
