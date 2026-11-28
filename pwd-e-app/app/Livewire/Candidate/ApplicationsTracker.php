<?php

namespace App\Livewire\Candidate;

use App\Livewire\Candidate\Concerns\HandlesCandidateOfferDecisions;
use App\Models\Application;
use App\Models\ApplicationMessage;
use App\Models\CandidateProfile;
use App\Notifications\AccommodationMessageReceived;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.candidate')]
class ApplicationsTracker extends Component
{
    use HandlesCandidateOfferDecisions;
    use WithFileUploads;

    public string $activeTab = 'all'; // all, active, interviews, archived

    public string $search = '';

    public string $statusFilter = 'all';

    public string $dateFilter = 'all';

    public string $sortBy = 'latest'; // latest, oldest, fit_score

    public ?int $selectedBreakdownAppId = null;

    public bool $showBreakdownModal = false;

    public ?int $selectedOfferAppId = null;

    public bool $showOfferModal = false;

    public ?string $declineReason = '';

    public bool $showDeclineModal = false;

    public ?int $decliningAppId = null;

    public ?int $appToWithdrawId = null;

    public bool $showWithdrawConfirmModal = false;

    public ?int $selectedChatAppId = null;

    public bool $showChatModal = false;

    public string $chatMessageText = '';

    public ?string $actionNotification = null;

    public bool $showUploadDocModal = false;

    public ?int $uploadingAppId = null;

    public ?string $uploadingDocId = null;

    public ?string $uploadingDocName = '';

    public $uploadedDocumentFile = null;

    public bool $showPwdUpgradeModal = false;

    public string $upgradePwdIdNumber = '';

    public $upgradePwdIdFile = null;

    public function openUploadDocModal(int $applicationId, string $docId): void
    {
        $candidateProfile = Auth::user()?->candidateProfile;
        $app = Application::where('candidate_profile_id', $candidateProfile?->id)->findOrFail($applicationId);
        $doc = collect($app->requested_documents)->firstWhere('id', $docId);
        if (! $doc) {
            return;
        }

        $this->uploadingAppId = $app->id;
        $this->uploadingDocId = $docId;
        $this->uploadingDocName = $doc['name'] ?? 'Requested Document';
        $this->uploadedDocumentFile = null;
        $this->showUploadDocModal = true;
    }

    public function closeUploadDocModal(): void
    {
        $this->showUploadDocModal = false;
        $this->uploadingAppId = null;
        $this->uploadingDocId = null;
        $this->uploadingDocName = '';
        $this->uploadedDocumentFile = null;
    }

    public function submitRequestedDocument(): void
    {
        if (! $this->uploadingAppId || ! $this->uploadingDocId) {
            return;
        }

        $this->validate([
            'uploadedDocumentFile' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $candidateProfile = Auth::user()?->candidateProfile;
        $app = Application::with('jobPosting.employerProfile.user')
            ->where('candidate_profile_id', $candidateProfile?->id)
            ->findOrFail($this->uploadingAppId);

        $docs = $app->requested_documents ?? [];
        $updatedDocs = [];
        $origName = $this->uploadedDocumentFile->getClientOriginalName();
        $storedPath = $this->uploadedDocumentFile->store('applications/' . $app->id . '/documents', 'private');

        foreach ($docs as $d) {
            if (($d['id'] ?? null) === $this->uploadingDocId) {
                $d['status'] = 'uploaded';
                $d['file_path'] = $storedPath;
                $d['file_name'] = $origName;
                $d['uploaded_at'] = now()->toIso8601String();
            }
            $updatedDocs[] = $d;
        }

        $app->update([
            'requested_documents' => $updatedDocs,
        ]);

        ApplicationMessage::create([
            'application_id' => $app->id,
            'sender_id' => Auth::id(),
            'body' => 'Candidate uploaded requested document: ' . $this->uploadingDocName . ' (' . $origName . ')',
            'type' => 'system',
            'metadata' => [
                'event' => 'document_uploaded',
                'doc_id' => $this->uploadingDocId,
                'doc_name' => $this->uploadingDocName,
                'file_name' => $origName,
            ],
        ]);

        $employerUser = $app->jobPosting?->employerProfile?->user;
        if ($employerUser) {
            $employerUser->notify(
                new \App\Notifications\ApplicationStatusChanged(
                    $app->jobPosting?->title ?? 'Position',
                    $app->status,
                    'Candidate ' . (Auth::user()?->name ?? 'Applicant') . ' uploaded: ' . $this->uploadingDocName
                )
            );
        }

        $docName = $this->uploadingDocName;
        $this->closeUploadDocModal();
        $this->actionNotification = __('Document :doc successfully uploaded.', ['doc' => $docName]);
    }

    public function openPwdUpgradeModal(): void
    {
        $this->upgradePwdIdNumber = '';
        $this->upgradePwdIdFile = null;
        $this->showPwdUpgradeModal = true;
    }

    public function closePwdUpgradeModal(): void
    {
        $this->showPwdUpgradeModal = false;
        $this->upgradePwdIdNumber = '';
        $this->upgradePwdIdFile = null;
    }

    public function submitPwdUpgrade(): void
    {
        $this->validate([
            'upgradePwdIdNumber' => 'required|string|max:50',
            'upgradePwdIdFile' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $user = Auth::user();
        $path = $this->upgradePwdIdFile->store('documents/pwd_ids', 'private');

        $user->update([
            'pwd_id_number' => $this->upgradePwdIdNumber,
            'pwd_id_path' => $path,
            'pwd_document_type' => 'pwd_id',
        ]);

        \App\Models\VerificationRequest::create([
            'user_id' => $user->id,
            'type' => 'pwd_id',
            'id_number' => $this->upgradePwdIdNumber,
            'document_path' => $path,
            'status' => 'pending',
        ]);

        $this->closePwdUpgradeModal();
        $this->actionNotification = __('Your official PWD ID card has been submitted for verification! Your profile is now updated.');
    }

    public function openChatModal(int $appId): void
    {
        $user = Auth::user();
        $candidateProfile = $user?->candidateProfile;

        $app = Application::where('id', $appId)
            ->where('candidate_profile_id', $candidateProfile?->id)
            ->first();

        if (! $app) {
            return;
        }

        $this->selectedChatAppId = $appId;
        $this->showChatModal = true;
        $this->chatMessageText = '';

        // Mark unread messages from employer as read
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

    public function sendChatMessage(?string $chipTopic = null): void
    {
        $user = Auth::user();
        $candidateProfile = $user?->candidateProfile;

        if (! $user || ! $this->selectedChatAppId) {
            return;
        }

        $app = Application::with('jobPosting.employerProfile.user')
            ->where('id', $this->selectedChatAppId)
            ->where('candidate_profile_id', $candidateProfile?->id)
            ->first();

        if (! $app) {
            return;
        }

        $body = trim($this->chatMessageText);

        if ($chipTopic) {
            $body = match ($chipTopic) {
                'wheelchair' => 'Inquiry: Does your workplace or interview venue have step-free entrance and wheelchair ramp access?',
                'fsl' => 'Inquiry: Will there be a Filipino Sign Language (FSL) interpreter provided for the interview, or should I arrange one?',
                'screen_reader' => 'Inquiry: Could interview test tasks and materials be provided in accessible digital format (screen-reader compatible plain text or HTML)?',
                'schedule' => 'Inquiry: Is it possible to request a flexible schedule or scheduled rest breaks during the interview process?',
                default => $chipTopic,
            };
            $type = 'accommodation_inquiry';
            $metadata = ['topic' => $chipTopic, 'resolved' => false];
        } else {
            if (empty($body)) {
                return;
            }
            $type = 'message';
            $metadata = null;
        }

        $msg = ApplicationMessage::create([
            'application_id' => $app->id,
            'sender_id' => $user->id,
            'body' => $body,
            'type' => $type,
            'metadata' => $metadata,
        ]);

        $this->chatMessageText = '';

        // Notify employer
        $employerUser = $app->jobPosting?->employerProfile?->user;
        if ($employerUser) {
            $employerUser->notify(new AccommodationMessageReceived($msg, $app->jobPosting, $user));
        }

        \Flux\Flux::toast(variant: 'success', text: __('Message sent to employer.'));
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function viewFitBreakdown(int $appId): void
    {
        $this->selectedBreakdownAppId = $appId;
        $this->showBreakdownModal = true;
    }

    public function openDefaultFitBreakdown(): void
    {
        $user = Auth::user();
        $candidateProfile = $user?->candidateProfile;

        if (! $candidateProfile) {
            return;
        }

        $app = Application::where('candidate_profile_id', $candidateProfile->id)
            ->orderByDesc('updated_at')
            ->first();

        if ($app) {
            $this->viewFitBreakdown($app->id);
        }
    }

    public function closeBreakdownModal(): void
    {
        $this->showBreakdownModal = false;
        $this->selectedBreakdownAppId = null;
    }

    public function viewOffer(int $appId): void
    {
        $this->selectedOfferAppId = $appId;
        $this->showOfferModal = true;
    }

    public function closeOfferModal(): void
    {
        $this->showOfferModal = false;
        $this->selectedOfferAppId = null;
    }

    public function openDeclineModal(int $appId): void
    {
        $this->decliningAppId = $appId;
        $this->declineReason = '';
        $this->showDeclineModal = true;
    }

    public function closeDeclineModal(): void
    {
        $this->decliningAppId = null;
        $this->declineReason = '';
        $this->showDeclineModal = false;
    }

    public function confirmDeclineOffer(): void
    {
        if (! $this->decliningAppId) {
            return;
        }

        $user = Auth::user();
        $candidateProfile = $user?->candidateProfile;
        $app = Application::where('id', $this->decliningAppId)
            ->where('candidate_profile_id', $candidateProfile?->id)
            ->with(['jobPosting.employerProfile'])
            ->first();

        if (! $app || ! $app->canTransitionTo(Application::STATUS_OFFER_DECLINED)) {
            $this->actionNotification = 'Unable to decline offer in current state.';
            $this->closeDeclineModal();
            return;
        }

        $reason = trim($this->declineReason) ?: 'Declined by candidate';

        $app->update([
            'status' => Application::STATUS_OFFER_DECLINED,
            'offer_declined_at' => now(),
            'offer_decline_reason' => $reason,
            'status_subtext' => Application::computeStatusSubtext(Application::STATUS_OFFER_DECLINED, now()),
        ]);

        ApplicationMessage::create([
            'application_id' => $app->id,
            'sender_id' => $user->id,
            'type' => 'system',
            'body' => 'Offer Declined: Candidate declined the job offer. Reason: ' . $reason,
            'metadata' => [
                'event' => 'offer_declined',
                'declined_at' => now()->toIso8601String(),
            ],
        ]);

        $this->closeDeclineModal();
        $this->closeOfferModal();
        $this->actionNotification = 'You have declined the employment offer.';
    }

    public function promptWithdraw(int $appId): void
    {
        $this->appToWithdrawId = $appId;
        $this->showWithdrawConfirmModal = true;
    }

    public function closeWithdrawModal(): void
    {
        $this->showWithdrawConfirmModal = false;
        $this->appToWithdrawId = null;
    }

    public function confirmWithdraw(): void
    {
        if ($this->appToWithdrawId) {
            $user = Auth::user();
            $candidateProfile = $user?->candidateProfile;
            $app = Application::where('candidate_profile_id', $candidateProfile?->id)
                ->with('jobPosting.employerProfile')
                ->find($this->appToWithdrawId);

            if ($app) {
                if ($app->isTerminal()) {
                    $this->actionNotification = "Cannot withdraw application that is already in {$app->status} status.";
                    $this->closeWithdrawModal();
                    return;
                }

                $app->update([
                    'status' => 'closed',
                    'status_subtext' => 'Withdrawn by you · '.Carbon::now()->format('M j'),
                    'updated_at' => Carbon::now(),
                ]);

                \App\Models\WorkforceRecord::where('application_id', $app->id)
                    ->whereNull('ended_at')
                    ->update(['ended_at' => Carbon::now()]);

                $this->actionNotification = "Application for {$app->jobPosting->title} has been withdrawn.";
            }
        }
        $this->closeWithdrawModal();
    }

    public function acceptAndAddToCalendar(int $appId): void
    {
        $user = Auth::user();
        $candidateProfile = $user?->candidateProfile;
        if (! $candidateProfile) {
            return;
        }

        $app = Application::where('id', $appId)
            ->where('candidate_profile_id', $candidateProfile->id)
            ->with('jobPosting.employerProfile')
            ->first();

        if (! $app || $app->status !== 'interview') {
            return;
        }

        $app->update([
            'interview_accepted_at' => now(),
            'status_subtext' => 'Interview accepted · Scheduled for ' . ($app->interview_at ? $app->interview_at->format('M j, g:i A') : 'upcoming session'),
        ]);

        ApplicationMessage::create([
            'application_id' => $app->id,
            'sender_id' => $user->id,
            'type' => 'system',
            'body' => 'Interview Confirmed: Candidate accepted the scheduled interview for ' . ($app->interview_at ? $app->interview_at->format('F j, Y \a\t g:i A') : 'upcoming session') . '.',
            'metadata' => [
                'event' => 'interview_accepted',
                'accepted_at' => now()->toIso8601String(),
            ],
        ]);

        $company = $app->jobPosting?->employerProfile?->company_name ?? 'the employer';
        $this->actionNotification = "Interview with {$company} accepted and confirmed on your schedule!";
    }

    public function downloadIcs(int $appId)
    {
        $user = Auth::user();
        $candidateProfile = $user?->candidateProfile;
        if (! $candidateProfile) {
            return;
        }

        $app = Application::where('id', $appId)
            ->where('candidate_profile_id', $candidateProfile->id)
            ->with('jobPosting.employerProfile')
            ->first();

        if (! $app) {
            return;
        }

        $start = $app->interview_at ?? now()->addDay();
        $end = (clone $start)->addHour();
        $summary = "Interview: " . ($app->jobPosting?->title ?? 'Position') . " with " . ($app->jobPosting?->employerProfile?->company_name ?? 'Company');
        $description = "Accessible Interview for " . ($app->jobPosting?->title ?? 'Position') . "\\nMeeting URL: " . ($app->interview_url ?? '') . "\\nAccommodations: " . ($app->interview_accommodations ?? '');
        $location = $app->interview_url ?: ($app->interview_location ?: 'Online');

        $ics = "BEGIN:VCALENDAR\r\n"
            . "VERSION:2.0\r\n"
            . "PRODID:-//PWD-E Ecosystem//EN\r\n"
            . "CALSCALE:GREGORIAN\r\n"
            . "METHOD:PUBLISH\r\n"
            . "BEGIN:VEVENT\r\n"
            . "UID:pwde-interview-{$app->id}@pwd-e.local\r\n"
            . "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n"
            . "DTSTART:" . $start->format('Ymd\THis\Z') . "\r\n"
            . "DTEND:" . $end->format('Ymd\THis\Z') . "\r\n"
            . "SUMMARY:" . $summary . "\r\n"
            . "DESCRIPTION:" . $description . "\r\n"
            . "LOCATION:" . $location . "\r\n"
            . "STATUS:CONFIRMED\r\n"
            . "END:VEVENT\r\n"
            . "END:VCALENDAR\r\n";

        return response()->streamDownload(function () use ($ics) {
            echo $ics;
        }, "interview-{$app->id}.ics", [
            'Content-Type' => 'text/calendar; charset=utf-8',
        ]);
    }

    public function toggleChecklistItem(int $appId, string $itemKey): void
    {
        $user = Auth::user();
        $candidateProfile = $user?->candidateProfile;
        if (! $candidateProfile) {
            return;
        }

        $app = Application::where('id', $appId)
            ->where('candidate_profile_id', $candidateProfile->id)
            ->first();

        if (! $app || $app->status !== 'interview') {
            return;
        }

        if (! array_key_exists($itemKey, Application::CHECKLIST_ITEMS)) {
            return;
        }

        $checklist = $app->interview_checklist ?? [];
        $wasChecked = ! empty($checklist[$itemKey]['checked']);
        $nowChecked = ! $wasChecked;

        $checklist[$itemKey] = [
            'checked' => $nowChecked,
            'checked_at' => $nowChecked ? now()->toIso8601String() : null,
        ];

        $app->update(['interview_checklist' => $checklist]);

        if ($app->fresh()->isInterviewChecklistComplete()) {
            ApplicationMessage::create([
                'application_id' => $app->id,
                'sender_id' => $user->id,
                'body' => 'Assistive Tech Verified: Candidate completed the pre-interview readiness checklist.',
                'type' => 'system',
                'metadata' => [
                    'event' => 'checklist_completed',
                    'completed_at' => now()->toIso8601String(),
                ],
            ]);
            $this->actionNotification = 'All 4 assistive tech readiness items verified! The employer has been notified.';
        } else {
            $label = Application::CHECKLIST_ITEMS[$itemKey]['label'] ?? 'Item';
            $this->actionNotification = $nowChecked ? "{$label} marked as ready." : "{$label} unchecked.";
        }
    }

    public function markChecklistReady(int $appId): void
    {
        $user = Auth::user();
        $candidateProfile = $user?->candidateProfile;
        if (! $candidateProfile) {
            return;
        }

        $app = Application::where('id', $appId)
            ->where('candidate_profile_id', $candidateProfile->id)
            ->first();

        if (! $app || $app->status !== 'interview') {
            return;
        }

        $checklist = [];
        $now = now()->toIso8601String();
        foreach (Application::CHECKLIST_ITEMS as $key => $meta) {
            $checklist[$key] = [
                'checked' => true,
                'checked_at' => $now,
            ];
        }

        $app->update(['interview_checklist' => $checklist]);

        ApplicationMessage::create([
            'application_id' => $app->id,
            'sender_id' => $user->id,
            'body' => 'Assistive Tech Verified: Candidate completed the pre-interview readiness checklist.',
            'type' => 'system',
            'metadata' => [
                'event' => 'checklist_completed',
                'completed_at' => $now,
            ],
        ]);

        $this->actionNotification = 'All pre-interview assistive tech items confirmed ready! Employer notified.';
    }

    public function render()
    {
        $user = Auth::user();
        $candidateProfile = $user?->candidateProfile ?? CandidateProfile::firstOrCreate(['user_id' => $user->id]);

        // Base query for applications belonging to candidate
        $query = Application::where('candidate_profile_id', $candidateProfile->id)
            ->with(['jobPosting.employerProfile', 'jobPosting.accommodations', 'workforceRecord']);

        // Tab counts
        $allCount = Application::where('candidate_profile_id', $candidateProfile->id)->count();
        $activeCount = Application::where('candidate_profile_id', $candidateProfile->id)
            ->whereIn('status', ['applied', 'under_review', 'shortlisted', 'interview', 'offered', 'submitted'])
            ->count();
        $interviewsCount = Application::where('candidate_profile_id', $candidateProfile->id)
            ->where(function ($q) {
                $q->where('status', 'interview')->orWhereNotNull('interview_at');
            })
            ->count();
        $archivedCount = Application::where('candidate_profile_id', $candidateProfile->id)
            ->whereIn('status', ['closed', 'rejected', 'hired', 'withdrawn', 'offer_declined'])
            ->count();

        // Apply Tab Filter
        if ($this->activeTab === 'active') {
            $query->whereIn('status', ['applied', 'under_review', 'shortlisted', 'interview', 'offered', 'submitted']);
        } elseif ($this->activeTab === 'interviews') {
            $query->where(function ($q) {
                $q->where('status', 'interview')->orWhereNotNull('interview_at');
            });
        } elseif ($this->activeTab === 'archived') {
            $query->whereIn('status', ['closed', 'rejected', 'hired', 'withdrawn', 'offer_declined']);
        }

        // Search Filter
        if (! empty($this->search)) {
            $term = '%'.$this->search.'%';
            $query->where(function ($q) use ($term) {
                $q->whereHas('jobPosting', function ($jp) use ($term) {
                    $jp->where('title', 'like', $term)
                        ->orWhereHas('employerProfile', fn ($emp) => $emp->where('company_name', 'like', $term));
                });
            });
        }

        // Status Filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Date Filter
        if ($this->dateFilter === '30') {
            $query->where('applied_at', '>=', Carbon::now()->subDays(30));
        } elseif ($this->dateFilter === '60') {
            $query->where('applied_at', '>=', Carbon::now()->subDays(60));
        } elseif ($this->dateFilter === '90') {
            $query->where('applied_at', '>=', Carbon::now()->subDays(90));
        }

        // Sorting
        if ($this->sortBy === 'oldest') {
            $query->orderBy('updated_at', 'asc');
        } elseif ($this->sortBy === 'fit_score') {
            $query->orderByDesc('fit_score_at_apply');
        } else {
            // Default latest update
            $query->orderByDesc('updated_at');
        }

        $applications = $query->get();

        $emptyStateMessage = match ($this->activeTab) {
            'interviews' => 'You have no upcoming interviews scheduled right now.',
            'active' => 'You have no active job applications right now.',
            'archived' => 'You have no past or archived applications yet.',
            default => (! empty($this->search) || $this->statusFilter !== 'all' || $this->dateFilter !== 'all')
                ? 'No applications match your current search or filter criteria.'
                : 'You have not submitted any job applications yet.',
        };

        $breakdownApp = $this->selectedBreakdownAppId
            ? Application::with(['jobPosting.employerProfile', 'jobPosting.accommodations'])->find($this->selectedBreakdownAppId)
            : null;

        $offerApp = $this->selectedOfferAppId
            ? Application::with(['jobPosting.employerProfile'])->find($this->selectedOfferAppId)
            : null;

        $chatApp = $this->selectedChatAppId
            ? Application::with(['jobPosting.employerProfile', 'messages.sender'])->find($this->selectedChatAppId)
            : null;

        $offerModalsData = $this->buildOfferModalsViewData($candidateProfile?->id);

        return view('livewire.candidate.applications-tracker', array_merge([
            'user' => $user,
            'candidateProfile' => $candidateProfile,
            'applications' => $applications,
            'emptyStateMessage' => $emptyStateMessage,
            'allCount' => $allCount,
            'activeCount' => $activeCount,
            'interviewsCount' => $interviewsCount,
            'archivedCount' => $archivedCount,
            'breakdownApp' => $breakdownApp,
            'offerApp' => $offerApp,
            'chatApp' => $chatApp,
            'showChatModal' => $this->showChatModal,
            'activeTab' => $this->activeTab,
            'search' => $this->search,
            'statusFilter' => $this->statusFilter,
            'dateFilter' => $this->dateFilter,
            'sortBy' => $this->sortBy,
            'showBreakdownModal' => $this->showBreakdownModal,
            'showOfferModal' => $this->showOfferModal,
            'showWithdrawConfirmModal' => $this->showWithdrawConfirmModal,
            'actionNotification' => $this->actionNotification,
        ], $offerModalsData));
    }
}
