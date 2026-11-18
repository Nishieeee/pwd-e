<?php

namespace App\Livewire\Candidate;

use App\Models\Application;
use App\Models\JobPosting;
use App\Notifications\ApplicationReceived;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Apply for Job')]
class Apply extends Component
{
    use WithFileUploads;

    public JobPosting $job;

    public string $coverLetter = '';

    public $pwdIdFile = null;

    public $resumeFile = null;

    public $fslVideoFile = null;

    public bool $submitted = false;

    public ?int $referenceNumber = null;

    public bool $missingPwdId = false;

    public function mount(JobPosting $job): void
    {
        $user = Auth::user();
        if (! $user || ! $user->isCandidate()) {
            abort(403, 'Unauthorized. Candidate access required.');
        }

        $this->job = $job->load('employerProfile');

        $profile = $user->candidateProfile;
        if (! $profile || empty($user->pwd_id_number)) {
            $this->missingPwdId = true;
        }
    }

    public function submit(): void
    {
        if ($this->missingPwdId) {
            return;
        }

        $this->validate([
            'coverLetter' => ['nullable', 'string', 'max:5000'],
            'pwdIdFile' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'resumeFile' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'fslVideoFile' => ['nullable', 'file', 'mimes:mp4,mov,webm', 'max:51200'],
        ]);

        $user = Auth::user();
        $profile = $user->candidateProfile;

        // Check duplicate
        $existing = Application::where('candidate_profile_id', $profile->id)
            ->where('job_posting_id', $this->job->id)
            ->first();

        if ($existing) {
            $this->applicationSuccessMessage = 'You have already applied to this job.';
            $this->submitted = true;
            $this->referenceNumber = $existing->id;

            return;
        }

        $pwdIdPath = $this->pwdIdFile?->store('applications/pwd_ids', 'private');
        $resumePath = $this->resumeFile?->store('applications/resumes', 'private');
        $fslPath = $this->fslVideoFile?->store('applications/fsl', 'private');

        $match = $profile->jobMatches()->where('job_posting_id', $this->job->id)->first();
        $fitScore = $match ? (float) round($match->total_score * 100, 2) : 95.00;

        $application = Application::create([
            'candidate_profile_id' => $profile->id,
            'job_posting_id' => $this->job->id,
            'status' => 'applied',
            'status_subtext' => 'Application submitted and under review',
            'cover_letter' => $this->coverLetter ?: null,
            'fsl_video_path' => $fslPath,
            'fit_score_at_apply' => $fitScore,
            'applied_at' => now(),
        ]);

        // Notify employer
        $employerUser = $this->job->employerProfile?->user;
        if ($employerUser) {
            $employerUser->notify(new ApplicationReceived(
                candidateName: $user->name,
                jobTitle: $this->job->title,
                applicationUrl: route('employer.jobs.index'),
            ));
        }

        $this->referenceNumber = $application->id;
        $this->submitted = true;
    }

    public function render(): View
    {
        return view('livewire.candidate.apply');
    }
}
