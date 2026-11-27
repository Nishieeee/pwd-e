<?php

use App\Livewire\Candidate\ProfileBuilder;
use App\Livewire\Employer\Jobs\Create as JobCreate;
use App\Livewire\Employer\Profile as EmployerProfileComponent;
use App\Models\Accommodation;
use App\Models\CandidateProfile;
use App\Models\EmployerBranch;
use App\Models\EmployerProfile;
use App\Models\JobPosting;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (['candidate', 'employer', 'admin'] as $role) {
        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
    }
});

test('employer registration stores representative middle name, phone, company type, chopped address, and headquarters branch', function () {
    $response = $this->post(route('register.store'), [
        'role' => 'employer',
        'company_name' => 'Inclusive Horizons Corp',
        'company_type' => 'Private Corporation',
        'industry' => 'Information Technology & Software',
        'first_name' => 'Ramon',
        'middle_name' => 'Delos Reyes',
        'last_name' => 'Magsaysay',
        'email' => 'hr@inclusivehorizons.ph',
        'phone' => '+639178889900',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'street_address' => '18F Tower One, Ayala Triangle',
        'country' => 'Philippines',
        'province' => 'Metro Manila',
        'city' => 'Makati City',
        'barangay' => 'Bel-Air',
        'zip_code' => '1209',
        'terms' => '1',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $user = User::where('email', 'hr@inclusivehorizons.ph')->firstOrFail();
    expect($user->first_name)->toBe('Ramon')
        ->and($user->middle_name)->toBe('Delos Reyes')
        ->and($user->last_name)->toBe('Magsaysay')
        ->and($user->name)->toBe('Ramon Delos Reyes Magsaysay')
        ->and($user->phone)->toBe('+639178889900');

    $profile = EmployerProfile::where('user_id', $user->id)->firstOrFail();
    expect($profile->company_type)->toBe('Private Corporation')
        ->and($profile->industry)->toBe('Information Technology & Software')
        ->and($profile->contact_phone)->toBe('+639178889900')
        ->and($profile->street_address)->toBe('18F Tower One, Ayala Triangle')
        ->and($profile->province)->toBe('Metro Manila')
        ->and($profile->city)->toBe('Makati City')
        ->and($profile->barangay)->toBe('Bel-Air')
        ->and($profile->zip_code)->toBe('1209')
        ->and($profile->location)->toBe('Bel-Air, Makati City, Metro Manila, Philippines');

    $hqBranch = EmployerBranch::where('employer_profile_id', $profile->id)->first();
    expect($hqBranch)->not->toBeNull()
        ->and($hqBranch->is_headquarters)->toBeTrue()
        ->and($hqBranch->city)->toBe('Makati City');
});

test('candidate registration supports multiple disabilities and phone number and syncs with profile builder', function () {
    $response = $this->post(route('register.store'), [
        'role' => 'candidate',
        'first_name' => 'Liza',
        'middle_name' => 'Gomez',
        'last_name' => 'Soberano',
        'email' => 'liza.multi@example.com',
        'phone' => '09171239876',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'date_of_birth' => '1998-04-12',
        'disability_types' => ['Visual', 'Motor/Physical'],
        'pwd_document_type' => 'pwd_id',
        'pwd_id_number' => 'PWD-09-2026-321',
        'consent' => '1',
        'terms' => '1',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('verification.notice'));

    $user = User::where('email', 'liza.multi@example.com')->firstOrFail();
    $profile = CandidateProfile::where('user_id', $user->id)->firstOrFail();

    expect($user->phone)->toBe('09171239876')
        ->and($profile->phone)->toBe('09171239876')
        ->and($profile->disability_types)->toBe(['Visual', 'Motor/Physical'])
        ->and($profile->disability_type)->toBe('Visual, Motor/Physical');

    $user->update(['status' => 'active', 'verified_at' => now()]);

    Livewire::actingAs($user)
        ->test(ProfileBuilder::class)
        ->assertSet('disabilityTypes', ['Visual', 'Motor/Physical'])
        ->set('disabilityTypes', ['Visual', 'Hearing', 'Motor/Physical'])
        ->set('city', 'Zamboanga City')
        ->call('saveProfileAndRedirect')
        ->assertHasNoErrors();

    $profile->refresh();
    expect($profile->disability_types)->toBe(['Visual', 'Hearing', 'Motor/Physical'])
        ->and($profile->disability_type)->toBe('Visual, Hearing, Motor/Physical');
});

test('employer can manage multiple branches and assign a branch with chopped location to a job posting', function () {
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    $employer->assignRole('employer');
    $profile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Nationwide Inclusive BPO',
        'company_type' => 'BPO / Multinational',
    ]);

    $acc = Accommodation::firstOrCreate(['name' => 'Wheelchair Ramp', 'category' => 'mobility']);

    Livewire::actingAs($employer)
        ->test(EmployerProfileComponent::class)
        ->call('setTab', 'edit')
        ->set('branchName', 'Main Manila HQ')
        ->set('branchStreet', '10F BGC Corporate Center')
        ->set('branchProvince', 'Metro Manila')
        ->set('branchCity', 'Taguig City')
        ->set('branchBarangay', 'Fort Bonifacio')
        ->set('branchZipCode', '1634')
        ->set('branchIsHq', true)
        ->call('saveBranch')
        ->assertHasNoErrors()
        ->set('branchName', 'Cebu IT Park Branch')
        ->set('branchStreet', '5F eBloc Tower 2')
        ->set('branchProvince', 'Cebu')
        ->set('branchCity', 'Cebu City')
        ->set('branchBarangay', 'Apas')
        ->set('branchZipCode', '6000')
        ->set('branchIsHq', false)
        ->call('saveBranch')
        ->assertHasNoErrors();

    expect($profile->branches()->count())->toBe(2);
    $cebuBranch = $profile->branches()->where('name', 'Cebu IT Park Branch')->firstOrFail();

    Livewire::actingAs($employer)
        ->test(JobCreate::class)
        ->set('title', 'Inclusive Customer Experience Specialist')
        ->set('description', 'Provide accessible multi-channel support in an inclusive hybrid team.')
        ->set('employment_type', 'full_time')
        ->set('work_arrangement', 'hybrid')
        ->set('employer_branch_id', $cebuBranch->id)
        ->assertSet('city', 'Cebu City')
        ->assertSet('province', 'Cebu')
        ->assertSet('barangay', 'Apas')
        ->assertSet('zip_code', '6000')
        ->set('selectedAccommodations', [$acc->id])
        ->call('publish')
        ->assertHasNoErrors();

    $job = JobPosting::where('employer_profile_id', $profile->id)->firstOrFail();
    expect($job->employer_branch_id)->toBe($cebuBranch->id)
        ->and($job->city)->toBe('Cebu City')
        ->and($job->province)->toBe('Cebu')
        ->and($job->barangay)->toBe('Apas')
        ->and($job->zip_code)->toBe('6000')
        ->and($job->location)->toBe('Apas, Cebu City, Cebu, Philippines');
});

test('employer selecting Other for company type and industry saves custom specified values', function () {
    $response = $this->post(route('register.store'), [
        'role' => 'employer',
        'company_name' => 'GreenMobility Collective',
        'company_type' => 'Other',
        'company_type_other' => 'Worker-Owned Federation',
        'industry' => 'Other',
        'industry_other' => 'Clean Energy & Assistive Mobility',
        'email' => 'team@greenmobility.ph',
        'phone' => '09180001122',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'province' => 'Davao del Sur',
        'city' => 'Davao City',
        'terms' => '1',
    ]);

    $response->assertSessionHasNoErrors();

    $user = User::where('email', 'team@greenmobility.ph')->firstOrFail();
    $profile = EmployerProfile::where('user_id', $user->id)->firstOrFail();

    expect($profile->company_type)->toBe('Worker-Owned Federation')
        ->and($profile->industry)->toBe('Clean Energy & Assistive Mobility');

    // Editing in Employer Profile recognizes custom Other values and allows updating them
    Livewire::actingAs($user)
        ->test(EmployerProfileComponent::class)
        ->assertSet('company_type', 'Other')
        ->assertSet('company_type_other', 'Worker-Owned Federation')
        ->assertSet('industry', 'Other')
        ->assertSet('industry_other', 'Clean Energy & Assistive Mobility')
        ->set('industry_other', 'Assistive Robotics & Clean Tech')
        ->call('save')
        ->assertHasNoErrors();

    expect($profile->fresh()->industry)->toBe('Assistive Robotics & Clean Tech');
});

test('validation rejects empty Other specifications, short phone numbers, and supports branch edit/delete and job edit branch switching', function () {
    // 1. Selecting Other without specifying custom text or short phone fails validation
    $response = $this->post(route('register.store'), [
        'role' => 'employer',
        'company_name' => 'Incomplete Other Co',
        'company_type' => 'Other',
        'company_type_other' => '',
        'industry' => 'Other',
        'industry_other' => '',
        'email' => 'bad@otherco.ph',
        'phone' => '123',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'terms' => '1',
    ]);

    $response->assertSessionHasErrors(['company_type_other', 'industry_other', 'phone']);

    // 2. Branch edit & delete + Job Edit branch switching
    $employer = User::factory()->verified()->create(['role' => 'employer']);
    $employer->assignRole('employer');
    $profile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Branch Lifecycle Corp',
    ]);

    Livewire::actingAs($employer)
        ->test(EmployerProfileComponent::class)
        ->set('branchName', 'Initial Branch')
        ->set('branchCity', 'Iloilo City')
        ->set('branchProvince', 'Iloilo')
        ->call('saveBranch')
        ->assertHasNoErrors();

    $branch = EmployerBranch::where('employer_profile_id', $profile->id)->firstOrFail();

    Livewire::actingAs($employer)
        ->test(EmployerProfileComponent::class)
        ->call('editBranch', $branch->id)
        ->assertSet('branchName', 'Initial Branch')
        ->set('branchName', 'Updated Iloilo Hub')
        ->set('branchZipCode', '5000')
        ->call('saveBranch')
        ->assertHasNoErrors();

    expect($branch->fresh()->name)->toBe('Updated Iloilo Hub')
        ->and($branch->fresh()->zip_code)->toBe('5000');

    $job = JobPosting::create([
        'employer_profile_id' => $profile->id,
        'title' => 'Regional Coordinator',
        'description' => 'Coordinate regional accessibility programs across branches.',
        'status' => 'draft',
    ]);

    Livewire::actingAs($employer)
        ->test(\App\Livewire\Employer\Jobs\Edit::class, ['job' => $job])
        ->set('employer_branch_id', $branch->id)
        ->assertSet('city', 'Iloilo City')
        ->assertSet('province', 'Iloilo')
        ->assertSet('zip_code', '5000')
        ->call('saveChanges')
        ->assertHasNoErrors();

    expect($job->fresh()->employer_branch_id)->toBe($branch->id)
        ->and($job->fresh()->city)->toBe('Iloilo City');

    Livewire::actingAs($employer)
        ->test(EmployerProfileComponent::class)
        ->call('deleteBranch', $branch->id);

    expect(EmployerBranch::find($branch->id))->toBeNull();
});
