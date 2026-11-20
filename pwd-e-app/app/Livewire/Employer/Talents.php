<?php

namespace App\Livewire\Employer;

use App\Models\Accommodation;
use App\Models\CandidateProfile;
use App\Models\JobPosting;
use App\Notifications\JobInvitationReceived;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Talent Discovery')]
#[Layout('layouts.employer')]
class Talents extends Component
{
    use WithPagination;

    public string $search = '';

    public string $selectedAccommodation = '';

    public string $selectedEducation = '';

    public bool $verifiedOnly = false;

    public string $sort = 'recent';

    public int $perPage = 9;

    public bool $showInviteModal = false;

    public ?int $invitingCandidateProfileId = null;

    public string $invitingCandidateName = '';

    public string $invitingCandidateHeadline = '';

    public ?int $selectedJobId = null;

    public string $invitationMessage = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedAccommodation' => ['except' => ''],
        'selectedEducation' => ['except' => ''],
        'verifiedOnly' => ['except' => false],
        'sort' => ['except' => 'recent'],
        'page' => ['except' => 1],
    ];

    public function mount(): void
    {
        $user = Auth::user();
        if (! $user || ! $user->isEmployer()) {
            abort(403, 'Unauthorized access to employer portal.');
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedAccommodation(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedEducation(): void
    {
        $this->resetPage();
    }

    public function updatingVerifiedOnly(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'selectedAccommodation', 'selectedEducation', 'verifiedOnly', 'sort']);
        $this->resetPage();
    }

    public function openInviteModal(int $candidateProfileId): void
    {
        $profile = CandidateProfile::with('user')->findOrFail($candidateProfileId);

        $this->invitingCandidateProfileId = $profile->id;
        $this->invitingCandidateName = $profile->user->name ?? 'Candidate';
        $this->invitingCandidateHeadline = $profile->headline ?? 'Candidate';

        $employerProfile = Auth::user()?->employerProfile;
        $activeJob = JobPosting::where('employer_profile_id', $employerProfile?->id)
            ->where('status', 'published')
            ->first();

        $this->selectedJobId = $activeJob?->id;
        $this->invitationMessage = "Hello {$this->invitingCandidateName}, we were impressed by your profile and qualifications on PWD-E and would like to invite you to apply for our open position!";
        $this->showInviteModal = true;
    }

    public function closeInviteModal(): void
    {
        $this->showInviteModal = false;
        $this->reset(['invitingCandidateProfileId', 'invitingCandidateName', 'invitingCandidateHeadline', 'selectedJobId', 'invitationMessage']);
    }

    public function sendInvitation(): void
    {
        $employerProfile = Auth::user()?->employerProfile;

        $this->validate([
            'selectedJobId' => 'required|integer|exists:job_postings,id',
            'invitationMessage' => 'nullable|string|max:1000',
        ]);

        $job = JobPosting::where('employer_profile_id', $employerProfile?->id)
            ->where('id', $this->selectedJobId)
            ->firstOrFail();

        $candidateProfile = CandidateProfile::with('user')->findOrFail($this->invitingCandidateProfileId);

        $candidateUser = $candidateProfile->user;
        if ($candidateUser) {
            $candidateUser->notify(new JobInvitationReceived(
                $job,
                $employerProfile?->company_name ?? Auth::user()->name,
                $this->invitationMessage
            ));
        }

        session()->flash('invitation_success', "Invitation successfully sent to {$candidateUser?->name} for {$job->title}!");

        $this->closeInviteModal();
    }

    public function render(): View
    {
        $employerProfile = Auth::user()?->employerProfile;

        $query = CandidateProfile::query()
            ->whereHas('user', function ($uq) {
                $uq->where('role', 'candidate');
            })
            ->where(function ($sub) {
                $sub->whereNull('visibility_settings')
                    ->orWhereRaw("visibility_settings NOT LIKE '%\"is_discoverable\":false%'");
            })
            ->with(['user', 'accommodations', 'workExperiences', 'education']);

        if (! empty(trim($this->search))) {
            $term = trim($this->search);
            $query->where(function ($q) use ($term) {
                $q->where('headline', 'like', "%{$term}%")
                    ->orWhere('bio', 'like', "%{$term}%")
                    ->orWhere('city', 'like', "%{$term}%")
                    ->orWhere('location', 'like', "%{$term}%")
                    ->orWhere('skills', 'like', "%{$term}%")
                    ->orWhere('highest_education', 'like', "%{$term}%")
                    ->orWhereHas('user', function ($uq) use ($term) {
                        $uq->where('name', 'like', "%{$term}%")
                            ->orWhere('first_name', 'like', "%{$term}%")
                            ->orWhere('last_name', 'like', "%{$term}%");
                    });
            });
        }

        if (! empty($this->selectedAccommodation)) {
            $query->whereHas('accommodations', function ($aq) {
                $aq->where('accommodations.name', 'like', "%{$this->selectedAccommodation}%")
                    ->orWhere('accommodations.category', $this->selectedAccommodation);
            });
        }

        if (! empty($this->selectedEducation)) {
            $query->where('highest_education', 'like', "%{$this->selectedEducation}%");
        }

        if ($this->verifiedOnly) {
            $query->whereHas('user', function ($uq) {
                $uq->where('status', 'verified')->whereNotNull('pwd_id_number');
            });
        }

        if ($this->sort === 'name_asc') {
            $query->join('users', 'candidate_profiles.user_id', '=', 'users.id')
                ->orderBy('users.name', 'asc')
                ->select('candidate_profiles.*');
        } elseif ($this->sort === 'recent') {
            $query->latest('candidate_profiles.updated_at');
        } else {
            $query->latest('candidate_profiles.id');
        }

        $candidates = $query->paginate($this->perPage);

        $accommodations = Accommodation::orderBy('name')->get();

        $activeJobs = JobPosting::where('employer_profile_id', $employerProfile?->id)
            ->where('status', 'published')
            ->orderBy('title')
            ->get();

        return view('livewire.employer.talents', [
            'candidates' => $candidates,
            'accommodations' => $accommodations,
            'activeJobs' => $activeJobs,
        ]);
    }
}
