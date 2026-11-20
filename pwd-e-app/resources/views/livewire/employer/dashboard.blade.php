<div class="space-y-6">
    <!-- Employer Hero Banner -->
    <div class="relative overflow-hidden rounded-2xl border border-primary-border bg-white p-6 lg:p-7 shadow-sm" style="background: linear-gradient(135deg, #DCEAF4 0%, #FFFFFF 100%); box-shadow: 0 4px 20px rgba(26,40,64,0.06);">
        <div class="flex flex-col 2xl:flex-row 2xl:items-end 2xl:justify-between gap-6">
            <div class="space-y-3 max-w-3xl">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-primary-border bg-white px-3 py-1 text-xs font-bold text-secondary shadow-2xs" title="{{ __('Republic Act No. 10524 Accredited Partner') }}">
                        <svg class="size-3.5 text-secondary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ __('Republic Act No. 10524 Accredited Partner') }}</span>
                    </span>
                    @if (auth()->user()->employerProfile?->matrix_completed_at)
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-secondary/30 bg-secondary-tint px-3 py-1 text-xs font-bold text-secondary shadow-2xs">
                            <svg class="size-3.5 text-secondary shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>{{ __('Accessibility Matrix Verified') }}</span>
                        </span>
                    @endif
                </div>

                <h1 class="font-heading text-2xl sm:text-3xl font-bold text-primary-dark tracking-tight">
                    {{ __('Welcome back,') }} {{ auth()->user()->employerProfile?->company_name ?? auth()->user()->name }}!
                </h1>

                <p class="text-xs sm:text-sm text-body-muted leading-relaxed max-w-2xl">
                    {{ __('Connect with skilled PWD talent across the Philippines, review candidate bio-data, manage barrier-free accommodations, and track RA 10524 employment incentives.') }}
                </p>
            </div>

            <!-- Quick Action Links -->
            <div class="flex flex-wrap items-center gap-2.5 shrink-0 pt-1">
                <a
                    href="{{ route('employer.jobs.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary-dark px-4 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-xs transition hover:bg-primary focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 whitespace-nowrap"
                >
                    <svg class="size-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>{{ __('Post a New Job') }}</span>
                </a>

                <a
                    href="{{ route('employer.talents') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-primary-border bg-white px-4 py-2.5 text-xs sm:text-sm font-semibold text-primary-dark shadow-2xs transition hover:bg-primary-tint/60 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 whitespace-nowrap"
                >
                    <svg class="size-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                    <span>{{ __('Discover Talents') }}</span>
                </a>

                <a
                    href="{{ route('employer.applicants') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-primary-border bg-white px-4 py-2.5 text-xs sm:text-sm font-semibold text-primary-dark shadow-2xs transition hover:bg-primary-tint/60 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 whitespace-nowrap"
                >
                    <svg class="size-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>{{ __('All Applicants') }}</span>
                    @if($needsReviewCount > 0)
                        <span class="rounded-full bg-primary-tint px-2 py-0.5 text-xs font-bold text-primary-dark">{{ $needsReviewCount }}</span>
                    @endif
                </a>
            </div>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <!-- Card 1: Active Jobs -->
        <a href="{{ route('employer.jobs.index') }}" class="rounded-2xl border border-primary-border bg-white p-5 shadow-sm transition hover:border-primary/50 group block">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wide text-body-muted">{{ __('Active Postings') }}</p>
                <div class="size-8 rounded-lg bg-primary-tint text-primary-dark flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <p class="mt-2 text-3xl font-bold text-primary-dark tabular-nums">{{ $activeJobsCount }}</p>
            <p class="mt-1 text-xs text-body-muted">{{ __('Published job listings') }}</p>
        </a>

        <!-- Card 2: Total Applicants -->
        <a href="{{ route('employer.applicants') }}" class="rounded-2xl border border-primary-border bg-white p-5 shadow-sm transition hover:border-primary/50 group block">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wide text-body-muted">{{ __('Total Applicants') }}</p>
                <div class="size-8 rounded-lg bg-sky-100 text-sky-800 flex items-center justify-center group-hover:bg-sky-600 group-hover:text-white transition-colors">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>
            <p class="mt-2 text-3xl font-bold text-primary-dark tabular-nums">{{ $totalApplicantsCount }}</p>
            <p class="mt-1 text-xs text-secondary font-medium">
                @if($needsReviewCount > 0)
                    {{ $needsReviewCount }} {{ __('awaiting your review') }}
                @else
                    {{ __('All reviews up to date') }}
                @endif
            </p>
        </a>

        <!-- Card 3: Interviews Scheduled -->
        <a href="{{ route('employer.applicants', ['status' => 'interview']) }}" class="rounded-2xl border border-primary-border bg-white p-5 shadow-sm transition hover:border-primary/50 group block">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wide text-body-muted">{{ __('Interviews') }}</p>
                <div class="size-8 rounded-lg bg-purple-100 text-purple-800 flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition-colors">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <p class="mt-2 text-3xl font-bold text-primary-dark tabular-nums">{{ $interviewsCount }}</p>
            <p class="mt-1 text-xs text-body-muted">{{ __('Scheduled candidate meetings') }}</p>
        </a>

        <!-- Card 4: RA 10524 Hires -->
        <a href="{{ route('employer.workforce') }}" class="rounded-2xl border border-secondary bg-secondary-tint p-5 shadow-sm transition hover:border-secondary/80 group block">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wide text-secondary">{{ __('RA 10524 Hires') }}</p>
                <div class="size-8 rounded-lg bg-secondary text-white flex items-center justify-center">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
            <p class="mt-2 text-3xl font-bold text-secondary tabular-nums">{{ $hiredCount }}</p>
            <p class="mt-1 text-xs text-secondary/80 font-medium">
                @if($taxIncentiveEst > 0)
                    ₱{{ number_format($taxIncentiveEst, 0) }} {{ __('est. tax deduction') }}
                @else
                    {{ __('25% gross wage deduction eligible') }}
                @endif
            </p>
        </a>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Left 2 Cols: Actionable Recent Applicants -->
        <div class="lg:col-span-2 space-y-4">
            <div class="rounded-2xl border border-primary-border bg-white shadow-sm overflow-hidden">
                <div class="flex items-center justify-between p-5 border-b border-primary-border/60">
                    <div>
                        <h2 class="font-heading text-lg font-bold text-primary-dark">{{ __('Recent Applicants') }}</h2>
                        <p class="text-xs text-body-muted">{{ __('Candidates who applied to your open listings, ranked by match fit.') }}</p>
                    </div>
                    <a href="{{ route('employer.applicants') }}" class="text-xs font-bold text-primary hover:text-primary-dark inline-flex items-center gap-1">
                        {{ __('View All Applicants') }} &rarr;
                    </a>
                </div>

                @if($recentApplicants->isEmpty())
                    <div class="p-10 text-center space-y-3">
                        <div class="mx-auto size-12 rounded-full bg-primary-tint text-primary flex items-center justify-center">
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <h3 class="font-heading text-base font-bold text-primary-dark">{{ __('No applicants yet') }}</h3>
                        <p class="text-xs text-body-muted max-w-sm mx-auto">
                            {{ __('As candidates discover your barrier-free opportunities, applications will appear here for review, interviewing, and hiring.') }}
                        </p>
                        @if($activeJobsCount === 0)
                            <div class="pt-2">
                                <a href="{{ route('employer.jobs.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary-dark px-4 py-2 text-xs font-semibold text-white hover:bg-primary transition-colors">
                                    {{ __('Create your first job posting') }}
                                </a>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-primary-border/60 bg-surface text-left text-xs uppercase tracking-wide text-body-muted">
                                    <th scope="col" class="px-4 py-3">{{ __('Candidate') }}</th>
                                    <th scope="col" class="px-4 py-3">{{ __('Position') }}</th>
                                    <th scope="col" class="px-4 py-3">{{ __('Fit') }}</th>
                                    <th scope="col" class="px-4 py-3">{{ __('Status') }}</th>
                                    <th scope="col" class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-primary-border/40">
                                @foreach($recentApplicants as $app)
                                    @php
                                        $userCandidate = $app->candidateProfile?->user;
                                        $appliedJob = $app->jobPosting;
                                        $materials = $app->application_materials ?? [];
                                        $hasBio = $materials['biodata'] ?? true;
                                        $hasResume = ($materials['resume'] ?? false) || !empty($app->resume_path) || !empty($app->candidateProfile?->resume_pdf_path);
                                        $hasFsl = ($materials['fsl_video'] ?? false) || !empty($app->fsl_video_path);
                                    @endphp
                                    <tr class="hover:bg-primary-tint/20 transition-colors">
                                        <td class="px-4 py-3.5">
                                            <div class="flex items-center gap-2.5">
                                                <div class="size-8 rounded-full bg-primary-tint text-primary-dark font-bold flex items-center justify-center text-xs shrink-0">
                                                    {{ $userCandidate ? substr($userCandidate->name, 0, 2) : 'CA' }}
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="font-semibold text-primary-dark truncate flex items-center gap-1.5 text-xs sm:text-sm">
                                                        <span>{{ $userCandidate?->name ?? 'Candidate' }}</span>
                                                        @if(!empty($userCandidate?->pwd_id_number))
                                                            <span class="text-[10px] bg-secondary-tint text-secondary px-1.5 py-0.2 rounded font-semibold" title="Verified PWD ID">✓ PWD</span>
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center gap-1 mt-0.5 text-[10px] text-body-muted">
                                                        @if($hasBio) <span title="Bio-Data">📋 Bio-Data</span> @endif
                                                        @if($hasResume) <span title="Résumé">📑 CV</span> @endif
                                                        @if($hasFsl) <span title="FSL">🤟 FSL</span> @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <div class="font-semibold text-primary-dark text-xs truncate max-w-[150px]">
                                                {{ $appliedJob?->title ?? 'Position' }}
                                            </div>
                                            <div class="text-[10px] text-body-muted">
                                                {{ $appliedJob?->location ?? 'Location' }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <span class="font-bold text-primary-dark text-xs tabular-nums">
                                                {{ $app->fit_score_at_apply !== null ? number_format($app->fit_score_at_apply, 0).'%' : '95%' }}
                                            </span>
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
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $badgeClass }}">
                                                {{ ucfirst(str_replace('_', ' ', $app->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5 text-right">
                                            <div class="inline-flex items-center justify-end gap-1.5">
                                                @if($app->candidate_profile_id)
                                                    <a href="{{ route('employer.candidates.show', $app->candidate_profile_id) }}" target="_blank"
                                                       class="inline-flex items-center gap-1 rounded-md bg-primary-dark px-2.5 py-1 text-xs font-semibold text-white hover:bg-primary shadow-2xs transition-colors"
                                                       title="{{ __('Open full candidate profile') }}">
                                                        <span>{{ __('Profile') }}</span>
                                                    </a>
                                                @endif
                                                <button type="button" wire:click="viewDetails({{ $app->id }})"
                                                        class="rounded-md border border-primary-border bg-white px-2 py-1 text-xs font-semibold text-primary-dark hover:bg-primary-tint transition-colors">
                                                    {{ __('Details') }}
                                                </button>
                                                @if($app->status !== 'hired')
                                                    <button type="button" wire:click="openInterviewModal({{ $app->id }})"
                                                            class="rounded-md border border-primary-border bg-white px-2 py-1 text-xs font-semibold text-primary hover:bg-primary-tint transition-colors">
                                                        {{ __('Interview') }}
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="p-3 bg-surface border-t border-primary-border/60 text-center">
                    <a href="{{ route('employer.applicants') }}" class="text-xs font-bold text-primary hover:underline">
                        {{ __('Open Centralized Applicants Pipeline') }} &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- Right 1 Col: Active Jobs Snapshot & Tax Incentives -->
        <div class="space-y-6">
            <!-- Active Jobs Snapshot -->
            <div class="rounded-2xl border border-primary-border bg-white p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-primary-border/60">
                    <h2 class="font-heading text-base font-bold text-primary-dark">{{ __('Active Job Postings') }}</h2>
                    <a href="{{ route('employer.jobs.index') }}" class="text-xs font-semibold text-primary hover:text-primary-dark">
                        {{ __('View all') }} &rarr;
                    </a>
                </div>

                @if($activeJobs->isEmpty())
                    <div class="text-center py-6 space-y-2">
                        <p class="text-xs text-body-muted">{{ __('No published jobs at the moment.') }}</p>
                        <a href="{{ route('employer.jobs.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-primary-dark px-3 py-1.5 text-xs font-semibold text-white hover:bg-primary transition-colors">
                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            {{ __('Create Job Posting') }}
                        </a>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($activeJobs as $aj)
                            <div class="p-3 rounded-xl border border-primary-border bg-surface hover:bg-primary-tint/30 transition-colors">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <h3 class="font-semibold text-primary-dark text-xs truncate">{{ $aj->title }}</h3>
                                        <p class="text-[11px] text-body-muted mt-0.5">{{ $aj->location }}</p>
                                    </div>
                                    <a href="{{ route('employer.jobs.applicants', $aj->id) }}"
                                       class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-primary-tint text-primary-dark hover:bg-primary hover:text-white transition-colors shrink-0">
                                        {{ $aj->applications_count }} {{ __('applicants') }}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- RA 10524 Statutory Incentive Widget -->
            <div class="rounded-2xl border border-primary-border border-t-4 border-t-primary-dark bg-white p-5 shadow-sm space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-primary">{{ __('Tax Compliance & Incentives') }}</span>
                    <span class="rounded-full bg-secondary-tint text-secondary px-2 py-0.5 text-xs font-bold">RA 10524</span>
                </div>
                <h3 class="font-heading text-base font-bold text-primary-dark">{{ __('25% Additional Wage Deduction') }}</h3>
                <p class="text-xs text-body-muted leading-relaxed">
                    {{ __('Private employers who hire qualified persons with disability are entitled to an additional gross deduction from income equivalent to 25% of annual salaries paid.') }}
                </p>
                <div class="pt-2 border-t border-primary-border/60">
                    <a href="{{ route('employer.workforce') }}" class="inline-flex items-center gap-1 text-xs font-bold text-secondary hover:underline">
                        <span>{{ __('Generate DOLE/BIR Certification') }}</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>
        </div>
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
