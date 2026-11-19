<?php

use App\Livewire\AccessibilitySettings;
use App\Livewire\Admin\AuditLog;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\Lexicon;
use App\Livewire\Admin\Users as AdminUsers;
use App\Livewire\Admin\VerificationQueue;
use App\Livewire\Candidate\ApplicationsTracker;
use App\Livewire\Candidate\Apply;
use App\Livewire\Candidate\Dashboard as CandidateDashboard;
use App\Livewire\Candidate\JobMatches;
use App\Livewire\Candidate\ProfileBuilder;
use App\Livewire\Employer\Applicants;
use App\Livewire\Employer\Jobs\Create as CreateJob;
use App\Livewire\Employer\Jobs\Index as EmployerJobsIndex;
use App\Livewire\Employer\Workforce;
use App\Livewire\Jobs\PublicListing;
use App\Livewire\NotificationBell;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\Security;
use App\Models\Accommodation;
use App\Models\Application;
use App\Models\JobPosting;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\CandidatePortalSeeder;
use Database\Seeders\EmployerUserSeeder;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(CandidatePortalSeeder::class);
    $this->seed(EmployerUserSeeder::class);
    $this->seed(AdminUserSeeder::class);
    $this->candidate = User::where('email', 'maria.clara.santos@wmsu.edu.ph')->first();
    $this->employer = User::where('role', 'employer')->first();
    $this->admin = User::where('role', 'admin')->first();
});

test('public listing component renders and filters jobs by search query and accommodation', function () {
    Livewire::test(PublicListing::class)
        ->assertSee('Inclusive Job Directory')
        ->set('search', 'Accessibility')
        ->assertSet('search', 'Accessibility')
        ->call('filterByAccommodation', 'Wheelchair Ramp Access')
        ->assertSet('accommodationFilter', 'Wheelchair Ramp Access');
});

test('candidate portal components render without error', function () {
    $this->actingAs($this->candidate);

    // 1. Dashboard
    Livewire::test(CandidateDashboard::class)
        ->assertSee('Candidate Dashboard')
        ->assertSee('Maria Clara Santos');

    // 2. Job Matches
    Livewire::test(JobMatches::class)
        ->assertSee('Job Matches')
        ->assertSee('Apply Now');

    // 3. Applications Tracker
    Livewire::test(ApplicationsTracker::class)
        ->assertSee('Applications');

    // 4. Single-Page Apply
    $job = JobPosting::where('status', 'published')->first();
    Livewire::test(Apply::class, ['job' => $job])
        ->assertSee($job->title)
        ->assertSee('Voice Dictation')
        ->assertSee('Quick insert:')
        ->assertSee('Submit Application');
});

test('employer portal components render without error', function () {
    $this->actingAs($this->employer);

    // 1. Job Management Index
    Livewire::test(EmployerJobsIndex::class)
        ->assertSee('Job Postings Management')
        ->assertSee('Post a New Opportunity');

    // 2. Job Creation Flow
    Livewire::test(CreateJob::class)
        ->assertSee('Post a New Opportunity')
        ->assertSee('Job Title');

    // 3. Applicants Review & RA 10524 Hire Workflow
    $job = JobPosting::where('employer_profile_id', $this->employer->employerProfile->id)->first() ?? JobPosting::first();
    Livewire::test(Applicants::class, ['job' => $job])
        ->assertSee('Applicants for')
        ->assertSee('All')
        ->assertSee('New')
        ->assertSee('Interviews')
        ->assertSee('Hired')
        ->call('setStatusFilter', 'interview')
        ->assertSet('statusFilter', 'interview')
        ->call('setStatusFilter', 'all')
        ->assertSet('statusFilter', 'all');

    // 4. Workforce & Tax Deductions
    Livewire::test(Workforce::class)
        ->assertSee('Workforce')
        ->assertSee('10524');
});

test('settings components render cleanly in both candidate and employer portal layouts', function () {
    // Candidate role
    $this->actingAs($this->candidate);
    Livewire::test(Profile::class)
        ->assertSee('Profile')
        ->assertSee('Save');
    Livewire::test(Security::class)
        ->assertSee('Update password')
        ->assertSee('Save');
    Livewire::test(Appearance::class)
        ->assertSee('Appearance');

    // Employer role
    $this->actingAs($this->employer);
    Livewire::test(Profile::class)
        ->assertSee('Profile');
    Livewire::test(Security::class)
        ->assertSee('Update password');
    Livewire::test(Appearance::class)
        ->assertSee('Appearance');
});

test('auxiliary accessibility and notification components function properly', function () {
    $this->actingAs($this->candidate);

    Livewire::test(NotificationBell::class)
        ->assertSee('Notifications');

    Livewire::test(AccessibilitySettings::class)
        ->set('highContrast', true)
        ->assertSet('highContrast', true)
        ->set('dyslexiaFont', true)
        ->assertSet('dyslexiaFont', true);
});

test('admin portal components render for authorized admin', function () {
    $this->actingAs($this->admin);

    Livewire::test(AdminDashboard::class)
        ->assertSee('Admin Dashboard');

    Livewire::test(VerificationQueue::class)
        ->assertSee('Verification');

    Livewire::test(AdminUsers::class)
        ->assertSee('User Management');

    Livewire::test(Lexicon::class)
        ->assertSee('Lexicon');

    Livewire::test(AuditLog::class)
        ->assertSee('Audit Log');
});
