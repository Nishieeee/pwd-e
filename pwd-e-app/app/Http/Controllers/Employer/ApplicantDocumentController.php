<?php

namespace App\Http\Controllers\Employer;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApplicantDocumentController
{
    public function show(Request $request, Application $application, string $type)
    {
        $user = $request->user();
        if (! $user || ! $user->isEmployer()) {
            abort(403);
        }

        $job = $application->jobPosting;
        if (! $job || $job->employer_profile_id !== $user->employerProfile?->id) {
            abort(403);
        }

        $path = match ($type) {
            'resume' => $application->resume_path ?: $application->candidateProfile?->resume_pdf_path,
            'fsl' => $application->fsl_video_path ?: $application->candidateProfile?->fsl_video_path,
            'cover_letter' => $application->cover_letter_pdf_path,
            'pwd_id' => $application->candidateProfile?->user?->pwd_id_path,
            'medical_certificate' => $application->candidateProfile?->user?->medical_certificate_path,
            'requested' => $this->getRequestedDocPath($application, $request->query('doc_id')),
            default => $this->getRequestedDocPath($application, $type),
        };

        if (! $path) {
            abort(404);
        }

        if (Storage::disk('private')->exists($path)) {
            return Storage::disk('private')->response($path);
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->response($path);
        }

        abort(404);
    }

    public function showCandidateDocument(Request $request, Application $application, string $type)
    {
        $user = $request->user();
        if (! $user || ! $user->isCandidate()) {
            abort(403);
        }

        if ($application->candidate_profile_id !== $user->candidateProfile?->id) {
            abort(403);
        }

        $path = match ($type) {
            'resume' => $application->resume_path ?: $application->candidateProfile?->resume_pdf_path,
            'fsl' => $application->fsl_video_path ?: $application->candidateProfile?->fsl_video_path,
            'cover_letter' => $application->cover_letter_pdf_path,
            'requested' => $this->getRequestedDocPath($application, $request->query('doc_id')),
            default => $this->getRequestedDocPath($application, $type),
        };

        if (! $path) {
            abort(404);
        }

        if (Storage::disk('private')->exists($path)) {
            return Storage::disk('private')->response($path);
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->response($path);
        }

        abort(404);
    }

    private function getRequestedDocPath(Application $application, ?string $docId): ?string
    {
        if (! $docId || empty($application->requested_documents)) {
            return null;
        }

        $doc = collect($application->requested_documents)->first(function ($d) use ($docId) {
            return ($d['id'] ?? null) === $docId || ($d['name'] ?? null) === $docId;
        });

        return $doc['file_path'] ?? null;
    }
}
