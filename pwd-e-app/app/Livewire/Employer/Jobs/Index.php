<?php

namespace App\Livewire\Employer\Jobs;

use App\Models\JobPosting;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('My Job Postings')]
#[Layout('layouts.employer')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'status')]
    public string $status = 'all';

    #[Url(as: 'q')]
    public string $search = '';

    public bool $showConfirmModal = false;

    public ?string $confirmAction = null;

    public ?int $confirmJobId = null;

    public string $confirmTitle = '';

    public string $confirmDescription = '';

    public string $confirmButtonText = '';

    public string $confirmButtonClass = 'bg-primary-dark';

    public function openConfirmModal(string $action, int $jobId, string $title, string $description, string $buttonText = 'Confirm', string $buttonClass = 'bg-primary-dark'): void
    {
        $this->confirmAction = $action;
        $this->confirmJobId = $jobId;
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
        $this->confirmJobId = null;
    }

    public function executeConfirmedAction(): void
    {
        if (! $this->confirmJobId || ! $this->confirmAction) {
            $this->closeConfirmModal();
            return;
        }

        $jobId = $this->confirmJobId;
        $action = $this->confirmAction;
        $this->closeConfirmModal();

        match ($action) {
            'closeJob' => $this->closeJob($jobId),
            'deleteJob' => $this->deleteJob($jobId),
            default => null,
        };
    }

    public function mount(): void
    {
        $user = Auth::user();

        if (! $user || ! $user->isEmployer()) {
            abort(403, 'Unauthorized. Employer access required.');
        }

        // Ensure employer profile exists
        if (! $user->employerProfile) {
            $user->employerProfile()->create([
                'company_name' => $user->name,
                'business_permit_number' => 'PENDING',
            ]);
            $user->refresh();
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
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

    public function closeJob(int $jobId): void
    {
        $job = $this->getEmployerJob($jobId);
        $job->update(['status' => 'closed']);

        Flux::toast(variant: 'success', text: __('Job posting marked as closed.'));
    }

    public function reopenJob(int $jobId): void
    {
        $job = $this->getEmployerJob($jobId);
        $job->update([
            'status' => 'published',
            'published_at' => $job->published_at ?? now(),
        ]);

        Flux::toast(variant: 'success', text: __('Job posting republished.'));
    }

    public function deleteJob(int $jobId): void
    {
        $job = $this->getEmployerJob($jobId);
        $job->delete();

        Flux::toast(variant: 'success', text: __('Job posting removed.'));
    }

    protected function getEmployerJob(int $jobId): JobPosting
    {
        $profileId = Auth::user()->employerProfile?->id;

        return JobPosting::where('employer_profile_id', $profileId)
            ->findOrFail($jobId);
    }

    public function render(): View
    {
        $profile = Auth::user()->employerProfile;

        $baseQuery = JobPosting::where('employer_profile_id', $profile->id ?? 0);

        $counts = [
            'all' => (clone $baseQuery)->count(),
            'published' => (clone $baseQuery)->where('status', 'published')->count(),
            'draft' => (clone $baseQuery)->where('status', 'draft')->count(),
            'closed' => (clone $baseQuery)->where('status', 'closed')->count(),
        ];

        $jobsQuery = (clone $baseQuery)
            ->withCount('applications')
            ->with('accommodations')
            ->latest();

        if ($this->status !== 'all') {
            $jobsQuery->where('status', $this->status);
        }

        if (filled($this->search)) {
            $searchTerm = '%'.trim($this->search).'%';
            $jobsQuery->where(function ($query) use ($searchTerm) {
                $query->where('title', 'like', $searchTerm)
                    ->orWhere('location', 'like', $searchTerm);
            });
        }

        $jobs = $jobsQuery->paginate(10);

        return view('livewire.employer.jobs.index', [
            'jobs' => $jobs,
            'counts' => $counts,
        ]);
    }
}
