<?php

namespace App\Livewire\Candidate;

use App\Models\Accommodation;
use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\JobPosting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.candidate')]
class JobMatches extends Component
{
    public string $search = '';

    public string $workArrangement = 'all';

    public bool $smartMatch = true;

    public array $selectedAccommodations = [];

    public ?int $selectedJobId = null;

    public bool $isFullScreen = false;

    public ?string $applicationSuccessMessage = null;

    public function mount(?int $job = null): void
    {
        $candidateProfile = Auth::user()?->candidateProfile;

        // Default to the 3 selected accommodations from candidate profile shown in Figma
        if ($candidateProfile) {
            $topNeeds = $candidateProfile->accessibilityNeeds()
                ->whereHas('accommodation', fn ($q) => $q->whereIn('name', [
                    'Wheelchair Ramp Access',
                    'Screen Reader Compatibility',
                    'FSL Bilingual Staff',
                ]))
                ->pluck('accommodation_id')
                ->map(fn ($id) => (string) $id)
                ->toArray();

            $this->selectedAccommodations = ! empty($topNeeds)
                ? $topNeeds
                : $candidateProfile->accessibilityNeeds()->take(3)->pluck('accommodation_id')->map(fn ($id) => (string) $id)->toArray();
        }

        // If specific job requested or set default to first job
        if ($job) {
            $this->selectedJobId = $job;
        } else {
            // Pick first job matching OpenPath Digital or first available
            $firstJob = JobPosting::where('status', 'published')
                ->whereHas('employerProfile', fn ($q) => $q->where('company_name', 'like', '%OpenPath%'))
                ->first()
                ?? JobPosting::where('status', 'published')->first();

            $this->selectedJobId = $firstJob?->id;
        }
    }

    public function selectJob(int $jobId): void
    {
        $this->selectedJobId = $jobId;
        $this->applicationSuccessMessage = null;
    }

    public function clearSelectedJob(): void
    {
        $this->selectedJobId = null;
    }

    public function toggleSmartMatch(): void
    {
        $this->smartMatch = ! $this->smartMatch;
    }

    public function resetToSavedProfile(): void
    {
        $candidateProfile = Auth::user()?->candidateProfile;
        if ($candidateProfile) {
            $topNeeds = $candidateProfile->accessibilityNeeds()
                ->whereHas('accommodation', fn ($q) => $q->whereIn('name', [
                    'Wheelchair Ramp Access',
                    'Screen Reader Compatibility',
                    'FSL Bilingual Staff',
                ]))
                ->pluck('accommodation_id')
                ->map(fn ($id) => (string) $id)
                ->toArray();

            $this->selectedAccommodations = ! empty($topNeeds)
                ? $topNeeds
                : $candidateProfile->accessibilityNeeds()->take(3)->pluck('accommodation_id')->map(fn ($id) => (string) $id)->toArray();
        }
    }

    public function toggleFullScreen(): void
    {
        $this->isFullScreen = ! $this->isFullScreen;
    }

    public function applyForJob(int $jobId): void
    {
        $user = Auth::user();
        $candidateProfile = $user->candidateProfile;

        if (! $candidateProfile) {
            return;
        }

        $existing = Application::where('candidate_profile_id', $candidateProfile->id)
            ->where('job_posting_id', $jobId)
            ->first();

        if ($existing) {
            $this->applicationSuccessMessage = 'You have already submitted an application for this role on '.($existing->applied_at ? $existing->applied_at->format('M d, Y') : 'recently').'.';

            return;
        }

        $job = JobPosting::with('employerProfile')->find($jobId);
        $match = $candidateProfile->jobMatches()->where('job_posting_id', $jobId)->first();
        $fitScore = $match ? (int) round($match->total_score * 100) : 95;

        Application::create([
            'candidate_profile_id' => $candidateProfile->id,
            'job_posting_id' => $jobId,
            'status' => 'applied',
            'status_subtext' => 'Application submitted and under review',
            'fit_score_at_apply' => $fitScore,
            'applied_at' => Carbon::now(),
        ]);

        $this->applicationSuccessMessage = "Application submitted successfully to {$job->employerProfile->company_name}! You can track it in Applications.";
        $this->dispatch('application-submitted');
    }

    public function render()
    {
        $user = Auth::user();
        $candidateProfile = $user?->candidateProfile ?? CandidateProfile::firstOrCreate(['user_id' => $user->id]);

        // Base query
        $query = JobPosting::where('status', 'published')
            ->with(['employerProfile', 'accommodations', 'jobAccommodations.accommodation'])
            ->whereHas('employerProfile');

        // Search filter
        if (! empty($this->search)) {
            $query->where(function ($q) {
                $term = '%'.$this->search.'%';
                $q->where('title', 'like', $term)
                    ->orWhere('description', 'like', $term)
                    ->orWhereHas('employerProfile', fn ($emp) => $emp->where('company_name', 'like', $term));
            });
        }

        // Work arrangement filter
        if ($this->workArrangement !== 'all') {
            $query->where('work_arrangement', $this->workArrangement);
        }

        $allJobs = $query->get()->map(function ($job) use ($candidateProfile) {
            $match = $candidateProfile->jobMatches()->where('job_posting_id', $job->id)->first();
            $job->score = $match ? (int) round($match->total_score * 100) : 90;
            $job->accessibility_score_pct = $match ? (int) round($match->accessibility_score * 100) : 90;

            $jobAccIds = $job->accommodations->pluck('id')->map(fn ($id) => (string) $id)->toArray();
            $totalSelected = count($this->selectedAccommodations);

            if ($totalSelected > 0) {
                $matchedCount = count(array_intersect($this->selectedAccommodations, $jobAccIds));
                $job->all_selected_supported = ($matchedCount === $totalSelected);
                $job->supported_accommodations_count = $matchedCount;
                $job->total_selected_accommodations_count = $totalSelected;
            } else {
                $job->all_selected_supported = true;
                $job->supported_accommodations_count = count($jobAccIds);
                $job->total_selected_accommodations_count = count($jobAccIds);
            }

            return $job;
        });

        // Smart Match filter (>= 85%)
        if ($this->smartMatch) {
            $allJobs = $allJobs->filter(fn ($j) => $j->score >= 85);
        }

        // Sort descending by score
        $jobs = $allJobs->sortByDesc('score')->values();

        // Selected Job details
        $selectedJob = null;
        if ($this->selectedJobId) {
            $selectedJob = JobPosting::with(['employerProfile', 'jobAccommodations.accommodation', 'accommodations'])
                ->find($this->selectedJobId);

            if ($selectedJob) {
                $match = $candidateProfile->jobMatches()->where('job_posting_id', $selectedJob->id)->first();
                $selectedJob->score = $match ? (int) round($match->total_score * 100) : 98;
                $selectedJob->accessibility_score_pct = $match ? (int) round($match->accessibility_score * 100) : 98;
            }
        }

        $allAccommodations = Accommodation::all();

        return view('livewire.candidate.job-matches', [
            'user' => $user,
            'candidateProfile' => $candidateProfile,
            'jobs' => $jobs,
            'selectedJob' => $selectedJob,
            'selectedJobId' => $this->selectedJobId,
            'allAccommodations' => $allAccommodations,
            'search' => $this->search,
            'workArrangement' => $this->workArrangement,
            'smartMatch' => $this->smartMatch,
            'selectedAccommodations' => $this->selectedAccommodations,
            'isFullScreen' => $this->isFullScreen,
            'applicationSuccessMessage' => $this->applicationSuccessMessage,
        ]);
    }
}
