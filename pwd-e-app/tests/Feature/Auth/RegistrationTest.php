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
        'name' => 'John Doe',
        'email' => 'candidate@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'disability_type' => 'Visual',
        'pwd_id_number' => 'PWD-12345',
        'consent' => '1',
    ]);

    $response->assertSessionHasNoErrors();

    $user = User::where('email', 'candidate@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe('candidate');
    expect($user->status)->toBe('pending');
    expect($user->verified_at)->toBeNull();
    expect(CandidateProfile::where('user_id', $user->id)->exists())->toBeTrue();
    expect(VerificationRequest::where('user_id', $user->id)->where('type', 'pwd_id')->exists())->toBeTrue();
});

test('employer can register with permit and creates profile', function () {
    $response = $this->post(route('register.store'), [
        'role' => 'employer',
        'name' => 'Acme Corp',
        'email' => 'employer@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'company_name' => 'Acme Inc',
        'business_permit_number' => 'BP-999',
    ]);

    $response->assertSessionHasNoErrors();

    $user = User::where('email', 'employer@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe('employer');
    expect($user->status)->toBe('pending');
    expect(EmployerProfile::where('user_id', $user->id)->exists())->toBeTrue();
    expect(VerificationRequest::where('user_id', $user->id)->where('type', 'business_permit')->exists())->toBeTrue();
});

test('registration fails without required role fields', function () {
    $response = $this->post(route('register.store'), [
        'role' => 'candidate',
        'name' => 'John Doe',
        'email' => 'x@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors(['disability_type', 'pwd_id_number', 'consent']);
});
