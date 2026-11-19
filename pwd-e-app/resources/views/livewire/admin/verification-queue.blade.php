<div class="space-y-6">
    <!-- Header Banner -->
    <div class="relative overflow-hidden rounded-2xl border border-primary-border bg-white p-6 sm:p-8 shadow-sm" style="background: linear-gradient(135deg, #DCEAF4 0%, #FFFFFF 100%); box-shadow: 0 4px 20px rgba(26,40,64,0.06);">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-2 max-w-2xl">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-primary-border bg-white px-3 py-1 text-xs font-bold text-secondary shadow-xs">
                    <svg class="size-3.5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    {{ __('Identity & Accreditation Verification') }}
                </span>

                <h1 class="font-heading text-2xl sm:text-3xl font-bold text-primary-dark">
                    {{ __('Verification Queue') }}
                </h1>

                <p class="text-sm sm:text-base text-body-muted leading-relaxed">
                    {{ __('Review government-issued PWD IDs and employer business registration documents to maintain platform integrity.') }}
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-primary-border bg-white px-4 py-2.5 text-sm font-semibold text-primary-dark shadow-xs transition hover:bg-primary-tint"
                >
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('Back to Dashboard') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex flex-wrap gap-2.5" role="tablist" aria-label="{{ __('Filter requests by status') }}">
        @foreach (['pending', 'needs_more_docs', 'approved', 'rejected', 'all'] as $s)
            @php
                $isActive = $status === $s;
                $count = $counts[$s] ?? 0;
            @endphp
            <button
                type="button"
                wire:click="setStatusFilter('{{ $s }}')"
                class="inline-flex items-center gap-2 px-4 py-2.5 min-h-[44px] rounded-xl text-sm font-semibold transition-all focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 {{ $isActive ? 'bg-primary-dark text-white shadow-sm' : 'border border-primary-border bg-white text-primary-dark hover:bg-primary-tint' }}"
                aria-pressed="{{ $isActive ? 'true' : 'false' }}"
                role="tab"
            >
                <span>{{ ucfirst(str_replace('_', ' ', $s)) }}</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $isActive ? 'bg-white/20 text-white' : 'bg-primary-tint text-primary-dark' }}">
                    {{ $count }}
                </span>
            </button>
        @endforeach
    </div>

    <!-- Request Cards Stream -->
    @if ($requests->isEmpty())
        <div class="rounded-2xl border border-primary-border bg-white p-12 text-center shadow-sm">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-primary-tint text-primary">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="mt-4 font-heading text-lg font-bold text-primary-dark">{{ __('No verification requests found') }}</h3>
            <p class="mt-1 text-sm text-body-muted">
                {{ __('There are no records matching the selected status filter.') }}
            </p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($requests as $vr)
                <article class="rounded-2xl border border-primary-border bg-white p-6 shadow-sm transition hover:border-primary/50">
                    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-gray-100">
                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-full bg-[#244F73] text-white flex items-center justify-center font-bold text-sm shadow-xs shrink-0">
                                {{ strtoupper(substr($vr->user?->name ?? 'U', 0, 2)) }}
                            </div>
                            <div>
                                <h2 class="font-heading text-base sm:text-lg font-bold text-primary-dark flex items-center gap-2">
                                    <span>{{ $vr->user->name ?? 'User #'.$vr->user_id }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $vr->user?->role === 'employer' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                        {{ ucfirst($vr->user?->role ?? 'user') }}
                                    </span>
                                </h2>
                                <p class="text-xs sm:text-sm text-body-muted mt-0.5">{{ $vr->user?->email ?? 'No email available' }}</p>
                            </div>
                        </div>

                        <div>
                            @if ($vr->status === 'approved')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-secondary-tint text-secondary border border-emerald-200">
                                    <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    {{ __('Approved') }}
                                </span>
                            @elseif ($vr->status === 'rejected')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                    <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    {{ __('Rejected') }}
                                </span>
                            @elseif ($vr->status === 'needs_more_docs')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-sky-50 text-sky-800 border border-sky-200">
                                    <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    {{ __('Needs More Docs') }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/></svg>
                                    {{ __('Pending Review') }}
                                </span>
                            @endif
                        </div>
                    </header>

                    <!-- Document & Applicant Metadata -->
                    <dl class="grid grid-cols-2 gap-4 sm:grid-cols-4 py-4 text-sm">
                        <div class="space-y-1">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-body-muted">{{ __('Document Type') }}</dt>
                            <dd class="font-semibold text-primary-dark">{{ ucwords(str_replace('_', ' ', $vr->type)) }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-body-muted">{{ __('ID / Reference Number') }}</dt>
                            <dd class="font-semibold text-primary-dark font-mono">{{ $vr->id_number ?: '—' }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-body-muted">{{ __('Date Submitted') }}</dt>
                            <dd class="text-primary-dark tabular-nums">{{ $vr->created_at?->format('M d, Y H:i') ?? '—' }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-body-muted">{{ __('Last Review') }}</dt>
                            <dd class="text-primary-dark tabular-nums">{{ $vr->reviewed_at?->format('M d, Y H:i') ?? 'Pending review' }}</dd>
                        </div>
                    </dl>

                    <!-- Document Attachment Link -->
                    @if ($vr->document_path)
                        <div class="my-2 p-3 rounded-xl bg-surface border border-primary-border flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-white border border-primary-border text-primary shadow-2xs">
                                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold text-primary-dark">{{ __('Attached Verification File') }}</div>
                                    <div class="text-[11px] text-body-muted">{{ __('Secure private document link') }}</div>
                                </div>
                            </div>
                            <a
                                href="{{ route('admin.verifications.document', $vr) }}"
                                target="_blank"
                                rel="noopener"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-primary-dark px-3.5 py-1.5 text-xs font-semibold text-white transition hover:bg-primary"
                            >
                                <span>{{ __('Open Document') }}</span>
                                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </div>
                    @endif

                    <!-- Reviewer Rejection / Feedback Note Callout -->
                    @if ($vr->rejection_reason)
                        <div class="my-3 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs sm:text-sm text-rose-900 flex items-start gap-2.5">
                            <svg class="size-4 text-rose-700 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <span class="font-bold">{{ __('Reviewer Feedback:') }}</span>
                                <span class="ml-1">{{ $vr->rejection_reason }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    @if (in_array($vr->status, ['pending', 'needs_more_docs']))
                        <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap items-center gap-3">
                            <button
                                type="button"
                                wire:click="approve({{ $vr->id }})"
                                class="inline-flex items-center gap-2 rounded-lg bg-secondary px-5 py-2.5 min-h-[44px] text-sm font-semibold text-white shadow-xs transition hover:bg-secondary/90 focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-offset-2"
                            >
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                {{ __('Approve Verification') }}
                            </button>

                            <button
                                type="button"
                                wire:click="openReview({{ $vr->id }})"
                                class="inline-flex items-center gap-2 rounded-lg border border-primary-border bg-white px-5 py-2.5 min-h-[44px] text-sm font-semibold text-primary-dark shadow-xs transition hover:bg-primary-tint focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                            >
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                {{ __('Provide Review Feedback') }}
                            </button>
                        </div>

                        <!-- Review Action Form Drawer -->
                        @if ($reviewingId === $vr->id)
                            <div class="mt-4 rounded-xl border border-primary-border bg-surface p-4 sm:p-5 space-y-3">
                                <div>
                                    <label for="note-{{ $vr->id }}" class="block text-xs sm:text-sm font-bold text-primary-dark">
                                        {{ __('Feedback / Request Details for Applicant') }}
                                        <span class="text-rose-600">*</span>
                                    </label>
                                    <p class="text-xs text-body-muted mt-0.5">
                                        {{ __('Explain what is missing (e.g. blurred photo, expired ID, invalid registration) or reason for rejection.') }}
                                    </p>
                                </div>

                                <textarea
                                    id="note-{{ $vr->id }}"
                                    wire:model="reviewNote"
                                    rows="3"
                                    class="w-full rounded-xl border border-primary-border bg-white p-3 text-sm text-primary-dark placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary focus:outline-none"
                                    placeholder="{{ __('Enter clear instructions or the rejection rationale here...') }}"
                                ></textarea>

                                <div class="flex flex-wrap items-center gap-2.5 pt-2">
                                    <button
                                        type="button"
                                        wire:click="requestMoreDocs({{ $vr->id }})"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-amber-600 px-4 py-2 min-h-[44px] text-xs sm:text-sm font-semibold text-white shadow-xs transition hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-600 focus:ring-offset-2"
                                    >
                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        {{ __('Request More Documents') }}
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="reject({{ $vr->id }})"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-rose-600 px-4 py-2 min-h-[44px] text-xs sm:text-sm font-semibold text-white shadow-xs transition hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600 focus:ring-offset-2"
                                    >
                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        {{ __('Reject Request') }}
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="closeReview"
                                        class="inline-flex items-center rounded-lg border border-primary-border bg-white px-4 py-2 min-h-[44px] text-xs sm:text-sm font-semibold text-primary-dark hover:bg-primary-tint"
                                    >
                                        {{ __('Cancel') }}
                                    </button>
                                </div>
                            </div>
                        @endif
                    @endif
                </article>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $requests->links() }}
        </div>
    @endif
</div>
