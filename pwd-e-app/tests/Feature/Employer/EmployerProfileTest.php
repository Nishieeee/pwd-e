<?php

use App\Livewire\Candidate\EmployerShow;
use App\Livewire\Employer\Profile;
use App\Models\Accommodation;
use App\Models\EmployerProfile;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    foreach (['candidate', 'employer', 'admin'] as $r) {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => $r, 'guard_name' => 'web']);
    }
});

function createTestEmployer(): array
{
    $user = User::factory()->verified()->create(['role' => 'employer']);
    $user->assignRole('employer');
    $profile = EmployerProfile::create([
        'user_id' => $user->id,
        'company_name' => 'Acme Accessibility Solutions',
        'industry' => 'Information Technology & Software',
        'location' => 'BGC, Taguig, Philippines',
        'website' => 'https://acme-access.ph',
        'about' => 'Pioneering accessible workplace software in the Philippines.',
        'accessibility_highlights' => ['Full Wheelchair Ramp Access', 'Sign Language Ready'],
    ]);

    return [$user, $profile];
}

test('guests and candidates cannot access employer profile management', function () {
    $this->get(route('employer.profile'))->assertRedirect(route('login'));

    $candidate = User::factory()->verified()->create(['role' => 'candidate']);
    $candidate->assignRole('candidate');
    $this->actingAs($candidate)->get(route('employer.profile'))->assertForbidden();
});

test('employer can view dedicated company profile workspace and switch tabs', function () {
    [$employer, $profile] = createTestEmployer();

    $this->actingAs($employer)
        ->get(route('employer.profile'))
        ->assertOk()
        ->assertSee('Acme Accessibility Solutions')
        ->assertSee('Profile Overview')
        ->assertSee('Edit Profile & Accessibility Matrix');

    Livewire::actingAs($employer)
        ->test(Profile::class)
        ->assertSet('activeTab', 'overview')
        ->assertSee('Acme Accessibility Solutions')
        ->assertSee('BGC, Taguig, Philippines')
        ->call('setTab', 'edit')
        ->assertSet('activeTab', 'edit')
        ->assertSee('Basic Company Information')
        ->assertSee('Workplace Accessibility Matrix Certifications');
});

test('employer can edit profile, add accessibility highlights, and sync matrix accommodations', function () {
    [$employer, $profile] = createTestEmployer();

    $acc1 = Accommodation::firstOrCreate(
        ['name' => 'Elevator & Ramp Access'],
        ['category' => 'mobility', 'description' => 'Wheelchair friendly access']
    );
    $acc2 = Accommodation::firstOrCreate(
        ['name' => 'Screen Reader Compatibility'],
        ['category' => 'visual', 'description' => 'Tagged software and materials']
    );

    Livewire::actingAs($employer)
        ->test(Profile::class)
        ->set('activeTab', 'edit')
        ->set('company_name', 'Acme Global Inclusivity Corp')
        ->set('industry', 'Healthcare & Medical Services')
        ->set('location', 'Makati City, Metro Manila')
        ->set('website', 'https://acmeglobal.ph')
        ->set('about', 'Empowering inclusive healthcare teams through accessible workplace adaptations.')
        ->set('newHighlight', 'Tactile Braille Wayfinding')
        ->call('addHighlight')
        ->set('selectedAccommodations', [$acc1->id, $acc2->id])
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('activeTab', 'overview')
        ->assertSee('Company profile and workplace accessibility matrix saved successfully!');

    $profile->refresh();
    expect($profile->company_name)->toBe('Acme Global Inclusivity Corp')
        ->and($profile->industry)->toBe('Healthcare & Medical Services')
        ->and($profile->location)->toBe('Makati City, Metro Manila')
        ->and($profile->website)->toBe('https://acmeglobal.ph')
        ->and($profile->accessibility_highlights)->toContain('Tactile Braille Wayfinding')
        ->and($profile->accommodations->pluck('id')->toArray())->toEqualCanonicalizing([$acc1->id, $acc2->id])
        ->and($profile->matrix_completed_at)->not->toBeNull();
});

test('employer viewing own public profile sees visitor preview mode banner with return links', function () {
    [$employer, $profile] = createTestEmployer();

    Livewire::actingAs($employer)
        ->test(EmployerShow::class, ['employer' => $profile])
        ->assertSet('isOwnProfile', true)
        ->assertSee('Viewing in Candidate / Visitor Preview Mode')
        ->assertSee('Edit Company Profile')
        ->assertSee('Return to Workspace');

    $candidate = User::factory()->verified()->create(['role' => 'candidate']);
    $candidate->assignRole('candidate');

    Livewire::actingAs($candidate)
        ->test(EmployerShow::class, ['employer' => $profile])
        ->assertSet('isOwnProfile', false)
        ->assertDontSee('Viewing in Candidate / Visitor Preview Mode')
        ->assertSee('Back to Job Matches');
});
