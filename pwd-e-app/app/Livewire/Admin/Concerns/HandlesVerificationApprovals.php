<?php

namespace App\Livewire\Admin\Concerns;

use App\Models\VerificationRequest;
use App\Notifications\VerificationStatusUpdated;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait HandlesVerificationApprovals
{
    public array $selectedIds = [];

    public bool $selectAll = false;

    public string $category = 'all';

    public ?string $bulkRejectReason = null;

    public bool $showBulkRejectModal = false;

    public bool $showBulkApproveModal = false;

    public ?int $singleReviewingId = null;

    public ?string $singleReviewNote = null;

    public function setCategory(string $category): void
    {
        $this->category = in_array($category, ['all', 'pwd', 'employer'], true) ? $category : 'all';
        $this->selectedIds = [];
        $this->selectAll = false;

        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }

    public function updatedSelectAll(bool $value): void
    {
        if ($value) {
            $this->selectedIds = array_map('intval', $this->getSelectableRequestIds());
        } else {
            $this->selectedIds = [];
        }
    }

    public function toggleSelect(int $id): void
    {
        if (in_array($id, $this->selectedIds, true)) {
            $this->selectedIds = array_values(array_filter($this->selectedIds, fn ($item) => (int) $item !== $id));
        } else {
            $this->selectedIds[] = $id;
        }

        $selectable = array_map('intval', $this->getSelectableRequestIds());
        $this->selectAll = ! empty($selectable) && count(array_intersect($selectable, $this->selectedIds)) === count($selectable);
    }

    public function openBulkApprove(): void
    {
        if (empty($this->selectedIds)) {
            Flux::toast(variant: 'warning', text: __('Please select at least one verification request to approve.'));

            return;
        }

        $this->showBulkApproveModal = true;
    }

    public function closeBulkApprove(): void
    {
        $this->showBulkApproveModal = false;
    }

    public function getSelectedRequestsProperty()
    {
        if (empty($this->selectedIds)) {
            return collect();
        }

        return VerificationRequest::with(['user.candidateProfile', 'user.employerProfile'])
            ->whereIn('id', $this->selectedIds)
            ->get();
    }

    public function bulkApprove(): void
    {
        $this->showBulkApproveModal = false;

        if (empty($this->selectedIds)) {
            Flux::toast(variant: 'warning', text: __('Please select at least one verification request to approve.'));

            return;
        }

        $requests = VerificationRequest::with('user')
            ->whereIn('id', $this->selectedIds)
            ->whereIn('status', ['pending', 'needs_more_docs'])
            ->get();

        if ($requests->isEmpty()) {
            Flux::toast(variant: 'warning', text: __('No pending verification requests selected.'));
            $this->selectedIds = [];
            $this->selectAll = false;

            return;
        }

        $admin = Auth::user();
        $count = 0;

        DB::transaction(function () use ($requests, $admin, &$count) {
            foreach ($requests as $vr) {
                $vr->update([
                    'status' => 'approved',
                    'rejection_reason' => null,
                    'reviewed_by' => $admin?->id,
                    'reviewed_at' => now(),
                ]);

                if ($vr->user) {
                    $vr->user->update([
                        'status' => 'active',
                        'verified_at' => now(),
                    ]);
                    $vr->user->notify(new VerificationStatusUpdated('approved'));
                }

                activity()
                    ->causedBy($admin)
                    ->performedOn($vr)
                    ->event('approved')
                    ->log('Bulk approved verification request #'.$vr->id);

                $count++;
            }
        });

        $this->selectedIds = [];
        $this->selectAll = false;

        Flux::toast(variant: 'success', text: __("Successfully approved {$count} verification request(s)."));
    }

    public function openBulkReject(): void
    {
        if (empty($this->selectedIds)) {
            Flux::toast(variant: 'warning', text: __('Please select at least one verification request to reject.'));

            return;
        }

        $this->bulkRejectReason = '';
        $this->showBulkRejectModal = true;
    }

    public function closeBulkReject(): void
    {
        $this->showBulkRejectModal = false;
        $this->bulkRejectReason = null;
    }

    public function bulkReject(): void
    {
        if (empty($this->selectedIds)) {
            $this->closeBulkReject();

            return;
        }

        $reason = trim($this->bulkRejectReason ?? '') ?: 'Document verification failed to meet platform requirements.';

        $requests = VerificationRequest::with('user')
            ->whereIn('id', $this->selectedIds)
            ->whereIn('status', ['pending', 'needs_more_docs'])
            ->get();

        $admin = Auth::user();
        $count = 0;

        DB::transaction(function () use ($requests, $admin, $reason, &$count) {
            foreach ($requests as $vr) {
                $vr->update([
                    'status' => 'rejected',
                    'rejection_reason' => $reason,
                    'reviewed_by' => $admin?->id,
                    'reviewed_at' => now(),
                ]);

                if ($vr->user) {
                    $vr->user->update([
                        'status' => 'rejected',
                    ]);
                    $vr->user->notify(new VerificationStatusUpdated('rejected', $reason));
                }

                activity()
                    ->causedBy($admin)
                    ->performedOn($vr)
                    ->event('rejected')
                    ->log('Bulk rejected verification request #'.$vr->id.': '.$reason);

                $count++;
            }
        });

        $this->selectedIds = [];
        $this->selectAll = false;
        $this->closeBulkReject();

        Flux::toast(variant: 'success', text: __("Successfully rejected {$count} verification request(s)."));
    }

    public function openSingleReview(int $id): void
    {
        $this->singleReviewingId = $id;
        $this->singleReviewNote = null;
    }

    public function closeSingleReview(): void
    {
        $this->singleReviewingId = null;
        $this->singleReviewNote = null;
    }

    public function approveSingle(int $id): void
    {
        $vr = VerificationRequest::with('user')->findOrFail($id);
        $admin = Auth::user();

        $vr->update([
            'status' => 'approved',
            'rejection_reason' => null,
            'reviewed_by' => $admin?->id,
            'reviewed_at' => now(),
        ]);

        if ($vr->user) {
            $vr->user->update([
                'status' => 'active',
                'verified_at' => now(),
            ]);
            $vr->user->notify(new VerificationStatusUpdated('approved'));
        }

        activity()
            ->causedBy($admin)
            ->performedOn($vr)
            ->event('approved')
            ->log('Approved verification request #'.$vr->id);

        $this->selectedIds = array_values(array_filter($this->selectedIds, fn ($item) => (int) $item !== $id));
        Flux::toast(variant: 'success', text: __('Verification approved.'));
        $this->closeSingleReview();
    }

    public function rejectSingle(int $id): void
    {
        $vr = VerificationRequest::with('user')->findOrFail($id);
        $admin = Auth::user();
        $reason = trim($this->singleReviewNote ?? '') ?: 'Document verification failed to meet platform requirements.';

        $vr->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_by' => $admin?->id,
            'reviewed_at' => now(),
        ]);

        if ($vr->user) {
            $vr->user->update([
                'status' => 'rejected',
            ]);
            $vr->user->notify(new VerificationStatusUpdated('rejected', $reason));
        }

        activity()
            ->causedBy($admin)
            ->performedOn($vr)
            ->event('rejected')
            ->log('Rejected verification request #'.$vr->id.': '.$reason);

        $this->selectedIds = array_values(array_filter($this->selectedIds, fn ($item) => (int) $item !== $id));
        Flux::toast(variant: 'success', text: __('Verification rejected.'));
        $this->closeSingleReview();
    }
}
