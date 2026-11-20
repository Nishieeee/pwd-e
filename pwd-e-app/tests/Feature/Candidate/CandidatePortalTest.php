<?php

use App\Livewire\Candidate\ApplicationsTracker;
use App\Livewire\Candidate\Dashboard;
use App\Livewire\Candidate\JobMatches;
use App\Livewire\Candidate\ProfileBuilder;
use App\Livewire\Candidate\ShowProfile;
use App\Models\Application;
use App\Models\JobPosting;
use App\Models\User;
use Database\Seeders\CandidatePortalSeeder;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(CandidatePortalSeeder::class);
    $this->candidate = User::where('email', 'maria.clara.santos@wmsu.edu.ph')->first();
});

test('unauthenticated user is redirected to login from candidate portal', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
    $this->get(route('candidate.jobs'))->assertRedirect(route('login'));
    $this->get(route('candidate.applications'))->assertRedirect(route('login'));
});

test('candidate dashboard renders faithfully with active credentials, stats, and interview banner', function () {
    $this->actingAs($this->candidate);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertSee('Candidate Dashboard');
    $response->assertSee('Maria Clara Santos');
    $response->assertSee('PWD ID: 09-7332-0001842-A (Verified)');
    $response->assertSee('WMSU Alumna');
    $response->assertSee('Active Applications');
    $response->assertSee('Highest Accessibility Fit');
    $response->assertSee('Interview Invitation');
    $response->assertSee('Accommodations Specified');
    $response->assertSee('Upcoming interview');
    $response->assertSee('Digital Accessibility & Frontend Specialist');
    $response->assertSee('InnovateTech Philippines Corp.');
    $response->assertSee('Accept & Add to Calendar');
    $response->assertSee('Active Job Application Status');
    $response->assertSee('98% Composite Fit');
    $response->assertSee('Interview Scheduled');
    $response->assertSee('Recommended Jobs Based on Your Accommodations');
});

test('livewire candidate dashboard can open fit breakdown modal and accept interview', function () {
    $this->actingAs($this->candidate);

    $app = Application::where('status', 'interview')->first();

    Livewire::test(Dashboard::class)
        ->assertSee('Candidate Dashboard')
        ->call('viewFitBreakdown', $app->id)
        ->assertSet('showBreakdownModal', true)
        ->assertSee('Composite Fit Breakdown')
        ->call('closeBreakdownModal')
        ->assertSet('showBreakdownModal', false)
        ->call('acceptAndAddToCalendar', $app->id)
        ->assertDispatched('interview-accepted');
});

test('job matches page displays 2-column layout and workspace accessibility matrix', function () {
    $this->actingAs($this->candidate);

    $response = $this->get(route('candidate.jobs'));
    $response->assertOk();
    $response->assertSee('Job Matches & Search');
    $response->assertSee('Smart Match');
    $response->assertSee('OpenPath Digital');
    $response->assertSee('Junior Frontend Developer');
    $response->assertSee('98% Accessibility fit');
    $response->assertSee('Workspace Accessibility Matrix');
    $response->assertSee('Wheelchair Ramp Access');
    $response->assertSee('Screen Reader Compatibility');
    $response->assertSee('FSL Bilingual Staff');
    $response->assertSee('+ Guaranteed');
    $response->assertSee('Apply Now');
});

test('livewire job matches allows switching selected job and applying', function () {
    $this->actingAs($this->candidate);

    $job = JobPosting::where('title', 'Junior Frontend Developer')->first();
    $otherJob = JobPosting::where('title', 'Web Accessibility Associate')->first();

    Livewire::test(JobMatches::class)
        ->assertSee('Junior Frontend Developer')
        ->call('selectJob', $otherJob->id)
        ->assertSet('selectedJobId', $otherJob->id)
        ->assertSee('Web Accessibility Associate')
        ->call('applyForJob', $job->id)
        ->assertDispatched('application-submitted');

    // Confirm application was persisted in DB
    expect(Application::where('candidate_profile_id', $this->candidate->candidateProfile->id)
        ->where('job_posting_id', $job->id)
        ->exists())->toBeTrue();
});

test('applications tracker displays tabs, interview card, and supports withdrawal', function () {
    $this->actingAs($this->candidate);

    $response = $this->get(route('candidate.applications'));
    $response->assertOk();
    $response->assertSee('Applications');
    $response->assertSee('All Applications');
    $response->assertSee('Active Applications');
    $response->assertSee('Interviews');
    $response->assertSee('Past/Archived');
    $response->assertSee('Digital Accessibility & Frontend Specialist');
    $response->assertSee('InnovateTech Philippines Corp.');
    $response->assertSee('Interview Scheduled');
    $response->assertSee('View Fit Breakdown');
    $response->assertSee('Withdraw Application');

    // Test with Livewire
    $activeApp = Application::where('candidate_profile_id', $this->candidate->candidateProfile->id)
        ->where('status', 'under_review')
        ->first();

    Livewire::test(ApplicationsTracker::class)
        ->set('activeTab', 'all')
        ->assertSee('Digital Records & Data Archiving Specialist')
        ->call('promptWithdraw', $activeApp->id)
        ->assertSet('showWithdrawConfirmModal', true)
        ->call('confirmWithdraw')
        ->assertSet('showWithdrawConfirmModal', false);

    expect($activeApp->fresh()->status)->toBe('closed');
});

test('sidebar stays fixed with sticky h-screen overflow-y-auto and conditionally hides zero application badge', function () {
    $this->actingAs($this->candidate);

    // When active applications exist (2 seeded), badge is visible
    $responseWithApps = $this->get(route('dashboard'));
    $responseWithApps->assertOk();
    $responseWithApps->assertSee('lg:sticky lg:top-0 lg:h-screen overflow-y-auto', false);
    $responseWithApps->assertSee('aria-label="2 active applications"', false);

    // When all active applications are closed/zero, badge is hidden
    Application::where('candidate_profile_id', $this->candidate->candidateProfile->id)
        ->update(['status' => 'closed']);

    $responseZeroApps = $this->get(route('dashboard'));
    $responseZeroApps->assertOk();
    $responseZeroApps->assertDontSee('active applications"');
});

test('applications tracker hides pagination on empty states, shows context-aware messages, and opens fit breakdown from footer flux button', function () {
    $this->actingAs($this->candidate);

    // 1. Footer Flux button opens Fit Breakdown modal when applications exist
    Livewire::test(ApplicationsTracker::class)
        ->assertSee('Showing 1-6 of 6 applications')
        ->assertSee('Composite Fit measures how a job matches your skills and accessibility needs.')
        ->call('openDefaultFitBreakdown')
        ->assertSet('showBreakdownModal', true)
        ->assertSee('Composite Fit Breakdown')
        ->call('closeBreakdownModal');

    // 2. Clear interview applications and verify context-aware empty state + hidden pagination
    Application::where('candidate_profile_id', $this->candidate->candidateProfile->id)
        ->where('status', 'interview')
        ->update(['status' => 'closed', 'interview_at' => null]);

    Livewire::test(ApplicationsTracker::class)
        ->call('setTab', 'interviews')
        ->assertSee('You have no upcoming interviews scheduled right now.')
        ->assertSee('Find job matches')
        ->assertDontSee('Showing 1-')
        ->assertDontSee('aria-label="Pagination"', false);
});

test('profile builder streamlines onboarding, auto-populates zip code, generates resume pdf, and redirects to dashboard with success banner', function () {
    Storage::fake('public');
    $this->actingAs($this->candidate);

    $component = Livewire::test(ProfileBuilder::class)
        // Step 1: Input mode selection
        ->assertSet('currentStep', 1)
        ->assertSee('How would you like to build your profile?')
        ->assertSee('Standard Typing')
        ->assertSee('Voice Dictation')
        ->call('selectInputMode', 'standard')
        ->assertSet('inputMode', 'standard')
        // Advance to Step 2: Professional details
        ->call('nextStep')
        ->assertSet('currentStep', 2)
        ->assertSee('Personal Identification')
        ->assertSee('2x2 Photo / Avatar')
        ->assertSee('e.g., PWD-0931-7000-1234')
        ->assertSee('e.g., Administrative Assistant, Data Entry Clerk, or Customer Support')
        ->assertSee('Zamboanga City (ZIP 7000)')
        ->assertSee('Primary (Elementary)')
        ->assertSee('Secondary (High School)')
        ->assertSee('Tertiary (College / University)')
        ->assertSee('Vocational / Technical Certification')
        ->assertSee('Administrative Support')
        ->assertSee('Packaging & Sorting')
        ->assertSee('Bookkeeping')
        ->assertSee('Communication')
        ->assertSet('pwdIdNumber', '09-7332-0001842-A')
        // Verify Zip Code auto-populates when City changes
        ->set('city', 'Cebu City')
        ->assertSet('zipCode', '6000')
        ->set('city', 'Zamboanga City')
        ->assertSet('zipCode', '7000')
        // Verify Spoken Languages + "Others" conditional input
        ->set('hasOtherLanguage', true)
        ->set('otherLanguage', 'Hiligaynon')
        // Verify Headline fallback when left blank uses most recent Job Title
        ->set('headline', '')
        ->set('experiences.0.job_title', 'Administrative Assistant')
        // Verify Highest Educational Attainment dropdown & toggleable + custom skill tags
        ->set('highestEducation', 'Vocational / Technical Certification')
        ->call('toggleSkill', 'Data Entry')
        ->set('customSkillInput', 'Braille Transcription')
        ->call('addCustomSkill')
        ->assertSet('customSkillInput', '')
        ->assertSee('Braille Transcription')
        // Advance to Step 3: Accommodations
        ->call('nextStep')
        ->assertSet('currentStep', 3)
        ->assertSet('headline', 'Administrative Assistant')
        ->assertSee('Your workplace, made accessible')
        // Advance to Step 4: Auto-generate Resume PDF + Conditional FSL & PWD ID Document Cards
        ->call('nextStep')
        ->assertSet('currentStep', 4)
        ->assertSee('Professional resume')
        ->assertSee('Download Resume')
        ->assertSee('Preview resume')
        ->assertSee('Not attached')
        ->assertSee('PWD ID Card / Verification Document')
        ->assertSee('Attached from Registration')
        ->call('previewResume')
        ->assertSet('showResumePreviewModal', true)
        ->assertSee('Auto-Generated Resume Preview')
        ->call('closeResumePreviewModal')
        ->assertSet('showResumePreviewModal', false);

    // Verify auto-generated PDF exists on public disk
    $resumePath = $component->get('resumePdfPath');
    expect($resumePath)->not->toBeNull();
    Storage::disk('public')->assertExists($resumePath);

    // Advance to Step 5: Review & Save -> Redirect to Dashboard with green banner
    $component->call('nextStep')
        ->assertSet('currentStep', 5)
        ->assertSee('Your employment profile')
        ->assertSee('All sections complete')
        ->call('saveProfileAndRedirect')
        ->assertRedirect(route('dashboard'));

    // Follow redirect to Dashboard and verify exact green success banner
    $dashboardResponse = $this->get(route('dashboard'));
    $dashboardResponse->assertOk();
    $dashboardResponse->assertSee('Profile saved successfully!');
    $dashboardResponse->assertSee('Your details, accommodation needs, and both attachments are saved. Your job matches are ready.');
});

test('public job directory shows appropriate apply actions based on auth state', function () {
    // 1. Guest sees Log in to Apply
    $response = $this->get(route('jobs'));
    $response->assertOk();
    $response->assertSee('Log in to Apply');

    // 2. Candidate sees Apply via Portal pointing to candidate.apply
    $this->actingAs($this->candidate);
    $response = $this->get(route('jobs'));
    $response->assertOk();
    $response->assertSee('Apply via Portal');
    $response->assertSee(route('candidate.apply', JobPosting::where('status', 'published')->first()));

    // 3. Employer sees Employer Account badge
    $employer = User::where('role', 'employer')->first();
    if ($employer) {
        $this->actingAs($employer);
        $response = $this->get(route('jobs'));
        $response->assertOk();
        $response->assertSee('Employer Account');
    }
});

test('profile builder validates required fields before allowing advancing to next step', function () {
    $this->actingAs($this->candidate);

    Livewire::test(ProfileBuilder::class)
        ->assertSet('currentStep', 1)
        ->call('nextStep')
        ->assertSet('currentStep', 2)
        // Clear required fields in Step 2
        ->set('firstName', '')
        ->set('lastName', '')
        ->set('phone', '')
        ->set('city', '')
        // Attempting to advance to step 3 should fail validation and remain on step 2
        ->call('nextStep')
        ->assertHasErrors(['firstName', 'lastName', 'phone', 'city'])
        ->assertSet('currentStep', 2)
        // Attempting to jump to step 3 via goToStep should also fail
        ->call('goToStep', 3)
        ->assertHasErrors(['firstName', 'lastName', 'phone', 'city'])
        ->assertSet('currentStep', 2)
        // Filling required fields allows advancing to Step 3
        ->set('firstName', 'Maria')
        ->set('lastName', 'Santos')
        ->set('phone', '+63 917 123 4567')
        ->set('city', 'Zamboanga City')
        ->call('nextStep')
        ->assertHasNoErrors()
        ->assertSet('currentStep', 3);
});

test('candidate can view their digital profile', function () {
    $this->actingAs($this->candidate);

    $response = $this->get(route('candidate.profile.show'));
    $response->assertOk();
    $response->assertSee('View my Profile');
    $response->assertSee('Digital Profile');
    $response->assertSee('Maria Clara Santos');
    $response->assertSee('RA 7277 Verified PWD');
    $response->assertSee('Print / Save Profile');
    $response->assertSee('Required Workplace Accommodations');
});

test('candidate can toggle between profile view and edit profile modes on unified profile page', function () {
    $this->actingAs($this->candidate);

    Livewire::test(ShowProfile::class)
        ->assertSet('tab', 'view')
        ->assertSee('Digital Profile')
        ->call('setTab', 'edit')
        ->assertSet('tab', 'edit')
        ->assertSee('Edit Profile & Credentials')
        ->call('setTab', 'view')
        ->assertSet('tab', 'view');
});

test('employer can view candidate profile', function () {
    $employer = User::where('role', 'employer')->first();
    if (! $employer) {
        $employer = User::factory()->create([
            'role' => 'employer',
            'status' => 'active',
            'verified_at' => now(),
        ]);
        $employer->assignRole('employer');
    }

    $this->actingAs($employer);

    $response = $this->get(route('employer.candidates.show', $this->candidate->candidateProfile));
    $response->assertOk();
    $response->assertSee('Candidate Digital Profile');
    $response->assertSee('Maria Clara Santos');
    $response->assertSee('Print / Save Profile');
});

test('unauthorized candidate cannot view another candidate profile', function () {
    $otherCandidate = User::factory()->create([
        'role' => 'candidate',
        'status' => 'active',
        'verified_at' => now(),
    ]);
    $otherCandidate->assignRole('candidate');

    $this->actingAs($otherCandidate);

    $response = $this->get(route('employer.candidates.show', $this->candidate->candidateProfile));
    $response->assertForbidden();
});
