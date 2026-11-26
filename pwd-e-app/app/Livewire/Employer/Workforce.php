<?php

namespace App\Livewire\Employer;

use App\Models\WorkforceRecord;
use App\Notifications\EmploymentSeparationNotice;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Workforce & Tax Incentives')]
#[Layout('layouts.employer')]
class Workforce extends Component
{
    public string $workforceTab = 'active'; // 'active', 'separated', 'disputed'

    public bool $showEndEmploymentModal = false;

    public ?int $endingRecordId = null;

    public string $endedAt = '';

    public string $endedReason = 'resigned';

    public ?string $endedNotes = '';

    public ?int $viewingDisputeId = null;

    public bool $showConfirmModal = false;

    public ?string $confirmAction = null;

    public ?int $confirmRecordId = null;

    public string $confirmTitle = '';

    public string $confirmDescription = '';

    public string $confirmButtonText = '';

    public string $confirmButtonClass = 'bg-primary-dark';

    public function openConfirmModal(string $action, int $recordId, string $title, string $description, string $buttonText = 'Confirm', string $buttonClass = 'bg-primary-dark'): void
    {
        $this->confirmAction = $action;
        $this->confirmRecordId = $recordId;
        $this->confirmTitle = $title;
        $this->confirmDescription = $description;
        $this->confirmButtonText = $buttonText;
        $this->confirmButtonClass = $buttonClass;
        $this->showConfirmModal = true;
    }

    public function closeConfirmModal(): void
    {
        $this->showConfirmModal = false;
        $this->confirmAction = null;
        $this->confirmRecordId = null;
    }

    public function executeConfirmedAction(): void
    {
        if (! $this->confirmRecordId || ! $this->confirmAction) {
            $this->closeConfirmModal();
            return;
        }

        $recordId = $this->confirmRecordId;
        $action = $this->confirmAction;
        $this->closeConfirmModal();

        match ($action) {
            'cancelNotice' => $this->cancelNotice($recordId),
            'reactivateRecord' => $this->reactivateRecord($recordId),
            'removeRecord' => $this->removeRecord($recordId),
            default => null,
        };
    }

    public function mount(): void
    {
        $user = Auth::user();
        if (! $user || ! $user->isEmployer()) {
            abort(403);
        }
    }

    public function setWorkforceTab(string $tab): void
    {
        $this->workforceTab = in_array($tab, ['active', 'separated', 'disputed']) ? $tab : 'active';
    }

    public function openEndEmploymentModal(int $id): void
    {
        $record = WorkforceRecord::where('employer_profile_id', Auth::user()->employerProfile?->id)->findOrFail($id);
        $this->endingRecordId = $record->id;
        $this->endedAt = now()->format('Y-m-d');
        $this->endedReason = 'resigned';
        $this->endedNotes = '';
        $this->showEndEmploymentModal = true;
    }

    public function closeEndEmploymentModal(): void
    {
        $this->showEndEmploymentModal = false;
        $this->endingRecordId = null;
        $this->resetValidation();
    }

    public function confirmEndEmployment(): void
    {
        $this->validate([
            'endedAt' => ['required', 'date'],
            'endedReason' => ['required', 'string', 'in:resigned,relieved,contract_ended,other'],
            'endedNotes' => ['nullable', 'string', 'max:500'],
        ]);

        $record = WorkforceRecord::with(['candidateProfile.user', 'application.jobPosting'])
            ->where('employer_profile_id', Auth::user()->employerProfile?->id)
            ->findOrFail($this->endingRecordId);

        $record->update([
            'ended_at' => Carbon::parse($this->endedAt),
            'ended_reason' => $this->endedReason,
            'ended_notes' => $this->endedNotes,
            'separation_status' => 'pending_review',
            'separation_notice_at' => now(),
            'candidate_response_at' => null,
            'candidate_dispute_reason' => null,
        ]);

        // Send statutory notification to candidate
        if ($record->candidateProfile?->user) {
            $companyName = Auth::user()->employerProfile?->company_name ?? Auth::user()->name;
            $positionTitle = $record->application?->jobPosting?->title ?? 'Position';
            $record->candidateProfile->user->notify(
                new EmploymentSeparationNotice(
                    companyName: $companyName,
                    positionTitle: $positionTitle,
                    reason: $this->endedReason,
                    effectiveDate: Carbon::parse($this->endedAt)->format('M d, Y'),
                    notes: $this->endedNotes,
                    workforceRecordId: $record->id,
                )
            );
        }

        if (function_exists('activity')) {
            activity('workforce')
                ->performedOn($record)
                ->causedBy(Auth::user())
                ->withProperties([
                    'candidate_profile_id' => $record->candidate_profile_id,
                    'ended_at' => $this->endedAt,
                    'ended_reason' => $this->endedReason,
                    'separation_status' => 'pending_review',
                ])
                ->log("Notice of employment separation issued ({$this->endedReason})");
        }

        $this->dispatch('toast', message: __('Notice of Separation issued. The candidate has been notified for due process review.'));
        $this->closeEndEmploymentModal();
    }

    public function cancelNotice(int $id): void
    {
        $record = WorkforceRecord::where('employer_profile_id', Auth::user()->employerProfile?->id)->findOrFail($id);

        $record->update([
            'ended_at' => null,
            'ended_reason' => null,
            'ended_notes' => null,
            'separation_status' => 'active',
            'separation_notice_at' => null,
            'candidate_response_at' => null,
            'candidate_dispute_reason' => null,
        ]);

        if (function_exists('activity')) {
            activity('workforce')
                ->performedOn($record)
                ->causedBy(Auth::user())
                ->withProperties(['candidate_profile_id' => $record->candidate_profile_id])
                ->log('Notice of separation cancelled by employer');
        }

        $this->dispatch('toast', message: __('Notice of separation cancelled. Employee remains active.'));
    }

    public function viewDispute(int $id): void
    {
        $this->viewingDisputeId = $id;
    }

    public function closeDisputeModal(): void
    {
        $this->viewingDisputeId = null;
    }

    public function reactivateRecord(int $id): void
    {
        $record = WorkforceRecord::where('employer_profile_id', Auth::user()->employerProfile?->id)->findOrFail($id);

        $record->update([
            'ended_at' => null,
            'ended_reason' => null,
            'ended_notes' => null,
            'separation_status' => 'active',
            'separation_notice_at' => null,
            'candidate_response_at' => null,
            'candidate_dispute_reason' => null,
        ]);

        if (function_exists('activity')) {
            activity('workforce')
                ->performedOn($record)
                ->causedBy(Auth::user())
                ->withProperties(['candidate_profile_id' => $record->candidate_profile_id])
                ->log('Workforce record reactivated to active status');
        }

        $this->dispatch('toast', message: __('Record reactivated to Active Workforce.'));
    }

    public function removeRecord(int $id): void
    {
        $record = WorkforceRecord::where('employer_profile_id', Auth::user()->employerProfile?->id)->findOrFail($id);

        if (function_exists('activity')) {
            activity('workforce')
                ->performedOn($record)
                ->causedBy(Auth::user())
                ->withProperties([
                    'candidate_profile_id' => $record->candidate_profile_id,
                    'annual_wage' => $record->annual_wage,
                ])
                ->log('Workforce record removed');
        }

        $record->delete();
        $this->dispatch('toast', message: __('Record removed.'));
    }

    public function syncPwdVerification(int $id): void
    {
        $record = WorkforceRecord::with('candidateProfile.user')
            ->where('employer_profile_id', Auth::user()->employerProfile?->id)
            ->findOrFail($id);

        $user = $record->candidateProfile?->user;
        $isVerified = (bool) ($user && $user->isVerified() && ! empty($user->pwd_id_number));

        $record->update(['pwd_verified' => $isVerified]);

        if (function_exists('activity')) {
            activity('workforce')
                ->performedOn($record)
                ->causedBy(Auth::user())
                ->withProperties(['pwd_verified' => $isVerified])
                ->log('Workforce PWD verification status synchronized');
        }

        $this->dispatch('toast', message: $isVerified
            ? __('PWD status verified against registry.')
            : __('Candidate does not have verified PWD credentials.'));
    }

    public function togglePwdVerified(int $id): void
    {
        $this->syncPwdVerification($id);
    }

    public function render(): View
    {
        $profile = Auth::user()->employerProfile;
        $baseQuery = WorkforceRecord::with(['candidateProfile.user', 'application.jobPosting'])
            ->where('employer_profile_id', $profile?->id);

        // Active/In-Review includes active, pending_review, or disputed
        $activeQuery = (clone $baseQuery)->where(function ($q) {
            $q->whereNull('ended_at')
              ->orWhereIn('separation_status', ['active', 'pending_review', 'disputed']);
        });

        // Completed/Acknowledged separations
        $separatedQuery = (clone $baseQuery)->where('separation_status', 'acknowledged');

        // Disputed query
        $disputedQuery = (clone $baseQuery)->where('separation_status', 'disputed');

        $activeCount = $activeQuery->count();
        $separatedCount = $separatedQuery->count();
        $disputedCount = $disputedQuery->count();

        $displayRecords = match ($this->workforceTab) {
            'separated' => $separatedQuery->latest('ended_at')->get(),
            'disputed' => $disputedQuery->latest('candidate_response_at')->get(),
            default => $activeQuery->latest('hired_at')->get(),
        };

        // For tax deduction, only verified PWDs actively working count
        $activeEmployeesList = (clone $baseQuery)->where('separation_status', 'active')->whereNull('ended_at')->get();
        $totalEmployees = $activeEmployeesList->count();
        $pwdCount = $activeEmployeesList->where('pwd_verified', true)->count();
        $pwdPercent = $totalEmployees > 0 ? round(($pwdCount / $totalEmployees) * 100, 1) : 0;

        // BIR R.A. 10524: official PWD ID required for tax deduction; medical certificate qualifies for employment
        $taxEligibleList = $activeEmployeesList->filter(function ($rec) {
            $u = $rec->candidateProfile?->user;
            return $rec->pwd_verified && (in_array($u?->pwd_document_type, ['pwd_id', 'both'], true) || empty($u?->pwd_document_type));
        });
        $medCertOnlyCount = $activeEmployeesList->filter(function ($rec) {
            $u = $rec->candidateProfile?->user;
            return $rec->pwd_verified && $u?->pwd_document_type === 'medical_certificate';
        })->count();

        $totalWages = $taxEligibleList->sum('annual_wage');
        $deduction = round($totalWages * 0.25, 2);

        $endingRecord = $this->endingRecordId
            ? WorkforceRecord::with('candidateProfile.user')->find($this->endingRecordId)
            : null;

        $viewingDisputeRecord = $this->viewingDisputeId
            ? WorkforceRecord::with(['candidateProfile.user', 'application.jobPosting'])->find($this->viewingDisputeId)
            : null;

        return view('livewire.employer.workforce', [
            'records' => $displayRecords,
            'totalEmployees' => $totalEmployees,
            'pwdCount' => $pwdCount,
            'pwdPercent' => $pwdPercent,
            'totalWages' => $totalWages,
            'deduction' => $deduction,
            'medCertOnlyCount' => $medCertOnlyCount,
            'activeCount' => $activeCount,
            'separatedCount' => $separatedCount,
            'disputedCount' => $disputedCount,
            'endingRecord' => $endingRecord,
            'viewingDisputeRecord' => $viewingDisputeRecord,
        ]);
    }
}
