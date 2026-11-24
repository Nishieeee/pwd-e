<?php

use App\Models\Accommodation;
use App\Models\EmployerProfile;
use App\Models\User;
use App\Models\VerificationRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Accommodation::firstOrCreate(['name' => 'Wheelchair Ramp Access', 'category' => 'mobility']);
});

test('non-admin users cannot access admin routes at the HTTP layer', function () {
    $candidate = User::factory()->verified()->create(['role' => 'candidate']);
    $employer = User::factory()->verified()->create(['role' => 'employer']);

    $this->actingAs($candidate)->get(route('admin.dashboard'))->assertForbidden();
    $this->actingAs($candidate)->get(route('admin.users'))->assertForbidden();

    $this->actingAs($employer)->get(route('admin.dashboard'))->assertForbidden();
    $this->actingAs($employer)->get(route('admin.verifications.index'))->assertForbidden();
});

test('candidates cannot access employer portal routes', function () {
    $candidate = User::factory()->verified()->create(['role' => 'candidate']);

    $this->actingAs($candidate)->get(route('employer.jobs.index'))->assertForbidden();
    $this->actingAs($candidate)->get(route('employer.talents'))->assertForbidden();
    $this->actingAs($candidate)->get(route('employer.workforce'))->assertForbidden();
});

test('employers cannot access candidate portal routes', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);

    $this->actingAs($employer)->get(route('candidate.profile.builder'))->assertForbidden();
    $this->actingAs($employer)->get(route('candidate.applications'))->assertForbidden();
});

test('employers without business permit are blocked from job creation', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Unverified Firm',
        'business_permit_number' => null,
        'business_permit_path' => null,
    ]);

    $this->actingAs($employer)->get(route('employer.jobs.create'))->assertForbidden();
});

test('verified employers with business permit can access job creation', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Verified Inclusivity Corp',
        'business_permit_number' => 'BP-2026-9876',
    ]);

    $this->actingAs($employer)->get(route('employer.jobs.create'))->assertOk();
});

test('admins have access to admin routes', function () {
    $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

    $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
    $this->actingAs($admin)->get(route('admin.users'))->assertOk();
});
