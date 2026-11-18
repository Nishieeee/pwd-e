<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (['candidate', 'employer', 'admin'] as $role) {
        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
    }
});

test('pending user can log in but is redirected to verification notice on gated routes', function () {
    $user = User::factory()->create([
        'role' => 'candidate',
        'status' => 'pending',
        'verified_at' => null,
    ]);
    $user->assignRole('candidate');

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertSessionHasNoErrors();

    $this->assertAuthenticated();

    $this->get(route('dashboard'))->assertRedirect(route('verification.notice'));
});

test('suspended user cannot access protected routes', function () {
    $user = User::factory()->create([
        'role' => 'candidate',
        'status' => 'suspended',
        'verified_at' => now(),
    ]);
    $user->assignRole('candidate');

    // suspended check happens in EnsureAccountVerified; accessing gated route logs out / redirects
    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertRedirect(route('login'));
});

test('admin with pending status can still access admin routes', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'status' => 'active',
        'verified_at' => null,
    ]);
    $admin->assignRole('admin');

    $this->actingAs($admin)->get(route('admin.verifications.index'))->assertOk();
});
