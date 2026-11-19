<?php

use App\Livewire\Employer\Jobs\Create;
use App\Livewire\Employer\Jobs\Edit;
use App\Models\Accommodation;
use App\Models\EmployerProfile;
use App\Models\ExclusionaryTerm;
use App\Models\JobPosting;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    // Seed test accommodations
    Accommodation::firstOrCreate(['name' => 'Wheelchair Ramp', 'category' => 'mobility']);
    Accommodation::firstOrCreate(['name' => 'Screen Reader Compatible', 'category' => 'visual']);
    Accommodation::firstOrCreate(['name' => 'Sign Language Interpreter', 'category' => 'hearing']);

    // Seed test exclusionary terms
    ExclusionaryTerm::firstOrCreate([
        'phrase' => 'able-bodied',
        'category' => 'ability',
        'suggestion' => 'qualified applicant',
        'is_active' => true,
    ]);
});

test('guests are redirected from create job page', function () {
    $response = $this->get(route('employer.jobs.create'));
    $response->assertRedirect(route('login'));
});

test('candidates cannot access create job page', function () {
    $candidate = User::factory()->verified()->create(['role' => 'candidate']);
    $response = $this->actingAs($candidate)->get(route('employer.jobs.create'));
    $response->assertForbidden();
});

test('verified employers can view create job page and see accommodations', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Access PH',
        'business_permit_number' => 'BP-999',
    ]);

    $response = $this->actingAs($employer)->get(route('employer.jobs.create'));
    $response->assertOk();
    $response->assertSee('Post a New Opportunity');
    $response->assertSee('Wheelchair Ramp');
    $response->assertSee('Screen Reader Compatible');
});

test('employer can add and remove skill chips', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Access PH',
        'business_permit_number' => 'BP-999',
    ]);

    $this->actingAs($employer);

    Livewire::test(Create::class)
        ->set('newSkill', 'Laravel')
        ->call('addSkill')
        ->assertSet('required_skills', ['Laravel'])
        ->call('addSuggestedSkill', 'Accessibility Testing')
        ->assertSet('required_skills', ['Laravel', 'Accessibility Testing'])
        ->call('removeSkill', 0)
        ->assertSet('required_skills', ['Accessibility Testing']);
});

test('employer can save a job posting as draft', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    $profile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Access PH',
        'business_permit_number' => 'BP-999',
    ]);

    $ramp = Accommodation::where('name', 'Wheelchair Ramp')->first();

    $this->actingAs($employer);

    Livewire::test(Create::class)
        ->set('title', 'Draft Accessibility Specialist')
        ->set('description', 'Short draft description')
        ->set('employment_type', 'full_time')
        ->set('work_arrangement', 'onsite')
        ->set('location', 'Taguig City')
        ->set('selectedAccommodations', [$ramp->id])
        ->call('saveDraft')
        ->assertHasNoErrors()
        ->assertRedirect(route('employer.jobs.index'));

    $job = JobPosting::where('employer_profile_id', $profile->id)->first();
    expect($job)->not->toBeNull()
        ->and($job->status)->toBe('draft')
        ->and($job->title)->toBe('Draft Accessibility Specialist')
        ->and($job->accommodations)->toHaveCount(1);
});

test('employer can publish a job posting when no exclusionary language is found', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    $profile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Access PH',
        'business_permit_number' => 'BP-999',
    ]);

    $ramp = Accommodation::where('name', 'Wheelchair Ramp')->first();

    $this->actingAs($employer);

    Livewire::test(Create::class)
        ->set('title', 'Frontend Accessibility Specialist')
        ->set('description', 'We are looking for a qualified applicant to conduct WCAG testing across web products.')
        ->set('employment_type', 'full_time')
        ->set('work_arrangement', 'remote')
        ->set('location', '')
        ->set('salary_min', '45000')
        ->set('salary_max', '60000')
        ->set('required_skills', ['HTML/CSS', 'WCAG', 'Screen Readers'])
        ->set('selectedAccommodations', [$ramp->id])
        ->call('publish')
        ->assertHasNoErrors()
        ->assertRedirect(route('employer.jobs.index'));

    $job = JobPosting::where('employer_profile_id', $profile->id)->first();
    expect($job)->not->toBeNull()
        ->and($job->status)->toBe('published')
        ->and($job->published_at)->not->toBeNull()
        ->and($job->accommodations)->toHaveCount(1)
        ->and($profile->fresh()->isMatrixComplete())->toBeTrue();
});

test('inclusive language scanner flags exclusionary terms and allows replacement', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    $profile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Access PH',
        'business_permit_number' => 'BP-999',
    ]);

    $this->actingAs($employer);

    // Attempting to publish with "able-bodied" in description
    $test = Livewire::test(Create::class)
        ->set('title', 'Warehouse Associate')
        ->set('description', 'Looking for an able-bodied individual to manage warehouse packing.')
        ->set('employment_type', 'full_time')
        ->set('work_arrangement', 'onsite')
        ->set('location', 'Pasig City')
        ->call('publish')
        ->assertSet('showScanModal', true)
        ->assertSee('able-bodied')
        ->assertSee('qualified applicant');

    // No job should be published yet
    expect(JobPosting::count())->toBe(0);

    // Apply replacement
    $test->call('applyReplacement', 'able-bodied', 'qualified applicant')
        ->assertSet('showScanModal', false);

    expect($test->get('description'))->toContain('qualified applicant')
        ->and($test->get('description'))->not->toContain('able-bodied');

    // Now publish should succeed cleanly
    $test->call('publish')
        ->assertHasNoErrors()
        ->assertRedirect(route('employer.jobs.index'));

    expect(JobPosting::where('status', 'published')->count())->toBe(1);
});

test('employer can dismiss language warnings and publish anyway', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Access PH',
        'business_permit_number' => 'BP-999',
    ]);

    $this->actingAs($employer);

    Livewire::test(Create::class)
        ->set('title', 'Staff Associate')
        ->set('description', 'Seeking an able-bodied candidate for logistics handling.')
        ->set('employment_type', 'full_time')
        ->set('work_arrangement', 'onsite')
        ->set('location', 'Cebu City')
        ->call('publish')
        ->assertSet('showScanModal', true)
        ->call('dismissAndPublish')
        ->assertRedirect(route('employer.jobs.index'));

    expect(JobPosting::where('status', 'published')->count())->toBe(1);
});

test('employer can edit an existing job posting', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    $profile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Access PH',
        'business_permit_number' => 'BP-999',
    ]);

    $job = JobPosting::create([
        'employer_profile_id' => $profile->id,
        'title' => 'Original Title',
        'description' => 'Original description for testing purposes.',
        'status' => 'draft',
    ]);

    $this->actingAs($employer);

    Livewire::test(Edit::class, ['job' => $job])
        ->set('title', 'Updated Job Title')
        ->set('description', 'Updated description meeting the length requirement.')
        ->set('location', 'Makati City')
        ->call('saveChanges')
        ->assertHasNoErrors()
        ->assertRedirect(route('employer.jobs.index'));

    expect($job->fresh()->title)->toBe('Updated Job Title');
});

test('employer cannot edit a job posting belonging to another employer', function () {
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

    $job = JobPosting::create([
        'employer_profile_id' => $profile2->id,
        'title' => 'Secret Job',
        'description' => 'Secret description.',
        'status' => 'draft',
    ]);

    $this->actingAs($employer1);

    Livewire::test(Edit::class, ['job' => $job])
        ->assertForbidden();
});
