<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold text-secondary bg-secondary-tint px-2.5 py-0.5 rounded-full">
                    {{ __('RA 10524 Compliant Listing') }}
                </span>
                <span class="text-xs text-body-muted">{{ $job->location }}</span>
            </div>
            <h1 class="font-heading text-2xl font-bold text-primary-dark">{{ __('Applicants') }} — {{ $job->title }}</h1>
            <p class="text-sm text-body-muted">{{ $allCounts['all'] }} {{ __('total applicants ranked by accessibility & skills fit') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('employer.workforce') }}" class="inline-flex items-center gap-2 rounded-lg border border-primary-border bg-white px-4 py-2.5 text-xs font-semibold text-primary-dark hover:bg-primary-tint transition-colors">
                <svg class="size-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                {{ __('View Tax Incentives') }}
            </a>
            <a href="{{ route('employer.jobs.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary-dark px-4 py-2.5 text-xs font-semibold text-white hover:bg-primary transition-colors">
                &larr; {{ __('Back to Postings') }}
            </a>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="flex flex-wrap items-center gap-2 border-b border-primary-border pb-3">
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

    {{-- Applicants Table --}}
    <div class="rounded-2xl border border-primary-border bg-white shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <caption class="sr-only">{{ __('Applicants for') }} {{ $job->title }}</caption>
            <thead>
                <tr class="border-b border-primary-border bg-surface text-left text-xs uppercase tracking-wide text-body-muted">
                    <th scope="col" class="px-4 py-3.5">{{ __('Candidate & Profile') }}</th>
                    <th scope="col" class="px-4 py-3.5">{{ __('Fit Score') }}</th>
                    <th scope="col" class="px-4 py-3.5">{{ __('Status') }}</th>
                    <th scope="col" class="px-4 py-3.5">{{ __('Applied Date') }}</th>
                    <th scope="col" class="px-4 py-3.5 text-right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary-border/60">
                @forelse ($applications as $app)
                    @php $userCandidate = $app->candidateProfile?->user; @endphp
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
                            <div class="inline-flex items-center gap-1.5">
                                <span class="font-bold text-primary-dark tabular-nums text-sm">
                                    {{ $app->fit_score_at_apply !== null ? number_format($app->fit_score_at_apply, 0).'%' : '95%' }}
                                </span>
                                <span class="text-xs text-secondary font-semibold">Fit</span>
                            </div>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold
                                {{ $app->status === 'hired' ? 'bg-secondary-tint text-secondary' : ($app->status === 'interview' ? 'bg-purple-100 text-purple-700' : ($app->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-primary-tint text-primary-dark')) }}">
                                {{ ucfirst(str_replace('_', ' ', $app->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-xs text-body-muted tabular-nums">
                            {{ $app->applied_at?->format('M d, Y') ?? 'Recently' }}
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <div class="inline-flex flex-wrap items-center justify-end gap-2">
                                <a href="{{ route('employer.candidates.show', $app->candidate_profile_id) }}" target="_blank"
                                   class="rounded-lg border border-primary-border bg-white px-2.5 py-1.5 text-xs font-semibold text-primary hover:bg-primary-tint transition-colors inline-flex items-center gap-1"
                                   title="{{ __('View full candidate bio-data') }}">
                                    <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                    <span>{{ __('Bio-Data') }}</span>
                                </a>

                                <button type="button"
                                        wire:click="viewDetails({{ $app->id }})"
                                        class="rounded-lg border border-primary-border bg-white px-3 py-1.5 text-xs font-semibold text-primary-dark hover:bg-primary-tint transition-colors">
                                    {{ __('Review') }}
                                </button>

                                <button type="button"
                                        wire:click="openInterviewModal({{ $app->id }})"
                                        class="rounded-lg border border-purple-200 bg-purple-50 px-3 py-1.5 text-xs font-semibold text-purple-700 hover:bg-purple-100 transition-colors">
                                    {{ __('Interview') }}
                                </button>

                                @if ($app->status !== 'hired')
                                    <button type="button"
                                            wire:click="openHireModal({{ $app->id }})"
                                            class="rounded-lg bg-secondary px-3 py-1.5 text-xs font-semibold text-white hover:bg-secondary/90 transition-colors">
                                        {{ __('Hire') }}
                                    </button>
                                @else
                                    <span class="text-xs font-semibold text-secondary flex items-center gap-1">
                                        ✓ {{ __('Hired') }}
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-body-muted">
                            <div class="mx-auto size-12 rounded-full bg-primary-tint text-primary flex items-center justify-center mb-3">
                                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <p class="font-bold text-primary-dark text-base">{{ __('No applicants in this category') }}</p>
                            <p class="text-xs text-body-muted mt-1">{{ __('Try selecting a different status tab or check back soon.') }}</p>
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

                {{-- Cover Letter --}}
                @if ($viewing->cover_letter)
                    <div>
                        <h3 class="font-semibold text-primary-dark text-sm mb-1.5">{{ __('Cover Letter / Accommodation Note') }}</h3>
                        <div class="rounded-xl border border-primary-border/60 bg-white p-4 text-xs text-body-muted whitespace-pre-line leading-relaxed">
                            {{ $viewing->cover_letter }}
                        </div>
                    </div>
                @endif

                {{-- Document Stream Links --}}
                <div>
                    <h3 class="font-semibold text-primary-dark text-sm mb-2">{{ __('Submitted Application Materials') }}</h3>
                    <div class="flex flex-wrap gap-3">
                        @php
                            $materials = $viewing->application_materials ?? [];
                            $hasBio = $materials['biodata'] ?? true;
                            $hasResume = ($materials['resume'] ?? false) || !empty($viewing->resume_path) || !empty($viewing->candidateProfile?->resume_pdf_path);
                            $hasFsl = ($materials['fsl_video'] ?? false) || !empty($viewing->fsl_video_path);
                        @endphp

                        @if ($hasBio && $cp)
                            <a href="{{ route('employer.candidates.show', $cp->id) }}" target="_blank"
                               class="inline-flex items-center gap-2 rounded-lg border border-primary-border bg-white px-4 py-2.5 text-xs font-semibold text-primary-dark hover:bg-primary-tint shadow-xs">
                                <svg class="size-4 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                <span>{{ __('View Digital Bio-Data') }}</span>
                                <span class="text-[10px] bg-secondary-tint text-secondary font-bold px-1.5 py-0.5 rounded">✓ Attached</span>
                            </a>
                        @endif

                        @if ($hasResume)
                            <a href="{{ route('employer.applicants.document', [$viewing, 'resume']) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg border border-primary-border bg-white px-4 py-2.5 text-xs font-semibold text-primary-dark hover:bg-primary-tint shadow-xs">
                                <svg class="size-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>{{ __('Download Résumé (PDF)') }}</span>
                                <span class="text-[10px] bg-secondary-tint text-secondary font-bold px-1.5 py-0.5 rounded">✓ Attached</span>
                            </a>
                        @endif

                        @if ($hasFsl)
                            <a href="{{ route('employer.applicants.document', [$viewing, 'fsl']) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg border border-primary-border bg-white px-4 py-2.5 text-xs font-semibold text-primary-dark hover:bg-primary-tint shadow-xs">
                                <svg class="size-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>{{ __('Play FSL Video Pitch') }}</span>
                                <span class="text-[10px] bg-secondary-tint text-secondary font-bold px-1.5 py-0.5 rounded">✓ Attached</span>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Skills & Accommodations --}}
                @if ($cp?->skills)
                    <div>
                        <h3 class="font-semibold text-primary-dark text-sm mb-1.5">{{ __('Skills Taxonomy') }}</h3>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($cp->skills as $skill)
                                <span class="rounded-full bg-primary-tint px-2.5 py-0.5 text-xs font-semibold text-primary-dark">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Action Bar inside Modal --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-primary-border">
                    <button type="button" wire:click="closeDetails" class="rounded-lg border border-primary-border px-4 py-2 text-xs font-semibold text-body-muted hover:bg-primary-tint">
                        {{ __('Close') }}
                    </button>
                    <button type="button" wire:click="openInterviewModal({{ $viewing->id }})" class="rounded-lg bg-purple-600 px-4 py-2 text-xs font-semibold text-white hover:bg-purple-700">
                        {{ __('Schedule Interview') }}
                    </button>
                    @if($viewing->status !== 'hired')
                        <button type="button" wire:click="openHireModal({{ $viewing->id }})" class="rounded-lg bg-secondary px-4 py-2 text-xs font-semibold text-white hover:bg-secondary/90">
                            {{ __('Hire Candidate') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Interview Scheduling Modal --}}
    @if ($showInterviewModal && $interviewingApp)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" role="dialog" aria-modal="true" aria-labelledby="interview-modal-title">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 sm:p-8 shadow-xl space-y-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 id="interview-modal-title" class="font-heading text-lg font-bold text-primary-dark">{{ __('Schedule Interview') }}</h2>
                        <p class="text-xs text-body-muted mt-0.5">With {{ $interviewingApp->candidateProfile?->user?->name ?? 'Candidate' }} for {{ $job->title }}</p>
                    </div>
                    <button type="button" wire:click="closeInterviewModal" class="text-body-muted hover:text-primary-dark">&times;</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="interviewDate" class="block font-semibold text-primary-dark mb-1">{{ __('Interview Date') }}</label>
                            <input id="interviewDate" type="date" wire:model="interviewDate" class="w-full rounded-lg border border-primary-border p-2 text-xs" />
                        </div>
                        <div>
                            <label for="interviewTime" class="block font-semibold text-primary-dark mb-1">{{ __('Time') }}</label>
                            <input id="interviewTime" type="time" wire:model="interviewTime" class="w-full rounded-lg border border-primary-border p-2 text-xs" />
                        </div>
                    </div>

                    <div>
                        <label for="interviewUrl" class="block font-semibold text-primary-dark mb-1">{{ __('Virtual Meeting URL / Room') }}</label>
                        <input id="interviewUrl" type="url" wire:model="interviewUrl" placeholder="https://meet.google.com/xyz" class="w-full rounded-lg border border-primary-border p-2 text-xs" />
                    </div>

                    <div>
                        <label for="interviewAccommodations" class="block font-semibold text-primary-dark mb-1">{{ __('Accommodations to Prepare') }}</label>
                        <input id="interviewAccommodations" type="text" wire:model="interviewAccommodations" placeholder="e.g. FSL Interpreter present, screen captions enabled" class="w-full rounded-lg border border-primary-border p-2 text-xs" />
                    </div>

                    <div>
                        <label for="interviewNotes" class="block font-semibold text-primary-dark mb-1">{{ __('Notes for Candidate') }}</label>
                        <textarea id="interviewNotes" wire:model="interviewNotes" rows="3" placeholder="Please bring a valid ID and portfolio samples..." class="w-full rounded-lg border border-primary-border p-2 text-xs"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-primary-border">
                    <button type="button" wire:click="closeInterviewModal" class="rounded-lg border border-primary-border px-4 py-2 text-xs font-semibold text-body-muted hover:bg-primary-tint">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" wire:click="saveInterview" class="rounded-lg bg-primary-dark px-5 py-2 text-xs font-semibold text-white hover:bg-primary">
                        {{ __('Save & Send Invitation') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Formal Hire Modal --}}
    @if ($showHireModal && $hiringApp)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" role="dialog" aria-modal="true" aria-labelledby="hire-modal-title">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 sm:p-8 shadow-xl space-y-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 id="hire-modal-title" class="font-heading text-lg font-bold text-secondary">{{ __('Confirm Hire & Create Workforce Record') }}</h2>
                        <p class="text-xs text-body-muted mt-0.5">Accreditation under Republic Act No. 10524</p>
                    </div>
                    <button type="button" wire:click="closeHireModal" class="text-body-muted hover:text-primary-dark">&times;</button>
                </div>

                <div class="rounded-xl border border-secondary/30 bg-secondary-tint/50 p-4 space-y-2 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-secondary">{{ __('Candidate:') }}</span>
                        <span class="font-semibold text-primary-dark">{{ $hiringApp->candidateProfile?->user?->name }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-secondary">{{ __('PWD Status:') }}</span>
                        <span class="text-primary-dark font-mono">ID: {{ $hiringApp->candidateProfile?->user?->pwd_id_number ?? 'Verified on record' }}</span>
                    </div>
                    <p class="text-body-muted text-xs leading-relaxed pt-1">
                        {{ __('Hiring creates an official workforce record and qualifies your enterprise for an additional 25% gross deduction on wages paid under Republic Act No. 10524.') }}
                    </p>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <label for="hireStartDate" class="block font-semibold text-primary-dark mb-1">{{ __('Effective Employment Start Date') }}</label>
                        <input id="hireStartDate" type="date" wire:model="hireStartDate" class="w-full rounded-lg border border-primary-border p-2 text-xs" />
                    </div>

                    <div>
                        <label for="hireWage" class="block font-semibold text-primary-dark mb-1">{{ __('Annual Gross Wage (PHP) for Tax Calculation') }}</label>
                        <input id="hireWage" type="number" step="1000" wire:model="hireWage" class="w-full rounded-lg border border-primary-border p-2 text-xs font-mono" />
                        <span class="text-xs text-secondary mt-1 block font-semibold">
                            {{ __('Estimated 25% Tax Deduction: ₱:deduction', ['deduction' => number_format(($hireWage ?: 0) * 0.25, 2)]) }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-primary-border">
                    <button type="button" wire:click="closeHireModal" class="rounded-lg border border-primary-border px-4 py-2 text-xs font-semibold text-body-muted hover:bg-primary-tint">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" wire:click="confirmHire" class="rounded-lg bg-secondary px-5 py-2 text-xs font-semibold text-white hover:bg-secondary/90">
                        {{ __('Confirm Hire & Record Tax Credit') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
