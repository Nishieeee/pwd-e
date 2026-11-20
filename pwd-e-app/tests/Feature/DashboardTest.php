<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->verified()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('verified employers see job management links in dashboard and navigation', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    $this->actingAs($employer);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertSee(route('employer.jobs.index'));
    $response->assertSee(route('employer.jobs.create'));
});

test('candidates do not see employer job management links in dashboard or navigation', function () {
    $candidate = User::factory()->verified()->create(['role' => 'candidate']);
    $this->actingAs($candidate);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertDontSee(route('employer.jobs.index'));
});

test('candidate can decline job invitation from dashboard', function () {
    $candidate = User::factory()->verified()->create(['role' => 'candidate']);
    $cp = \App\Models\CandidateProfile::firstOrCreate(['user_id' => $candidate->id]);

    $employer = User::factory()->verified()->create(['role' => 'employer']);
    $ep = \App\Models\EmployerProfile::create(['user_id' => $employer->id, 'company_name' => 'Tech Corp']);
    $job = \App\Models\JobPosting::create([
        'employer_profile_id' => $ep->id,
        'title' => 'Software Engineer',
        'description' => 'Test',
        'status' => 'published',
    ]);

    $invite = \App\Models\JobInvitation::create([
        'employer_profile_id' => $ep->id,
        'candidate_profile_id' => $cp->id,
        'job_posting_id' => $job->id,
        'status' => 'pending',
    ]);

    $this->actingAs($candidate);
    \Livewire\Livewire::test(\App\Livewire\Candidate\Dashboard::class)
        ->assertSee('Direct Job Invitation')
        ->assertSee('Decline')
        ->call('declineInvitation', $invite->id)
        ->assertHasNoErrors();

    expect($invite->fresh()->status)->toBe('declined');
});
