<?php

namespace App\Livewire\Admin;

use App\Models\VerificationRequest;
use App\Notifications\VerificationStatusUpdated;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Verification Queue')]
class VerificationQueue extends Component
{
    use WithPagination;

    #[Url(as: 'status')]
    public string $status = 'pending';

    public ?string $reviewNote = null;

    public ?int $reviewingId = null;

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
    }

    public function setStatusFilter(string $status): void
    {
        $this->status = $status;
        $this->resetPage();
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

    public function approve(int $id): void
    {
        $vr = VerificationRequest::with('user')->findOrFail($id);
        $vr->update([
            'status' => 'approved',
            'rejection_reason' => null,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $vr->user->update([
            'status' => 'active',
            'verified_at' => now(),
        ]);

        $vr->user->notify(new VerificationStatusUpdated('approved'));

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

        Flux::toast(variant: 'success', text: __('Verification rejected.'));
        $this->closeReview();
    }

    public function requestMoreDocs(int $id): void
    {
        $vr = VerificationRequest::with('user')->findOrFail($id);
        $vr->update([
            'status' => 'needs_more_docs',
            'rejection_reason' => $this->reviewNote,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $vr->user->notify(new VerificationStatusUpdated('needs_more_docs', $this->reviewNote));

        Flux::toast(variant: 'success', text: __('Additional documents requested.'));
        $this->closeReview();
    }

    public function render(): View
    {
        $baseQuery = VerificationRequest::with('user')->latest();

        if ($this->status !== 'all') {
            $baseQuery->where('status', $this->status);
        }

        $counts = [
            'all' => VerificationRequest::count(),
            'pending' => VerificationRequest::where('status', 'pending')->count(),
            'approved' => VerificationRequest::where('status', 'approved')->count(),
            'rejected' => VerificationRequest::where('status', 'rejected')->count(),
            'needs_more_docs' => VerificationRequest::where('status', 'needs_more_docs')->count(),
        ];

        $requests = $baseQuery->paginate(15);

        return view('livewire.admin.verification-queue', [
            'requests' => $requests,
            'counts' => $counts,
        ]);
    }
}
