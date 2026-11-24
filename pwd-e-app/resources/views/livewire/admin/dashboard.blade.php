<div class="space-y-6">
    <!-- Hero Banner -->
    <div class="relative overflow-hidden rounded-2xl border border-primary-border bg-white p-6 sm:p-8 shadow-sm" style="background: linear-gradient(135deg, #DCEAF4 0%, #FFFFFF 100%); box-shadow: 0 4px 20px rgba(26,40,64,0.06);">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-3 max-w-2xl">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-primary-border bg-white px-3 py-1 text-xs font-bold text-secondary shadow-xs">
                    <svg class="size-3.5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    {{ __('Platform Administration') }}
                </span>

                <h1 class="font-heading text-2xl sm:text-3xl font-bold text-primary-dark">
                    {{ __('Admin Dashboard') }}
                </h1>

                <p class="text-sm sm:text-base text-body-muted leading-relaxed">
                    {{ __('Monitor platform activity, verify candidate & employer accounts, and safeguard inclusive employment standards.') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <a
                    href="{{ route('admin.verifications.index') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary-dark px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                >
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ __('Verification Queue') }}
                    <span class="ml-1 rounded-full bg-white/20 px-2 py-0.5 text-xs font-bold">{{ $stats['pending_verifications'] }}</span>
                </a>

                <a
                    href="{{ route('admin.users') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-primary-border bg-white px-5 py-3 text-sm font-semibold text-primary-dark shadow-xs transition hover:bg-primary-tint focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                >
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    {{ __('Manage Users') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-2xl border border-primary-border bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-body-muted">{{ __('Total Users') }}</p>
            <p class="mt-2 text-3xl font-bold text-primary-dark tabular-nums">{{ $stats['total_users'] }}</p>
        </div>
        <div class="rounded-2xl border border-primary-border bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-body-muted">{{ __('Applications / 7 days') }}</p>
            <p class="mt-2 text-3xl font-bold text-primary-dark tabular-nums">{{ $stats['applications_this_week'] }}</p>
        </div>
        <div class="rounded-2xl border border-primary-border bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-body-muted">{{ __('Active Postings') }}</p>
            <p class="mt-2 text-3xl font-bold text-primary-dark tabular-nums">{{ $stats['active_postings'] }}</p>
        </div>
        <div class="rounded-2xl border border-secondary bg-secondary-tint p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-secondary">{{ __('Pending Verifications') }}</p>
            <p class="mt-2 text-3xl font-bold text-secondary tabular-nums">{{ $stats['pending_verifications'] }}</p>
        </div>
    </div>

    @if(($stats['disputed_separations'] ?? 0) > 0)
        <!-- Contested Employment Separations Compliance Alert -->
        <div class="p-4 sm:p-5 rounded-2xl bg-red-50 border-2 border-red-300 text-red-950 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs" role="alert">
            <div class="flex items-start gap-3">
                <div class="size-9 rounded-xl bg-red-100 border border-red-200 text-red-700 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div class="text-xs leading-relaxed">
                    <strong class="block font-bold text-sm text-red-950">{{ __(':count PWD Employment Separation(s) Contested by Candidates', ['count' => $stats['disputed_separations']]) }}</strong>
                    <span class="text-red-900">{{ __('Employees have filed formal disputes against separation notices under RA 7277 (Magna Carta for Disabled Persons). Tax deductions for contested records are held pending resolution.') }}</span>
                </div>
            </div>
            <a href="{{ route('admin.users') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-red-600 text-white text-xs font-bold hover:bg-red-700 transition-colors shadow-xs shrink-0">
                <span>{{ __('Review in Compliance') }}</span>
                <span>&rarr;</span>
            </a>
        </div>
    @endif

    <!-- Pending Verification Approvals & Bulk Actions Section -->
    <section class="rounded-2xl border border-primary-border bg-white shadow-sm overflow-hidden" aria-labelledby="verification-approvals-heading">
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gradient-to-r from-surface to-white">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center size-6 rounded-lg bg-secondary-tint text-secondary">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </span>
                    <h2 id="verification-approvals-heading" class="font-heading text-lg font-bold text-primary-dark">
                        {{ __('Pending Verification Approvals') }}
                    </h2>
                    <span class="rounded-full bg-secondary-tint text-secondary px-2.5 py-0.5 text-xs font-bold border border-secondary/20">
                        {{ $pendingCounts['all'] }}
                    </span>
                </div>
                <p class="text-xs text-body-muted mt-1">
                    {{ __('Review and multi-approve or multi-reject candidate clinical medical certificates and PWD credentials.') }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.verifications.index') }}" class="text-xs font-semibold text-primary hover:text-primary-dark inline-flex items-center gap-1">
                    <span>{{ __('Open Full Queue') }}</span>
                    <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>

        <!-- Categorization & Bulk Actions Toolbar -->
        <div class="p-4 sm:px-6 bg-surface/50 border-b border-primary-border flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <!-- Category Pills -->
            <div class="flex flex-wrap items-center gap-2" role="group" aria-label="{{ __('Filter by account category') }}">
                <span class="text-xs font-bold text-body-muted mr-1">{{ __('Categorize:') }}</span>
                <button type="button"
                        wire:click="setCategory('all')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $category === 'all' ? 'bg-primary-dark text-white shadow-2xs' : 'bg-white border border-primary-border text-primary-dark hover:bg-primary-tint' }}">
                    <span>{{ __('All Pending') }}</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $category === 'all' ? 'bg-white/20 text-white' : 'bg-primary-tint text-primary-dark font-bold' }}">{{ $pendingCounts['all'] }}</span>
                </button>

                <button type="button"
                        wire:click="setCategory('pwd')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $category === 'pwd' ? 'bg-emerald-700 text-white shadow-2xs' : 'bg-white border border-emerald-300 text-emerald-800 hover:bg-emerald-50' }}">
                    <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>{{ __('PWD Candidates') }}</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $category === 'pwd' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-900 font-bold' }}">{{ $pendingCounts['pwd'] }}</span>
                </button>

                @if (($pendingCounts['employer'] ?? 0) > 0)
                <button type="button"
                        wire:click="setCategory('employer')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $category === 'employer' ? 'bg-indigo-700 text-white shadow-2xs' : 'bg-white border border-indigo-300 text-indigo-800 hover:bg-indigo-50' }}">
                    <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>{{ __('Employers') }}</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $category === 'employer' ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-900 font-bold' }}">{{ $pendingCounts['employer'] }}</span>
                </button>
                @endif
            </div>

            <!-- Bulk Multi-Action Buttons -->
            <div class="flex items-center gap-2">
                @if(count($selectedIds) > 0)
                    <span class="text-xs font-semibold text-primary-dark mr-1">
                        {{ count($selectedIds) }} {{ __('selected') }}
                    </span>

                    <button type="button"
                            wire:click="openBulkApprove"
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-secondary text-white text-xs font-bold hover:bg-secondary/90 shadow-2xs transition-colors">
                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ __('Multi-Approve (:count)', ['count' => count($selectedIds)]) }}</span>
                    </button>

                    <button type="button"
                            wire:click="openBulkReject"
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-rose-600 text-white text-xs font-bold hover:bg-rose-700 shadow-2xs transition-colors">
                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>{{ __('Multi-Reject (:count)', ['count' => count($selectedIds)]) }}</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Verification Table / Stream -->
        @if($pendingVerifications->isEmpty())
            <div class="py-12 px-6 text-center">
                <div class="size-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <p class="font-bold text-primary-dark text-sm">{{ __('Verification queue is clear!') }}</p>
                <p class="text-xs text-body-muted mt-1">{{ __('No pending verifications matching the selected category.') }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-primary-border bg-surface text-body-muted font-bold uppercase tracking-wider">
                            <th class="py-3 px-4 w-10">
                                <input type="checkbox"
                                       wire:model.live="selectAll"
                                       class="size-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer"
                                       title="{{ __('Select all on page') }}" />
                            </th>
                            <th class="py-3 px-4">{{ __('Applicant & Category') }}</th>
                            <th class="py-3 px-4">{{ __('Credentials & Document') }}</th>
                            <th class="py-3 px-4">{{ __('Submitted') }}</th>
                            <th class="py-3 px-4 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($pendingVerifications as $vr)
                            @php
                                $isCandidate = $vr->user?->role === 'candidate';
                                $isSelected = in_array($vr->id, $selectedIds);
                            @endphp
                            <tr class="hover:bg-primary-tint/20 transition-colors {{ $isSelected ? 'bg-primary-tint/30' : '' }}">
                                <td class="py-3.5 px-4">
                                    <input type="checkbox"
                                           wire:click="toggleSelect({{ $vr->id }})"
                                           @checked($isSelected)
                                           class="size-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer" />
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="size-8 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $isCandidate ? 'bg-emerald-100 text-emerald-800' : 'bg-indigo-100 text-indigo-800' }}">
                                            {{ strtoupper(substr($vr->user?->name ?? 'U', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-primary-dark flex items-center gap-1.5">
                                                <span>{{ $vr->user?->name ?? 'User #'.$vr->user_id }}</span>
                                                @if($isCandidate)
                                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        {{ __('PWD Candidate') }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                        {{ __('Employer') }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-[11px] text-body-muted">
                                                {{ $vr->user?->email }}
                                                @if(!$isCandidate && $vr->user?->employerProfile?->company_name)
                                                    · <span class="font-semibold text-primary-dark">{{ $vr->user->employerProfile->company_name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div>
                                        <span class="font-semibold text-primary-dark">{{ ucwords(str_replace('_', ' ', $vr->type)) }}</span>
                                        <span class="text-body-muted font-mono ml-1">({{ $vr->id_number ?: 'No ID #' }})</span>
                                    </div>
                                    @if($vr->document_path)
                                        <a href="{{ route('admin.verifications.document', $vr) }}" target="_blank" rel="noopener" class="text-[11px] font-semibold text-primary hover:underline inline-flex items-center gap-1 mt-0.5">
                                            <span>{{ __('Inspect document') }}</span>
                                            <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-body-muted text-[11px] tabular-nums whitespace-nowrap">
                                    {{ $vr->created_at?->format('M d, Y h:i A') ?? '—' }}
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-1.5">
                                        <button type="button"
                                                wire:click="approveSingle({{ $vr->id }})"
                                                class="px-2.5 py-1 rounded-md bg-secondary text-white text-[11px] font-bold hover:bg-secondary/90 transition-colors shadow-2xs">
                                            {{ __('Approve') }}
                                        </button>

                                        <button type="button"
                                                wire:click="openSingleReview({{ $vr->id }})"
                                                class="px-2.5 py-1 rounded-md border border-rose-200 text-rose-700 bg-white hover:bg-rose-50 text-[11px] font-semibold transition-colors">
                                            {{ __('Reject...') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($pendingVerifications->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $pendingVerifications->links() }}
                </div>
            @endif
        @endif
    </section>

    <!-- Bulk Approve Confirmation Modal -->
    @if($showBulkApproveModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" role="dialog" aria-modal="true" aria-labelledby="bulk-approve-title">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl space-y-4" wire:click.stop>
                <div class="flex items-start justify-between border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="size-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <h3 id="bulk-approve-title" class="font-heading text-base font-bold text-primary-dark">
                                {{ __('Confirm Multi-Approval') }}
                            </h3>
                            <p class="text-xs text-body-muted">{{ __('Review selected applicants before granting verified status.') }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeBulkApprove" class="text-body-muted hover:text-primary-dark text-lg">&times;</button>
                </div>

                <div class="rounded-xl bg-emerald-50/70 border border-emerald-200 p-3.5 text-xs text-emerald-950 flex items-start gap-2.5">
                    <svg class="size-4 text-emerald-700 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <strong class="font-bold">{{ __('Impact Warning:') }}</strong>
                        <span>{{ __('Approving these :count accounts will immediately activate their profiles, grant access to protected platform tools, and dispatch official approval notifications.', ['count' => count($selectedIds)]) }}</span>
                    </div>
                </div>

                <div>
                    <h4 class="text-xs font-bold text-primary-dark mb-2 uppercase tracking-wide text-body-muted">{{ __('Selected Applicants Preview (:count)', ['count' => count($selectedIds)]) }}</h4>
                    <div class="max-h-48 overflow-y-auto space-y-1.5 rounded-xl border border-primary-border p-2 bg-surface">
                        @foreach($this->selectedRequests as $sel)
                            <div class="flex items-center justify-between p-2 rounded-lg bg-white border border-gray-100 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="size-2 rounded-full {{ $sel->user?->role === 'candidate' ? 'bg-emerald-500' : 'bg-indigo-500' }}"></span>
                                    <span class="font-bold text-primary-dark">{{ $sel->user?->name }}</span>
                                    <span class="text-[10px] text-body-muted">({{ $sel->user?->email }})</span>
                                </div>
                                <span class="text-[10px] font-semibold text-primary-dark uppercase font-mono bg-primary-tint px-2 py-0.5 rounded">
                                    {{ $sel->user?->role === 'employer' ? 'Employer' : 'PWD' }} · {{ $sel->type }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" wire:click="closeBulkApprove" class="px-4 py-2 rounded-lg border border-primary-border text-xs font-semibold text-primary-dark hover:bg-primary-tint">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" wire:click="bulkApprove" class="px-5 py-2 rounded-lg bg-secondary text-white text-xs font-bold hover:bg-secondary/90 shadow-xs flex items-center gap-1.5">
                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ __('Confirm Multi-Approve (:count)', ['count' => count($selectedIds)]) }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Bulk Reject Reason Modal -->
    @if($showBulkRejectModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" role="dialog" aria-modal="true" aria-labelledby="bulk-reject-title">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl space-y-4" wire:click.stop>
                <div class="flex items-start justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h3 id="bulk-reject-title" class="font-heading text-base font-bold text-rose-800 flex items-center gap-2">
                            <svg class="size-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            {{ __('Multi-Reject :count Verification Requests', ['count' => count($selectedIds)]) }}
                        </h3>
                        <p class="text-xs text-body-muted mt-0.5">{{ __('Provide feedback that will be sent to all selected applicants.') }}</p>
                    </div>
                    <button type="button" wire:click="closeBulkReject" class="text-body-muted hover:text-primary-dark">&times;</button>
                </div>

                <div>
                    <label class="block text-xs font-bold text-primary-dark mb-1">
                        {{ __('Rejection Reason / Required Correction') }}
                    </label>
                    <textarea wire:model="bulkRejectReason" rows="3" class="w-full rounded-xl border border-primary-border p-3 text-xs text-primary-dark focus:ring-2 focus:ring-primary focus:outline-none" placeholder="{{ __('e.g. Incomplete document submission, invalid government ID format, or expired business registration.') }}"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" wire:click="closeBulkReject" class="px-3.5 py-1.5 rounded-lg border border-primary-border text-xs font-semibold text-primary-dark hover:bg-primary-tint">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" wire:click="bulkReject" class="px-4 py-1.5 rounded-lg bg-rose-600 text-white text-xs font-bold hover:bg-rose-700 shadow-2xs">
                        {{ __('Confirm Multi-Reject') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Single Reject Reason Modal -->
    @if($singleReviewingId !== null)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" role="dialog" aria-modal="true" aria-labelledby="single-reject-title">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl space-y-4" wire:click.stop>
                <div class="flex items-start justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h3 id="single-reject-title" class="font-heading text-base font-bold text-rose-800 flex items-center gap-2">
                            <svg class="size-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            {{ __('Reject Verification Request') }}
                        </h3>
                        <p class="text-xs text-body-muted mt-0.5">{{ __('Enter the specific reason for rejecting this document.') }}</p>
                    </div>
                    <button type="button" wire:click="closeSingleReview" class="text-body-muted hover:text-primary-dark">&times;</button>
                </div>

                <div>
                    <label class="block text-xs font-bold text-primary-dark mb-1">
                        {{ __('Rejection Reason') }}
                    </label>
                    <textarea wire:model="singleReviewNote" rows="3" class="w-full rounded-xl border border-primary-border p-3 text-xs text-primary-dark focus:ring-2 focus:ring-primary focus:outline-none" placeholder="{{ __('e.g. ID photo is unreadable, expired document, or mismatched name.') }}"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" wire:click="closeSingleReview" class="px-3.5 py-1.5 rounded-lg border border-primary-border text-xs font-semibold text-primary-dark hover:bg-primary-tint">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" wire:click="rejectSingle({{ $singleReviewingId }})" class="px-4 py-1.5 rounded-lg bg-rose-600 text-white text-xs font-bold hover:bg-rose-700 shadow-2xs">
                        {{ __('Reject Request') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Analytics & Details Grid -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Users by role -->
        <div class="rounded-2xl border border-primary-border bg-white p-6 shadow-sm">
            <h2 class="font-heading text-lg font-bold text-primary-dark mb-4">{{ __('Users by Role') }}</h2>
            <dl class="space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                    <dt class="text-sm text-body-muted flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-sky-600"></span>
                        {{ __('Candidates') }}
                    </dt>
                    <dd class="text-lg font-bold text-primary-dark tabular-nums">{{ $usersByRole['candidates'] }}</dd>
                </div>
                <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                    <dt class="text-sm text-body-muted flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                        {{ __('Employers') }}
                    </dt>
                    <dd class="text-lg font-bold text-primary-dark tabular-nums">{{ $usersByRole['employers'] }}</dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-sm text-body-muted flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-primary-dark"></span>
                        {{ __('Admins') }}
                    </dt>
                    <dd class="text-lg font-bold text-primary-dark tabular-nums">{{ $usersByRole['admins'] }}</dd>
                </div>
            </dl>
        </div>

        <!-- Top employers -->
        <div class="lg:col-span-2 rounded-2xl border border-primary-border bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-heading text-lg font-bold text-primary-dark">{{ __('Top Employers by Postings') }}</h2>
                <a href="{{ route('admin.users', ['role' => 'employer']) }}" class="text-xs font-semibold text-primary hover:text-primary-dark">
                    {{ __('View all employers') }} &rarr;
                </a>
            </div>
            @if ($topEmployers->isEmpty())
                <p class="text-sm text-body-muted py-6 text-center">{{ __('No employer job postings recorded yet.') }}</p>
            @else
                <ul class="divide-y divide-primary-border/60">
                    @foreach ($topEmployers as $profile)
                        <li class="flex items-center justify-between py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary-tint text-primary-dark flex items-center justify-center font-bold text-xs">
                                    {{ substr($profile->company_name, 0, 2) }}
                                </div>
                                <span class="text-sm font-semibold text-primary-dark">{{ $profile->company_name }}</span>
                            </div>
                            <span class="rounded-full bg-primary-tint px-3 py-1 text-xs font-semibold text-primary-dark">
                                {{ $profile->job_postings_count }} {{ __('postings') }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <!-- Platform Modules Grid -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div class="rounded-2xl border border-primary-border bg-white p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 text-primary font-semibold text-sm mb-2">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span>{{ __('Activity Audit Trail') }}</span>
                </div>
                <h3 class="font-heading text-lg font-bold text-primary-dark">{{ __('Compliance & Activity Log') }}</h3>
                <p class="mt-2 text-sm text-body-muted leading-relaxed">
                    {{ __('Audit events, user modifications, and statutory actions captured across candidate, employer, and administrative workflows.') }}
                </p>
            </div>
            <div class="mt-6">
                <a href="{{ route('admin.audit-log') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary hover:text-primary-dark">
                    <span>{{ __('Inspect audit records') }}</span>
                    <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>

        <div class="rounded-2xl border border-primary-border bg-white p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 text-secondary font-semibold text-sm mb-2">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>{{ __('Inclusive Vocabulary Standards') }}</span>
                </div>
                <h3 class="font-heading text-lg font-bold text-primary-dark">{{ __('Inclusive Language Lexicon') }}</h3>
                <p class="mt-2 text-sm text-body-muted leading-relaxed">
                    {{ __('Manage terms flagged by the job posting scanner to eliminate discriminatory terminology and ensure RA 7277 compliance.') }}
                </p>
            </div>
            <div class="mt-6">
                <a href="{{ route('admin.lexicon') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-secondary hover:text-secondary/80">
                    <span>{{ __('Manage inclusive vocabulary') }}</span>
                    <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>
    </div>
</div>
