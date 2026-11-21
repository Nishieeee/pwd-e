<?php

use App\Livewire\Candidate\ApplicationsTracker;
use App\Livewire\Employer\Applicants;
use App\Models\Application;
use App\Models\ApplicationMessage;
use App\Models\CandidateProfile;
use App\Models\EmployerProfile;
use App\Models\JobPosting;
use App\Models\User;
use App\Notifications\AccommodationMessageReceived;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

beforeEach(function () {
    foreach (['candidate', 'employer', 'admin'] as $r) {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => $r, 'guard_name' => 'web']);
    }
});

function createApplicationWithParticipants(): array
{
    $candidateUser = User::factory()->verified()->create(['role' => 'candidate', 'name' => 'Maria Candidate']);
    $candidateUser->assignRole('candidate');
    $candidateProfile = CandidateProfile::create(['user_id' => $candidateUser->id]);

    $employerUser = User::factory()->verified()->create(['role' => 'employer', 'name' => 'John Employer']);
    $employerUser->assignRole('employer');
    $employerProfile = EmployerProfile::create([
        'user_id' => $employerUser->id,
        'company_name' => 'Accessible Tech Corp',
        'business_permit_number' => 'BP-100',
    ]);

    $job = JobPosting::create([
        'employer_profile_id' => $employerProfile->id,
        'title' => 'Accessibility Specialist',
        'description' => 'Role description',
        'status' => 'published',
    ]);

    $application = Application::create([
        'candidate_profile_id' => $candidateProfile->id,
        'job_posting_id' => $job->id,
        'status' => 'applied',
        'applied_at' => now(),
    ]);

    return [$candidateUser, $employerUser, $application, $job];
}

test('candidate can send accommodation inquiry chip to employer', function () {
    Notification::fake();
    [$candidate, $employer, $app, $job] = createApplicationWithParticipants();

    Livewire::actingAs($candidate)
        ->test(ApplicationsTracker::class)
        ->call('openChatModal', $app->id)
        ->assertSet('showChatModal', true)
        ->call('sendChatMessage', 'wheelchair');

    $message = ApplicationMessage::where('application_id', $app->id)->first();
    expect($message)->not->toBeNull();
    expect($message->type)->toBe('accommodation_inquiry');
    expect($message->metadata['topic'])->toBe('wheelchair');
    expect($message->metadata['resolved'])->toBeFalse();
    expect($message->body)->toContain('step-free entrance and wheelchair ramp access');

    Notification::assertSentTo($employer, AccommodationMessageReceived::class);
});

test('candidate can send custom chat message to employer', function () {
    Notification::fake();
    [$candidate, $employer, $app, $job] = createApplicationWithParticipants();

    Livewire::actingAs($candidate)
        ->test(ApplicationsTracker::class)
        ->call('openChatModal', $app->id)
        ->set('chatMessageText', 'Can I bring my service animal to the interview?')
        ->call('sendChatMessage');

    $message = ApplicationMessage::where('application_id', $app->id)->first();
    expect($message)->not->toBeNull();
    expect($message->type)->toBe('message');
    expect($message->body)->toBe('Can I bring my service animal to the interview?');

    Notification::assertSentTo($employer, AccommodationMessageReceived::class);
});

test('employer can reply and confirm candidate accommodation inquiry', function () {
    Notification::fake();
    [$candidate, $employer, $app, $job] = createApplicationWithParticipants();

    // Candidate creates inquiry
    $inquiry = ApplicationMessage::create([
        'application_id' => $app->id,
        'sender_id' => $candidate->id,
        'body' => 'Inquiry: Does your venue have wheelchair ramp access?',
        'type' => 'accommodation_inquiry',
        'metadata' => ['topic' => 'Wheelchair Ramp Access', 'resolved' => false],
    ]);

    // Employer opens chat and marks unread messages as read
    Livewire::actingAs($employer)
        ->test(Applicants::class)
        ->call('openChatModal', $app->id)
        ->assertSet('showChatModal', true)
        ->call('confirmAccommodation', $inquiry->id);

    $inquiry->refresh();
    expect($inquiry->metadata['resolved'])->toBeTrue();

    // Check system confirmation message was created
    $systemMsg = ApplicationMessage::where('application_id', $app->id)->where('type', 'system')->first();
    expect($systemMsg)->not->toBeNull();
    expect($systemMsg->body)->toContain('Accommodation Confirmed: Wheelchair Ramp Access');

    Notification::assertSentTo($candidate, AccommodationMessageReceived::class);

    // Employer sends a reply
    Livewire::actingAs($employer)
        ->test(Applicants::class)
        ->call('openChatModal', $app->id)
        ->set('chatMessageText', 'Yes, our building has elevator and ramp access.')
        ->call('sendChatMessage');

    $reply = ApplicationMessage::where('application_id', $app->id)->where('type', 'message')->where('sender_id', $employer->id)->first();
    expect($reply)->not->toBeNull();
    expect($reply->body)->toBe('Yes, our building has elevator and ramp access.');
});

test('unauthorized candidate cannot access or message another application', function () {
    [$candidate, $employer, $app, $job] = createApplicationWithParticipants();
    $otherCandidate = User::factory()->verified()->create(['role' => 'candidate']);
    $otherCandidate->assignRole('candidate');
    CandidateProfile::create(['user_id' => $otherCandidate->id]);

    Livewire::actingAs($otherCandidate)
        ->test(ApplicationsTracker::class)
        ->call('openChatModal', $app->id)
        ->assertSet('showChatModal', false)
        ->call('sendChatMessage', 'wheelchair');

    expect(ApplicationMessage::where('application_id', $app->id)->count())->toBe(0);
});
