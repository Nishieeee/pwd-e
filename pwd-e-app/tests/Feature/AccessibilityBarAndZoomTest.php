<?php

use App\Livewire\AccessibilitySettings;
use App\Models\CandidateProfile;
use App\Models\EmployerProfile;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (['candidate', 'employer', 'admin'] as $role) {
        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
    }
});

test('employer, candidate, and admin portals render unified single-row accessibility bar and synchronized scale init', function () {
    $employer = User::factory()->create([
        'role' => 'employer',
        'status' => 'active',
        'verified_at' => now(),
        'display_settings' => ['large_text' => true],
    ]);
    $employer->assignRole('employer');
    EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Acme Accessibility Corp',
    ]);

    $candidate = User::factory()->create([
        'role' => 'candidate',
        'status' => 'active',
        'verified_at' => now(),
    ]);
    $candidate->assignRole('candidate');
    CandidateProfile::create([
        'user_id' => $candidate->id,
        'disability_type' => 'Visual',
    ]);

    $admin = User::factory()->create([
        'role' => 'admin',
        'status' => 'active',
        'verified_at' => now(),
    ]);
    $admin->assignRole('admin');

    foreach ([
        [$employer, route('dashboard')],
        [$candidate, route('dashboard')],
        [$admin, route('admin.dashboard')],
    ] as [$user, $url]) {
        $response = $this->actingAs($user)->get($url);

        $response->assertOk()
            ->assertSee('a11y-utility-bar', false)
            ->assertSee('lg:flex-nowrap', false)
            ->assertSee('window.__pwdeInitialScale = scale;', false)
            ->assertSee('applyScale();', false)
            ->assertSee('Accessibility')
            ->assertSee('Contrast')
            ->assertSee('Dyslexia-friendly font')
            ->assertSee('Screen reader')
            ->assertSee('Settings')
            ->assertSee('Log out');
    }
});

test('accessibility settings component persists synchronized font_scale and dispatches settings payload', function () {
    $user = User::factory()->create([
        'role' => 'employer',
        'status' => 'active',
        'verified_at' => now(),
    ]);
    $user->assignRole('employer');

    Livewire::actingAs($user)
        ->test(AccessibilitySettings::class)
        ->set('largeText', true)
        ->assertDispatched('a11y-settings-changed');

    $saved = $user->refresh()->display_settings;
    expect($saved['large_text'])->toBeTrue()
        ->and($saved['font_scale'])->toBe(125);

    Livewire::actingAs($user)
        ->test(AccessibilitySettings::class)
        ->set('largeText', false)
        ->assertDispatched('a11y-settings-changed');

    $reset = $user->refresh()->display_settings;
    expect($reset['large_text'])->toBeFalse()
        ->and($reset['font_scale'])->toBe(100);
});
