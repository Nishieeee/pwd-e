<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold text-secondary bg-secondary-tint px-2.5 py-0.5 rounded-full">
                    {{ __('RA 10524 Compliant Hiring Pipeline') }}
                </span>
                @if($job)
                    <span class="text-xs text-body-muted">{{ $job->location }}</span>
                @else
                    <span class="text-xs text-body-muted">{{ __('Across all active opportunities') }}</span>
                @endif
            </div>
            <h1 class="font-heading text-2xl font-bold text-primary-dark">
                @if($job)
                    {{ __('Applicants for') }} {{ $job->title }}
                @else
                    {{ __('Applicants — All Positions') }}
                @endif
            </h1>
            <p class="text-sm text-body-muted">
                {{ $allCounts['all'] }} {{ __('total applicants ranked by accessibility accommodations & skills match') }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('employer.workforce') }}" class="inline-flex items-center gap-2 rounded-lg border border-primary-border bg-white px-4 py-2.5 text-xs font-semibold text-primary-dark hover:bg-primary-tint transition-colors">
                <svg class="size-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                {{ __('View Tax Incentives') }}
            </a>
            <a href="{{ route('employer.jobs.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary-dark px-4 py-2.5 text-xs font-semibold text-white hover:bg-primary transition-colors">
                &larr; {{ __('Job Postings') }}
            </a>
        </div>
    </div>

    {{-- Controls & Job Selector Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-primary-border pb-3">
        {{-- Filter Tabs --}}
        <div class="flex flex-wrap items-center gap-2">
            @foreach ([
                'all' => 'All Applicants',
                'applied' => 'New',
                'under_review' => 'Under Review',
                'interview' => 'Interviews',
                'hired' => 'Hired',
                'rejected' => 'Declined',
            ] as $key => $label)
                <button type="button"
                        wire:click="setStatusFilter('{{ $key }}')"
                        class="inline-flex items-center gap-2 px-3.5 py-2 min-h-[44px] rounded-lg text-xs font-semibold transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary {{ $statusFilter === $key ? 'bg-primary-dark text-white shadow-xs' : 'bg-white text-body-muted border border-primary-border hover:bg-primary-tint hover:text-primary-dark' }}">
                    <span>{{ __($label) }}</span>
                    <span class="px-2 py-0.5 rounded-full text-xs {{ $statusFilter === $key ? 'bg-white/20 text-white font-bold' : 'bg-primary-tint text-primary-dark font-bold' }}">
                        {{ $allCounts[$key] ?? 0 }}
                    </span>
                </button>
            @endforeach
        </div>

        {{-- Job Filter Dropdown --}}
        @if($employerJobs->isNotEmpty())
            <div class="flex items-center gap-2 shrink-0">
                <label for="filter-job-select" class="text-xs font-semibold text-body-muted whitespace-nowrap">{{ __('Filter by Job:') }}</label>
                <select id="filter-job-select"
                        wire:change="filterByJob($event.target.value ? $event.target.value : null)"
                        class="rounded-xl border border-primary-border bg-white px-3 py-2 text-xs font-semibold text-primary-dark focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="" @selected(empty($selectedJobId))>{{ __('All Jobs') }} ({{ $employerJobs->count() }})</option>
                    @foreach($employerJobs as $ej)
                        <option value="{{ $ej->id }}" @selected($selectedJobId == $ej->id)>
                            {{ $ej->title }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

    {{-- Applicants Table --}}
    <div class="rounded-2xl border border-primary-border bg-white shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <caption class="sr-only">@if($job){{ __('Applicants for') }} {{ $job->title }}@else{{ __('Applicants') }}@endif</caption>
            <thead>
                <tr class="border-b border-primary-border bg-surface text-left text-xs uppercase tracking-wide text-body-muted">
                    <th scope="col" class="px-4 py-3.5">{{ __('Candidate & Profile') }}</th>
                    <th scope="col" class="px-4 py-3.5">{{ __('Applied Position') }}</th>
                    <th scope="col" class="px-4 py-3.5">{{ __('Fit Score') }}</th>
                    <th scope="col" class="px-4 py-3.5">{{ __('Status') }}</th>
                    <th scope="col" class="px-4 py-3.5">{{ __('Applied Date') }}</th>
                    <th scope="col" class="px-4 py-3.5 text-right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary-border/60">
                @forelse ($applications as $app)
                    @php
                        $userCandidate = $app->candidateProfile?->user;
                        $appliedJob = $app->jobPosting;
                    @endphp
                    <tr class="hover:bg-primary-tint/30 transition-colors">
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="size-9 rounded-full bg-primary-tint text-primary-dark font-bold flex items-center justify-center text-xs shrink-0">
                                    {{ $userCandidate ? substr($userCandidate->name, 0, 2) : 'CA' }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-semibold text-primary-dark truncate flex items-center gap-1.5">
                                        <span>{{ $userCandidate?->name ?? 'Candidate' }}</span>
                                        @if(!empty($userCandidate?->pwd_id_number))
                                            <span class="inline-flex items-center text-xs bg-secondary-tint text-secondary px-2 py-0.5 rounded font-semibold" title="Verified PWD ID">✓ PWD</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-body-muted truncate">{{ $app->candidateProfile?->headline ?? 'Candidate profile' }}</div>
                                    <div class="flex items-center gap-1.5 mt-1 text-[11px]">
                                        @php
                                            $materials = $app->application_materials ?? [];
                                            $hasBio = $materials['biodata'] ?? true;
                                            $hasResume = ($materials['resume'] ?? false) || !empty($app->resume_path) || !empty($app->candidateProfile?->resume_pdf_path);
                                            $hasFsl = ($materials['fsl_video'] ?? false) || !empty($app->fsl_video_path);
                                        @endphp
                                        @if($hasBio)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-primary-tint text-primary-dark font-medium" title="{{ __('Digital Bio-Data submitted') }}">📋 Bio-Data</span>
                                        @endif
                                        @if($hasResume)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 font-medium" title="{{ __('Résumé PDF submitted') }}">📑 Résumé</span>
                                        @endif
                                        @if($hasFsl)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 font-medium" title="{{ __('FSL Video pitch submitted') }}">🤟 FSL</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="font-semibold text-primary-dark text-xs sm:text-sm">
                                {{ $appliedJob?->title ?? 'Position' }}
                            </div>
                            <div class="text-[11px] text-body-muted">
                                {{ $appliedJob?->location ?? 'Location' }}
                            </div>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="inline-flex items-center gap-1.5">
                                <span class="font-bold text-primary-dark tabular-nums text-sm">
                                    {{ $app->fit_score_at_apply !== null ? number_format($app->fit_score_at_apply, 0).'%' : '95%' }}
                                </span>
                                <span class="text-xs text-secondary font-semibold">Fit</span>
                            </div>
                        </td>
                        <td class="px-4 py-3.5">
                            @php
                                $badgeClass = match($app->status) {
                                    'hired' => 'bg-secondary-tint text-secondary border border-emerald-200',
                                    'interview' => 'bg-purple-100 text-purple-800 border border-purple-200',
                                    'under_review' => 'bg-amber-100 text-amber-800 border border-amber-200',
                                    'rejected' => 'bg-red-100 text-red-700 border border-red-200',
                                    default => 'bg-primary-tint text-primary-dark border border-primary-border',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                {{ ucfirst(str_replace('_', ' ', $app->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-xs text-body-muted whitespace-nowrap">
                            {{ $app->created_at?->format('M d, Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <div class="inline-flex items-center justify-end gap-1.5">
                                @if($app->candidate_profile_id)
                                    <a href="{{ route('employer.candidates.show', $app->candidate_profile_id) }}" target="_blank"
                                       class="inline-flex items-center gap-1 rounded-lg bg-primary-dark px-3 py-1.5 text-xs font-semibold text-white hover:bg-primary shadow-2xs transition-colors"
                                       title="{{ __('Open full digital bio-data with 2x2 photo') }}">
                                        <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                        <span>{{ __('Bio-Data') }}</span>
                                    </a>
                                @endif

                                <button type="button" wire:click="viewDetails({{ $app->id }})"
                                        class="rounded-lg border border-primary-border bg-white px-2.5 py-1.5 text-xs font-semibold text-primary-dark hover:bg-primary-tint transition-colors">
                                    {{ __('Details') }}
                                </button>

                                @if($app->status !== 'hired')
                                    <button type="button" wire:click="openInterviewModal({{ $app->id }})"
                                            class="rounded-lg border border-primary-border bg-white px-2.5 py-1.5 text-xs font-semibold text-primary hover:bg-primary-tint transition-colors">
                                        {{ __('Interview') }}
                                    </button>

                                    <button type="button" wire:click="openHireModal({{ $app->id }})"
                                            class="rounded-lg bg-secondary px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-secondary/90 transition-colors shadow-2xs">
                                        {{ __('Hire') }}
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="mx-auto size-12 rounded-full bg-primary-tint text-primary flex items-center justify-center mb-3">
                                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <p class="font-bold text-primary-dark text-base">{{ __('No applicants in this category') }}</p>
                            <p class="text-xs text-body-muted mt-1">{{ __('Try selecting a different status tab or job filter.') }}</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Detail Modal --}}
    @if ($viewing)
        @php $cp = $viewing->candidateProfile; $u = $cp?->user; @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" wire:click="closeDetails" role="dialog" aria-modal="true" aria-label="{{ __('Applicant details') }}">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 sm:p-8 shadow-xl space-y-6" wire:click.stop>
                <div class="flex items-start justify-between gap-4 border-b border-primary-border pb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="font-heading text-xl font-bold text-primary-dark">{{ $u?->name ?? 'Candidate' }}</h2>
                            @if(!empty($u?->pwd_id_number))
                                <span class="text-xs bg-secondary-tint text-secondary font-semibold px-2 py-0.5 rounded-full">✓ Verified PWD (ID: {{ $u->pwd_id_number }})</span>
                            @endif
                        </div>
                        <p class="text-xs text-body-muted mt-1">{{ $cp?->headline }} · {{ $cp?->location ?? 'Zamboanga City, Philippines' }}</p>
                        <p class="text-xs text-body-muted">{{ $u?->email }} · {{ $cp?->phone ?? 'Phone on file' }}</p>
                        <div class="mt-1 text-xs text-primary font-semibold">
                            {{ __('Applied for:') }} {{ $viewing->jobPosting?->title }}
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($cp)
                            <a href="{{ route('employer.candidates.show', $cp->id) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 rounded-lg bg-primary-dark px-3 py-1.5 text-xs font-semibold text-white hover:bg-primary shadow-xs transition-colors">
                                <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                <span>{{ __('View Full Bio-Data') }}</span>
                            </a>
                        @endif
                        <button type="button" wire:click="closeDetails" class="rounded-lg border border-primary-border bg-white px-3 py-1.5 text-xs font-semibold text-primary-dark hover:bg-primary-tint">
                            {{ __('Close') }}
                        </button>
                    </div>
                </div>

                {{-- Fit score & status --}}
                <div class="rounded-xl border border-primary-border bg-surface p-4 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-body-muted block">{{ __('Accessibility & Skills Match') }}</span>
                        <span class="text-xl font-bold text-primary-dark tabular-nums">{{ $viewing->fit_score_at_apply !== null ? number_format($viewing->fit_score_at_apply, 0).'%' : '95%' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-body-muted block">{{ __('Current Status') }}</span>
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $viewing->status === 'hired' ? 'bg-secondary-tint text-secondary' : 'bg-primary-tint text-primary-dark' }}">
                            {{ ucfirst(str_replace('_', ' ', $viewing->status)) }}
                        </span>
                    </div>
                </div>

                {{-- Documents Submitted --}}
                <div class="space-y-3">
                    <h3 class="font-heading text-sm font-bold text-primary-dark">{{ __('Submitted Materials') }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @if($cp)
                            <a href="{{ route('employer.candidates.show', $cp->id) }}" target="_blank"
                               class="flex items-center gap-3 p-3 rounded-xl border border-primary-border bg-surface hover:bg-primary-tint/50 transition-colors">
                                <div class="size-8 rounded-lg bg-primary-tint text-primary-dark flex items-center justify-center font-bold text-xs">📋</div>
                                <div class="min-w-0">
                                    <div class="text-xs font-semibold text-primary-dark truncate">{{ __('Digital Bio-Data') }}</div>
                                    <div class="text-[11px] text-body-muted">{{ __('Executive view + Philippine format') }}</div>
                                </div>
                            </a>
                        @endif

                        @if ($viewing->resume_path || $cp?->resume_pdf_path)
                            <a href="{{ route('employer.applicants.document', ['application' => $viewing->id, 'type' => 'resume']) }}" target="_blank"
                               class="flex items-center gap-3 p-3 rounded-xl border border-primary-border bg-surface hover:bg-primary-tint/50 transition-colors">
                                <div class="size-8 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-xs">📑</div>
                                <div class="min-w-0">
                                    <div class="text-xs font-semibold text-primary-dark truncate">{{ __('Résumé / CV (PDF)') }}</div>
                                    <div class="text-[11px] text-body-muted">{{ __('Download uploaded document') }}</div>
                                </div>
                            </a>
                        @endif

                        @if ($viewing->fsl_video_path)
                            <a href="{{ route('employer.applicants.document', ['application' => $viewing->id, 'type' => 'fsl']) }}" target="_blank"
                               class="flex items-center gap-3 p-3 rounded-xl border border-primary-border bg-surface hover:bg-primary-tint/50 transition-colors">
                                <div class="size-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xs">🤟</div>
                                <div class="min-w-0">
                                    <div class="text-xs font-semibold text-primary-dark truncate">{{ __('FSL Video Pitch') }}</div>
                                    <div class="text-[11px] text-body-muted">{{ __('Filipino Sign Language recording') }}</div>
                                </div>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Status Change Dropdown --}}
                <div class="pt-4 border-t border-primary-border flex items-center justify-between">
                    <span class="text-xs font-semibold text-body-muted">{{ __('Update application stage:') }}</span>
                    <div class="flex items-center gap-2">
                        <select wire:change="updateStatus({{ $viewing->id }}, $event.target.value)" class="rounded-lg border border-primary-border px-3 py-1.5 text-xs font-semibold text-primary-dark bg-white">
                            @foreach (['applied' => 'New Application', 'under_review' => 'Under Review', 'interview' => 'Interviewing', 'hired' => 'Hired', 'rejected' => 'Declined'] as $st => $stLabel)
                                <option value="{{ $st }}" @selected($viewing->status === $st)>{{ $stLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Interview Modal --}}
    @if ($showInterviewModal && $interviewingApp)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" role="dialog" aria-modal="true" aria-label="{{ __('Schedule interview') }}">
            <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl bg-white p-6 sm:p-8 shadow-xl space-y-5" wire:click.stop>
                <div class="flex items-start justify-between border-b border-primary-border pb-3">
                    <div>
                        <h2 class="font-heading text-lg font-bold text-primary-dark">{{ __('Schedule Accessible Interview') }}</h2>
                        <p class="text-xs text-body-muted mt-0.5">{{ $interviewingApp->candidateProfile?->user?->name }} · {{ $interviewingApp->jobPosting?->title }}</p>
                    </div>
                    <button type="button" wire:click="closeInterviewModal" class="text-body-muted hover:text-primary-dark text-lg">&times;</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-primary-dark mb-1">{{ __('Date') }}</label>
                            <input type="date" wire:model="interviewDate" class="w-full rounded-lg border border-primary-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary focus:outline-none" />
                        </div>
                        <div>
                            <label class="block font-bold text-primary-dark mb-1">{{ __('Time') }}</label>
                            <input type="time" wire:model="interviewTime" class="w-full rounded-lg border border-primary-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary focus:outline-none" />
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-primary-dark mb-1">{{ __('Virtual Meeting URL') }}</label>
                        <input type="url" wire:model="interviewUrl" placeholder="https://meet.google.com/..." class="w-full rounded-lg border border-primary-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary focus:outline-none" />
                    </div>

                    <div>
                        <label class="block font-bold text-primary-dark mb-1">{{ __('Physical Location / On-site notes') }}</label>
                        <input type="text" wire:model="interviewLocation" placeholder="{{ __('Company office or leave blank for virtual') }}" class="w-full rounded-lg border border-primary-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary focus:outline-none" />
                    </div>

                    <div>
                        <label class="block font-bold text-primary-dark mb-1">{{ __('Accommodations Provided') }}</label>
                        <input type="text" wire:model="interviewAccommodations" placeholder="{{ __('e.g. Filipino Sign Language Interpreter on-call, live captioning') }}" class="w-full rounded-lg border border-primary-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary focus:outline-none" />
                    </div>

                    <div>
                        <label class="block font-bold text-primary-dark mb-1">{{ __('Internal Notes') }}</label>
                        <textarea wire:model="interviewNotes" rows="2" placeholder="{{ __('Preparation notes for interviewing panel...') }}" class="w-full rounded-lg border border-primary-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary focus:outline-none"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-primary-border">
                    <button type="button" wire:click="closeInterviewModal" class="rounded-lg border border-primary-border px-4 py-2 text-xs font-semibold text-primary-dark hover:bg-primary-tint">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" wire:click="saveInterview" class="rounded-lg bg-primary-dark px-4 py-2 text-xs font-semibold text-white hover:bg-primary shadow-xs">
                        {{ __('Save & Notify Candidate') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Hire Modal (RA 10524) --}}
    @if ($showHireModal && $hiringApp)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" role="dialog" aria-modal="true" aria-label="{{ __('Confirm hire and record RA 10524 tax incentive') }}">
            <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl bg-white p-6 sm:p-8 shadow-xl space-y-5" wire:click.stop>
                <div class="flex items-start justify-between border-b border-primary-border pb-3">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-secondary-tint text-secondary mb-1">
                            ✓ {{ __('Republic Act No. 10524 Tax Incentive Qualified') }}
                        </div>
                        <h2 class="font-heading text-lg font-bold text-primary-dark">{{ __('Confirm Employment & Register Tax Benefit') }}</h2>
                        <p class="text-xs text-body-muted mt-0.5">{{ $hiringApp->candidateProfile?->user?->name }} · {{ $hiringApp->jobPosting?->title }}</p>
                    </div>
                    <button type="button" wire:click="closeHireModal" class="text-body-muted hover:text-primary-dark text-lg">&times;</button>
                </div>

                <div class="rounded-xl border border-secondary/30 bg-secondary-tint/40 p-4 text-xs text-secondary-dark space-y-1.5">
                    <div class="font-bold text-secondary text-sm">💡 {{ __('25% Additional Gross Deduction on Annual Wages') }}</div>
                    <p class="leading-relaxed text-secondary/90">
                        {{ __('Under RA 10524, private entities that employ qualified PWDs who meet the required terms of employment are entitled to an additional deduction from their gross income equivalent to 25% of the total amount paid as salaries.') }}
                    </p>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-primary-dark mb-1">{{ __('Annual Gross Wage (PHP)') }}</label>
                        <input type="number" wire:model="hireWage" step="1000" class="w-full rounded-lg border border-primary-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary focus:outline-none" />
                        <span class="text-[11px] text-body-muted mt-0.5 block">
                            {{ __('Est. statutory annual deduction:') }} <strong>PHP {{ number_format(($hireWage ?: 35000) * 0.25, 2) }}</strong>
                        </span>
                    </div>

                    <div>
                        <label class="block font-bold text-primary-dark mb-1">{{ __('Employment Effective Start Date') }}</label>
                        <input type="date" wire:model="hireStartDate" class="w-full rounded-lg border border-primary-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary focus:outline-none" />
                    </div>

                    <div class="flex items-center gap-2 p-3 rounded-lg border border-primary-border bg-surface">
                        <span class="text-sm">🪪</span>
                        <div class="flex-1 min-w-0">
                            <span class="font-bold text-primary-dark block">{{ __('PWD Identification Document') }}</span>
                            <span class="text-[11px] text-body-muted">
                                @if(!empty($hiringApp->candidateProfile?->user?->pwd_id_number))
                                    {{ __('Verified Government PWD ID:') }} {{ $hiringApp->candidateProfile->user->pwd_id_number }}
                                @else
                                    {{ __('Pending submission/manual verification with DOLE') }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-primary-border">
                    <button type="button" wire:click="closeHireModal" class="rounded-lg border border-primary-border px-4 py-2 text-xs font-semibold text-primary-dark hover:bg-primary-tint">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" wire:click="confirmHire" class="rounded-lg bg-secondary px-4 py-2 text-xs font-semibold text-white hover:bg-secondary/90 shadow-xs">
                        {{ __('Confirm Hire & Log RA 10524 Credit') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
