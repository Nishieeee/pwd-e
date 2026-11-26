<?php

namespace App\Http\Controllers\Admin;

use App\Models\VerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VerificationDocumentController
{
    public function show(Request $request, VerificationRequest $verificationRequest)
    {
        $user = $request->user();
        if (! $user || ! $user->isAdmin()) {
            abort(403);
        }

        $path = $request->query('doc') === 'secondary'
            ? $verificationRequest->secondary_document_path
            : ($verificationRequest->document_path ?: $verificationRequest->secondary_document_path);

        if (! $path || ! Storage::disk('private')->exists($path)) {
            abort(404);
        }

        return Storage::disk('private')->response($path);
    }
}
