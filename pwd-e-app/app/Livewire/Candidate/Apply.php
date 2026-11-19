<?php

namespace App\Livewire\Candidate;

use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\JobPosting;
use App\Notifications\ApplicationReceived;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Apply for Job')]
#[Layout('layouts.candidate')]
class Apply extends Component
{
    use WithFileUploads;

    public JobPosting $job;

    public string $coverLetter = '';

    public $pwdIdFile = null;

    public $resumeFile = null;

    public $fslVideoFile = null;

    public bool $useProfileResume = true;

    public bool $useProfilePwdId = true;

    public bool $useProfileFsl = true;

    public bool $submitted = false;

    public ?int $referenceNumber = null;

    public ?string $formattedReference = null;

    public bool $missingPwdId = false;

    public bool $alreadyApplied = false;

    public ?Application $existingApplication = null;

    public ?string $applicationSuccessMessage = null;

    public function mount(JobPosting $job): void
    {
        $user = Auth::user();
        if (! $user || ! $user->isCandidate()) {
            abort(403, 'Unauthorized. Candidate access required.');
        }

        $this->job = $job->load(['employerProfile', 'accommodations']);

        $profile = $user->candidateProfile;
        if (! $profile || empty($user->pwd_id_number)) {
            $this->missingPwdId = true;
        }

        if ($profile) {
            $this->existingApplication = Application::where('candidate_profile_id', $profile->id)
                ->where('job_posting_id', $this->job->id)
                ->first();

            if ($this->existingApplication) {
                $this->alreadyApplied = true;
                $this->referenceNumber = $this->existingApplication->id;
                $this->formattedReference = 'PWD-APP-' . date('Y') . '-' . str_pad((string) $this->existingApplication->id, 5, '0', STR_PAD_LEFT);
            }
        }
    }

    public function appendPhrase(string $phrase): void
    {
        $this->coverLetter = trim($this->coverLetter . ' ' . $phrase);
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
            $this->alreadyApplied = true;
            $this->applicationSuccessMessage = 'You have already applied to this job.';
            $this->submitted = true;
            $this->referenceNumber = $existing->id;
            $this->formattedReference = 'PWD-APP-' . date('Y') . '-' . str_pad((string) $existing->id, 5, '0', STR_PAD_LEFT);

            return;
        }

        if ($this->pwdIdFile) {
            $pwdIdPath = $this->pwdIdFile->store('applications/pwd_ids', 'private');
            $user->update(['pwd_id_path' => $pwdIdPath]);
        }

        if ($this->resumeFile) {
            $resumePath = $this->resumeFile->store('applications/resumes', 'private');
            $profile->update(['resume_pdf_path' => $resumePath]);
        }

        $fslPath = null;
        if ($this->fslVideoFile) {
            $fslPath = $this->fslVideoFile->store('applications/fsl', 'private');
        } elseif ($this->useProfileFsl && $profile->fsl_video_path) {
            $fslPath = $profile->fsl_video_path;
        }

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
        $this->formattedReference = 'PWD-APP-' . date('Y') . '-' . str_pad((string) $application->id, 5, '0', STR_PAD_LEFT);
        $this->submitted = true;
    }

    public function render(): View
    {
        $user = Auth::user();
        $candidateProfile = $user?->candidateProfile;

        return view('livewire.candidate.apply', [
            'candidateProfile' => $candidateProfile,
            'user' => $user,
        ]);
    }
}
