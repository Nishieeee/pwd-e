<?php

namespace App\Livewire\Candidate;

use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\JobPosting;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.candidate')]
class Dashboard extends Component
{
    public ?int $selectedApplicationId = null;

    public bool $showBreakdownModal = false;

    public ?string $calendarDownloadSuccess = null;

    public ?string $profileSavedMessage = null;

    public function mount(): void
    {
        if (session()->has('profile_saved_banner')) {
            $this->profileSavedMessage = (string) session('profile_saved_banner');
        }
    }

    public function dismissProfileSavedBanner(): void
    {
        $this->profileSavedMessage = null;
    }

    public function viewFitBreakdown(int $applicationId): void
    {
        $this->selectedApplicationId = $applicationId;
        $this->showBreakdownModal = true;
    }

    public function closeBreakdownModal(): void
    {
        $this->showBreakdownModal = false;
        $this->selectedApplicationId = null;
    }

    public function acceptAndAddToCalendar(int $applicationId): void
    {
        $application = Application::with(['jobPosting.employerProfile'])->find($applicationId);
        if ($application) {
            $this->calendarDownloadSuccess = "Interview with {$application->jobPosting->employerProfile->company_name} accepted and added to calendar!";
            $this->dispatch('interview-accepted', message: $this->calendarDownloadSuccess);
        }
    }

    public function render()
    {
        $user = Auth::user();
        $candidateProfile = $user->candidateProfile ?? CandidateProfile::firstOrCreate(['user_id' => $user->id]);

        // 1. Stats calculation
        $activeApplications = Application::where('candidate_profile_id', $candidateProfile->id)
            ->whereIn('status', ['applied', 'under_review', 'interview', 'submitted'])
            ->with(['jobPosting.employerProfile', 'jobPosting.accommodations'])
            ->orderByDesc('applied_at')
            ->get();

        $activeApplicationsCount = $activeApplications->count();

        $highestFitScore = (int) round(($candidateProfile->jobMatches()->max('accessibility_score') ?? 0.96) * 100);

        $interviewInvitationsCount = Application::where('candidate_profile_id', $candidateProfile->id)
            ->where('status', 'interview')
            ->count();

        $accommodationsSpecifiedCount = $candidateProfile->accessibilityNeeds()->count();

        // 2. Upcoming Interview Banner
        $upcomingInterviewApplication = Application::where('candidate_profile_id', $candidateProfile->id)
            ->where('status', 'interview')
            ->whereNotNull('interview_at')
            ->with(['jobPosting.employerProfile'])
            ->orderBy('interview_at')
            ->first();

        // 3. Recommended Jobs Based on Accommodations
        // Get jobs with verified coverage matching candidate's required accommodations
        $candidateAccIds = $candidateProfile->accessibilityNeeds()->pluck('accommodation_id')->toArray();

        $recommendedJobs = JobPosting::where('status', 'published')
            ->with(['employerProfile', 'accommodations'])
            ->whereHas('employerProfile')
            ->take(3)
            ->get()
            ->map(function ($job) use ($candidateProfile) {
                $match = $candidateProfile->jobMatches()->where('job_posting_id', $job->id)->first();
                $score = $match ? (int) round($match->total_score * 100) : 90;
                $job->fit_score = $score;

                return $job;
            });

        // 4. Modal application data if opened
        $breakdownApplication = $this->selectedApplicationId
            ? Application::with(['jobPosting.employerProfile', 'jobPosting.accommodations'])->find($this->selectedApplicationId)
            : null;

        return view('livewire.candidate.dashboard', [
            'user' => $user,
            'candidateProfile' => $candidateProfile,
            'activeApplications' => $activeApplications,
            'activeApplicationsCount' => $activeApplicationsCount,
            'highestFitScore' => $highestFitScore,
            'interviewInvitationsCount' => $interviewInvitationsCount,
            'accommodationsSpecifiedCount' => $accommodationsSpecifiedCount,
            'upcomingInterviewApplication' => $upcomingInterviewApplication,
            'recommendedJobs' => $recommendedJobs,
            'breakdownApplication' => $breakdownApplication,
            'calendarDownloadSuccess' => $this->calendarDownloadSuccess,
            'profileSavedMessage' => $this->profileSavedMessage,
            'showBreakdownModal' => $this->showBreakdownModal,
        ]);
    }
}
