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
