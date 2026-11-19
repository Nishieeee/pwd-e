<?php

use App\Livewire\Admin\Lexicon;
use App\Livewire\Admin\Users;
use App\Models\ExclusionaryTerm;
use App\Models\User;
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
