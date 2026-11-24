<?php

namespace App\Livewire\Candidate;

use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\JobInvitation;
use App\Models\JobPosting;
use App\Models\WorkforceRecord;
use App\Notifications\EmploymentSeparationResponse;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.candidate')]
class Dashboard extends Component
{
    public ?int $selectedApplicationId = null;

    public bool $showBreakdownModal = false;

    public ?string $calendarDownloadSuccess = null;

    public ?string $profileSavedMessage = null;

    public bool $showDisputeModal = false;

    public ?int $disputingRecordId = null;

    public string $candidateDisputeReason = '';

    public bool $showAcknowledgeModal = false;

    public ?int $acknowledgingRecordId = null;

    public bool $showDeclineInviteModal = false;

    public ?int $decliningInviteId = null;

    public string $decliningInviteJobTitle = '';

    public string $decliningInviteEmployer = '';

    public function mount(): void
    {
        if (session()->has('profile_saved_banner')) {
            $this->profileSavedMessage = (string) session('profile_saved_banner');
        }
    }

    public function dismissProfileSavedBanner(): void
    {
        $this->profileSavedMessage = null;
    }

    public function openAcknowledgeModal(int $id): void
    {
        $this->acknowledgingRecordId = $id;
        $this->showAcknowledgeModal = true;
    }

    public function closeAcknowledgeModal(): void
    {
        $this->showAcknowledgeModal = false;
        $this->acknowledgingRecordId = null;
    }

    public function acknowledgeSeparation(?int $id = null): void
    {
        $targetId = $id ?? $this->acknowledgingRecordId;
        if (! $targetId) {
            return;
        }

        $candidateProfile = Auth::user()?->candidateProfile;
        if (! $candidateProfile) {
            return;
        }

        $record = WorkforceRecord::with(['employerProfile.user', 'application.jobPosting'])
            ->where('candidate_profile_id', $candidateProfile->id)
            ->where('separation_status', 'pending_review')
            ->findOrFail($targetId);

        $record->update([
            'separation_status' => 'acknowledged',
            'candidate_response_at' => now(),
        ]);

        if ($record->employerProfile?->user) {
            $positionTitle = $record->application?->jobPosting?->title ?? 'Position';

            $record->employerProfile->user->notify(
                new EmploymentSeparationResponse(
                    candidateName: Auth::user()->name,
                    positionTitle: $positionTitle,
                    responseType: 'acknowledged',
                    disputeReason: null,
                    workforceRecordId: $record->id,
                )
            );
        }

        if (function_exists('activity')) {
            activity('workforce')
                ->performedOn($record)
                ->causedBy(Auth::user())
                ->withProperties(['separation_status' => 'acknowledged'])
                ->log('Employment separation acknowledged by candidate');
        }

        $this->closeAcknowledgeModal();
        $this->dispatch('toast', message: __('Employment separation acknowledged. Workforce records updated.'));
    }

    public function openDisputeModal(int $id): void
    {
        $this->disputingRecordId = $id;
        $this->candidateDisputeReason = '';
        $this->showDisputeModal = true;
    }

    public function closeDisputeModal(): void
    {
        $this->showDisputeModal = false;
        $this->disputingRecordId = null;
        $this->candidateDisputeReason = '';
        $this->resetValidation();
    }

    public function submitDispute(): void
    {
        $this->validate([
            'candidateDisputeReason' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'candidateDisputeReason.required' => 'Please provide the grounds or statement for contesting this separation.',
            'candidateDisputeReason.min' => 'Please provide at least 10 characters detailing your contestation grounds.',
        ]);

        $candidateProfile = Auth::user()?->candidateProfile;
        if (! $candidateProfile || ! $this->disputingRecordId) {
            return;
        }

        $record = WorkforceRecord::with(['employerProfile.user', 'application.jobPosting'])
            ->where('candidate_profile_id', $candidateProfile->id)
            ->findOrFail($this->disputingRecordId);

        $record->update([
            'separation_status' => 'disputed',
            'candidate_response_at' => now(),
            'candidate_dispute_reason' => $this->candidateDisputeReason,
        ]);

        if ($record->employerProfile?->user) {
            $positionTitle = $record->application?->jobPosting?->title ?? 'Position';

            $record->employerProfile->user->notify(
                new EmploymentSeparationResponse(
                    candidateName: Auth::user()->name,
                    positionTitle: $positionTitle,
                    responseType: 'disputed',
                    disputeReason: $this->candidateDisputeReason,
                    workforceRecordId: $record->id,
                )
            );
        }

        if (function_exists('activity')) {
            activity('workforce')
                ->performedOn($record)
                ->causedBy(Auth::user())
                ->withProperties([
                    'separation_status' => 'disputed',
                    'dispute_reason' => $this->candidateDisputeReason,
                ])
                ->log('Employment separation formally contested/disputed by candidate');
        }

        $this->dispatch('toast', message: __('Formal dispute submitted. Notice has been flagged for statutory compliance review.'));
        $this->closeDisputeModal();
    }

    public function viewFitBreakdown(int $applicationId): void
    {
        $this->selectedApplicationId = $applicationId;
        $this->showBreakdownModal = true;
    }

    public function closeBreakdownModal(): void
    {
        $this->showBreakdownModal = false;
        $this->selectedApplicationId = null;
    }

    public function acceptAndAddToCalendar(int $applicationId): void
    {
        $application = Application::with(['jobPosting.employerProfile'])->find($applicationId);
        if ($application) {
            $this->calendarDownloadSuccess = "Interview with {$application->jobPosting->employerProfile->company_name} accepted and added to calendar!";
            $this->dispatch('interview-accepted', message: $this->calendarDownloadSuccess);
        }
    }

    public function openDeclineInviteModal(int $invitationId): void
    {
        $user = Auth::user();
        $candidateProfile = $user?->candidateProfile;
        if (! $candidateProfile) {
            return;
        }

        $invitation = JobInvitation::where('candidate_profile_id', $candidateProfile->id)
            ->where('id', $invitationId)
            ->first();

        if (! $invitation) {
            return;
        }

        $this->decliningInviteId = $invitation->id;
        $this->decliningInviteJobTitle = $invitation->jobPosting?->title ?? 'Position';
        $this->decliningInviteEmployer = $invitation->jobPosting?->employerProfile?->company_name ?? 'Employer';
        $this->showDeclineInviteModal = true;
    }

    public function closeDeclineInviteModal(): void
    {
        $this->showDeclineInviteModal = false;
        $this->decliningInviteId = null;
    }

    public function confirmDeclineInvitation(): void
    {
        if ($this->decliningInviteId) {
            $this->declineInvitation($this->decliningInviteId);
        }
        $this->closeDeclineInviteModal();
    }

    public function declineInvitation(int $invitationId): void
    {
        $user = Auth::user();
        $candidateProfile = $user?->candidateProfile;
        if (! $candidateProfile) {
            return;
        }

        $invitation = JobInvitation::where('candidate_profile_id', $candidateProfile->id)
            ->where('id', $invitationId)
            ->first();

        if ($invitation) {
            $invitation->update(['status' => 'declined']);
            \Flux\Flux::toast(variant: 'success', text: __('Invitation declined.'));
        }
    }

    public function render()
    {
        $user = Auth::user();
        $candidateProfile = $user->candidateProfile ?? CandidateProfile::firstOrCreate(['user_id' => $user->id]);

        // 1. Stats calculation
        $activeApplications = Application::where('candidate_profile_id', $candidateProfile->id)
            ->whereIn('status', ['applied', 'under_review', 'interview', 'submitted'])
            ->with(['jobPosting.employerProfile', 'jobPosting.accommodations'])
            ->orderByDesc('applied_at')
            ->get();

        $activeApplicationsCount = $activeApplications->count();

        $highestFitScore = (int) round(($candidateProfile->jobMatches()->max('accessibility_score') ?? 0.96) * 100);

        $interviewInvitationsCount = Application::where('candidate_profile_id', $candidateProfile->id)
            ->where('status', 'interview')
            ->count();

        $accommodationsSpecifiedCount = $candidateProfile->accessibilityNeeds()->count();

        // 2. Upcoming Interview Banner
        $upcomingInterviewApplication = Application::where('candidate_profile_id', $candidateProfile->id)
            ->where('status', 'interview')
            ->whereNotNull('interview_at')
            ->with(['jobPosting.employerProfile'])
            ->orderBy('interview_at')
            ->first();

        // 3. Recommended Jobs Based on Accommodations
        // Get jobs with verified coverage matching candidate's required accommodations
        $candidateAccIds = $candidateProfile->accessibilityNeeds()->pluck('accommodation_id')->toArray();

        $recommendedJobs = JobPosting::where('status', 'published')
            ->with(['employerProfile', 'accommodations'])
            ->whereHas('employerProfile')
            ->take(3)
            ->get()
            ->map(function ($job) use ($candidateProfile) {
                $match = $candidateProfile->jobMatches()->where('job_posting_id', $job->id)->first();
                $score = $match ? (int) round($match->total_score * 100) : 90;
                $job->fit_score = $score;

                return $job;
            });

        // 4. Modal application data if opened
        $breakdownApplication = $this->selectedApplicationId
            ? Application::with(['jobPosting.employerProfile', 'jobPosting.accommodations'])->find($this->selectedApplicationId)
            : null;

        // 5. Active Direct Job Invitations from Employers
        $jobInvitations = JobInvitation::where('candidate_profile_id', $candidateProfile->id)
            ->where('status', 'pending')
            ->with(['jobPosting.employerProfile'])
            ->latest()
            ->take(5)
            ->get();

        // 6. Statutory Employment Separation Due Process Notices
        $pendingSeparationNotice = WorkforceRecord::where('candidate_profile_id', $candidateProfile->id)
            ->where('separation_status', 'pending_review')
            ->with(['employerProfile.user', 'application.jobPosting'])
            ->latest('separation_notice_at')
            ->first();

        $disputedSeparationNotice = WorkforceRecord::where('candidate_profile_id', $candidateProfile->id)
            ->where('separation_status', 'disputed')
            ->with(['employerProfile.user', 'application.jobPosting'])
            ->latest('candidate_response_at')
            ->first();

        $acknowledgingRecord = $this->acknowledgingRecordId
            ? WorkforceRecord::where('candidate_profile_id', $candidateProfile->id)
                ->with(['employerProfile.user', 'application.jobPosting'])
                ->find($this->acknowledgingRecordId)
            : null;

        return view('livewire.candidate.dashboard', [
            'user' => $user,
            'candidateProfile' => $candidateProfile,
            'activeApplications' => $activeApplications,
            'activeApplicationsCount' => $activeApplicationsCount,
            'highestFitScore' => $highestFitScore,
            'interviewInvitationsCount' => $interviewInvitationsCount,
            'accommodationsSpecifiedCount' => $accommodationsSpecifiedCount,
            'upcomingInterviewApplication' => $upcomingInterviewApplication,
            'recommendedJobs' => $recommendedJobs,
            'breakdownApplication' => $breakdownApplication,
            'calendarDownloadSuccess' => $this->calendarDownloadSuccess,
            'profileSavedMessage' => $this->profileSavedMessage,
            'showBreakdownModal' => $this->showBreakdownModal,
            'jobInvitations' => $jobInvitations,
            'pendingSeparationNotice' => $pendingSeparationNotice,
            'disputedSeparationNotice' => $disputedSeparationNotice,
            'acknowledgingRecord' => $acknowledgingRecord,
        ]);
    }
}
