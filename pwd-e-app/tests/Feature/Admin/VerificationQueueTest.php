<?php

use App\Livewire\Admin\VerificationQueue;
use App\Models\User;
use App\Models\VerificationRequest;
use App\Notifications\VerificationStatusUpdated;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (['candidate', 'employer', 'admin'] as $role) {
        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
    }
});

function makePendingRequest(string $type = 'pwd_id'): array
{
    $user = User::factory()->create([
        'role' => $type === 'pwd_id' ? 'candidate' : 'employer',
        'status' => 'pending',
        'verified_at' => null,
    ]);
    $user->assignRole($type === 'pwd_id' ? 'candidate' : 'employer');

    $vr = VerificationRequest::create([
        'user_id' => $user->id,
        'type' => $type,
        'id_number' => 'ID-123',
        'document_path' => null,
        'status' => 'pending',
    ]);

    return [$user, $vr];
}

test('guests are redirected from verification queue', function () {
    $this->get(route('admin.verifications.index'))->assertRedirect(route('login'));
});

test('non-admin users cannot access verification queue', function () {
    $user = User::factory()->create(['role' => 'candidate', 'status' => 'active', 'verified_at' => now()]);
    $user->assignRole('candidate');

    $this->actingAs($user)->get(route('admin.verifications.index'))->assertForbidden();
});

test('admin can view verification queue', function () {
    $admin = User::factory()->create(['role' => 'admin', 'status' => 'active', 'verified_at' => now()]);
    $admin->assignRole('admin');

    $this->actingAs($admin)->get(route('admin.verifications.index'))->assertOk();
});

test('admin can approve a pending request', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin', 'status' => 'active', 'verified_at' => now()]);
    $admin->assignRole('admin');

    [$user, $vr] = makePendingRequest();

    Livewire::actingAs($admin)
        ->test(VerificationQueue::class)
        ->call('approve', $vr->id)
        ->assertHasNoErrors();

    $vr->refresh();
    $user->refresh();

    expect($vr->status)->toBe('approved');
    expect($user->status)->toBe('active');
    expect($user->verified_at)->not->toBeNull();
    expect($vr->reviewed_by)->toBe($admin->id);
    expect($vr->reviewed_at)->not->toBeNull();

    Notification::assertSentTo($user, VerificationStatusUpdated::class);
});

test('admin can reject a pending request with a reason', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin', 'status' => 'active', 'verified_at' => now()]);
    $admin->assignRole('admin');

    [$user, $vr] = makePendingRequest();

    $livewire = Livewire::actingAs($admin)
        ->test(VerificationQueue::class)
        ->set('reviewNote', 'Blurry document')
        ->call('reject', $vr->id);

    $livewire->assertHasNoErrors();

    $vr->refresh();
    $user->refresh();

    expect($vr->status)->toBe('rejected');
    expect($vr->rejection_reason)->toBe('Blurry document');
    expect($user->status)->toBe('rejected');

    Notification::assertSentTo($user, VerificationStatusUpdated::class);
});

test('admin can request more documents', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin', 'status' => 'active', 'verified_at' => now()]);
    $admin->assignRole('admin');

    [$user, $vr] = makePendingRequest();

    Livewire::actingAs($admin)
        ->test(VerificationQueue::class)
        ->set('reviewNote', 'Need clearer ID photo')
        ->call('requestMoreDocs', $vr->id)
        ->assertHasNoErrors();

    $vr->refresh();
    expect($vr->status)->toBe('needs_more_docs');
    expect($vr->rejection_reason)->toBe('Need clearer ID photo');

    Notification::assertSentTo($user, VerificationStatusUpdated::class);
});

test('admin can stream a verification document', function () {
    Storage::fake('private');
    Storage::disk('private')->put('documents/test.pdf', 'dummy');

    $admin = User::factory()->create(['role' => 'admin', 'status' => 'active', 'verified_at' => now()]);
    $admin->assignRole('admin');

    $user = User::factory()->create(['role' => 'candidate', 'status' => 'pending', 'verified_at' => null]);
    $user->assignRole('candidate');

    $vr = VerificationRequest::create([
        'user_id' => $user->id,
        'type' => 'pwd_id',
        'id_number' => 'ID-123',
        'document_path' => 'documents/test.pdf',
        'status' => 'pending',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.verifications.document', $vr))
        ->assertOk();
});

test('admin can categorize verification requests by pwd and employer in queue', function () {
    $admin = User::factory()->create(['role' => 'admin', 'status' => 'active', 'verified_at' => now()]);
    $admin->assignRole('admin');

    [$pwdUser, $pwdVr] = makePendingRequest('pwd_id');
    [$empUser, $empVr] = makePendingRequest('business_permit');

    Livewire::actingAs($admin)
        ->test(VerificationQueue::class)
        ->assertSee('Categorize:')
        ->assertSee('PWD Candidates')
        ->assertSee('Employers')
        ->call('setCategory', 'pwd')
        ->assertSee($pwdUser->name)
        ->assertDontSee($empUser->name)
        ->call('setCategory', 'employer')
        ->assertSee($empUser->name)
        ->assertDontSee($pwdUser->name);
});

test('admin can multi approve verification requests in queue', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin', 'status' => 'active', 'verified_at' => now()]);
    $admin->assignRole('admin');

    [$user1, $vr1] = makePendingRequest('pwd_id');
    [$user2, $vr2] = makePendingRequest('business_permit');

    Livewire::actingAs($admin)
        ->test(VerificationQueue::class)
        ->set('selectedIds', [$vr1->id, $vr2->id])
        ->call('bulkApprove')
        ->assertHasNoErrors();

    expect($vr1->fresh()->status)->toBe('approved');
    expect($user1->fresh()->status)->toBe('active');
    expect($vr2->fresh()->status)->toBe('approved');
    expect($user2->fresh()->status)->toBe('active');

    Notification::assertSentTo($user1, VerificationStatusUpdated::class);
    Notification::assertSentTo($user2, VerificationStatusUpdated::class);
});

test('admin can multi reject verification requests with feedback reason in queue', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin', 'status' => 'active', 'verified_at' => now()]);
    $admin->assignRole('admin');

    [$user1, $vr1] = makePendingRequest('pwd_id');
    [$user2, $vr2] = makePendingRequest('business_permit');

    Livewire::actingAs($admin)
        ->test(VerificationQueue::class)
        ->set('selectedIds', [$vr1->id, $vr2->id])
        ->set('bulkRejectReason', 'Expired IDs provided')
        ->call('bulkReject')
        ->assertHasNoErrors();

    expect($vr1->fresh()->status)->toBe('rejected');
    expect($vr1->fresh()->rejection_reason)->toBe('Expired IDs provided');
    expect($user1->fresh()->status)->toBe('rejected');

    expect($vr2->fresh()->status)->toBe('rejected');
    expect($vr2->fresh()->rejection_reason)->toBe('Expired IDs provided');
    expect($user2->fresh()->status)->toBe('rejected');

    Notification::assertSentTo($user1, VerificationStatusUpdated::class);
    Notification::assertSentTo($user2, VerificationStatusUpdated::class);
});
