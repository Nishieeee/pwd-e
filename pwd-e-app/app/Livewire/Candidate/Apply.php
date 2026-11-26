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

    public $medicalCertificateFile = null;

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

        if ($job->status !== 'published') {
            abort(403, 'This job posting is not open for applications.');
        }

        if ($job->closes_at && $job->closes_at->isPast()) {
            abort(403, 'The application deadline for this position has passed.');
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

            $activeApp = Application::where('candidate_profile_id', $profile->id)
                ->where('job_posting_id', $this->job->id)
                ->whereNotIn('status', ['withdrawn', 'closed', 'rejected'])
                ->latest()
                ->first();

            if ($activeApp) {
                // If previously hired, check if candidate has completed separation
                if ($activeApp->status === 'hired') {
                    $workforceRecord = \App\Models\WorkforceRecord::where('application_id', $activeApp->id)->first();
                    if ($workforceRecord && $workforceRecord->separation_status === 'acknowledged') {
                        // Separated candidate: reapplication to an open job is permitted!
                        $this->existingApplication = null;
                        $this->alreadyApplied = false;
                    } else {
                        // Still active or in dispute/notice: block reapplication
                        $this->existingApplication = $activeApp;
                        $this->alreadyApplied = true;
                        $this->referenceNumber = $activeApp->id;
                        $this->formattedReference = 'PWD-APP-' . date('Y') . '-' . str_pad((string) $activeApp->id, 5, '0', STR_PAD_LEFT);
                    }
                } else {
                    $this->existingApplication = $activeApp;
                    $this->alreadyApplied = true;
                    $this->referenceNumber = $activeApp->id;
                    $this->formattedReference = 'PWD-APP-' . date('Y') . '-' . str_pad((string) $activeApp->id, 5, '0', STR_PAD_LEFT);
                }
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

        if ($this->job->status !== 'published') {
            $this->addError('materials', 'This job posting is no longer accepting applications.');
            return;
        }

        if ($this->job->closes_at && $this->job->closes_at->isPast()) {
            $this->addError('materials', 'The application deadline for this position has passed.');
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
            'medicalCertificateFile' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
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

        // Active duplicate check
        $activeExisting = Application::where('candidate_profile_id', $profile->id)
            ->where('job_posting_id', $this->job->id)
            ->whereNotIn('status', ['withdrawn', 'closed', 'rejected'])
            ->latest()
            ->first();

        if ($activeExisting) {
            if ($activeExisting->status === 'hired') {
                $workforceRecord = \App\Models\WorkforceRecord::where('application_id', $activeExisting->id)->first();
                if (! $workforceRecord || $workforceRecord->separation_status !== 'acknowledged') {
                    $this->alreadyApplied = true;
                    $this->applicationSuccessMessage = 'You are currently employed or under active separation review for this position.';
                    $this->submitted = true;
                    $this->referenceNumber = $activeExisting->id;
                    $this->formattedReference = 'PWD-APP-' . date('Y') . '-' . str_pad((string) $activeExisting->id, 5, '0', STR_PAD_LEFT);

                    return;
                }
            } else {
                $this->alreadyApplied = true;
                $this->applicationSuccessMessage = 'You already have an active application under review for this job.';
                $this->submitted = true;
                $this->referenceNumber = $activeExisting->id;
                $this->formattedReference = 'PWD-APP-' . date('Y') . '-' . str_pad((string) $activeExisting->id, 5, '0', STR_PAD_LEFT);

                return;
            }
        }

        // If candidate previously withdrew, was closed, or was separated from this job, link the previous application ID
        $previousApp = Application::where('candidate_profile_id', $profile->id)
            ->where('job_posting_id', $this->job->id)
            ->latest()
            ->first();

        if ($this->pwdIdFile) {
            $pwdIdPath = $this->pwdIdFile->store('applications/pwd_ids', 'private');
            $newDocType = ($user->medical_certificate_path || $this->medicalCertificateFile || $user->pwd_document_type === 'medical_certificate')
                ? 'both'
                : ($user->pwd_document_type ?: 'pwd_id');
            $user->update([
                'pwd_id_path' => $pwdIdPath,
                'pwd_document_type' => $newDocType,
            ]);
        }

        if ($this->medicalCertificateFile) {
            $medCertPath = $this->medicalCertificateFile->store('applications/medical_certificates', 'private');
            $newDocType = ($user->pwd_id_path || $this->pwdIdFile || $user->pwd_document_type === 'pwd_id')
                ? 'both'
                : ($user->pwd_document_type ?: 'medical_certificate');
            $user->update([
                'medical_certificate_path' => $medCertPath,
                'pwd_document_type' => $newDocType,
            ]);
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
        if ($match) {
            $fitScore = (float) round($match->total_score * 100, 2);
        } else {
            try {
                $scorer = app(\App\Services\Matching\MatchScorer::class);
                $scores = $scorer->score($profile->id, $this->job->id);
                $fitScore = (float) round(($scores['total_score'] ?? 0.0) * 100, 2);
            } catch (\Throwable $e) {
                $fitScore = null;
            }
        }

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
            'reapplied_from_id' => $previousApp?->id,
            'status' => 'applied',
            'status_subtext' => Application::computeStatusSubtext(Application::STATUS_APPLIED),
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
