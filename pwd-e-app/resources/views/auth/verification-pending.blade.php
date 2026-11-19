<x-layouts::auth :title="__('Account Verification Status')">
    @php
        $latestRequest = auth()->user()?->verificationRequests()->latest()->first();
        $status = $latestRequest?->status ?? auth()->user()?->status ?? 'pending';
        $reason = $latestRequest?->rejection_reason;
    @endphp

    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Account Verification Status')" :description="__('Your account credentials and statutory accreditation documents are reviewed by the PESO / LGU administrator under RA 10524.')" />

        @if($status === 'rejected')
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-900" role="alert">
                <div class="flex items-center gap-2 font-bold text-red-800">
                    <svg class="size-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <span>Status: Verification Rejected</span>
                </div>
                @if($reason)
                    <p class="mt-2 text-xs text-red-700 bg-white/80 p-2.5 rounded-lg border border-red-200">
                        <strong>Reason provided:</strong> {{ $reason }}
                    </p>
                @endif
                <p class="mt-2 text-xs text-red-700">Please contact your local PESO administrator or support desk for re-evaluation.</p>
            </div>
        @elseif($status === 'needs_more_docs')
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900" role="alert">
                <div class="flex items-center gap-2 font-bold text-amber-800">
                    <svg class="size-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span>Status: Additional Documentation Requested</span>
                </div>
                @if($reason)
                    <p class="mt-2 text-xs text-amber-800 bg-white/80 p-2.5 rounded-lg border border-amber-200">
                        <strong>Administrator Note:</strong> {{ $reason }}
                    </p>
                @endif
                <p class="mt-2 text-xs text-amber-700">The reviewing administrator requested clearer proof of accreditation or identification.</p>
            </div>
        @else
            <div class="rounded-xl border border-primary-border bg-primary-tint px-4 py-3.5 text-sm text-primary-dark" role="status" aria-live="polite">
                <div class="flex items-center gap-2 font-bold">
                    <svg class="size-4 animate-spin text-primary shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span>Status: Pending Review (Under RA 10524 Verification)</span>
                </div>
                <p class="mt-1.5 text-xs text-body-muted">Your registration documents are queued with the PESO / LGU administrator. You can sign in and explore public guides, but portal actions remain protected until verification is confirmed.</p>
            </div>
        @endif

        <div class="flex flex-col gap-3">
            <a href="{{ route('home') }}" class="inline-flex h-11 items-center justify-center rounded-lg bg-primary-dark text-white font-semibold hover:bg-primary transition-colors">Back to Home</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex h-11 w-full items-center justify-center rounded-lg border border-primary-border text-primary-dark font-semibold hover:bg-primary-tint transition-colors cursor-pointer">Sign out</button>
            </form>
        </div>
    </div>
</x-layouts::auth>