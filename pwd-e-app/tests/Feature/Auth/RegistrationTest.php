<?php

use App\Models\CandidateProfile;
use App\Models\EmployerProfile;
use App\Models\User;
use App\Models\VerificationRequest;
use Laravel\Fortify\Features;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
    foreach (['candidate', 'employer', 'admin'] as $role) {
        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
    }
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('candidate can register with pwd id and creates profile', function () {
    $response = $this->post(route('register.store'), [
        'role' => 'candidate',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'candidate@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'date_of_birth' => now()->subYears(20)->toDateString(),
        'disability_type' => 'Visual',
        'pwd_id_number' => 'PWD-12345',
        'consent' => '1',
        'terms' => '1',
    ]);

    $response->assertSessionHasNoErrors();

    $user = User::where('email', 'candidate@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe('candidate');
    expect($user->first_name)->toBe('John');
    expect($user->last_name)->toBe('Doe');
    expect($user->status)->toBe('pending');
    expect($user->verified_at)->toBeNull();
    expect(CandidateProfile::where('user_id', $user->id)->exists())->toBeTrue();
    expect(VerificationRequest::where('user_id', $user->id)->where('type', 'pwd_id')->exists())->toBeTrue();
});

test('employer can register with permit and creates profile', function () {
    $response = $this->post(route('register.store'), [
        'role' => 'employer',
        'first_name' => 'Acme',
        'last_name' => 'Corp',
        'email' => 'employer@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'company_name' => 'Acme Inc',
        'business_permit_number' => 'BP-999',
        'terms' => '1',
    ]);

    $response->assertSessionHasNoErrors();

    $user = User::where('email', 'employer@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe('employer');
    expect($user->first_name)->toBe('Acme');
    expect($user->last_name)->toBe('Corp');
    expect($user->status)->toBe('active');
    expect($user->verified_at)->not->toBeNull();
    expect(EmployerProfile::where('user_id', $user->id)->exists())->toBeTrue();
    expect(VerificationRequest::where('user_id', $user->id)->exists())->toBeFalse();
});

test('employer can register with company payload without representative name', function () {
    $response = $this->post(route('register.store'), [
        'role' => 'employer',
        'company_name' => 'Stark Industries PH',
        'email' => 'hr@starkph.example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'business_permit_number' => 'BP-STARK-2026',
        'terms' => '1',
    ]);

    $response->assertSessionHasNoErrors();

    $user = User::where('email', 'hr@starkph.example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe('employer');
    expect($user->first_name)->toBe('Stark Industries PH');
    expect($user->last_name)->toBe('Representative');
    expect($user->status)->toBe('active');
    expect($user->verified_at)->not->toBeNull();
    expect(EmployerProfile::where('user_id', $user->id)->exists())->toBeTrue();
});

test('employer can register with only company name and email without permit', function () {
    $response = $this->post(route('register.store'), [
        'role' => 'employer',
        'company_name' => 'Zero Permit Inc',
        'email' => 'nopermit@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'terms' => '1',
    ]);

    $response->assertSessionHasNoErrors();

    $user = User::where('email', 'nopermit@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe('employer');
    expect($user->status)->toBe('active');
    expect($user->verified_at)->not->toBeNull();
    expect(EmployerProfile::where('user_id', $user->id)->exists())->toBeTrue();
    expect(VerificationRequest::where('user_id', $user->id)->exists())->toBeFalse();
});

test('newly registered employer is immediately active and redirected to dashboard', function () {
    $response = $this->post(route('register.store'), [
        'role' => 'employer',
        'company_name' => 'Instant Access Corp',
        'email' => 'instant@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'business_permit_number' => 'BP-INSTANT-1',
        'terms' => '1',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->get(route('dashboard'))->assertOk();
});

test('newly registered candidate is redirected to verification pending', function () {
    $this->post(route('register.store'), [
        'role' => 'candidate',
        'first_name' => 'Maria',
        'last_name' => 'Santos',
        'email' => 'maria@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'date_of_birth' => now()->subYears(22)->toDateString(),
        'disability_type' => 'Visual',
        'pwd_id_number' => 'PWD-MARIA-1',
        'consent' => '1',
        'terms' => '1',
    ])->assertRedirect(route('verification.notice'));

    $this->get(route('dashboard'))->assertRedirect(route('verification.notice'));
});

test('registration fails without required role fields', function () {
    $response = $this->post(route('register.store'), [
        'role' => 'candidate',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'x@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors(['date_of_birth', 'disability_type', 'pwd_id_number', 'consent', 'terms']);
});
