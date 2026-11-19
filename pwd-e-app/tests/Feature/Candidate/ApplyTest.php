<?php

use App\Livewire\Candidate\Apply;
use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\JobPosting;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(\Database\Seeders\CandidatePortalSeeder::class);
    $this->candidate = User::where('email', 'maria.clara.santos@wmsu.edu.ph')->first();
    $this->job = JobPosting::where('title', 'Junior Frontend Developer')->first();
});

test('guests are redirected from apply page', function () {
    $this->get(route('candidate.apply', $this->job))->assertRedirect(route('login'));
});

test('candidate can view apply page', function () {
    $this->actingAs($this->candidate)
        ->get(route('candidate.apply', $this->job))
        ->assertOk()
        ->assertSee('Apply for');
});

test('candidate can submit an application with cover letter', function () {
    $this->actingAs($this->candidate);

    Livewire::test(Apply::class, ['job' => $this->job])
        ->set('coverLetter', 'I am a great fit for this role.')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('submitted', true);

    $app = Application::where('candidate_profile_id', $this->candidate->candidateProfile->id)
        ->where('job_posting_id', $this->job->id)
        ->first();

    expect($app)->not->toBeNull();
    expect($app->cover_letter)->toBe('I am a great fit for this role.');
    expect($app->status)->toBe('applied');
});

test('duplicate application shows already applied message', function () {
    $this->actingAs($this->candidate);

    Application::create([
        'candidate_profile_id' => $this->candidate->candidateProfile->id,
        'job_posting_id' => $this->job->id,
        'status' => 'applied',
        'applied_at' => now(),
    ]);

    $component = Livewire::test(Apply::class, ['job' => $this->job])
        ->call('submit');

    expect($component->get('submitted'))->toBeTrue();
});

test('candidate without PWD ID sees the gate prompt', function () {
    $user = User::factory()->create(['role' => 'candidate', 'status' => 'active', 'verified_at' => now(), 'pwd_id_number' => null]);
    $user->assignRole('candidate');
    CandidateProfile::firstOrCreate(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Apply::class, ['job' => $this->job])
        ->assertSet('missingPwdId', true)
        ->assertSee('PWD Identification Required');
});

test('candidate can append quick starter phrase and sees formatted reference on submit', function () {
    $this->actingAs($this->candidate);

    $component = Livewire::test(Apply::class, ['job' => $this->job])
        ->call('appendPhrase', 'I am excited to apply for this role.')
        ->assertSet('coverLetter', 'I am excited to apply for this role.')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('submitted', true);

    $ref = $component->get('formattedReference');
    expect($ref)->toMatch('/^PWD-APP-\d{4}-\d{5}$/');
    $component->assertSee($ref);
    $component->assertSee('Application Submitted Successfully!');
});

test('candidate cannot submit application with zero materials selected', function () {
    $this->actingAs($this->candidate);

    Livewire::test(Apply::class, ['job' => $this->job])
        ->set('includeBiodata', false)
        ->set('includeResume', false)
        ->set('includeFslVideo', false)
        ->call('submit')
        ->assertHasErrors(['materials'])
        ->assertSet('submitted', false);
});

test('candidate application stores multi-select application materials', function () {
    $this->actingAs($this->candidate);

    Livewire::test(Apply::class, ['job' => $this->job])
        ->set('includeBiodata', true)
        ->set('includeResume', false)
        ->set('includeFslVideo', false)
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('submitted', true);

    $app = Application::where('candidate_profile_id', $this->candidate->candidateProfile->id)
        ->where('job_posting_id', $this->job->id)
        ->first();

    expect($app)->not->toBeNull();
    expect($app->application_materials)->toBeArray();
    expect($app->application_materials['biodata'])->toBeTrue();
    expect($app->application_materials['resume'])->toBeFalse();
    expect($app->application_materials['fsl_video'])->toBeFalse();
});
