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
            'resume' => $application->candidateProfile?->resume_pdf_path,
            'fsl' => $application->fsl_video_path ?: $application->candidateProfile?->fsl_video_path,
            default => null,
        };

        if (! $path || ! Storage::disk('private')->exists($path)) {
            abort(404);
        }

        return Storage::disk('private')->response($path);
    }
}
