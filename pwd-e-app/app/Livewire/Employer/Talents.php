<?php

namespace App\Livewire\Employer;

use App\Models\Accommodation;
use App\Models\CandidateProfile;
use App\Models\JobInvitation;
use App\Models\JobPosting;
use App\Notifications\JobInvitationReceived;
use App\Notifications\JobInvitationWithdrawn;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public array $selectedAccommodations = [];

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

    public bool $showWithdrawModal = false;

    public ?int $withdrawingInvitationId = null;

    public string $withdrawalCandidateName = '';

    public string $withdrawalJobTitle = '';

    public string $withdrawalReason = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedAccommodations' => ['except' => []],
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

    public function updatingSelectedAccommodations(): void
    {
        $this->resetPage();
    }

    public function removeAccommodation(string $name): void
    {
        $this->selectedAccommodations = array_values(array_filter(
            $this->selectedAccommodations,
            fn ($item) => $item !== $name
        ));
        $this->resetPage();
    }

    public function clearAccommodations(): void
    {
        $this->selectedAccommodations = [];
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
        $this->reset(['search', 'selectedAccommodations', 'selectedEducation', 'verifiedOnly', 'sort']);
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

        $invitation = JobInvitation::create([
            'employer_profile_id' => $employerProfile->id,
            'job_posting_id' => $job->id,
            'candidate_profile_id' => $candidateProfile->id,
            'status' => 'pending',
            'personal_message' => $this->invitationMessage,
        ]);

        $candidateUser = $candidateProfile->user;
        if ($candidateUser) {
            $candidateUser->notify(new JobInvitationReceived(
                $job,
                $employerProfile?->company_name ?? Auth::user()->name,
                $this->invitationMessage,
                $invitation->id
            ));
        }

        session()->flash('invitation_success', "Invitation successfully sent to {$candidateUser?->name} for {$job->title}!");

        $this->closeInviteModal();
    }

    public function openWithdrawModal(int $invitationId): void
    {
        $employerProfile = Auth::user()?->employerProfile;
        $invitation = JobInvitation::where('employer_profile_id', $employerProfile?->id)
            ->with(['candidateProfile.user', 'jobPosting'])
            ->findOrFail($invitationId);

        $this->withdrawingInvitationId = $invitation->id;
        $this->withdrawalCandidateName = $invitation->candidateProfile?->user?->name ?? 'Candidate';
        $this->withdrawalJobTitle = $invitation->jobPosting?->title ?? 'Position';
        $this->withdrawalReason = '';
        $this->showWithdrawModal = true;
    }

    public function closeWithdrawModal(): void
    {
        $this->showWithdrawModal = false;
        $this->reset(['withdrawingInvitationId', 'withdrawalCandidateName', 'withdrawalJobTitle', 'withdrawalReason']);
    }

    public function confirmWithdrawInvitation(): void
    {
        $this->validate([
            'withdrawalReason' => 'required|string|min:3|max:500',
        ]);

        $employerProfile = Auth::user()?->employerProfile;
        $invitation = JobInvitation::where('employer_profile_id', $employerProfile?->id)
            ->with(['candidateProfile.user', 'jobPosting'])
            ->findOrFail($this->withdrawingInvitationId);

        $invitation->withdraw($this->withdrawalReason);

        $candidateUser = $invitation->candidateProfile?->user;
        if ($candidateUser && $invitation->jobPosting) {
            $candidateUser->notify(new JobInvitationWithdrawn(
                $invitation->jobPosting,
                $employerProfile?->company_name ?? Auth::user()->name,
                $this->withdrawalReason
            ));
        }

        session()->flash('invitation_success', "Invitation for {$this->withdrawalCandidateName} was withdrawn successfully.");
        $this->closeWithdrawModal();
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
                    ->orWhere('visibility_settings->is_discoverable', '!=', 'false')
                    ->orWhereNull('visibility_settings->is_discoverable');
            })
            ->with([
                'user',
                'accommodations',
                'workExperiences',
                'education',
                'jobInvitations' => fn ($iq) => $iq->where('employer_profile_id', $employerProfile?->id)->where('status', 'pending')->with('jobPosting'),
            ]);

        if (! empty(trim($this->search))) {
            $term = trim($this->search);
            $driver = DB::connection()->getDriverName();
            $query->where(function ($q) use ($term, $driver) {
                $q->where('headline', 'like', "%{$term}%")
                    ->orWhere('bio', 'like', "%{$term}%")
                    ->orWhere('city', 'like', "%{$term}%")
                    ->orWhere('location', 'like', "%{$term}%")
                    ->orWhere('highest_education', 'like', "%{$term}%")
                    ->orWhereHas('user', function ($uq) use ($term) {
                        $uq->where('name', 'like', "%{$term}%")
                            ->orWhere('first_name', 'like', "%{$term}%")
                            ->orWhere('last_name', 'like', "%{$term}%");
                    });

                if ($driver === 'pgsql') {
                    $q->orWhereRaw('CAST(skills AS text) ILIKE ?', ["%{$term}%"]);
                } else {
                    $q->orWhere('skills', 'like', "%{$term}%");
                }
            });
        }

        if (! empty($this->selectedAccommodations)) {
            $selected = array_values(array_filter($this->selectedAccommodations));
            if (! empty($selected)) {
                $driver = DB::connection()->getDriverName();
                $query->whereHas('accommodations', function ($aq) use ($selected, $driver) {
                    $aq->where(function ($sub) use ($selected, $driver) {
                        foreach ($selected as $accName) {
                            $term = strtolower(trim($accName));
                            if ($driver === 'pgsql') {
                                $sub->orWhereRaw('LOWER(accommodations.name) LIKE ?', ["%{$term}%"]);
                            } else {
                                $sub->orWhere('accommodations.name', 'like', "%{$accName}%");
                            }
                        }
                    });
                });
            }
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

        $accommodations = Accommodation::all()
            ->unique(fn ($item) => strtolower(trim($item->name)))
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

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
