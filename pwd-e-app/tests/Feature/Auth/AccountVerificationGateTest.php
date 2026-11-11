<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (['candidate', 'employer', 'admin'] as $role) {
        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
    }
});

test('unverified user is redirected to pending notice', function () {
    $user = User::factory()->create([
        'role' => 'candidate',
        'status' => 'pending',
        'verified_at' => null,
    ]);
    $user->assignRole('candidate');

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('verification.notice'));
});

test('verified user can access gated routes', function () {
    $user = User::factory()->create([
        'role' => 'candidate',
        'status' => 'active',
        'verified_at' => now(),
    ]);
    $user->assignRole('candidate');

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();
});

test('admin bypasses verification gate', function () {
    $user = User::factory()->create([
        'role' => 'admin',
        'status' => 'active',
        'verified_at' => null,
    ]);
    $user->assignRole('admin');

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();
});
