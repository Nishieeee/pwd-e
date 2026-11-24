<?php

namespace App\Livewire\Employer;

use App\Models\Application;
use App\Models\ApplicationMessage;
use App\Models\JobPosting;
use App\Models\WorkforceRecord;
use App\Notifications\AccommodationMessageReceived;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Applicants')]
#[Layout('layouts.employer')]
class Applicants extends Component
{
    public ?JobPosting $job = null;

    #[Url(as: 'job')]
    public ?int $selectedJobId = null;

    public ?int $viewingApplicationId = null;

    #[Url(as: 'status')]
    public string $statusFilter = 'all';

    public bool $showCloseJobPromptModal = false;

    public ?int $promptCloseJobId = null;

    public ?string $promptCloseJobTitle = '';

    public ?string $hiredCandidateName = '';

    public bool $showInterviewModal = false;

    public ?int $interviewAppId = null;

    public ?string $interviewDate = '';

    public ?string $interviewTime = '10:00';

    public ?string $interviewLocation = '';

    public ?string $interviewUrl = '';

    public ?string $interviewAccommodations = '';

    public ?string $interviewNotes = '';

    public bool $showHireModal = false;

    public ?int $hiringAppId = null;

    public ?float $hireWage = null;

    public string $hireStartDate = '';

    public bool $hirePwdVerified = true;

    public bool $showOfferModal = false;

    public ?int $offeringAppId = null;

    public ?float $offerSalary = null;

    public string $offerStartDate = '';

    public string $offerAccommodations = '';

    public string $offerNotes = '';

    public bool $showRejectModal = false;

    public ?int $rejectingAppId = null;

    public string $rejectionReason = '';

    public ?int $selectedChatAppId = null;

    public bool $showChatModal = false;

    public string $chatMessageText = '';

    public function openChatModal(int $appId): void
    {
        $user = Auth::user();
        $employerProfile = $user?->employerProfile;

        $app = Application::whereHas('jobPosting', function ($q) use ($employerProfile) {
            $q->where('employer_profile_id', $employerProfile?->id);
        })->where('id', $appId)->first();

        if (! $app) {
            return;
        }

        $this->selectedChatAppId = $appId;
        $this->showChatModal = true;
        $this->chatMessageText = '';

        // Mark unread messages from candidate as read
        ApplicationMessage::where('application_id', $appId)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function closeChatModal(): void
    {
        $this->showChatModal = false;
        $this->selectedChatAppId = null;
        $this->chatMessageText = '';
    }

    public function sendChatMessage(): void
    {
        $user = Auth::user();
        $employerProfile = $user?->employerProfile;
        $body = trim($this->chatMessageText);

        if (! $user || ! $this->selectedChatAppId || empty($body)) {
            return;
        }

        $app = Application::with(['jobPosting', 'candidateProfile.user'])
            ->whereHas('jobPosting', function ($q) use ($employerProfile) {
                $q->where('employer_profile_id', $employerProfile?->id);
            })->where('id', $this->selectedChatAppId)->first();

        if (! $app) {
            return;
        }

        $msg = ApplicationMessage::create([
            'application_id' => $app->id,
            'sender_id' => $user->id,
            'body' => $body,
            'type' => 'message',
        ]);

        $this->chatMessageText = '';

        // Notify candidate
        $candidateUser = $app->candidateProfile?->user;
        if ($candidateUser) {
            $candidateUser->notify(new AccommodationMessageReceived($msg, $app->jobPosting, $user));
        }

        \Flux\Flux::toast(variant: 'success', text: __('Reply sent to candidate.'));
    }

    public function confirmAccommodation(int $messageId): void
    {
        $user = Auth::user();
        $employerProfile = $user?->employerProfile;

        $message = ApplicationMessage::where('id', $messageId)
            ->whereHas('application.jobPosting', function ($q) use ($employerProfile) {
                $q->where('employer_profile_id', $employerProfile?->id);
            })->first();

        if (! $message) {
            return;
        }

        $metadata = $message->metadata ?? [];
        $metadata['resolved'] = true;
        $metadata['resolved_at'] = now()->toIso8601String();
        $metadata['resolved_by'] = $user->id;
        $message->update(['metadata' => $metadata]);

        // Post confirmation system message
        $app = $message->application;
        $sysMsg = ApplicationMessage::create([
            'application_id' => $app->id,
            'sender_id' => $user->id,
            'body' => 'Accommodation Confirmed: ' . ($metadata['topic'] ?? 'Requested accommodation') . ' is confirmed and will be provided for this candidate.',
            'type' => 'system',
            'metadata' => ['resolved' => true, 'topic' => $metadata['topic'] ?? null],
        ]);

        // Notify candidate
        $candidateUser = $app->candidateProfile?->user;
        if ($candidateUser) {
            $candidateUser->notify(new AccommodationMessageReceived($sysMsg, $app->jobPosting, $user));
        }

        \Flux\Flux::toast(variant: 'success', text: __('Accommodation marked as confirmed.'));
    }

    public function mount(?JobPosting $job = null): void
    {
        $user = Auth::user();
        if (! $user || ! $user->isEmployer()) {
            abort(403);
        }
        $profile = $user->employerProfile;
        if (! $profile) {
            abort(403);
        }

        if ($job && $job->exists) {
            if ($job->employer_profile_id !== $profile->id) {
                abort(403);
            }
            $this->job = $job->load(['employerProfile', 'accommodations']);
            $this->selectedJobId = $job->id;
            $this->hireWage = (float) ($job->salary_max ?? $job->salary_min ?? 35000);
        } else {
            $this->job = null;
            if ($this->selectedJobId) {
                $jobMatch = JobPosting::where('employer_profile_id', $profile->id)->find($this->selectedJobId);
                if ($jobMatch) {
                    $this->job = $jobMatch->load(['employerProfile', 'accommodations']);
                    $this->hireWage = (float) ($jobMatch->salary_max ?? $jobMatch->salary_min ?? 35000);
                }
            } else {
                $this->hireWage = 35000.0;
            }
        }

        $this->hireStartDate = now()->format('Y-m-d');
        $this->interviewDate = now()->addDays(2)->format('Y-m-d');
    }

    public function setStatusFilter(string $status): void
    {
        $this->statusFilter = $status;
    }

    public function filterByJob(?int $jobId): void
    {
        $this->selectedJobId = $jobId;
        $profile = Auth::user()?->employerProfile;
        if ($jobId && $profile) {
            $this->job = JobPosting::where('employer_profile_id', $profile->id)->find($jobId);
        } else {
            $this->job = null;
        }
    }

    public function viewDetails(int $applicationId): void
    {
        $this->viewingApplicationId = $applicationId;
    }

    public function closeDetails(): void
    {
        $this->viewingApplicationId = null;
    }

    public function openInterviewModal(int $applicationId): void
    {
        $profile = Auth::user()?->employerProfile;
        if (! $profile) {
            return;
        }

        $app = Application::whereHas('jobPosting', function ($q) use ($profile) {
            $q->where('employer_profile_id', $profile->id);
        })->with('jobPosting')->findOrFail($applicationId);

        $this->interviewAppId = $app->id;
        $this->interviewDate = $app->interview_at ? $app->interview_at->format('Y-m-d') : now()->addDays(2)->format('Y-m-d');
        $this->interviewTime = $app->interview_at ? $app->interview_at->format('H:i') : '10:00';
        $this->interviewLocation = $app->interview_location ?? $app->jobPosting?->location ?? '';
        $this->interviewUrl = $app->interview_url ?? 'https://meet.google.com/new';
        $this->interviewAccommodations = $app->interview_accommodations ?? 'Sign Language Interpreter requested';
        $this->interviewNotes = $app->employer_notes ?? '';
        $this->showInterviewModal = true;
    }

    public function closeInterviewModal(): void
    {
        $this->showInterviewModal = false;
        $this->interviewAppId = null;
    }

    public function saveInterview(): void
    {
        if (! $this->interviewAppId) {
            return;
        }

        $profile = Auth::user()?->employerProfile;
        if (! $profile) {
            return;
        }

        $app = Application::whereHas('jobPosting', function ($q) use ($profile) {
            $q->where('employer_profile_id', $profile->id);
        })->findOrFail($this->interviewAppId);

        $dateTimeString = trim($this->interviewDate . ' ' . $this->interviewTime);
        $interviewAt = ! empty($this->interviewDate) ? \Carbon\Carbon::parse($dateTimeString) : now()->addDays(2);

        $app->update([
            'status' => 'interview',
            'status_subtext' => 'Interview scheduled for ' . $interviewAt->format('M d, Y h:i A'),
            'interview_at' => $interviewAt,
            'interview_location' => $this->interviewLocation ?: null,
            'interview_url' => $this->interviewUrl ?: null,
            'interview_accommodations' => $this->interviewAccommodations ?: null,
            'employer_notes' => $this->interviewNotes ?: null,
        ]);

        $this->showInterviewModal = false;
        $this->interviewAppId = null;
        Flux::toast(variant: 'success', text: __('Interview scheduled successfully.'));
    }

    public function openHireModal(int $applicationId): void
    {
        $profile = Auth::user()?->employerProfile;
        if (! $profile) {
            return;
        }

        $app = Application::with(['candidateProfile.user', 'jobPosting'])->whereHas('jobPosting', function ($q) use ($profile) {
            $q->where('employer_profile_id', $profile->id);
        })->findOrFail($applicationId);

        $this->hiringAppId = $app->id;
        $this->hireWage = (float) ($app->jobPosting?->salary_max ?? $app->jobPosting?->salary_min ?? 35000);
        $this->hireStartDate = now()->format('Y-m-d');
        $this->hirePwdVerified = ! empty($app->candidateProfile?->user?->pwd_id_number);
        $this->showHireModal = true;
    }

    public function closeHireModal(): void
    {
        $this->showHireModal = false;
        $this->hiringAppId = null;
    }

    public function confirmHire(): void
    {
        if (! $this->hiringAppId) {
            return;
        }

        $this->hire($this->hiringAppId);
        $this->showHireModal = false;
        $this->hiringAppId = null;
    }

    public function openRejectModal(int $applicationId): void
    {
        $profile = Auth::user()?->employerProfile;
        if (! $profile) {
            return;
        }

        $app = Application::with(['candidateProfile.user', 'jobPosting'])->whereHas('jobPosting', function ($q) use ($profile) {
            $q->where('employer_profile_id', $profile->id);
        })->findOrFail($applicationId);

        $this->rejectingAppId = $app->id;
        $this->rejectionReason = $app->rejection_reason ?? '';
        $this->showRejectModal = true;
    }

    public function closeRejectModal(): void
    {
        $this->showRejectModal = false;
        $this->rejectingAppId = null;
        $this->rejectionReason = '';
    }

    public function confirmReject(): void
    {
        if (! $this->rejectingAppId) {
            return;
        }

        $profile = Auth::user()?->employerProfile;
        if (! $profile) {
            return;
        }

        $app = Application::with(['candidateProfile.user', 'jobPosting'])->whereHas('jobPosting', function ($q) use ($profile) {
            $q->where('employer_profile_id', $profile->id);
        })->findOrFail($this->rejectingAppId);

        $app->update([
            'status' => 'rejected',
            'status_subtext' => 'Application declined',
            'rejection_reason' => $this->rejectionReason ?: null,
            'rejected_at' => now(),
        ]);

        if ($app->candidateProfile?->user) {
            $app->candidateProfile->user->notify(
                new \App\Notifications\ApplicationStatusChanged(
                    $app->jobPosting?->title ?? 'Position',
                    'rejected',
                    $this->rejectionReason ?: null
                )
            );
        }

        $this->showRejectModal = false;
        $this->rejectingAppId = null;
        $this->rejectionReason = '';

        Flux::toast(variant: 'success', text: __('Applicant declined. Candidate notified politely.'));
    }

    public function openOfferModal(int $applicationId): void
    {
        $profile = Auth::user()?->employerProfile;
        if (! $profile) {
            return;
        }

        $app = Application::with(['candidateProfile.user', 'jobPosting'])->whereHas('jobPosting', function ($q) use ($profile) {
            $q->where('employer_profile_id', $profile->id);
        })->findOrFail($applicationId);

        $this->offeringAppId = $app->id;
        $this->offerSalary = (float) ($app->jobPosting?->salary_max ?? $app->jobPosting?->salary_min ?? 35000);
        $this->offerStartDate = now()->addWeeks(2)->format('Y-m-d');
        $this->offerAccommodations = $app->interview_accommodations ?? 'Standard verified accessibility package';
        $this->offerNotes = 'We are pleased to extend this offer of employment for the ' . ($app->jobPosting?->title ?? 'position') . '.';
        $this->showOfferModal = true;
    }

    public function closeOfferModal(): void
    {
        $this->showOfferModal = false;
        $this->offeringAppId = null;
    }

    public function sendOffer(): void
    {
        if (! $this->offeringAppId) {
            return;
        }

        $profile = Auth::user()?->employerProfile;
        if (! $profile) {
            return;
        }

        $app = Application::with(['candidateProfile.user', 'jobPosting'])->whereHas('jobPosting', function ($q) use ($profile) {
            $q->where('employer_profile_id', $profile->id);
        })->findOrFail($this->offeringAppId);

        if ($app->candidateProfile?->user_id === Auth::id()) {
            if (function_exists('activity')) {
                activity('fraud_alert')
                    ->performedOn($app)
                    ->causedBy(Auth::user())
                    ->withProperties(['candidate_user_id' => $app->candidateProfile?->user_id])
                    ->log('Self-hiring attempt blocked');
            }
            Flux::toast(variant: 'danger', text: __('Self-hiring is not permitted.'));
            $this->closeOfferModal();
            return;
        }

        if (! $app->canTransitionTo(Application::STATUS_OFFERED)) {
            Flux::toast(variant: 'danger', text: __('Cannot extend offer in current status.'));
            return;
        }

        $this->validate([
            'offerSalary' => 'required|numeric|min:1',
            'offerStartDate' => 'required|date',
            'offerAccommodations' => 'nullable|string|max:1000',
            'offerNotes' => 'nullable|string|max:2000',
        ]);

        $app->update([
            'status' => Application::STATUS_OFFERED,
            'status_subtext' => Application::computeStatusSubtext(Application::STATUS_OFFERED),
            'offer_extended_at' => now(),
            'offer_details' => [
                'salary' => (float) $this->offerSalary,
                'start_date' => $this->offerStartDate,
                'accommodations' => $this->offerAccommodations,
                'notes' => $this->offerNotes,
            ],
        ]);

        ApplicationMessage::create([
            'application_id' => $app->id,
            'sender_id' => Auth::id(),
            'body' => 'Formal Job Offer Extended: PHP ' . number_format($this->offerSalary) . ' annual/gross with start date ' . $this->offerStartDate . '.',
            'type' => 'system',
            'metadata' => [
                'event' => 'offer_extended',
                'extended_at' => now()->toIso8601String(),
            ],
        ]);

        $this->showOfferModal = false;
        $this->offeringAppId = null;

        if ($app->candidateProfile?->user) {
            $app->candidateProfile->user->notify(
                new \App\Notifications\ApplicationStatusChanged(
                    $app->jobPosting?->title ?? 'Position',
                    Application::STATUS_OFFERED,
                    'Formal offer extended: PHP ' . number_format($this->offerSalary) . ' annual/gross with start date ' . $this->offerStartDate . '.'
                )
            );
        }

        Flux::toast(variant: 'success', text: __('Formal job offer extended to candidate.'));
    }

    public function markInterviewCompleted(int $applicationId): void
    {
        $profile = Auth::user()?->employerProfile;
        if (! $profile) {
            return;
        }

        $app = Application::whereHas('jobPosting', function ($q) use ($profile) {
            $q->where('employer_profile_id', $profile->id);
        })->findOrFail($applicationId);

        if ($app->status !== Application::STATUS_INTERVIEW) {
            Flux::toast(variant: 'warning', text: __('Application is not currently in interview stage.'));
            return;
        }

        $app->update([
            'status_subtext' => 'Interview completed · Evaluation in progress',
        ]);

        ApplicationMessage::create([
            'application_id' => $app->id,
            'sender_id' => Auth::id(),
            'body' => 'Interview completed. Hiring team is finalizing the evaluation.',
            'type' => 'system',
        ]);

        Flux::toast(variant: 'success', text: __('Interview marked as completed.'));
    }

    public function updateStatus(int $applicationId, string $status): void
    {
        $profile = Auth::user()?->employerProfile;
        if (! $profile) {
            return;
        }

        $app = Application::with(['candidateProfile.user', 'jobPosting'])->whereHas('jobPosting', function ($q) use ($profile) {
            $q->where('employer_profile_id', $profile->id);
        })->findOrFail($applicationId);

        if ($status === 'rejected') {
            $this->openRejectModal($applicationId);
            return;
        }

        if ($status === 'offered') {
            $this->openOfferModal($applicationId);
            return;
        }

        if ($status === 'hired') {
            $this->openHireModal($applicationId);
            return;
        }

        if (! $app->canTransitionTo($status)) {
            Flux::toast(variant: 'danger', text: __("Cannot move application from :current to :target.", [
                'current' => ucfirst(str_replace('_', ' ', $app->status)),
                'target' => ucfirst(str_replace('_', ' ', $status)),
            ]));
            return;
        }

        $app->update([
            'status' => $status,
            'status_subtext' => Application::computeStatusSubtext($status, now()),
        ]);

        if ($app->candidateProfile?->user && ! in_array($status, ['rejected', 'offered'])) {
            $app->candidateProfile->user->notify(
                new \App\Notifications\ApplicationStatusChanged(
                    $app->jobPosting?->title ?? 'Position',
                    $status
                )
            );
        }

        if ($status === 'interview') {
            $this->openInterviewModal($applicationId);
            return;
        }

        Flux::toast(variant: 'success', text: __('Status updated to :status.', ['status' => ucfirst(str_replace('_', ' ', $status))]));
    }

    public function hire(int $applicationId): void
    {
        $profile = Auth::user()?->employerProfile;
        if (! $profile) {
            return;
        }

        $app = Application::with(['candidateProfile.user', 'jobPosting'])->whereHas('jobPosting', function ($q) use ($profile) {
            $q->where('employer_profile_id', $profile->id);
        })->findOrFail($applicationId);

        if ($app->candidateProfile?->user_id === Auth::id()) {
            if (function_exists('activity')) {
                activity('fraud_alert')
                    ->performedOn($app)
                    ->causedBy(Auth::user())
                    ->withProperties(['candidate_user_id' => $app->candidateProfile?->user_id])
                    ->log('Self-hiring attempt blocked');
            }
            Flux::toast(variant: 'danger', text: __('Self-hiring is not permitted.'));
            return;
        }

        $app->update([
            'status' => 'hired',
            'status_subtext' => 'Offer accepted and hired',
        ]);

        $wage = $this->hireWage ?: ($app->jobPosting?->salary_max ?? $app->jobPosting?->salary_min ?? 35000);
        $hiredAt = ! empty($this->hireStartDate) ? \Carbon\Carbon::parse($this->hireStartDate) : now();
        $isPwdVerified = ! empty($app->candidateProfile?->user?->pwd_id_number);

        WorkforceRecord::firstOrCreate(
            ['application_id' => $app->id],
            [
                'employer_profile_id' => $profile->id,
                'candidate_profile_id' => $app->candidate_profile_id,
                'hired_at' => $hiredAt,
                'pwd_verified' => $isPwdVerified,
                'annual_wage' => $wage,
            ]
        );

        if ($app->jobPosting && $app->jobPosting->status === 'published') {
            $this->promptCloseJobId = $app->jobPosting->id;
            $this->promptCloseJobTitle = $app->jobPosting->title;
            $this->hiredCandidateName = $app->candidateProfile?->user?->name ?? 'Candidate';
            $this->showCloseJobPromptModal = true;
        }

        Flux::toast(variant: 'success', text: __('Marked as hired; workforce record created with RA 10524 tax incentive.'));
    }

    public function openCloseJobPrompt(int $jobId): void
    {
        $profile = Auth::user()?->employerProfile;
        if (! $profile) {
            return;
        }

        $job = JobPosting::where('employer_profile_id', $profile->id)->find($jobId);
        if (! $job) {
            return;
        }

        $this->promptCloseJobId = $job->id;
        $this->promptCloseJobTitle = $job->title;
        $hiredApp = Application::where('job_posting_id', $job->id)
            ->where('status', 'hired')
            ->latest()
            ->first();
        $this->hiredCandidateName = $hiredApp?->candidateProfile?->user?->name ?? 'Candidate';
        $this->showCloseJobPromptModal = true;
    }

    public function closeJobListing(?int $jobId = null): void
    {
        $targetJobId = $jobId ?? $this->promptCloseJobId;
        $profile = Auth::user()?->employerProfile;
        if (! $profile || ! $targetJobId) {
            return;
        }

        $job = JobPosting::where('employer_profile_id', $profile->id)->findOrFail($targetJobId);
        $job->update([
            'status' => 'closed',
            'closes_at' => now(),
        ]);

        $this->showCloseJobPromptModal = false;
        $this->promptCloseJobId = null;

        Flux::toast(variant: 'success', text: __('Job listing ":title" closed. No new applications will be received.', ['title' => $job->title]));
    }

    public function keepJobListingOpen(): void
    {
        $this->showCloseJobPromptModal = false;
        $this->promptCloseJobId = null;
        Flux::toast(variant: 'info', text: __('Job listing remains published for additional hires.'));
    }

    public function render(): View
    {
        $profile = Auth::user()?->employerProfile;
        $employerJobs = $profile
            ? JobPosting::where('employer_profile_id', $profile->id)->orderBy('title')->get()
            : collect();

        $activeJobFilled = false;
        $filledJob = null;
        if ($this->job && $this->job->status === 'published') {
            $activeJobFilled = $this->job->applications()->where('status', 'hired')->exists();
            if ($activeJobFilled) {
                $filledJob = $this->job;
            }
        } elseif ($this->selectedJobId) {
            $selectedJobModel = $employerJobs->firstWhere('id', $this->selectedJobId);
            if ($selectedJobModel && $selectedJobModel->status === 'published') {
                $activeJobFilled = $selectedJobModel->applications()->where('status', 'hired')->exists();
                if ($activeJobFilled) {
                    $filledJob = $selectedJobModel;
                }
            }
        }

        $query = Application::with([
            'candidateProfile.user',
            'candidateProfile.education',
            'candidateProfile.workExperiences',
            'candidateProfile.certifications',
            'jobPosting.employerProfile',
            'messages',
            'workforceRecord',
            'reappliedFrom',
        ])->whereHas('jobPosting', function ($q) use ($profile) {
            $q->where('employer_profile_id', $profile?->id ?? 0);
        });

        // Deduplicate: Exclude superseded applications when candidate has a newer application for the same job
        $query->whereNotExists(function ($sub) {
            $sub->selectRaw(1)
                ->from('applications as newer_apps')
                ->whereColumn('newer_apps.candidate_profile_id', 'applications.candidate_profile_id')
                ->whereColumn('newer_apps.job_posting_id', 'applications.job_posting_id')
                ->whereColumn('newer_apps.id', '>', 'applications.id');
        });

        if ($this->job) {
            $query->where('job_posting_id', $this->job->id);
        } elseif ($this->selectedJobId) {
            $query->where('job_posting_id', $this->selectedJobId);
        }

        $baseCountQuery = clone $query;
        $allCounts = [
            'all' => (clone $baseCountQuery)->count(),
            'applied' => (clone $baseCountQuery)->where('status', 'applied')->count(),
            'under_review' => (clone $baseCountQuery)->where('status', 'under_review')->count(),
            'interview' => (clone $baseCountQuery)->where('status', 'interview')->count(),
            'offered' => (clone $baseCountQuery)->where('status', 'offered')->count(),
            'hired' => (clone $baseCountQuery)->where('status', 'hired')->count(),
            'rejected' => (clone $baseCountQuery)->whereIn('status', ['rejected', 'offer_declined'])->count(),
        ];

        if ($this->statusFilter === 'rejected') {
            $query->whereIn('status', ['rejected', 'offer_declined']);
        } elseif ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $applications = $query->orderByDesc('fit_score_at_apply')->get();

        $viewing = $this->viewingApplicationId
            ? Application::with([
                'candidateProfile.user',
                'candidateProfile.education',
                'candidateProfile.workExperiences',
                'candidateProfile.certifications',
                'jobPosting',
                'workforceRecord',
                'reappliedFrom.workforceRecord',
            ])->whereHas('jobPosting', function ($q) use ($profile) {
                $q->where('employer_profile_id', $profile?->id ?? 0);
            })->find($this->viewingApplicationId)
            : null;

        $interviewingApp = $this->interviewAppId
            ? Application::with(['candidateProfile.user', 'jobPosting'])->find($this->interviewAppId)
            : null;

        $offeringApp = $this->offeringAppId
            ? Application::with(['candidateProfile.user', 'jobPosting'])->find($this->offeringAppId)
            : null;

        $hiringApp = $this->hiringAppId
            ? Application::with(['candidateProfile.user', 'jobPosting'])->find($this->hiringAppId)
            : null;

        $rejectingApp = $this->rejectingAppId
            ? Application::with(['candidateProfile.user', 'jobPosting'])->find($this->rejectingAppId)
            : null;

        $chatApp = $this->selectedChatAppId
            ? Application::with(['candidateProfile.user', 'jobPosting', 'messages.sender'])->find($this->selectedChatAppId)
            : null;

        return view('livewire.employer.applicants', [
            'applications' => $applications,
            'allCounts' => $allCounts,
            'viewing' => $viewing,
            'interviewingApp' => $interviewingApp,
            'offeringApp' => $offeringApp,
            'hiringApp' => $hiringApp,
            'rejectingApp' => $rejectingApp,
            'chatApp' => $chatApp,
            'showChatModal' => $this->showChatModal,
            'showOfferModal' => $this->showOfferModal,
            'employerJobs' => $employerJobs,
            'activeJobFilled' => $activeJobFilled,
            'filledJob' => $filledJob,
        ]);
    }
}
