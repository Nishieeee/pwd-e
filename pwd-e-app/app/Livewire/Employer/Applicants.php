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
        $this->job = $job->load('employerProfile');
    }

    public function viewDetails(int $applicationId): void
    {
        $this->viewingApplicationId = $applicationId;
    }

    public function closeDetails(): void
    {
        $this->viewingApplicationId = null;
    }

    public function updateStatus(int $applicationId, string $status): void
    {
        $app = Application::where('job_posting_id', $this->job->id)->findOrFail($applicationId);
        $app->update(['status' => $status, 'status_subtext' => ucfirst(str_replace('_', ' ', $status))]);
        Flux::toast(variant: 'success', text: __('Status updated.'));
    }

    public function hire(int $applicationId): void
    {
        $app = Application::with('candidateProfile')->where('job_posting_id', $this->job->id)->findOrFail($applicationId);

        $app->update(['status' => 'hired']);

        WorkforceRecord::firstOrCreate(
            ['application_id' => $app->id],
            [
                'employer_profile_id' => $this->job->employer_profile_id,
                'candidate_profile_id' => $app->candidate_profile_id,
                'hired_at' => now(),
                'pwd_verified' => false,
                'annual_wage' => $this->job->salary_max ?? $this->job->salary_min,
            ]
        );

        Flux::toast(variant: 'success', text: __('Marked as hired; workforce record created.'));
    }

    public function render(): View
    {
        $applications = Application::with(['candidateProfile.user', 'candidateProfile.education', 'candidateProfile.workExperiences', 'candidateProfile.certifications'])
            ->where('job_posting_id', $this->job->id)
            ->orderByDesc('fit_score_at_apply')
            ->get();

        $viewing = $this->viewingApplicationId
            ? $applications->firstWhere('id', $this->viewingApplicationId)
            : null;

        return view('livewire.employer.applicants', [
            'applications' => $applications,
            'viewing' => $viewing,
        ]);
    }
}
