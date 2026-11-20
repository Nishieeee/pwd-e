<?php

namespace App\Livewire\Candidate;

use App\Models\Application;
use App\Models\ApplicationMessage;
use App\Models\CandidateProfile;
use App\Notifications\AccommodationMessageReceived;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.candidate')]
class ApplicationsTracker extends Component
{
    public string $activeTab = 'all'; // all, active, interviews, archived

    public string $search = '';

    public string $statusFilter = 'all';

    public string $dateFilter = 'all';

    public string $sortBy = 'latest'; // latest, oldest, fit_score

    public ?int $selectedBreakdownAppId = null;

    public bool $showBreakdownModal = false;

    public ?int $selectedOfferAppId = null;

    public bool $showOfferModal = false;

    public ?int $appToWithdrawId = null;

    public bool $showWithdrawConfirmModal = false;

    public ?int $selectedChatAppId = null;

    public bool $showChatModal = false;

    public string $chatMessageText = '';

    public ?string $actionNotification = null;

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
            $app = Application::with('jobPosting.employerProfile')->find($this->appToWithdrawId);
            if ($app) {
                $app->update([
                    'status' => 'closed',
                    'status_subtext' => 'Withdrawn by you · '.Carbon::now()->format('M j'),
                    'updated_at' => Carbon::now(),
                ]);
                $this->actionNotification = "Application for {$app->jobPosting->title} has been withdrawn.";
            }
        }
        $this->closeWithdrawModal();
    }

    public function acceptAndAddToCalendar(int $appId): void
    {
        $app = Application::with('jobPosting.employerProfile')->find($appId);
        if ($app) {
            $this->actionNotification = "Interview with {$app->jobPosting->employerProfile->company_name} accepted and synced to your schedule!";
        }
    }

    public function render()
    {
        $user = Auth::user();
        $candidateProfile = $user?->candidateProfile ?? CandidateProfile::firstOrCreate(['user_id' => $user->id]);

        // Base query for applications belonging to candidate
        $query = Application::where('candidate_profile_id', $candidateProfile->id)
            ->with(['jobPosting.employerProfile', 'jobPosting.accommodations']);

        // Tab counts
        $allCount = Application::where('candidate_profile_id', $candidateProfile->id)->count();
        $activeCount = Application::where('candidate_profile_id', $candidateProfile->id)
            ->whereIn('status', ['applied', 'under_review', 'interview', 'submitted'])
            ->count();
        $interviewsCount = Application::where('candidate_profile_id', $candidateProfile->id)
            ->where(function ($q) {
                $q->where('status', 'interview')->orWhereNotNull('interview_at');
            })
            ->count();
        $archivedCount = Application::where('candidate_profile_id', $candidateProfile->id)
            ->whereIn('status', ['closed', 'rejected', 'hired', 'withdrawn'])
            ->count();

        // Apply Tab Filter
        if ($this->activeTab === 'active') {
            $query->whereIn('status', ['applied', 'under_review', 'interview', 'submitted']);
        } elseif ($this->activeTab === 'interviews') {
            $query->where(function ($q) {
                $q->where('status', 'interview')->orWhereNotNull('interview_at');
            });
        } elseif ($this->activeTab === 'archived') {
            $query->whereIn('status', ['closed', 'rejected', 'hired', 'withdrawn']);
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

        return view('livewire.candidate.applications-tracker', [
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
        ]);
    }
}
