<?php

use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Candidate\ApplicationsTracker;
use App\Livewire\Employer\Applicants;
use App\Livewire\Employer\Workforce;
use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\EmployerBranch;
use App\Models\EmployerProfile;
use App\Models\JobPosting;
use App\Models\PlatformSetting;
use App\Models\User;
use App\Models\WorkforceRecord;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (['candidate', 'employer', 'admin'] as $role) {
        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
    }
});

test('admin can configure pwd tax deduction rate dynamically and employer workforce recalculates immediately (REV-07)', function () {
    $admin = User::factory()->create(['role' => 'admin', 'status' => 'active', 'verified_at' => now()]);
    $admin->assignRole('admin');

    $employer = User::factory()->create(['role' => 'employer', 'status' => 'active', 'verified_at' => now()]);
    $employer->assignRole('employer');
    $empProfile = EmployerProfile::create([
        'user_id' => $employer->id,
        'company_name' => 'Inclusive Tax Partner Inc.',
        'business_permit_number' => 'BP-TAX-01',
    ]);

    $candUser = User::factory()->create([
        'role' => 'candidate',
        'status' => 'active',
        'verified_at' => now(),
        'pwd_id_number' => 'PWD-09-2026-777',
        'pwd_document_type' => 'pwd_id',
    ]);
    $candUser->assignRole('candidate');
    $candProfile = CandidateProfile::create(['user_id' => $candUser->id]);

    WorkforceRecord::create([
        'employer_profile_id' => $empProfile->id,
        'candidate_profile_id' => $candProfile->id,
        'hired_at' => now()->subMonth(),
        'pwd_verified' => true,
        'annual_wage' => 100000,
        'separation_status' => 'active',
    ]);

    // Default seeded rate is 20% -> ₱20,000.00 deduction
    expect(PlatformSetting::pwdTaxDeductionRate())->toBe(20.0);

    Livewire::actingAs($employer)
        ->test(Workforce::class)
        ->assertSee('20%')
        ->assertSee('20,000.00');

    // Admin updates statutory rate to 25%
    Livewire::actingAs($admin)
        ->test(AdminDashboard::class)
        ->set('pwdTaxDeductionRate', '25')
        ->call('saveTaxDeductionRate')
        ->assertHasNoErrors();

    expect(PlatformSetting::pwdTaxDeductionRate())->toBe(25.0);

    // Employer Workforce immediately reflects 25% -> ₱25,000.00 deduction
    Livewire::actingAs($employer)
        ->test(Workforce::class)
        ->assertSee('25%')
        ->assertSee('25,000.00');
});

test('employer extend offer includes schedule and warns about external active employment without revealing external company name (REV-14)', function () {
    $empAUser = User::factory()->create(['role' => 'employer', 'status' => 'active', 'verified_at' => now()]);
    $empAUser->assignRole('employer');
    $empAProfile = EmployerProfile::create(['user_id' => $empAUser->id, 'company_name' => 'Confidential Alpha Corp', 'business_permit_number' => 'BP-A']);
    $branchA = EmployerBranch::create(['employer_profile_id' => $empAProfile->id, 'name' => 'Tetuan Hub', 'city' => 'Zamboanga City', 'is_headquarters' => true]);

    $empBUser = User::factory()->create(['role' => 'employer', 'status' => 'active', 'verified_at' => now()]);
    $empBUser->assignRole('employer');
    $empBProfile = EmployerProfile::create(['user_id' => $empBUser->id, 'company_name' => 'Beta Solutions PH', 'business_permit_number' => 'BP-B']);

    $candUser = User::factory()->create(['name' => 'Juan Dela Cruz', 'role' => 'candidate', 'status' => 'active', 'verified_at' => now(), 'pwd_id_number' => 'PWD-100']);
    $candUser->assignRole('candidate');
    $candProfile = CandidateProfile::create(['user_id' => $candUser->id]);

    $jobA1 = JobPosting::create([
        'employer_profile_id' => $empAProfile->id,
        'employer_branch_id' => $branchA->id,
        'title' => 'Morning Data Specialist',
        'description' => 'Morning shift role.',
        'employment_type' => 'part_time',
        'work_arrangement' => 'remote',
        'location' => 'Zamboanga City',
        'status' => 'published',
        'published_at' => now(),
    ]);
    $hiredAppA = Application::create([
        'candidate_profile_id' => $candProfile->id,
        'job_posting_id' => $jobA1->id,
        'status' => 'hired',
        'offer_details' => [
            'salary' => 18000,
            'pay_period' => 'monthly',
            'employment_type' => 'part_time',
            'work_days' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
            'shift_start' => '08:00',
            'shift_end' => '12:00',
            'is_flexible' => false,
        ],
        'applied_at' => now()->subDays(10),
    ]);
    WorkforceRecord::create([
        'application_id' => $hiredAppA->id,
        'employer_profile_id' => $empAProfile->id,
        'candidate_profile_id' => $candProfile->id,
        'hired_at' => now()->subDays(5),
        'pwd_verified' => true,
        'annual_wage' => 216000,
        'separation_status' => 'active',
    ]);

    // External Company B wants to extend an offer to the same candidate
    $jobB = JobPosting::create([
        'employer_profile_id' => $empBProfile->id,
        'title' => 'QA Support Analyst',
        'description' => 'QA role.',
        'employment_type' => 'part_time',
        'work_arrangement' => 'remote',
        'location' => 'Zamboanga City',
        'status' => 'published',
        'published_at' => now(),
    ]);
    $appB = Application::create([
        'candidate_profile_id' => $candProfile->id,
        'job_posting_id' => $jobB->id,
        'status' => 'interview',
        'applied_at' => now(),
    ]);

    // External Employer B sees warning + schedule conflict (default 08:00-17:00 overlaps 08:00-12:00), NEVER sees 'Confidential Alpha Corp'!
    $empBComponent = Livewire::actingAs($empBUser)
        ->test(Applicants::class)
        ->call('openOfferModal', $appB->id)
        ->assertSee('Candidate Active Employment Notice')
        ->assertSee('Currently employed with another verified employer on PWD-E')
        ->assertSee('Schedule Conflict')
        ->assertDontSee('Confidential Alpha Corp')
        ->assertDontSee('Morning Data Specialist');

    // Employer B adjusts shift to afternoon 13:00–17:00 (no overlap!) and sends formal offer
    $empBComponent
        ->set('offerSalary', 20000)
        ->set('offerPayPeriod', 'monthly')
        ->set('offerEmploymentType', 'part_time')
        ->set('offerShiftStart', '13:00')
        ->set('offerShiftEnd', '17:00')
        ->call('sendOffer')
        ->assertHasNoErrors();

    $appB->refresh();
    expect($appB->status)->toBe('offered')
        ->and($appB->offer_details['shift_start'])->toBe('13:00')
        ->and($appB->offer_details['shift_end'])->toBe('17:00')
        ->and($appB->offer_details['employment_type'])->toBe('part_time');

    // Internal Employer A extending a second role offer DOES see internal branch & role name
    $jobA2 = JobPosting::create([
        'employer_profile_id' => $empAProfile->id,
        'title' => 'Afternoon Accessibility Coordinator',
        'description' => 'Internal second role.',
        'employment_type' => 'part_time',
        'work_arrangement' => 'hybrid',
        'location' => 'Zamboanga City',
        'status' => 'published',
        'published_at' => now(),
    ]);
    $appA2 = Application::create([
        'candidate_profile_id' => $candProfile->id,
        'job_posting_id' => $jobA2->id,
        'status' => 'interview',
        'applied_at' => now(),
    ]);

    Livewire::actingAs($empAUser)
        ->test(Applicants::class)
        ->call('openOfferModal', $appA2->id)
        ->assertSee('Tetuan Hub')
        ->assertSee('Morning Data Specialist');
});

test('candidate sees multiple offers popup with accept or ignore and is warned before accepting an additional job when already employed (REV-14)', function () {
    $emp1 = User::factory()->create(['role' => 'employer', 'status' => 'active', 'verified_at' => now()]);
    $emp1->assignRole('employer');
    $emp1Profile = EmployerProfile::create(['user_id' => $emp1->id, 'company_name' => 'First Employer Co', 'business_permit_number' => 'BP-1']);

    $emp2 = User::factory()->create(['role' => 'employer', 'status' => 'active', 'verified_at' => now()]);
    $emp2->assignRole('employer');
    $emp2Profile = EmployerProfile::create(['user_id' => $emp2->id, 'company_name' => 'Second Employer Co', 'business_permit_number' => 'BP-2']);

    $candUser = User::factory()->create(['role' => 'candidate', 'status' => 'active', 'verified_at' => now(), 'pwd_id_number' => 'PWD-200']);
    $candUser->assignRole('candidate');
    $candProfile = CandidateProfile::create(['user_id' => $candUser->id]);

    $job1 = JobPosting::create([
        'employer_profile_id' => $emp1Profile->id,
        'title' => 'Remote Accessibility Tester',
        'description' => 'Testing role.',
        'employment_type' => 'full_time',
        'work_arrangement' => 'remote',
        'location' => 'Zamboanga City',
        'status' => 'published',
        'published_at' => now(),
    ]);
    $job2 = JobPosting::create([
        'employer_profile_id' => $emp2Profile->id,
        'title' => 'Inclusive UX Researcher',
        'description' => 'UX role.',
        'employment_type' => 'part_time',
        'work_arrangement' => 'remote',
        'location' => 'Zamboanga City',
        'status' => 'published',
        'published_at' => now(),
    ]);

    $offer1 = Application::create([
        'candidate_profile_id' => $candProfile->id,
        'job_posting_id' => $job1->id,
        'status' => 'offered',
        'offer_extended_at' => now(),
        'offer_details' => [
            'salary' => 30000,
            'pay_period' => 'monthly',
            'employment_type' => 'full_time',
            'work_days' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
            'shift_start' => '09:00',
            'shift_end' => '17:00',
            'is_flexible' => false,
        ],
        'applied_at' => now()->subDays(3),
    ]);

    $offer2 = Application::create([
        'candidate_profile_id' => $candProfile->id,
        'job_posting_id' => $job2->id,
        'status' => 'offered',
        'offer_extended_at' => now(),
        'offer_details' => [
            'salary' => 15000,
            'pay_period' => 'monthly',
            'employment_type' => 'part_time',
            'work_days' => ['Mon', 'Wed', 'Fri'],
            'shift_start' => '10:00',
            'shift_end' => '14:00',
            'is_flexible' => false,
        ],
        'applied_at' => now()->subDays(2),
    ]);

    // 1. Candidate sees Multiple Job Offers Received popup + Schedule Overlap warning
    $tracker = Livewire::actingAs($candUser)
        ->test(ApplicationsTracker::class)
        ->assertSee('Multiple Job Offers Received')
        ->assertSee('Schedule Overlap Detected')
        ->assertSee('Remote Accessibility Tester')
        ->assertSee('Inclusive UX Researcher');

    // 2. Accepting offer #1 (first job — not yet employed) hires immediately
    $tracker->call('acceptOffer', $offer1->id);
    expect($offer1->fresh()->status)->toBe('hired')
        ->and(WorkforceRecord::where('candidate_profile_id', $candProfile->id)->count())->toBe(1);

    // 3. Attempting to accept offer #2 NOW that candidate already has an active job triggers confirmation prompt first!
    $tracker->call('acceptOffer', $offer2->id);
    expect($offer2->fresh()->status)->toBe('offered')
        ->and($tracker->get('confirmingMultiJobAppId'))->toBe($offer2->id);

    $tracker->assertSee('You Already Have an Active Job');

    // 4. Confirming additional job hires candidate into second role too
    $tracker->call('confirmAcceptAdditionalJob');
    expect($offer2->fresh()->status)->toBe('hired')
        ->and(WorkforceRecord::where('candidate_profile_id', $candProfile->id)->count())->toBe(2);
});
