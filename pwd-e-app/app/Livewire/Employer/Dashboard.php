<?php

namespace App\Livewire\Employer;

use App\Models\Application;
use App\Models\JobPosting;
use App\Models\WorkforceRecord;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Employer Dashboard')]
#[Layout('layouts.employer')]
class Dashboard extends Component
{
    public ?int $viewingApplicationId = null;

    public bool $showInterviewModal = false;

    public ?int $interviewAppId = null;

    public ?string $interviewDate = '';

    public ?string $interviewTime = '10:00';

    public ?string $interviewLocation = '';

    public ?string $interviewUrl = '';

    public ?string $interviewAccommodations = '';

    public ?string $interviewNotes = '';

    public bool $showHireModal = false;

    public ?int $hiringAppId = null;

    public ?float $hireWage = null;

    public string $hireStartDate = '';

    public bool $hirePwdVerified = true;

    public function mount(): void
    {
        $user = Auth::user();
        if (! $user || ! $user->isEmployer()) {
            abort(403, 'Unauthorized. Employer access required.');
        }

        $this->hireStartDate = now()->format('Y-m-d');
        $this->interviewDate = now()->addDays(2)->format('Y-m-d');
    }

    public function viewDetails(int $applicationId): void
    {
        $this->viewingApplicationId = $applicationId;
    }

    public function closeDetails(): void
    {
        $this->viewingApplicationId = null;
    }

    public function openInterviewModal(int $applicationId): void
    {
        $profile = Auth::user()?->employerProfile;
        if (! $profile) {
            return;
        }

        $app = Application::whereHas('jobPosting', function ($q) use ($profile) {
            $q->where('employer_profile_id', $profile->id);
        })->with('jobPosting')->findOrFail($applicationId);

        $this->interviewAppId = $app->id;
        $this->interviewDate = $app->interview_at ? $app->interview_at->format('Y-m-d') : now()->addDays(2)->format('Y-m-d');
        $this->interviewTime = $app->interview_at ? $app->interview_at->format('H:i') : '10:00';
        $this->interviewLocation = $app->interview_location ?? $app->jobPosting?->location ?? '';
        $this->interviewUrl = $app->interview_url ?? 'https://meet.google.com/new';
        $this->interviewAccommodations = $app->interview_accommodations ?? 'Sign Language Interpreter requested';
        $this->interviewNotes = $app->employer_notes ?? '';
        $this->showInterviewModal = true;
    }

    public function closeInterviewModal(): void
    {
        $this->showInterviewModal = false;
        $this->interviewAppId = null;
    }

    public function saveInterview(): void
    {
        if (! $this->interviewAppId) {
            return;
        }

        $profile = Auth::user()?->employerProfile;
        if (! $profile) {
            return;
        }

        $app = Application::whereHas('jobPosting', function ($q) use ($profile) {
            $q->where('employer_profile_id', $profile->id);
        })->findOrFail($this->interviewAppId);

        $dateTimeString = trim($this->interviewDate . ' ' . $this->interviewTime);
        $interviewAt = ! empty($this->interviewDate) ? \Carbon\Carbon::parse($dateTimeString) : now()->addDays(2);

        $app->update([
            'status' => 'interview',
            'status_subtext' => 'Interview scheduled for ' . $interviewAt->format('M d, Y h:i A'),
            'interview_at' => $interviewAt,
            'interview_location' => $this->interviewLocation ?: null,
            'interview_url' => $this->interviewUrl ?: null,
            'interview_accommodations' => $this->interviewAccommodations ?: null,
            'employer_notes' => $this->interviewNotes ?: null,
        ]);

        $this->showInterviewModal = false;
        $this->interviewAppId = null;
        Flux::toast(variant: 'success', text: __('Interview scheduled successfully.'));
    }

    public function openHireModal(int $applicationId): void
    {
        $profile = Auth::user()?->employerProfile;
        if (! $profile) {
            return;
        }

        $app = Application::with(['candidateProfile.user', 'jobPosting'])->whereHas('jobPosting', function ($q) use ($profile) {
            $q->where('employer_profile_id', $profile->id);
        })->findOrFail($applicationId);

        $this->hiringAppId = $app->id;
        $this->hireWage = (float) ($app->jobPosting?->salary_max ?? $app->jobPosting?->salary_min ?? 35000);
        $this->hireStartDate = now()->format('Y-m-d');
        $this->hirePwdVerified = ! empty($app->candidateProfile?->user?->pwd_id_number);
        $this->showHireModal = true;
    }

    public function closeHireModal(): void
    {
        $this->showHireModal = false;
        $this->hiringAppId = null;
    }

    public function confirmHire(): void
    {
        if (! $this->hiringAppId) {
            return;
        }

        $this->hire($this->hiringAppId);
        $this->showHireModal = false;
        $this->hiringAppId = null;
    }

    public function updateStatus(int $applicationId, string $status): void
    {
        $profile = Auth::user()?->employerProfile;
        if (! $profile) {
            return;
        }

        $app = Application::whereHas('jobPosting', function ($q) use ($profile) {
            $q->where('employer_profile_id', $profile->id);
        })->findOrFail($applicationId);

        $app->update(['status' => $status, 'status_subtext' => ucfirst(str_replace('_', ' ', $status))]);

        if ($status === 'interview') {
            $this->openInterviewModal($applicationId);
            return;
        }

        if ($status === 'hired') {
            $this->openHireModal($applicationId);
            return;
        }

        Flux::toast(variant: 'success', text: __('Status updated.'));
    }

    public function hire(int $applicationId): void
    {
        $profile = Auth::user()?->employerProfile;
        if (! $profile) {
            return;
        }

        $app = Application::with(['candidateProfile.user', 'jobPosting'])->whereHas('jobPosting', function ($q) use ($profile) {
            $q->where('employer_profile_id', $profile->id);
        })->findOrFail($applicationId);

        $app->update([
            'status' => 'hired',
            'status_subtext' => 'Offer accepted and hired',
        ]);

        $wage = $this->hireWage ?: ($app->jobPosting?->salary_max ?? $app->jobPosting?->salary_min ?? 35000);
        $hiredAt = ! empty($this->hireStartDate) ? \Carbon\Carbon::parse($this->hireStartDate) : now();
        $isPwdVerified = ! empty($app->candidateProfile?->user?->pwd_id_number);

        WorkforceRecord::firstOrCreate(
            ['application_id' => $app->id],
            [
                'employer_profile_id' => $profile->id,
                'candidate_profile_id' => $app->candidate_profile_id,
                'hired_at' => $hiredAt,
                'pwd_verified' => $isPwdVerified,
                'annual_wage' => $wage,
            ]
        );

        Flux::toast(variant: 'success', text: __('Marked as hired; workforce record created with RA 10524 tax incentive.'));
    }

    public function render(): View
    {
        $user = Auth::user();
        $profile = $user?->employerProfile;

        $jobsQuery = $profile ? JobPosting::where('employer_profile_id', $profile->id) : null;
        $activeJobsCount = $jobsQuery ? (clone $jobsQuery)->where('status', 'published')->count() : 0;

        $baseAppQuery = Application::whereHas('jobPosting', function ($q) use ($profile) {
            $q->where('employer_profile_id', $profile?->id ?? 0);
        });

        $totalApplicantsCount = (clone $baseAppQuery)->count();
        $interviewsCount = (clone $baseAppQuery)->where('status', 'interview')->count();
        $hiredCount = (clone $baseAppQuery)->where('status', 'hired')->count();
        $needsReviewCount = (clone $baseAppQuery)->whereIn('status', ['applied', 'under_review'])->count();

        $taxRateDecimal = \App\Models\PlatformSetting::pwdTaxDeductionDecimal();
        $taxRateLabel = \App\Models\PlatformSetting::pwdTaxDeductionLabel();

        $workforceRecords = $profile ? WorkforceRecord::where('employer_profile_id', $profile->id)->get() : collect();
        $taxIncentiveEst = $workforceRecords->sum(fn ($rec) => (float) $rec->annual_wage * $taxRateDecimal);

        $recentApplicants = (clone $baseAppQuery)->with([
            'candidateProfile.user',
            'candidateProfile.education',
            'candidateProfile.workExperiences',
            'jobPosting',
        ])->latest('applied_at')->limit(6)->get();

        $activeJobs = $jobsQuery
            ? (clone $jobsQuery)->where('status', 'published')->withCount('applications')->latest()->limit(4)->get()
            : collect();

        $viewing = $this->viewingApplicationId
            ? (clone $baseAppQuery)->with([
                'candidateProfile.user',
                'candidateProfile.education',
                'candidateProfile.workExperiences',
                'candidateProfile.certifications',
                'jobPosting',
            ])->find($this->viewingApplicationId)
            : null;

        $interviewingApp = $this->interviewAppId
            ? Application::with(['candidateProfile.user', 'jobPosting'])->find($this->interviewAppId)
            : null;

        $hiringApp = $this->hiringAppId
            ? Application::with(['candidateProfile.user', 'jobPosting'])->find($this->hiringAppId)
            : null;

        return view('livewire.employer.dashboard', [
            'activeJobsCount' => $activeJobsCount,
            'totalApplicantsCount' => $totalApplicantsCount,
            'interviewsCount' => $interviewsCount,
            'hiredCount' => $hiredCount,
            'needsReviewCount' => $needsReviewCount,
            'taxIncentiveEst' => $taxIncentiveEst,
            'taxRateDecimal' => $taxRateDecimal,
            'taxRateLabel' => $taxRateLabel,
            'recentApplicants' => $recentApplicants,
            'activeJobs' => $activeJobs,
            'viewing' => $viewing,
            'interviewingApp' => $interviewingApp,
            'hiringApp' => $hiringApp,
        ]);
    }
}
