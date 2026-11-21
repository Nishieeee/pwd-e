<?php

use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Lexicon;
use App\Livewire\Admin\Users;
use App\Models\ExclusionaryTerm;
use App\Models\User;
use App\Models\VerificationRequest;
use App\Notifications\VerificationStatusUpdated;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (['candidate', 'employer', 'admin'] as $role) {
        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
    }
});

function adminUser(): User
{
    $admin = User::factory()->create(['role' => 'admin', 'status' => 'active', 'verified_at' => now()]);
    $admin->assignRole('admin');

    return $admin;
}

test('guests are redirected from admin pages', function () {
    $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    $this->get(route('admin.users'))->assertRedirect(route('login'));
    $this->get(route('admin.lexicon'))->assertRedirect(route('login'));
});

test('non-admin users cannot access admin pages', function () {
    $user = User::factory()->create(['role' => 'candidate', 'status' => 'active', 'verified_at' => now()]);
    $user->assignRole('candidate');

    $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
    $this->actingAs($user)->get(route('admin.users'))->assertForbidden();
    $this->actingAs($user)->get(route('admin.lexicon'))->assertForbidden();
});

test('admin can view dashboard with analytics and custom admin portal layout', function () {
    $response = $this->actingAs(adminUser())->get(route('admin.dashboard'));

    $response->assertOk()
        ->assertSee('Admin portal')
        ->assertSee('Platform Administrator')
        ->assertSee('Verification Queue')
        ->assertSee('User Management')
        ->assertSee('Inclusive Lexicon')
        ->assertSee('Audit Log')
        ->assertSee('Dyslexia-friendly font')
        ->assertSee('Screen reader');
});

test('admin can view user management and suspend a user', function () {
    $admin = adminUser();
    $target = User::factory()->create(['role' => 'candidate', 'status' => 'active', 'verified_at' => now()]);
    $target->assignRole('candidate');

    $this->actingAs($admin)->get(route('admin.users'))->assertOk();

    Livewire::actingAs($admin)
        ->test(Users::class)
        ->call('suspend', $target->id)
        ->assertHasNoErrors();

    expect($target->refresh()->status)->toBe('suspended');
});

test('admin cannot suspend another admin', function () {
    $admin = adminUser();
    $otherAdmin = adminUser();

    Livewire::actingAs($admin)
        ->test(Users::class)
        ->call('suspend', $otherAdmin->id)
        ->assertHasNoErrors();

    expect($otherAdmin->refresh()->status)->toBe('active');
});

test('admin can reinstate a suspended user', function () {
    $admin = adminUser();
    $target = User::factory()->create(['role' => 'candidate', 'status' => 'suspended', 'verified_at' => now()]);
    $target->assignRole('candidate');

    Livewire::actingAs($admin)
        ->test(Users::class)
        ->call('reinstate', $target->id)
        ->assertHasNoErrors();

    expect($target->refresh()->status)->toBe('active');
});

test('admin can create a lexicon term', function () {
    $admin = adminUser();

    Livewire::actingAs($admin)
        ->test(Lexicon::class)
        ->set('phrase', 'cripple')
        ->set('category', 'disability')
        ->set('suggestion', 'person with a disability')
        ->set('isActive', true)
        ->call('save')
        ->assertHasNoErrors();

    expect(ExclusionaryTerm::where('phrase', 'cripple')->exists())->toBeTrue();
});

test('lexicon term validation requires a phrase', function () {
    $admin = adminUser();

    Livewire::actingAs($admin)
        ->test(Lexicon::class)
        ->set('phrase', '')
        ->call('save')
        ->assertHasErrors(['phrase']);
});

test('admin can toggle and delete a lexicon term', function () {
    $admin = adminUser();

    $term = ExclusionaryTerm::create([
        'phrase' => 'wheelchair bound',
        'category' => 'disability',
        'suggestion' => 'wheelchair user',
        'is_active' => true,
    ]);

    Livewire::actingAs($admin)
        ->test(Lexicon::class)
        ->call('toggle', $term->id)
        ->assertHasNoErrors();

    expect($term->refresh()->is_active)->toBeFalse();

    Livewire::actingAs($admin)
        ->test(Lexicon::class)
        ->call('delete', $term->id)
        ->assertHasNoErrors();

    expect(ExclusionaryTerm::find($term->id))->toBeNull();
});

test('admin dashboard displays pending verification approvals with categorization', function () {
    $admin = adminUser();

    $pwdUser = User::factory()->create(['role' => 'candidate', 'name' => 'Carlos PWD', 'status' => 'pending']);
    $pwdUser->assignRole('candidate');
    $vrPwd = VerificationRequest::create([
        'user_id' => $pwdUser->id,
        'type' => 'pwd_id',
        'id_number' => 'PWD-777',
        'status' => 'pending',
    ]);

    $empUser = User::factory()->create(['role' => 'employer', 'name' => 'Acme Inclusive Corp', 'status' => 'pending']);
    $empUser->assignRole('employer');
    $vrEmp = VerificationRequest::create([
        'user_id' => $empUser->id,
        'type' => 'business_permit',
        'id_number' => 'BP-888',
        'status' => 'pending',
    ]);

    Livewire::actingAs($admin)
        ->test(Dashboard::class)
        ->assertSee('Pending Verification Approvals')
        ->assertSee('Categorize:')
        ->assertSee('PWD Candidates')
        ->assertSee('Employers')
        ->call('setCategory', 'pwd')
        ->assertSee('Carlos PWD')
        ->assertDontSee('Acme Inclusive Corp')
        ->call('setCategory', 'employer')
        ->assertSee('Acme Inclusive Corp')
        ->assertDontSee('Carlos PWD');
});

test('admin can multi approve and multi reject from admin dashboard', function () {
    Notification::fake();
    $admin = adminUser();

    $user1 = User::factory()->create(['role' => 'candidate', 'name' => 'User One', 'status' => 'pending']);
    $user1->assignRole('candidate');
    $vr1 = VerificationRequest::create(['user_id' => $user1->id, 'type' => 'pwd_id', 'status' => 'pending']);

    $user2 = User::factory()->create(['role' => 'employer', 'name' => 'User Two', 'status' => 'pending']);
    $user2->assignRole('employer');
    $vr2 = VerificationRequest::create(['user_id' => $user2->id, 'type' => 'business_permit', 'status' => 'pending']);

    // Multi-approve
    Livewire::actingAs($admin)
        ->test(Dashboard::class)
        ->set('selectedIds', [$vr1->id, $vr2->id])
        ->call('bulkApprove')
        ->assertHasNoErrors();

    expect($vr1->fresh()->status)->toBe('approved');
    expect($user1->fresh()->status)->toBe('active');
    expect($vr2->fresh()->status)->toBe('approved');
    expect($user2->fresh()->status)->toBe('active');

    // Create another pair for multi-reject
    $user3 = User::factory()->create(['role' => 'candidate', 'name' => 'User Three', 'status' => 'pending']);
    $user3->assignRole('candidate');
    $vr3 = VerificationRequest::create(['user_id' => $user3->id, 'type' => 'pwd_id', 'status' => 'pending']);

    Livewire::actingAs($admin)
        ->test(Dashboard::class)
        ->set('selectedIds', [$vr3->id])
        ->set('bulkRejectReason', 'Government ID blurred')
        ->call('bulkReject')
        ->assertHasNoErrors();

    expect($vr3->fresh()->status)->toBe('rejected');
    expect($vr3->fresh()->rejection_reason)->toBe('Government ID blurred');
    expect($user3->fresh()->status)->toBe('rejected');
});

test('multi approve modal prevents mishaps with preview and cancel flow', function () {
    $admin = adminUser();

    $user = User::factory()->create(['role' => 'candidate', 'name' => 'Accidental Click', 'status' => 'pending']);
    $user->assignRole('candidate');
    $vr = VerificationRequest::create(['user_id' => $user->id, 'type' => 'pwd_id', 'status' => 'pending']);

    Livewire::actingAs($admin)
        ->test(Dashboard::class)
        ->set('selectedIds', [$vr->id])
        ->call('openBulkApprove')
        ->assertSet('showBulkApproveModal', true)
        ->assertSee('Confirm Multi-Approval')
        ->assertSee('Accidental Click')
        ->assertSee('Impact Warning:')
        ->call('closeBulkApprove')
        ->assertSet('showBulkApproveModal', false);

    expect($vr->fresh()->status)->toBe('pending');
});
