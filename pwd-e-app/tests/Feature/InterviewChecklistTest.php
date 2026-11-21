<?php

use App\Livewire\Candidate\ApplicationsTracker;
use App\Livewire\Employer\Applicants;
use App\Models\Application;
use App\Models\ApplicationMessage;
use App\Models\CandidateProfile;
use App\Models\EmployerProfile;
use App\Models\JobPosting;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    foreach (['candidate', 'employer', 'admin'] as $r) {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => $r, 'guard_name' => 'web']);
    }
});

function createInterviewSetup(): array
{
    $candidateUser = User::factory()->verified()->create(['role' => 'candidate', 'name' => 'Ana Santos']);
    $candidateUser->assignRole('candidate');
    $candidateProfile = CandidateProfile::create(['user_id' => $candidateUser->id]);

    $employerUser = User::factory()->verified()->create(['role' => 'employer', 'name' => 'HR Director']);
    $employerUser->assignRole('employer');
    $employerProfile = EmployerProfile::create([
        'user_id' => $employerUser->id,
        'company_name' => 'Manila Inclusivity Hub',
        'business_permit_number' => 'BP-998',
    ]);

    $job = JobPosting::create([
        'employer_profile_id' => $employerProfile->id,
        'title' => 'Accessible Quality Assurance Analyst',
        'description' => 'QA tester with screen reader and keyboard accessibility background',
        'status' => 'published',
    ]);

    $application = Application::create([
        'candidate_profile_id' => $candidateProfile->id,
        'job_posting_id' => $job->id,
        'status' => 'interview',
        'applied_at' => now()->subDays(3),
        'interview_at' => now()->addDays(2),
        'interview_location' => 'Google Meet Virtual Room',
        'interview_url' => 'https://meet.google.com/abc-defg-hij',
        'interview_accommodations' => 'Filipino Sign Language Interpreter & Screen Reader Test Files',
    ]);

    return [$candidateUser, $employerUser, $application, $job];
}

test('candidate can toggle assistive tech checklist items for scheduled interview', function () {
    [$candidateUser, $employerUser, $application, $job] = createInterviewSetup();

    Livewire::actingAs($candidateUser)
        ->test(ApplicationsTracker::class)
        ->assertSee('Pre-Interview Assistive Tech Checklist')
        ->assertSee('Audio & Video Lighting')
        ->call('toggleChecklistItem', $application->id, 'audio_video_fsl')
        ->assertSet('actionNotification', 'Audio & Video Lighting marked as ready.');

    $fresh = $application->fresh();
    expect($fresh->interview_checklist['audio_video_fsl']['checked'])->toBeTrue()
        ->and($fresh->isInterviewChecklistComplete())->toBeFalse();

    // Toggle off
    Livewire::actingAs($candidateUser)
        ->test(ApplicationsTracker::class)
        ->call('toggleChecklistItem', $application->id, 'audio_video_fsl')
        ->assertSet('actionNotification', 'Audio & Video Lighting unchecked.');

    expect($application->fresh()->interview_checklist['audio_video_fsl']['checked'])->toBeFalse();
});

test('candidate can mark all checklist items ready and trigger system notification', function () {
    [$candidateUser, $employerUser, $application, $job] = createInterviewSetup();

    Livewire::actingAs($candidateUser)
        ->test(ApplicationsTracker::class)
        ->call('markChecklistReady', $application->id);

    $fresh = $application->fresh();
    expect($fresh->isInterviewChecklistComplete())->toBeTrue();
    expect($fresh->interviewChecklistProgress()['completed'])->toBe(4);
    expect($fresh->interviewChecklistProgress()['percent'])->toBe(100);

    // Verify system message created
    $sysMessage = ApplicationMessage::where('application_id', $application->id)
        ->where('type', 'system')
        ->first();

    expect($sysMessage)->not->toBeNull()
        ->and($sysMessage->body)->toContain('Assistive Tech Verified');
});

test('candidate cannot toggle checklist for application in non-interview status', function () {
    [$candidateUser, $employerUser, $application, $job] = createInterviewSetup();
    $application->update(['status' => 'applied']);

    Livewire::actingAs($candidateUser)
        ->test(ApplicationsTracker::class)
        ->call('toggleChecklistItem', $application->id, 'audio_video_fsl');

    expect($application->fresh()->interview_checklist)->toBeNull();
});

test('unauthorized candidate cannot toggle another candidates checklist', function () {
    [$candidateUser, $employerUser, $application, $job] = createInterviewSetup();

    $otherUser = User::factory()->verified()->create(['role' => 'candidate', 'name' => 'Intruder']);
    $otherUser->assignRole('candidate');
    CandidateProfile::create(['user_id' => $otherUser->id]);

    Livewire::actingAs($otherUser)
        ->test(ApplicationsTracker::class)
        ->call('toggleChecklistItem', $application->id, 'audio_video_fsl');

    expect($application->fresh()->interview_checklist)->toBeNull();
});

test('employer sees tech ready badge and checklist status in applicants pipeline', function () {
    [$candidateUser, $employerUser, $application, $job] = createInterviewSetup();

    // Pre-complete checklist
    $checklist = [];
    foreach (Application::CHECKLIST_ITEMS as $key => $meta) {
        $checklist[$key] = ['checked' => true, 'checked_at' => now()->toIso8601String()];
    }
    $application->update(['interview_checklist' => $checklist]);

    Livewire::actingAs($employerUser)
        ->test(Applicants::class)
        ->assertSee('Tech Ready')
        ->call('viewDetails', $application->id)
        ->assertSee('Pre-Interview Assistive Tech Status')
        ->assertSee('All 4 items verified');
});

test('pwa webmanifest and offline html are valid and accessible', function () {
    $manifestPath = public_path('manifest.webmanifest');
    $offlinePath = public_path('offline.html');
    $swPath = public_path('sw.js');

    expect(file_exists($manifestPath))->toBeTrue();
    expect(file_exists($offlinePath))->toBeTrue();
    expect(file_exists($swPath))->toBeTrue();

    $manifestJson = json_decode(file_get_contents($manifestPath), true);
    expect($manifestJson)->toBeArray()
        ->and($manifestJson['name'])->toBe('PWD-E | Accessible Employment Ecosystem')
        ->and($manifestJson['display'])->toBe('standalone');
});

test('candidate can accept interview schedule and download calendar event', function () {
    [$candidateUser, $employerUser, $application, $job] = createInterviewSetup();

    expect($application->isInterviewAccepted())->toBeFalse();

    Livewire::actingAs($candidateUser)
        ->test(ApplicationsTracker::class)
        ->call('acceptAndAddToCalendar', $application->id)
        ->assertSee('Interview Accepted')
        ->assertSee('Google Calendar')
        ->assertSee('Download .ICS');

    $fresh = $application->fresh();
    expect($fresh->isInterviewAccepted())->toBeTrue();
    expect($fresh->interview_accepted_at)->not->toBeNull();

    // Verify system notification message created
    $sysMsg = ApplicationMessage::where('application_id', $application->id)
        ->where('type', 'system')
        ->where('body', 'like', '%Interview Confirmed%')
        ->first();

    expect($sysMsg)->not->toBeNull();

    // Verify ICS download
    Livewire::actingAs($candidateUser)
        ->test(ApplicationsTracker::class)
        ->call('downloadIcs', $application->id)
        ->assertFileDownloaded("interview-{$application->id}.ics");
});
