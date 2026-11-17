<?php

use App\Livewire\Employer\Jobs\Create;
use App\Livewire\Employer\Jobs\Edit;
use App\Livewire\Employer\Jobs\Index;
use App\Models\EmployerProfile;
use App\Models\JobPosting;
use App\Models\User;
use Database\Seeders\ExclusionaryTermSeeder;
use Livewire\Livewire;

function makeEmployer(): array
{
    $user = User::factory()->verified()->create(['role' => 'employer']);
    $profile = EmployerProfile::create([
        'user_id' => $user->id,
        'company_name' => 'Acme Accessibility Inc.',
        'business_permit_number' => 'BP-12345',
    ]);

    return [$user, $profile];
}

beforeEach(function () {
    // Ensure seeder terms exist for the scanner
    $this->seed(ExclusionaryTermSeeder::class);
});

test('employer can save a job as draft', function () {
    [$user, $profile] = makeEmployer();

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('title', 'Draft Job')
        ->set('description', 'A draft description that is long enough to pass validation.')
        ->set('location', 'Manila')
        ->call('saveDraft')
        ->assertHasNoErrors();

    $job = JobPosting::where('title', 'Draft Job')->first();
    expect($job)->not->toBeNull();
    expect($job->status)->toBe('draft');
    expect($job->employer_profile_id)->toBe($profile->id);
});

test('employer can publish a clean job posting', function () {
    [$user, $profile] = makeEmployer();

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('title', 'Clean Job Title')
        ->set('description', 'A perfectly inclusive description with no flagged terms at all.')
        ->set('location', 'Manila')
        ->call('publish')
        ->assertHasNoErrors();

    $job = JobPosting::where('title', 'Clean Job Title')->first();
    expect($job)->not->toBeNull();
    expect($job->status)->toBe('published');
    expect($job->published_at)->not->toBeNull();
    expect($job->scanned_at)->not->toBeNull();
});

test('publishing exclusionary language opens the scan modal with flags', function () {
    [$user, $profile] = makeEmployer();

    $this->actingAs($user);

    $component = Livewire::test(Create::class)
        ->set('title', 'Handicapped Worker Needed')
        ->set('description', 'We need a handicapped person for this role.')
        ->set('location', 'Manila')
        ->call('publish');

    expect($component->get('showScanModal'))->toBeTrue();
    expect($component->get('scanFlags'))->not->toBeEmpty();
});

test('applyReplacement replaces the phrase and removes the flag', function () {
    [$user, $profile] = makeEmployer();

    $this->actingAs($user);

    $component = Livewire::test(Create::class)
        ->set('title', 'Handicapped Worker Needed')
        ->set('description', 'We need a handicapped person for this role.')
        ->set('location', 'Manila')
        ->call('publish');

    $flag = $component->get('scanFlags')[0];

    $component->call('applyReplacement', $flag['phrase'], $flag['suggestion']);

    expect($component->get('description'))->not->toContain('handicapped');
    expect($component->get('scanFlags'))->toBeEmpty();
    expect($component->get('showScanModal'))->toBeFalse();
});

test('applyAllReplacements replaces all flagged phrases', function () {
    [$user, $profile] = makeEmployer();

    $this->actingAs($user);

    $component = Livewire::test(Create::class)
        ->set('title', 'Handicapped Worker Needed')
        ->set('description', 'We need a handicapped person who suffers from mobility issues.')
        ->set('location', 'Manila')
        ->call('publish');

    $component->call('applyAllReplacements');

    expect($component->get('description'))->not->toContain('handicapped');
    expect($component->get('scanFlags'))->toBeEmpty();
    expect($component->get('showScanModal'))->toBeFalse();
});

test('dismissAndPublish publishes despite scan warnings', function () {
    [$user, $profile] = makeEmployer();

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('title', 'Handicapped Worker Needed')
        ->set('description', 'We need a handicapped person for this role.')
        ->set('location', 'Manila')
        ->call('publish')
        ->call('dismissAndPublish')
        ->assertHasNoErrors();

    $job = JobPosting::where('title', 'Handicapped Worker Needed')->first();
    expect($job)->not->toBeNull();
    expect($job->status)->toBe('published');
});

test('employer can add and remove skill chips', function () {
    [$user, $profile] = makeEmployer();

    $this->actingAs($user);

    $component = Livewire::test(Create::class)
        ->set('newSkill', 'Braille')
        ->call('addSkill')
        ->assertSet('required_skills', ['Braille']);

    $component->set('newSkill', 'Braille')->call('addSkill');
    expect($component->get('required_skills'))->toBe(['Braille']);

    $component->call('removeSkill', 0);
    expect($component->get('required_skills'))->toBe([]);
});

test('employer can reopen a closed job posting', function () {
    [$user, $profile] = makeEmployer();

    $job = JobPosting::create([
        'employer_profile_id' => $profile->id,
        'title' => 'Closed Position',
        'description' => 'Description',
        'status' => 'closed',
    ]);

    $this->actingAs($user);

    Livewire::test(Index::class)
        ->call('reopenJob', $job->id);

    expect($job->fresh()->status)->toBe('published');
});

test('employer can delete a job posting', function () {
    [$user, $profile] = makeEmployer();

    $job = JobPosting::create([
        'employer_profile_id' => $profile->id,
        'title' => 'Deletable Job',
        'description' => 'Description',
        'status' => 'draft',
    ]);

    $this->actingAs($user);

    Livewire::test(Index::class)
        ->call('deleteJob', $job->id);

    expect(JobPosting::find($job->id))->toBeNull();
});

test('employer can edit an existing job posting', function () {
    [$user, $profile] = makeEmployer();

    $job = JobPosting::create([
        'employer_profile_id' => $profile->id,
        'title' => 'Original Title',
        'description' => 'Original description that is long enough.',
        'location' => 'Cebu',
        'employment_type' => 'full_time',
        'work_arrangement' => 'onsite',
        'status' => 'draft',
    ]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['job' => $job])
        ->set('title', 'Updated Title')
        ->set('description', 'Updated description that is long enough to pass.')
        ->call('saveChanges')
        ->assertHasNoErrors();

    expect($job->fresh()->title)->toBe('Updated Title');
});

test('employer cannot edit another employers job posting', function () {
    [$user1, $profile1] = makeEmployer();
    [$user2, $profile2] = makeEmployer();

    $job = JobPosting::create([
        'employer_profile_id' => $profile1->id,
        'title' => 'Owner Job',
        'description' => 'Description',
        'status' => 'draft',
    ]);

    $this->actingAs($user2);

    Livewire::test(Edit::class, ['job' => $job])
        ->assertForbidden();
});
