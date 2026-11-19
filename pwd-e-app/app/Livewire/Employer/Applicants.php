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

#[Title('Applicants')]
#[Layout('layouts.employer')]
class Applicants extends Component
{
    public JobPosting $job;

    public ?int $viewingApplicationId = null;

    public string $statusFilter = 'all';

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

    public function mount(JobPosting $job): void
    {
        $user = Auth::user();
        if (! $user || ! $user->isEmployer()) {
            abort(403);
        }
        $profile = $user->employerProfile;
        if (! $profile || $job->employer_profile_id !== $profile->id) {
            abort(403);
        }
        $this->job = $job->load(['employerProfile', 'accommodations']);
        $this->hireStartDate = now()->format('Y-m-d');
        $this->hireWage = (float) ($job->salary_max ?? $job->salary_min ?? 35000);
        $this->interviewDate = now()->addDays(2)->format('Y-m-d');
    }

    public function setStatusFilter(string $status): void
    {
        $this->statusFilter = $status;
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
        $app = Application::where('job_posting_id', $this->job->id)->findOrFail($applicationId);
        $this->interviewAppId = $app->id;
        $this->interviewDate = $app->interview_at ? $app->interview_at->format('Y-m-d') : now()->addDays(2)->format('Y-m-d');
        $this->interviewTime = $app->interview_at ? $app->interview_at->format('H:i') : '10:00';
        $this->interviewLocation = $app->interview_location ?? $this->job->location;
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

        $app = Application::where('job_posting_id', $this->job->id)->findOrFail($this->interviewAppId);

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
        $app = Application::with('candidateProfile.user')->where('job_posting_id', $this->job->id)->findOrFail($applicationId);
        $this->hiringAppId = $app->id;
        $this->hireWage = (float) ($this->job->salary_max ?? $this->job->salary_min ?? 35000);
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
        $app = Application::where('job_posting_id', $this->job->id)->findOrFail($applicationId);
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
        $app = Application::with('candidateProfile.user')->where('job_posting_id', $this->job->id)->findOrFail($applicationId);

        $app->update([
            'status' => 'hired',
            'status_subtext' => 'Offer accepted and hired',
        ]);

        $wage = $this->hireWage ?: ($this->job->salary_max ?? $this->job->salary_min ?? 35000);
        $hiredAt = ! empty($this->hireStartDate) ? \Carbon\Carbon::parse($this->hireStartDate) : now();
        $isPwdVerified = ! empty($app->candidateProfile?->user?->pwd_id_number);

        WorkforceRecord::firstOrCreate(
            ['application_id' => $app->id],
            [
                'employer_profile_id' => $this->job->employer_profile_id,
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
        $query = Application::with(['candidateProfile.user', 'candidateProfile.education', 'candidateProfile.workExperiences', 'candidateProfile.certifications'])
            ->where('job_posting_id', $this->job->id)
            ->orderByDesc('fit_score_at_apply');

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $applications = $query->get();

        $allCounts = [
            'all' => Application::where('job_posting_id', $this->job->id)->count(),
            'applied' => Application::where('job_posting_id', $this->job->id)->where('status', 'applied')->count(),
            'under_review' => Application::where('job_posting_id', $this->job->id)->where('status', 'under_review')->count(),
            'interview' => Application::where('job_posting_id', $this->job->id)->where('status', 'interview')->count(),
            'hired' => Application::where('job_posting_id', $this->job->id)->where('status', 'hired')->count(),
            'rejected' => Application::where('job_posting_id', $this->job->id)->where('status', 'rejected')->count(),
        ];

        $viewing = $this->viewingApplicationId
            ? $applications->firstWhere('id', $this->viewingApplicationId) ?? Application::with(['candidateProfile.user', 'candidateProfile.education', 'candidateProfile.workExperiences', 'candidateProfile.certifications'])->find($this->viewingApplicationId)
            : null;

        $interviewingApp = $this->interviewAppId
            ? Application::with('candidateProfile.user')->find($this->interviewAppId)
            : null;

        $hiringApp = $this->hiringAppId
            ? Application::with('candidateProfile.user')->find($this->hiringAppId)
            : null;

        return view('livewire.employer.applicants', [
            'applications' => $applications,
            'allCounts' => $allCounts,
            'viewing' => $viewing,
            'interviewingApp' => $interviewingApp,
            'hiringApp' => $hiringApp,
        ]);
    }
}
