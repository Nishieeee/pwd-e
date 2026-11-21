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

    public string $coverLetterType = 'text';

    public $coverLetterFile = null;

    public $pwdIdFile = null;

    public $resumeFile = null;

    public $fslVideoFile = null;

    public bool $useProfileResume = true;

    public bool $useProfilePwdId = true;

    public bool $useProfileFsl = true;

    public bool $includeBiodata = true;

    public bool $includeResume = false;

    public bool $includeFslVideo = false;

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
            $this->includeBiodata = true;
            if ($profile->resume_pdf_path) {
                $this->includeResume = true;
            }
            if ($profile->fsl_video_path) {
                $this->includeFslVideo = true;
            }

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

    public function toggleBiodata(): void
    {
        if ($this->includeBiodata && ! $this->includeResume && ! $this->includeFslVideo) {
            $this->addError('materials', 'You must include at least one application format (Bio-Data, Résumé, or FSL Video).');
            return;
        }
        $this->resetErrorBag('materials');
        $this->includeBiodata = ! $this->includeBiodata;
    }

    public function toggleResume(): void
    {
        if ($this->includeResume && ! $this->includeBiodata && ! $this->includeFslVideo) {
            $this->addError('materials', 'You must include at least one application format (Bio-Data, Résumé, or FSL Video).');
            return;
        }
        $this->resetErrorBag('materials');
        $this->includeResume = ! $this->includeResume;
    }

    public function toggleFslVideo(): void
    {
        if ($this->includeFslVideo && ! $this->includeBiodata && ! $this->includeResume) {
            $this->addError('materials', 'You must include at least one application format (Bio-Data, Résumé, or FSL Video).');
            return;
        }
        $this->resetErrorBag('materials');
        $this->includeFslVideo = ! $this->includeFslVideo;
    }

    public function appendPhrase(string $phrase): void
    {
        $this->coverLetter = trim($this->coverLetter . ' ' . $phrase);
    }

    public function setCoverLetterType(string $type): void
    {
        $this->coverLetterType = in_array($type, ['text', 'upload'], true) ? $type : 'text';
    }

    public function removeCoverLetterFile(): void
    {
        $this->coverLetterFile = null;
        $this->resetErrorBag('coverLetterFile');
    }

    public function submit(): void
    {
        if ($this->missingPwdId) {
            return;
        }

        if (! $this->includeBiodata && ! $this->includeResume && ! $this->includeFslVideo) {
            $this->addError('materials', 'Please select at least one application format (Bio-Data, Résumé, or FSL Video).');
            return;
        }

        $this->validate([
            'coverLetter' => ['nullable', 'string', 'max:5000'],
            'coverLetterFile' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'pwdIdFile' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'resumeFile' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'fslVideoFile' => ['nullable', 'file', 'mimes:mp4,mov,webm', 'max:51200'],
        ]);

        $user = Auth::user();
        $profile = $user->candidateProfile;

        if ($this->includeResume && ! $this->resumeFile && ! $profile->resume_pdf_path) {
            $this->addError('resumeFile', 'Please upload a PDF résumé or uncheck the Résumé option.');
            return;
        }

        if ($this->includeFslVideo && ! $this->fslVideoFile && ! $profile->fsl_video_path) {
            $this->addError('fslVideoFile', 'Please upload an FSL video introduction or uncheck the FSL option.');
            return;
        }

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

        $resumePath = null;
        if ($this->includeResume) {
            if ($this->resumeFile) {
                $resumePath = $this->resumeFile->store('applications/resumes', 'private');
                $profile->update(['resume_pdf_path' => $resumePath]);
            } else {
                $resumePath = $profile->resume_pdf_path;
            }
        }

        $fslPath = null;
        if ($this->includeFslVideo) {
            if ($this->fslVideoFile) {
                $fslPath = $this->fslVideoFile->store('applications/fsl', 'private');
                $profile->update(['fsl_video_path' => $fslPath]);
            } else {
                $fslPath = $profile->fsl_video_path;
            }
        }

        $match = $profile->jobMatches()->where('job_posting_id', $this->job->id)->first();
        $fitScore = $match ? (float) round($match->total_score * 100, 2) : 95.00;

        $coverLetterPdfPath = null;
        if ($this->coverLetterFile) {
            $coverLetterPdfPath = $this->coverLetterFile->store('applications/cover_letters', 'private');
        }

        $materials = [
            'biodata' => $this->includeBiodata,
            'resume' => $this->includeResume,
            'fsl_video' => $this->includeFslVideo,
        ];

        $application = Application::create([
            'candidate_profile_id' => $profile->id,
            'job_posting_id' => $this->job->id,
            'status' => 'applied',
            'status_subtext' => 'Application submitted and under review',
            'cover_letter' => $this->coverLetter ?: null,
            'cover_letter_pdf_path' => $coverLetterPdfPath,
            'fsl_video_path' => $fslPath,
            'resume_path' => $resumePath,
            'application_materials' => $materials,
            'fit_score_at_apply' => $fitScore,
            'applied_at' => now(),
        ]);

        // Auto-accept pending job invitation for this role if one existed
        \App\Models\JobInvitation::where('candidate_profile_id', $profile->id)
            ->where('job_posting_id', $this->job->id)
            ->where('status', 'pending')
            ->update(['status' => 'accepted']);

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
