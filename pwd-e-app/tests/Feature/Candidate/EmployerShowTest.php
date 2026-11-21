<?php

use App\Livewire\Candidate\EmployerShow;
use App\Models\Accommodation;
use App\Models\EmployerProfile;
use App\Models\JobPosting;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    foreach (['candidate', 'employer', 'admin'] as $r) {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => $r, 'guard_name' => 'web']);
    }
});

function createEmployerWithProfile(): array
{
    $employerUser = User::factory()->verified()->create(['role' => 'employer', 'name' => 'John Enterprise']);
    $employerUser->assignRole('employer');

    $employer = EmployerProfile::create([
        'user_id' => $employerUser->id,
        'company_name' => 'Horizon Accessible Solutions Inc.',
        'industry' => 'Information Technology',
        'location' => 'Bonifacio Global City, Taguig',
        'about' => 'We are dedicated to building barrier-free digital platforms and hiring skilled PWD professionals across the Philippines.',
        'website' => 'https://horizon-accessible.ph',
        'verified_at' => now(),
        'matrix_completed_at' => now(),
    ]);

    $acc1 = Accommodation::create([
        'name' => 'Wheelchair Ramp & Elevator Route',
        'category' => 'mobility',
        'description' => 'Fully step-free route from ground lobby to office suites.',
    ]);

    $acc2 = Accommodation::create([
        'name' => 'Filipino Sign Language (FSL) Interpreter',
        'category' => 'hearing',
        'description' => 'On-call FSL interpreter for team meetings and interviews.',
    ]);

    $employer->accommodations()->attach([$acc1->id, $acc2->id]);

    $job = JobPosting::create([
        'employer_profile_id' => $employer->id,
        'title' => 'Accessibility Software Engineer',
        'description' => 'Frontend role specializing in WCAG compliance',
        'location' => 'Taguig / Hybrid',
        'status' => 'published',
    ]);

    $job->accommodations()->attach([$acc1->id]);

    return [$employer, $job];
}

test('guest is redirected from employer profile page', function () {
    [$employer] = createEmployerWithProfile();

    $this->get(route('candidate.employers.show', $employer))
        ->assertRedirect(route('login'));
});

test('candidate can view employer profile with accessibility matrix and jobs', function () {
    [$employer, $job] = createEmployerWithProfile();

    $candidateUser = User::factory()->verified()->create(['role' => 'candidate', 'name' => 'Candidate User']);
    $candidateUser->assignRole('candidate');

    $this->actingAs($candidateUser)
        ->get(route('candidate.employers.show', $employer))
        ->assertOk()
        ->assertSee('Horizon Accessible Solutions Inc.')
        ->assertSee('Verified Employer')
        ->assertSee('Republic Act No. 10524 Partner')
        ->assertSee('Accessibility Matrix Certified')
        ->assertSee('Wheelchair Ramp & Elevator Route')
        ->assertSee('Filipino Sign Language (FSL) Interpreter')
        ->assertSee('Accessibility Software Engineer');

    Livewire::actingAs($candidateUser)
        ->test(EmployerShow::class, ['employer' => $employer])
        ->assertSee('Horizon Accessible Solutions Inc.')
        ->assertSee('Accessibility Matrix')
        ->assertSee('Active Job Openings');
});
