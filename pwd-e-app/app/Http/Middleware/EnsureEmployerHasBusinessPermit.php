<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployerHasBusinessPermit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->isAdmin()) {
            return $next($request);
        }

        if (! $user->isEmployer()) {
            abort(403, 'Unauthorized. Employer access required.');
        }

        $profile = $user->employerProfile;
        $hasPermitNumber = ! empty($profile?->business_permit_number) || ! empty($profile?->business_permit_path);
        $hasApprovedRequest = $user->verificationRequests()->where('status', 'approved')->exists();

        if (! $user->isVerified() || (! $hasPermitNumber && ! $hasApprovedRequest)) {
            abort(403, 'Business permit verification is required to manage job postings.');
        }

        return $next($request);
    }
}
