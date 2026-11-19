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

        if (! $verificationRequest->document_path) {
            abort(404);
        }

        return Storage::disk('private')->response($verificationRequest->document_path);
    }
}
