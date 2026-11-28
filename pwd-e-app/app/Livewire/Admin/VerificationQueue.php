<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\Concerns\HandlesVerificationApprovals;
use App\Models\VerificationRequest;
use App\Notifications\VerificationStatusUpdated;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Verification Queue')]
class VerificationQueue extends Component
{
    use HandlesVerificationApprovals;
    use WithPagination;

    #[Url(as: 'status')]
    public string $status = 'pending';

    #[Url(as: 'category')]
    public string $category = 'all';

    public ?string $reviewNote = null;

    public ?int $reviewingId = null;

    /** @var array<int, array<string, bool>> */
    public array $checklists = [];

    public function mount(): void
    {
        $user = Auth::user();
        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function setStatusFilter(string $status): void
    {
        $this->status = $status;
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function getSelectableRequestIds(): array
    {
        $query = VerificationRequest::query();
        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        } else {
            $query->whereIn('status', ['pending', 'needs_more_docs']);
        }

        if ($this->category === 'pwd') {
            $query->whereHas('user', fn ($q) => $q->where('role', 'candidate'));
        } elseif ($this->category === 'employer') {
            $query->whereHas('user', fn ($q) => $q->where('role', 'employer'));
        }

        return $query->pluck('id')->all();
    }

    public function openReview(int $id): void
    {
        $this->reviewingId = $id;
        $this->reviewNote = null;
    }

    public function closeReview(): void
    {
        $this->reviewingId = null;
        $this->reviewNote = null;
    }

    public function checkAllCriteria(int $id): void
    {
        $vr = VerificationRequest::findOrFail($id);
        $map = [];
        foreach ($vr->requiredChecklistKeys() as $key) {
            $map[$key] = true;
        }
        $this->checklists[$id] = $map;
        $this->resetErrorBag('checklist.'.$id);
    }

    public function approve(int $id): void
    {
        $vr = VerificationRequest::with('user')->findOrFail($id);
        $checkedMap = $this->checklists[$vr->id] ?? [];

        if (! $vr->hasCompleteChecklist($checkedMap)) {
            $this->addError('checklist.'.$vr->id, __('Please verify and check all required credential criteria before approving.'));
            Flux::toast(variant: 'warning', text: __('Complete the verification checklist before approving.'));

            return;
        }

        $checkedKeys = array_values(array_filter(
            $vr->requiredChecklistKeys(),
            fn (string $key) => ! empty($checkedMap[$key])
        ));

        $vr->update([
            'status' => 'approved',
            'rejection_reason' => null,
            'verification_checklist' => [
                'checked_keys' => $checkedKeys,
                'completed_at' => now()->toIso8601String(),
                'reviewer_id' => Auth::id(),
            ],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $vr->user->update([
            'status' => 'active',
            'verified_at' => now(),
        ]);

        unset($this->checklists[$vr->id]);

        $vr->user->notify(new VerificationStatusUpdated('approved'));

        activity()
            ->causedBy(Auth::user())
            ->performedOn($vr)
            ->event('approved')
            ->log('Approved verification request #'.$vr->id);

        Flux::toast(variant: 'success', text: __('Verification approved.'));
        $this->closeReview();
    }

    public function reject(int $id): void
    {
        $vr = VerificationRequest::with('user')->findOrFail($id);
        $vr->update([
            'status' => 'rejected',
            'rejection_reason' => $this->reviewNote,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $vr->user->update([
            'status' => 'rejected',
        ]);

        $vr->user->notify(new VerificationStatusUpdated('rejected', $this->reviewNote));

        activity()
            ->causedBy(Auth::user())
            ->performedOn($vr)
            ->event('rejected')
            ->log('Rejected verification request #'.$vr->id.($this->reviewNote ? ': '.$this->reviewNote : ''));

        Flux::toast(variant: 'success', text: __('Verification rejected.'));
        $this->closeReview();
    }

    public function requestMoreDocs(int $id): void
    {
        $vr = VerificationRequest::with('user')->findOrFail($id);
        $note = trim($this->reviewNote ?? '') ?: 'Please upload a clearer copy of your PWD ID card or Clinical Medical Certificate.';

        $vr->update([
            'status' => 'needs_more_docs',
            'rejection_reason' => $note,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $vr->user->notify(new VerificationStatusUpdated('needs_more_docs', $note));

        activity()
            ->causedBy(Auth::user())
            ->performedOn($vr)
            ->event('needs_more_docs')
            ->log('Requested more documents for verification request #'.$vr->id.': '.$note);

        Flux::toast(variant: 'success', text: __('Additional documents requested.'));
        $this->closeReview();
    }

    public function render(): View
    {
        $baseQuery = VerificationRequest::with(['user.candidateProfile', 'user.employerProfile'])->latest();

        if ($this->status !== 'all') {
            $baseQuery->where('status', $this->status);
        }

        if ($this->category === 'pwd') {
            $baseQuery->whereHas('user', fn ($q) => $q->where('role', 'candidate'));
        } elseif ($this->category === 'employer') {
            $baseQuery->whereHas('user', fn ($q) => $q->where('role', 'employer'));
        }

        $counts = [
            'all' => VerificationRequest::count(),
            'pending' => VerificationRequest::where('status', 'pending')->count(),
            'approved' => VerificationRequest::where('status', 'approved')->count(),
            'rejected' => VerificationRequest::where('status', 'rejected')->count(),
            'needs_more_docs' => VerificationRequest::where('status', 'needs_more_docs')->count(),
        ];

        $catBase = VerificationRequest::query();
        if ($this->status !== 'all') {
            $catBase->where('status', $this->status);
        }

        $categoryCounts = [
            'all' => (clone $catBase)->count(),
            'pwd' => (clone $catBase)->whereHas('user', fn ($q) => $q->where('role', 'candidate'))->count(),
            'employer' => (clone $catBase)->whereHas('user', fn ($q) => $q->where('role', 'employer'))->count(),
        ];

        $requests = $baseQuery->paginate(15);

        return view('livewire.admin.verification-queue', [
            'requests' => $requests,
            'counts' => $counts,
            'categoryCounts' => $categoryCounts,
        ]);
    }
}
