<?php

use App\Livewire\Employer\Jobs\Index;
use App\Models\EmployerProfile;
use App\Models\JobPosting;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page from employer jobs', function () {
    $response = $this->get(route('employer.jobs.index'));
    $response->assertRedirect(route('login'));
});

test('unverified employers are redirected to verification notice', function () {
    $user = User::factory()->create([
        'role' => 'employer',
        'status' => 'pending',
        'verified_at' => null,
    ]);

    $response = $this->actingAs($user)->get(route('employer.jobs.index'));
    $response->assertRedirect(route('verification.notice'));
});

test('candidates cannot access employer jobs listing', function () {
    $candidate = User::factory()->verified()->create([
        'role' => 'candidate',
    ]);

    $response = $this->actingAs($candidate)->get(route('employer.jobs.index'));
    $response->assertForbidden();
});

test('verified employers can view employer jobs listing', function () {
    $employer = User::factory()->verified()->create([
        'role' => 'employer',
    ]);

    $profile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Acme Accessibility Inc.',
        'business_permit_number' => 'BP-12345',
    ]);

    $job = JobPosting::create([
        'employer_profile_id' => $profile->id,
        'title' => 'Accessibility Specialist',
        'description' => 'Help make apps accessible.',
        'location' => 'Makati City',
        'status' => 'published',
        'published_at' => now(),
    ]);

    $response = $this->actingAs($employer)->get(route('employer.jobs.index'));
    $response->assertOk();
    $response->assertSee('Accessibility Specialist');
    $response->assertSee('Acme Accessibility Inc.');
});

test('employers only see their own job postings', function () {
    $employer1 = User::factory()->verified()->create(['role' => 'employer']);
    $profile1 = EmployerProfile::create([
        'user_id' => $employer1->id,
        'company_name' => 'First Corp',
        'business_permit_number' => 'BP-111',
    ]);

    $employer2 = User::factory()->verified()->create(['role' => 'employer']);
    $profile2 = EmployerProfile::create([
        'user_id' => $employer2->id,
        'company_name' => 'Second Corp',
        'business_permit_number' => 'BP-222',
    ]);

    JobPosting::create([
        'employer_profile_id' => $profile1->id,
        'title' => 'First Corp Accessible Job',
        'description' => 'Description 1',
        'status' => 'published',
    ]);

    JobPosting::create([
        'employer_profile_id' => $profile2->id,
        'title' => 'Second Corp Secret Job',
        'description' => 'Description 2',
        'status' => 'published',
    ]);

    $this->actingAs($employer1);

    Livewire::test(Index::class)
        ->assertSee('First Corp Accessible Job')
        ->assertDontSee('Second Corp Secret Job');
});

test('employers can filter jobs by status and search by title', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    $profile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Tech Hub',
        'business_permit_number' => 'BP-333',
    ]);

    JobPosting::create([
        'employer_profile_id' => $profile->id,
        'title' => 'Software Engineer (Frontend)',
        'description' => 'Description',
        'status' => 'published',
    ]);

    JobPosting::create([
        'employer_profile_id' => $profile->id,
        'title' => 'QA Analyst (Accessibility)',
        'description' => 'Description',
        'status' => 'draft',
    ]);

    $this->actingAs($employer);

    // Filter by draft
    Livewire::test(Index::class)
        ->set('status', 'draft')
        ->assertSee('QA Analyst (Accessibility)')
        ->assertDontSee('Software Engineer (Frontend)');

    // Search by keyword
    Livewire::test(Index::class)
        ->set('search', 'Software')
        ->assertSee('Software Engineer (Frontend)')
        ->assertDontSee('QA Analyst (Accessibility)');
});

test('employer can close a published job posting', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    $profile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Tech Hub',
        'business_permit_number' => 'BP-444',
    ]);

    $job = JobPosting::create([
        'employer_profile_id' => $profile->id,
        'title' => 'Open Position',
        'description' => 'Description',
        'status' => 'published',
    ]);

    $this->actingAs($employer);

    Livewire::test(Index::class)
        ->call('closeJob', $job->id);

    expect($job->fresh()->status)->toBe('closed');
});
