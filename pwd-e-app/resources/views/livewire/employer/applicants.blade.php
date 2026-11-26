<div class="space-y-6">
    <!-- Header Banner Card -->
    <div class="relative overflow-hidden rounded-2xl border border-primary-border bg-white p-6 lg:p-7 shadow-sm" style="background: linear-gradient(135deg, #DCEAF4 0%, #FFFFFF 100%); box-shadow: 0 4px 20px rgba(26,40,64,0.06);">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-2.5 max-w-3xl min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-secondary bg-white px-3 py-1 rounded-full border border-primary-border shadow-2xs whitespace-nowrap">
                        <svg class="size-3.5 text-secondary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ __('RA 10524 Compliant Hiring Pipeline') }}</span>
                    </span>
                    @if($job)
                        <span class="rounded-full bg-primary-tint px-3 py-1 text-xs font-semibold text-primary-dark border border-primary-border/70 whitespace-nowrap">{{ $job->location }}</span>
                    @else
                        <span class="rounded-full bg-primary-tint px-3 py-1 text-xs font-semibold text-primary-dark border border-primary-border/70 whitespace-nowrap">{{ __('Across all active opportunities') }}</span>
                    @endif
                </div>
                <h1 class="font-heading text-2xl sm:text-3xl font-bold text-primary-dark tracking-tight">
                    @if($job)
                        {{ __('Applicants for') }} {{ $job->title }}
                    @else
                        {{ __('Applicants — All Positions') }}
                    @endif
                </h1>
                <p class="text-xs sm:text-sm text-body-muted leading-relaxed max-w-2xl">
                    {{ $allCounts['all'] }} {{ __('total applicants ranked by accessibility accommodations & skills match') }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="{{ route('employer.workforce') }}" class="inline-flex items-center justify-center gap-2 h-11 rounded-xl border border-primary-border bg-white px-4 text-xs sm:text-sm font-semibold text-primary-dark hover:bg-primary-tint/60 shadow-2xs transition-colors whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2">
                    <svg class="size-4 text-secondary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>{{ __('View Tax Incentives') }}</span>
                </a>
                <a href="{{ route('employer.jobs.index') }}" class="inline-flex items-center justify-center gap-2 h-11 rounded-xl bg-primary-dark px-4 text-xs sm:text-sm font-semibold text-white hover:bg-primary shadow-xs transition-colors whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-dark focus-visible:ring-offset-2">
                    <span>&larr; {{ __('Job Postings') }}</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Controls & Job Selector Bar --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 border-b border-primary-border/80 pb-3">
        {{-- Filter Tabs --}}
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 max-w-full" role="tablist" aria-label="{{ __('Application status filter') }}">
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
                        role="tab"
                        aria-selected="{{ $statusFilter === $key ? 'true' : 'false' }}"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold transition-all whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary shrink-0 {{ $statusFilter === $key ? 'bg-primary-dark text-white shadow-xs' : 'bg-white text-body-muted border border-primary-border/70 hover:bg-primary-tint/50 hover:text-primary-dark' }}">
                    <span>{{ __($label) }}</span>
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold tabular-nums {{ $statusFilter === $key ? 'bg-white/20 text-white' : 'bg-primary-tint text-primary-dark' }}">
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
                        class="rounded-xl border border-primary-border bg-white px-3 py-2 text-xs font-semibold text-primary-dark focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary shadow-2xs">
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

    {{-- Position Filled Prompt Banner --}}
    @if(!empty($activeJobFilled) && !empty($filledJob))
        <div class="p-4 sm:p-5 rounded-2xl bg-sky-50 border border-sky-200 text-sky-950 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
            <div class="flex items-start gap-3">
                <div class="size-8 rounded-xl bg-emerald-100 border border-emerald-200 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="size-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="text-xs leading-relaxed">
                    <strong class="block font-bold text-sm text-sky-950">{{ __('Position Filled: :title', ['title' => $filledJob->title]) }}</strong>
                    <span class="text-sky-900">{{ __('A candidate has already been hired for this role. If you are not recruiting additional headcount, close this job listing to prevent new submissions.') }}</span>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button"
                        wire:click="openCloseJobPrompt({{ $filledJob->id }})"
                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-primary-dark text-white text-xs font-bold hover:bg-primary transition-colors shadow-xs cursor-pointer">
                    <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span>{{ __('Close Job Listing') }}</span>
                </button>
            </div>
        </div>
    @endif

    {{-- Applicants Table --}}
    <div class="rounded-2xl border border-primary-border bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto min-h-[360px] pb-12">
            <table class="w-full text-sm divide-y divide-primary-border/60">
                <caption class="sr-only">@if($job){{ __('Applicants for') }} {{ $job->title }}@else{{ __('Applicants') }}@endif</caption>
                <thead class="bg-surface border-b border-primary-border/70">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-body-muted">
                        <th scope="col" class="px-4 py-3.5 text-left whitespace-nowrap">{{ __('Candidate & Profile') }}</th>
                        <th scope="col" class="px-3.5 py-3.5 text-left whitespace-nowrap">{{ __('Applied Position') }}</th>
                        <th scope="col" class="px-3 py-3.5 text-left whitespace-nowrap">{{ __('Fit Score') }}</th>
                        <th scope="col" class="px-3 py-3.5 text-left whitespace-nowrap">{{ __('Status') }}</th>
                        <th scope="col" class="px-3 py-3.5 text-left whitespace-nowrap">{{ __('Applied Date') }}</th>
                        <th scope="col" class="px-4 py-3.5 text-center whitespace-nowrap">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-border/60">
                    @forelse ($applications as $app)
                        @php
                            $userCandidate = $app->candidateProfile?->user;
                            $appliedJob = $app->jobPosting;
                            $materials = $app->application_materials ?? [];
                            $hasBio = $materials['biodata'] ?? true;
                            $hasResume = ($materials['resume'] ?? false) || !empty($app->resume_path) || !empty($app->candidateProfile?->resume_pdf_path);
                            $hasFsl = ($materials['fsl_video'] ?? false) || !empty($app->fsl_video_path);
                            $isRehire = $app->reappliedFrom?->status === 'hired' || $app->candidateProfile?->workforceRecords()->where('employer_profile_id', auth()->user()->employerProfile?->id)->where('separation_status', 'acknowledged')->exists();
                        @endphp
                        <tr class="hover:bg-primary-tint/25 transition-colors align-middle">
                            <td class="px-4 py-4 min-w-[210px]">
                                <div class="flex items-start gap-3">
                                    <div class="size-10 rounded-full bg-primary-tint text-primary-dark font-bold flex items-center justify-center text-xs shrink-0 border border-primary-border/60 shadow-2xs mt-0.5">
                                        {{ $userCandidate ? substr($userCandidate->name, 0, 2) : 'CA' }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="font-bold text-primary-dark flex flex-wrap items-center gap-1.5">
                                            <span class="text-sm leading-snug">{{ $userCandidate?->name ?? 'Candidate' }}</span>
                                            @if(!empty($userCandidate?->pwd_id_number))
                                                <span class="inline-flex items-center gap-1 text-[11px] bg-secondary-tint text-secondary px-2 py-0.5 rounded-full font-bold border border-secondary/20 shrink-0 whitespace-nowrap" title="Verified PWD ID: {{ $userCandidate->pwd_id_number }}">
                                                    <svg class="size-3 text-secondary" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                    <span>PWD</span>
                                                </span>
                                            @endif
                                            @if($isRehire)
                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-full border border-indigo-200 shrink-0 whitespace-nowrap" title="{{ __('Candidate previously worked at your company and has completed separation.') }}">
                                                    <svg class="size-2.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span>{{ __('Former Staff / Rehire') }}</span>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-body-muted truncate mt-0.5">{{ $app->candidateProfile?->headline ?? 'Candidate profile' }}</div>
                                        <div class="flex flex-wrap items-center gap-1.5 mt-1.5 text-[11px]">
                                            @if($hasBio)
                                                @if($app->candidate_profile_id)
                                                    <a href="{{ route('employer.candidates.show', $app->candidate_profile_id) }}" target="_blank" rel="noopener noreferrer"
                                                       class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-primary-tint/80 text-primary-dark font-medium border border-primary-border/50 hover:bg-primary-tint hover:border-primary/40 transition-colors whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-1"
                                                       title="{{ __('Open full candidate profile in a new tab') }}">
                                                        <svg class="size-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                        <span>Profile</span>
                                                    </a>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-primary-tint/80 text-primary-dark font-medium border border-primary-border/50 whitespace-nowrap" title="{{ __('Digital Profile submitted') }}">
                                                        <svg class="size-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                        <span>Profile</span>
                                                    </span>
                                                @endif
                                            @endif
                                            @if($hasResume)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-medium border border-blue-200 whitespace-nowrap" title="{{ __('Résumé PDF submitted') }}">
                                                    <svg class="size-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                    <span>Résumé</span>
                                                </span>
                                            @endif
                                            @if($hasFsl)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-medium border border-emerald-200 whitespace-nowrap" title="{{ __('FSL Video pitch submitted') }}">
                                                    <svg class="size-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                    <span>FSL</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3.5 py-4 min-w-[160px] max-w-[230px]">
                                <div class="font-bold text-primary-dark text-xs sm:text-sm leading-snug">
                                    {{ $appliedJob?->title ?? 'Position' }}
                                </div>
                                <div class="text-[11px] text-body-muted flex items-center gap-1 mt-1">
                                    <svg class="size-3 text-body-muted/70 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span class="truncate">{{ $appliedJob?->location ?? 'Location' }}</span>
                                </div>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap">
                                @php
                                    $score = $app->fit_score_at_apply !== null ? (float)$app->fit_score_at_apply : 95.0;
                                    $scoreConfig = $score >= 90
                                        ? ['bg' => 'bg-emerald-50 text-emerald-800 border-emerald-200', 'dot' => 'bg-emerald-500']
                                        : ($score >= 75
                                            ? ['bg' => 'bg-sky-50 text-sky-800 border-sky-200', 'dot' => 'bg-sky-500']
                                            : ['bg' => 'bg-amber-50 text-amber-800 border-amber-200', 'dot' => 'bg-amber-500']);
                                @endphp
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border {{ $scoreConfig['bg'] }}" title="{{ __('Accessibility & accommodation fit score') }}">
                                    <span class="size-1.5 rounded-full {{ $scoreConfig['dot'] }}"></span>
                                    <span class="tabular-nums">{{ number_format($score, 0) }}%</span>
                                    <span class="font-semibold text-[11px] opacity-80">{{ __('Fit') }}</span>
                                </div>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap">
                                @php
                                    $statusConfig = match($app->status) {
                                        'hired' => $app->workforceRecord?->ended_at
                                            ? ['bg' => 'bg-gray-100 text-gray-700 border-gray-200', 'dot' => 'bg-gray-400', 'label' => 'Separated']
                                            : ['bg' => 'bg-emerald-50 text-emerald-800 border-emerald-200', 'dot' => 'bg-emerald-500', 'label' => 'Hired'],
                                        'offered' => ['bg' => 'bg-teal-50 text-teal-800 border-teal-200', 'dot' => 'bg-teal-500', 'label' => 'Offer Extended'],
                                        'interview' => ['bg' => 'bg-purple-50 text-purple-800 border-purple-200', 'dot' => 'bg-purple-500', 'label' => 'Interview'],
                                        'under_review' => ['bg' => 'bg-amber-50 text-amber-800 border-amber-200', 'dot' => 'bg-amber-500', 'label' => 'Under Review'],
                                        'rejected' => ['bg' => 'bg-red-50 text-red-700 border-red-200', 'dot' => 'bg-red-500', 'label' => 'Declined'],
                                        'offer_declined' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'dot' => 'bg-rose-400', 'label' => 'Offer Declined'],
                                        default => ['bg' => 'bg-sky-50 text-sky-800 border-sky-200', 'dot' => 'bg-sky-500', 'label' => 'Applied'],
                                    };
                                @endphp
                                <div class="space-y-1">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border {{ $statusConfig['bg'] }}">
                                        <span class="size-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                                        <span>{{ __($statusConfig['label']) }}</span>
                                    </span>
                                    @if($app->status === 'interview')
                                        @if($app->isInterviewChecklistComplete())
                                            <div>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300" title="{{ __('Candidate verified all pre-interview assistive tech accommodations') }}">
                                                    <svg class="size-3 text-emerald-700" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                    <span>{{ __('Tech Ready') }}</span>
                                                </span>
                                            </div>
                                        @elseif($app->interview_checklist && $app->interviewChecklistProgress()['completed'] > 0)
                                            <div>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium bg-amber-50 text-amber-800 border border-amber-200">
                                                    <span>{{ $app->interviewChecklistProgress()['completed'] }}/4 {{ __('Tech items') }}</span>
                                                </span>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-4 text-xs text-body-muted whitespace-nowrap">
                                <div>{{ $app->created_at?->format('M d, Y') ?? '—' }}</div>
                                @if($app->reapplied_from_id)
                                    <div class="text-[10px] text-indigo-700 font-semibold mt-0.5">{{ __('Re-application') }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                @php
                                    $unreadChatCount = $app->unreadMessagesFor(auth()->user());
                                @endphp
                                <div class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                                    {{-- Profile --}}
                                    @if($app->candidate_profile_id)
                                        <a href="{{ route('employer.candidates.show', $app->candidate_profile_id) }}" target="_blank" rel="noopener noreferrer"
                                           class="inline-flex items-center justify-center gap-1 h-[34px] rounded-lg bg-primary-dark px-2.5 text-xs font-semibold text-white hover:bg-primary shadow-2xs transition-colors whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-dark focus-visible:ring-offset-2"
                                           title="{{ __('Open full candidate profile with 2x2 photo') }}">
                                            <svg class="size-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                            <span class="whitespace-nowrap">{{ __('Profile') }}</span>
                                        </a>
                                    @endif

                                    {{-- Details --}}
                                    <button type="button" wire:click="viewDetails({{ $app->id }})"
                                            class="inline-flex items-center justify-center h-[34px] rounded-lg border border-primary-border bg-white px-2.5 text-xs font-semibold text-primary-dark hover:bg-primary-tint/60 transition-colors whitespace-nowrap cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2">
                                        {{ __('Details') }}
                                    </button>

                                    {{-- Q&A --}}
                                    <button type="button" wire:click="openChatModal({{ $app->id }})"
                                            class="inline-flex items-center justify-center gap-1 h-[34px] rounded-lg border border-primary-border bg-white px-2.5 text-xs font-semibold text-primary-dark hover:bg-primary-tint/60 transition-colors whitespace-nowrap relative cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2"
                                            title="{{ __('Accommodation inquiries and messages') }}">
                                        <svg class="size-3.5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                        <span class="whitespace-nowrap">{{ __('Q&A') }}</span>
                                        @if($unreadChatCount > 0)
                                            <span class="inline-flex items-center justify-center size-4 rounded-full bg-red-600 text-white text-[10px] font-bold shrink-0">{{ $unreadChatCount }}</span>
                                        @endif
                                    </button>

                                    {{-- Actions Dropdown (Interview, Extend Offer, Decline) --}}
                                    <div class="relative inline-block text-left" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                                        <button type="button"
                                                @click="open = !open"
                                                class="inline-flex items-center justify-center gap-1 h-[34px] rounded-lg border border-primary-border bg-white px-2.5 text-xs font-semibold text-primary-dark hover:bg-primary-tint/60 transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2"
                                                aria-haspopup="true"
                                                :aria-expanded="open.toString()"
                                                title="{{ __('Application workflow actions') }}">
                                            <span>{{ __('Actions') }}</span>
                                            <svg class="size-3.5 text-body-muted transition-transform duration-150" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </button>

                                        <div x-show="open"
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="transform opacity-0 scale-95"
                                             x-transition:enter-end="transform opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="transform opacity-100 scale-100"
                                             x-transition:leave-end="transform opacity-0 scale-95"
                                             class="absolute right-0 mt-1 w-44 rounded-xl border border-primary-border bg-white py-1 shadow-lg z-30 text-left focus:outline-none"
                                             style="display: none;">
                                            @if($app->status !== 'hired')
                                                @if($app->status !== 'rejected')
                                                    {{-- Schedule Interview --}}
                                                    <button type="button"
                                                            wire:click="openInterviewModal({{ $app->id }})"
                                                            @click="open = false"
                                                            class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold text-primary hover:bg-primary-tint/60 transition-colors cursor-pointer text-left">
                                                        <svg class="size-3.5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                        <span>{{ __('Interview') }}</span>
                                                    </button>

                                                    {{-- Extend Offer --}}
                                                    <button type="button"
                                                            wire:click="openOfferModal({{ $app->id }})"
                                                            @click="open = false"
                                                            class="w-full flex items-center gap-2 px-3 py-2 text-xs font-bold text-secondary hover:bg-secondary/10 transition-colors cursor-pointer text-left">
                                                        <svg class="size-3.5 text-secondary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                        <span>{{ $app->status === 'offered' ? __('Update Offer') : __('Extend Offer') }}</span>
                                                    </button>

                                                    <div class="my-1 border-t border-primary-border/60"></div>

                                                    {{-- Decline --}}
                                                    <button type="button"
                                                            wire:click="openRejectModal({{ $app->id }})"
                                                            @click="open = false"
                                                            class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 transition-colors cursor-pointer text-left">
                                                        <svg class="size-3.5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        <span>{{ __('Decline') }}</span>
                                                    </button>
                                                @else
                                                    {{-- Decline Note --}}
                                                    <button type="button"
                                                            wire:click="openRejectModal({{ $app->id }})"
                                                            @click="open = false"
                                                            class="w-full flex items-center gap-2 px-3 py-2 text-xs font-medium text-body-muted hover:bg-primary-tint/60 transition-colors cursor-pointer text-left">
                                                        <svg class="size-3.5 text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                        <span>{{ __('Decline Note') }}</span>
                                                    </button>
                                                @endif
                                            @else
                                                {{-- Hired Status --}}
                                                @if($app->workforceRecord?->ended_at)
                                                    <div class="px-3 py-2 text-[11px] text-body-muted">
                                                        <span class="font-bold text-gray-700 block">{{ __('Separated') }}</span>
                                                        <span>{{ $app->workforceRecord->ended_at->format('M d, Y') }}</span>
                                                    </div>
                                                @else
                                                    <div class="px-3 py-2 text-[11px] text-emerald-800 bg-emerald-50/60 rounded-t-lg">
                                                        <span class="font-bold flex items-center gap-1">
                                                            <svg class="size-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                            {{ __('Active Employee') }}
                                                        </span>
                                                    </div>
                                                @endif
                                                <a href="{{ route('employer.workforce') }}"
                                                   class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold text-primary hover:bg-primary-tint/60 transition-colors text-left">
                                                    <svg class="size-3.5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                    <span>{{ __('Workforce Record') }}</span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="mx-auto size-12 rounded-full bg-primary-tint text-primary flex items-center justify-center mb-3">
                                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                </div>
                                <p class="font-bold text-primary-dark text-base">{{ __('No applicants in this category') }}</p>
                                <p class="text-xs text-body-muted mt-1">{{ __('Try selecting a different status tab or job filter.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Detail Modal --}}
    @if ($viewing)
        @php $cp = $viewing->candidateProfile; $u = $cp?->user; @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" wire:click="closeDetails" role="dialog" aria-modal="true" aria-label="{{ __('Applicant details') }}">
            <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white p-6 sm:p-8 shadow-xl space-y-6" wire:click.stop>
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 border-b border-primary-border pb-5">
                    <div class="min-w-0 flex-1 space-y-1.5">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="font-heading text-xl font-bold text-primary-dark leading-snug">{{ $u?->name ?? 'Candidate' }}</h2>
                            @if(!empty($u?->pwd_id_number))
                                <span class="inline-flex items-center gap-1 text-xs bg-secondary-tint text-secondary font-semibold px-2.5 py-0.5 rounded-full border border-secondary/20 whitespace-nowrap shrink-0">✓ Verified PWD (ID: {{ $u->pwd_id_number }})</span>
                            @endif
                        </div>
                        <p class="text-xs text-body-muted leading-relaxed">{{ $cp?->headline }} &middot; {{ $cp?->location ?? 'Zamboanga City, Philippines' }}</p>
                        <p class="text-xs text-body-muted leading-relaxed break-words">{{ $u?->email }} &middot; {{ $cp?->phone ?? 'Phone on file' }}</p>
                        <div class="pt-0.5 text-xs text-primary font-semibold">
                            {{ __('Applied for:') }} {{ $viewing->jobPosting?->title }}
                        </div>
                    </div>
                    <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 shrink-0">
                        <button type="button"
                                wire:click="openChatModal({{ $viewing->id }})"
                                class="inline-flex items-center justify-center gap-1.5 h-9 rounded-lg border border-primary-border bg-white px-3 text-xs font-semibold text-primary-dark hover:bg-primary-tint transition-colors whitespace-nowrap shrink-0 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2">
                            <svg class="size-3.5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                            <span>{{ __('Accommodation Q&A') }}</span>
                            @if($viewing->unreadMessagesFor(auth()->user()) > 0)
                                <span class="size-2 rounded-full bg-red-600 shrink-0"></span>
                            @endif
                        </button>
                        @if($cp)
                            <a href="{{ route('employer.candidates.show', $cp->id) }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center justify-center gap-1.5 h-9 rounded-lg bg-primary-dark px-3 text-xs font-semibold text-white hover:bg-primary shadow-xs transition-colors whitespace-nowrap shrink-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-dark focus-visible:ring-offset-2">
                                <svg class="size-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                <span>{{ __('View Full Profile') }}</span>
                            </a>
                        @endif
                        <button type="button" wire:click="closeDetails"
                                class="inline-flex items-center justify-center h-9 rounded-lg border border-primary-border bg-white px-3 text-xs font-semibold text-primary-dark hover:bg-primary-tint transition-colors whitespace-nowrap shrink-0 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2">
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
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $viewing->status === 'hired' ? 'bg-secondary-tint text-secondary' : ($viewing->status === 'rejected' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-primary-tint text-primary-dark') }}">
                            {{ ucfirst(str_replace('_', ' ', $viewing->status)) }}
                        </span>
                    </div>
                </div>

                {{-- Former Employee Rehire Alert --}}
                @php
                    $prevRecord = $cp?->workforceRecords()->where('employer_profile_id', auth()->user()->employerProfile?->id)->where('separation_status', 'acknowledged')->latest('ended_at')->first();
                @endphp
                @if($prevRecord)
                    <div class="rounded-xl border border-indigo-200 bg-indigo-50/70 p-4 space-y-1.5 text-xs text-indigo-950">
                        <div class="flex items-center gap-2 font-bold">
                            <span class="size-2 rounded-full bg-indigo-600"></span>
                            <span>{{ __('Former Employee / Rehire Applicant') }}</span>
                        </div>
                        <p class="leading-relaxed">
                            {{ __('This candidate previously worked at your company as :position from :start to :end (:reason).', [
                                'position' => $prevRecord->application?->jobPosting?->title ?? 'Team Member',
                                'start' => $prevRecord->hired_at?->format('M Y') ?? 'Start',
                                'end' => $prevRecord->ended_at?->format('M d, Y') ?? 'End',
                                'reason' => ucfirst($prevRecord->ended_reason ?? 'Separated'),
                            ]) }}
                            @if($prevRecord->ended_notes)
                                <span class="block italic mt-1 text-indigo-900">"{{ $prevRecord->ended_notes }}"</span>
                            @endif
                        </p>
                    </div>
                @endif

                @if($viewing->status === 'interview')
                    <div class="rounded-xl border border-primary-border bg-surface p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="size-2 rounded-full {{ $viewing->isInterviewChecklistComplete() ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                <span class="text-xs font-bold text-primary-dark">{{ __('Pre-Interview Assistive Tech Status') }}</span>
                            </div>
                            @if($viewing->isInterviewChecklistComplete())
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    <svg class="size-3 text-emerald-700" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    {{ __('All 4 items verified') }}
                                </span>
                            @else
                                <span class="text-[11px] font-semibold text-amber-800 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                    {{ $viewing->interviewChecklistProgress()['completed'] }}/4 {{ __('items verified by candidate') }}
                                </span>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                            @foreach($viewing->checklist_status as $chk)
                                <div class="flex items-start gap-2 p-2 rounded-lg border {{ $chk['checked'] ? 'bg-emerald-50/50 border-emerald-200 text-emerald-950' : 'bg-white border-gray-200 text-gray-500' }}">
                                    <span class="mt-0.5 size-3.5 rounded flex items-center justify-center shrink-0 {{ $chk['checked'] ? 'bg-emerald-600 text-white' : 'border border-gray-300' }}">
                                        @if($chk['checked'])
                                            <svg class="size-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @endif
                                    </span>
                                    <div>
                                        <div class="font-bold text-[11px]">{{ $chk['label'] }}</div>
                                        <div class="text-[10px] text-body-muted">{{ $chk['desc'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($viewing->status === 'rejected' && $viewing->rejection_reason)
                    <div class="rounded-xl border border-red-200 bg-red-50/60 p-3.5 text-xs text-red-900 space-y-1">
                        <div class="flex items-center gap-1.5 font-bold text-red-800">
                            <svg class="size-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>{{ __('Decline Feedback / Remark') }}</span>
                            @if($viewing->rejected_at)
                                <span class="text-[11px] font-normal text-red-700/80">({{ $viewing->rejected_at->format('M d, Y') }})</span>
                            @endif
                        </div>
                        <p class="leading-relaxed text-red-800/90 italic">"{{ $viewing->rejection_reason }}"</p>
                    </div>
                @endif

                {{-- Documents Submitted --}}
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-heading text-sm font-bold text-primary-dark">{{ __('Submitted Materials & Clearances') }}</h3>
                        <button type="button" wire:click="openRequestDocsModal({{ $viewing->id }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-primary bg-primary/5 hover:bg-primary/10 text-primary-dark font-semibold text-xs transition-colors">
                            <svg class="size-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>{{ __('Request Documents') }}</span>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @if($cp)
                            <a href="{{ route('employer.candidates.show', $cp->id) }}" target="_blank"
                               class="flex items-center gap-3 p-3 rounded-xl border border-primary-border bg-surface hover:bg-primary-tint/50 transition-colors">
                                <div class="size-8 rounded-lg bg-primary-tint text-primary-dark flex items-center justify-center font-bold text-xs">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-semibold text-primary-dark truncate">{{ __('Digital Profile') }}</div>
                                    <div class="text-[11px] text-body-muted">{{ __('Executive view + credentials') }}</div>
                                </div>
                            </a>
                        @endif

                        @if ($viewing->resume_path || $cp?->resume_pdf_path)
                            <a href="{{ route('employer.applicants.document', ['application' => $viewing->id, 'type' => 'resume']) }}" target="_blank"
                               class="flex items-center gap-3 p-3 rounded-xl border border-primary-border bg-surface hover:bg-primary-tint/50 transition-colors">
                                <div class="size-8 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-xs">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-semibold text-primary-dark truncate">{{ __('Résumé / CV (PDF)') }}</div>
                                    <div class="text-[11px] text-body-muted">{{ __('Download uploaded document') }}</div>
                                </div>
                            </a>
                        @endif

                        @if ($viewing->fsl_video_path)
                            <a href="{{ route('employer.applicants.document', ['application' => $viewing->id, 'type' => 'fsl']) }}" target="_blank"
                               class="flex items-center gap-3 p-3 rounded-xl border border-primary-border bg-surface hover:bg-primary-tint/50 transition-colors">
                                <div class="size-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xs">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-semibold text-primary-dark truncate">{{ __('FSL Video Pitch') }}</div>
                                    <div class="text-[11px] text-body-muted">{{ __('Filipino Sign Language recording') }}</div>
                                </div>
                            </a>
                        @endif

                        @if ($viewing->cover_letter_pdf_path)
                            <a href="{{ route('employer.applicants.document', ['application' => $viewing->id, 'type' => 'cover_letter']) }}" target="_blank"
                               class="flex items-center gap-3 p-3 rounded-xl border border-primary-border bg-surface hover:bg-primary-tint/50 transition-colors">
                                <div class="size-8 rounded-lg bg-purple-50 text-purple-700 flex items-center justify-center font-bold text-xs">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-semibold text-primary-dark truncate">{{ __('Cover Letter (PDF)') }}</div>
                                    <div class="text-[11px] text-body-muted">{{ __('Download attached cover letter') }}</div>
                                </div>
                            </a>
                        @endif

                        @if ($u?->pwd_id_path)
                            <a href="{{ route('employer.applicants.document', ['application' => $viewing->id, 'type' => 'pwd_id']) }}" target="_blank"
                               class="flex items-center gap-3 p-3 rounded-xl border border-emerald-200 bg-emerald-50/50 hover:bg-emerald-50 transition-colors">
                                <div class="size-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs shrink-0">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-semibold text-primary-dark truncate">{{ __('Government PWD ID Card') }}</div>
                                    <div class="text-[11px] text-emerald-800 truncate">
                                        {{ ($u->pwd_document_type !== 'medical_certificate' && $u->pwd_id_number) ? 'ID: '.$u->pwd_id_number.' · View scan' : __('View submitted ID card') }}
                                    </div>
                                </div>
                            </a>
                        @elseif ($u?->pwd_document_type !== 'medical_certificate' && !empty($u?->pwd_id_number))
                            <div class="flex items-center gap-3 p-3 rounded-xl border border-emerald-200 bg-emerald-50/30">
                                <div class="size-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs shrink-0">✓</div>
                                <div class="min-w-0">
                                    <div class="text-xs font-semibold text-primary-dark truncate">{{ __('Government PWD ID Record') }}</div>
                                    <div class="text-[11px] text-emerald-800 font-mono truncate">{{ $u->pwd_id_number }}</div>
                                </div>
                            </div>
                        @endif

                        @if ($u?->medical_certificate_path)
                            <a href="{{ route('employer.applicants.document', ['application' => $viewing->id, 'type' => 'medical_certificate']) }}" target="_blank"
                               class="flex items-center gap-3 p-3 rounded-xl border border-purple-200 bg-purple-50/50 hover:bg-purple-50 transition-colors">
                                <div class="size-8 rounded-lg bg-purple-100 text-purple-800 flex items-center justify-center font-bold text-xs shrink-0">🩺</div>
                                <div class="min-w-0">
                                    <div class="text-xs font-semibold text-primary-dark truncate">{{ __('Clinical Medical Certificate') }}</div>
                                    <div class="text-[11px] text-purple-800 truncate">
                                        {{ $u->medical_certificate_number ? 'Ref: '.$u->medical_certificate_number.' · View cert' : __('View Fit-to-Work certificate') }}
                                    </div>
                                </div>
                            </a>
                        @elseif ($u?->pwd_document_type === 'medical_certificate' && !empty($u?->pwd_id_number))
                            <div class="flex items-center gap-3 p-3 rounded-xl border border-purple-200 bg-purple-50/30">
                                <div class="size-8 rounded-lg bg-purple-100 text-purple-800 flex items-center justify-center font-bold text-xs shrink-0">🩺</div>
                                <div class="min-w-0">
                                    <div class="text-xs font-semibold text-primary-dark truncate">{{ __('Clinical Medical Certificate') }}</div>
                                    <div class="text-[11px] text-purple-800 font-mono truncate">{{ $u->pwd_id_number }}</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Requested Clearances / Additional Documents --}}
                    @if (!empty($viewing->requested_documents))
                        <div class="mt-3 rounded-xl border border-primary-border bg-surface p-3.5 space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-primary-dark flex items-center gap-1.5">
                                    <svg class="size-3.5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    {{ __('Employer Requested Documents') }}
                                </span>
                                <span class="text-[11px] text-body-muted">
                                    {{ collect($viewing->requested_documents)->where('status', 'uploaded')->count() }}/{{ count($viewing->requested_documents) }} {{ __('Uploaded') }}
                                </span>
                            </div>

                            <div class="divide-y divide-gray-100">
                                @foreach ($viewing->requested_documents as $reqDoc)
                                    <div class="py-2 flex items-center justify-between gap-3 text-xs">
                                        <div class="min-w-0">
                                            <div class="font-semibold text-primary-dark truncate">{{ $reqDoc['name'] }}</div>
                                            @if (!empty($reqDoc['notes']))
                                                <div class="text-[11px] text-body-muted italic">{{ $reqDoc['notes'] }}</div>
                                            @endif
                                            <div class="text-[10px] text-body-muted mt-0.5">
                                                {{ __('Requested') }} {{ \Carbon\Carbon::parse($reqDoc['requested_at'])->format('M d, Y') }}
                                            </div>
                                        </div>

                                        <div class="shrink-0 flex items-center gap-2">
                                            @if (($reqDoc['status'] ?? 'pending') === 'uploaded' && !empty($reqDoc['file_path']))
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                    ✓ {{ __('Uploaded') }}
                                                </span>
                                                <a href="{{ route('employer.applicants.document', ['application' => $viewing->id, 'type' => 'requested', 'doc_id' => $reqDoc['id']]) }}" target="_blank"
                                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-primary-tint text-primary-dark font-semibold text-xs hover:bg-primary-tint/70 transition-colors">
                                                    <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                    {{ __('View') }}
                                                </a>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                                    ⏳ {{ __('Awaiting Upload') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                @if ($viewing->cover_letter)
                    <div class="space-y-1.5 rounded-xl border border-primary-border bg-surface p-4 text-xs">
                        <div class="font-bold text-primary-dark flex items-center gap-1.5">
                            <svg class="size-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>{{ __('Candidate Introductory / Accommodation Note') }}</span>
                        </div>
                        <p class="leading-relaxed text-primary-dark/90 whitespace-pre-wrap">{{ $viewing->cover_letter }}</p>
                    </div>
                @endif

                {{-- Status Stage in Details Modal --}}
                @if($viewing->status === 'hired')
                    <div class="pt-4 border-t border-primary-border flex flex-wrap items-center justify-between gap-3">
                        <span class="text-xs font-semibold text-body-muted">{{ __('Application stage:') }}</span>
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold">
                                <svg class="size-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>{{ __('Hired') }}</span>
                            </span>
                            @if($viewing->workforceRecord?->ended_at)
                                <span class="text-xs text-body-muted">
                                    ({{ __('Employment ended :date · :reason', [
                                        'date' => $viewing->workforceRecord->ended_at->format('M d, Y'),
                                        'reason' => ucfirst(str_replace('_', ' ', $viewing->workforceRecord->ended_reason ?? 'Separated'))
                                    ]) }})
                                </span>
                            @endif
                            <a href="{{ route('employer.workforce') }}" class="text-xs font-semibold text-primary hover:underline">
                                {{ __('View in Workforce & Tax →') }}
                            </a>
                        </div>
                    </div>
                @else
                    <div class="pt-4 border-t border-primary-border flex flex-wrap items-center justify-between gap-3">
                        <span class="text-xs font-semibold text-body-muted">{{ __('Update application stage:') }}</span>
                        <div class="flex items-center gap-2">
                            @if($viewing->status !== 'rejected')
                                <button type="button" wire:click="openRejectModal({{ $viewing->id }})" class="h-9 rounded-lg border border-red-200 px-3 text-xs font-semibold text-red-600 hover:bg-red-50 transition-colors whitespace-nowrap cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2">
                                    {{ __('Decline Candidate') }}
                                </button>
                            @endif
                            <select wire:change="updateStatus({{ $viewing->id }}, $event.target.value)" class="h-9 rounded-lg border border-primary-border px-3 text-xs font-semibold text-primary-dark bg-white focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1">
                                @foreach ([
                                    'applied' => __('New Application'),
                                    'under_review' => __('Under Review'),
                                    'interview' => __('Interviewing'),
                                    'offered' => __('Extend Offer'),
                                    'rejected' => __('Declined')
                                ] as $st => $stLabel)
                                    <option value="{{ $st }}" @selected($viewing->status === $st)>{{ $stLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
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

    {{-- Offer Modal --}}
    @if ($showOfferModal && $offeringApp)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" role="dialog" aria-modal="true" aria-label="{{ __('Extend job offer') }}">
            <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl bg-white p-6 sm:p-8 shadow-xl space-y-5" wire:click.stop>
                <div class="flex items-start justify-between border-b border-primary-border pb-3">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary-tint text-primary-dark mb-1">
                            📄 {{ __('Formal Employment Offer') }}
                        </div>
                        <h2 class="font-heading text-lg font-bold text-primary-dark">{{ __('Extend Job Offer') }}</h2>
                        <p class="text-xs text-body-muted mt-0.5">{{ $offeringApp->candidateProfile?->user?->name }} · {{ $offeringApp->jobPosting?->title }}</p>
                    </div>
                    <button type="button" wire:click="closeOfferModal" class="text-body-muted hover:text-primary-dark text-lg">&times;</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-primary-dark mb-1">{{ __('Annual Gross Wage (PHP)') }}</label>
                        <input type="number" wire:model="offerSalary" step="1000" class="w-full rounded-lg border border-primary-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary focus:outline-none" />
                        <span class="text-[11px] text-body-muted mt-0.5 block">
                            {{ __('Est. RA 10524 annual deduction:') }} <strong>PHP {{ number_format(($offerSalary ?: 35000) * 0.25, 2) }}</strong>
                        </span>
                    </div>

                    <div>
                        <label class="block font-bold text-primary-dark mb-1">{{ __('Target Start Date') }}</label>
                        <input type="date" wire:model="offerStartDate" class="w-full rounded-lg border border-primary-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary focus:outline-none" />
                    </div>

                    <div>
                        <label class="block font-bold text-primary-dark mb-1">{{ __('Accessibility & Accommodations Package') }}</label>
                        <input type="text" wire:model="offerAccommodations" class="w-full rounded-lg border border-primary-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary focus:outline-none" />
                    </div>

                    <div>
                        <label class="block font-bold text-primary-dark mb-1">{{ __('Offer Message & Terms') }}</label>
                        <textarea wire:model="offerNotes" rows="3" class="w-full rounded-lg border border-primary-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary focus:outline-none"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-primary-border">
                    <button type="button" wire:click="closeOfferModal" class="rounded-lg border border-primary-border px-4 py-2 text-xs font-semibold text-primary-dark hover:bg-primary-tint">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" wire:click="sendOffer" class="rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-white hover:bg-primary/90 shadow-xs">
                        {{ __('Send Formal Offer to Candidate') }}
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
                    <div class="font-bold text-secondary text-sm flex items-center gap-1.5">
                        <svg class="size-4 shrink-0 text-secondary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        <span>{{ __('25% Additional Gross Deduction on Annual Wages') }}</span>
                    </div>
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

                    <div class="flex items-center gap-2.5 p-3 rounded-lg border border-primary-border bg-surface">
                        <div class="size-8 rounded-lg bg-primary-tint text-primary-dark flex items-center justify-center shrink-0">
                            <svg class="size-4 text-primary-dark" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                        </div>
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

    {{-- Reject / Decline Modal --}}
    @if ($showRejectModal && $rejectingApp)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" role="dialog" aria-modal="true" aria-label="{{ __('Decline candidate application') }}">
            <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl bg-white p-6 sm:p-8 shadow-xl space-y-5" wire:click.stop>
                <div class="flex items-start justify-between border-b border-primary-border pb-3">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200 mb-1">
                            {{ __('Decline Application') }}
                        </div>
                        <h2 class="font-heading text-lg font-bold text-primary-dark">{{ __('Decline Candidate Application') }}</h2>
                        <p class="text-xs text-body-muted mt-0.5">{{ $rejectingApp->candidateProfile?->user?->name }} · {{ $rejectingApp->jobPosting?->title }}</p>
                    </div>
                    <button type="button" wire:click="closeRejectModal" class="text-body-muted hover:text-primary-dark text-lg">&times;</button>
                </div>

                <p class="text-xs text-body-muted leading-relaxed">
                    {{ __('Politely notify the candidate that they will not be advancing to the next stage. A feedback remark helps candidates improve their accessibility and application materials.') }}
                </p>

                <div class="space-y-3 text-xs">
                    <label class="block font-bold text-primary-dark">{{ __('Select a Quick Reason (Click to Fill)') }}</label>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ([
                            'Position filled by another qualified applicant',
                            'Skill set or experience level mismatch for this role',
                            'Requisition currently placed on hold',
                            'Schedule / shift availability incompatibility'
                        ] as $quickReason)
                            <button type="button"
                                    wire:click="$set('rejectionReason', '{{ $quickReason }}')"
                                    class="text-[11px] px-2.5 py-1 rounded-md border border-gray-200 bg-gray-50 hover:bg-primary-tint hover:border-primary-border text-primary-dark transition-colors text-left">
                                {{ $quickReason }}
                            </button>
                        @endforeach
                    </div>

                    <div class="pt-2">
                        <label class="block font-bold text-primary-dark mb-1">{{ __('Custom Feedback or Remarks (Optional)') }}</label>
                        <textarea wire:model="rejectionReason" rows="3"
                                  placeholder="{{ __('e.g., Thank you for your application. Although your profile is strong, we have moved forward with another candidate whose background is a closer fit.') }}"
                                  class="w-full rounded-lg border border-primary-border px-3 py-2 text-xs focus:ring-2 focus:ring-red-500 focus:outline-none"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-primary-border">
                    <button type="button" wire:click="closeRejectModal" class="rounded-lg border border-primary-border px-4 py-2 text-xs font-semibold text-primary-dark hover:bg-primary-tint">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" wire:click="confirmReject" class="rounded-lg bg-red-600 px-4 py-2 text-xs font-semibold text-white hover:bg-red-700 shadow-xs">
                        {{ __('Confirm & Decline Candidate') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Accommodation Chat Modal --}}
    @if ($showChatModal && $chatApp)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" role="dialog" aria-modal="true" aria-label="{{ __('Accommodation inquiries and chat') }}">
            <div class="max-h-[90vh] w-full max-w-xl overflow-y-auto rounded-2xl bg-white p-6 shadow-xl space-y-4" wire:click.stop>
                <div class="flex items-start justify-between border-b border-primary-border pb-3">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-secondary-tint text-secondary mb-1">
                            <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <span>{{ __('Accommodation & Logistics Q&A') }}</span>
                        </div>
                        <h2 class="font-heading text-lg font-bold text-primary-dark">
                            {{ $chatApp->candidateProfile?->user?->name ?? __('Candidate') }}
                        </h2>
                        <p class="text-xs text-body-muted">
                            {{ __('Applied for:') }} {{ $chatApp->jobPosting?->title }}
                        </p>
                    </div>
                    <button type="button" wire:click="closeChatModal" class="text-body-muted hover:text-primary-dark text-lg">&times;</button>
                </div>

                <!-- Messages Log -->
                <div class="h-64 overflow-y-auto space-y-3 p-3.5 bg-surface rounded-xl border border-primary-border/60" role="log" aria-live="polite">
                    @forelse($chatApp->messages as $msg)
                        @php
                            $isMe = $msg->sender_id === auth()->id();
                            $isSystem = $msg->type === 'system';
                            $isInquiry = $msg->type === 'accommodation_inquiry';
                            $resolved = $msg->metadata['resolved'] ?? false;
                        @endphp

                        @if($isSystem)
                            <div class="p-2.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs text-center font-medium">
                                <span>{{ $msg->body }}</span>
                                <span class="block text-[10px] text-emerald-700/80 mt-0.5">{{ $msg->created_at?->diffForHumans() }}</span>
                            </div>
                        @else
                            <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                                <div class="max-w-[85%] rounded-2xl px-3.5 py-2.5 text-xs shadow-2xs {{ $isMe ? 'bg-primary-dark text-white rounded-br-xs' : 'bg-white text-dark-neutral border border-primary-border rounded-bl-xs' }}">
                                    @if($isInquiry)
                                        <div class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider mb-1 {{ $isMe ? 'text-sky-300' : 'text-primary' }}">
                                            <svg class="size-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>{{ __('Accommodation Question') }}</span>
                                            @if($resolved)
                                                <span class="inline-flex items-center px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 font-bold text-[9px]">✓ Confirmed</span>
                                            @endif
                                        </div>
                                    @endif
                                    <p class="leading-relaxed whitespace-pre-wrap">{{ $msg->body }}</p>

                                    @if(!$isMe && $isInquiry && !$resolved)
                                        <div class="pt-2 border-t border-gray-100 mt-2">
                                            <button type="button"
                                                    wire:click="confirmAccommodation({{ $msg->id }})"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-secondary text-white font-bold text-[11px] hover:bg-secondary/90 transition-colors shadow-2xs">
                                                <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                <span>{{ __('Confirm We Provide This Accommodation') }}</span>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <span class="text-[10px] text-body-muted mt-1 px-1">
                                    {{ $isMe ? __('You') : ($msg->sender?->name ?? __('Candidate')) }} · {{ $msg->created_at?->format('M d, g:i A') }}
                                </span>
                            </div>
                        @endif
                    @empty
                        <div class="h-full flex flex-col items-center justify-center text-center p-6 text-body-muted">
                            <svg class="size-8 text-body-muted/40 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <p class="text-xs font-semibold text-primary-dark">{{ __('No messages yet') }}</p>
                            <p class="text-[11px] text-body-muted mt-0.5">{{ __('Candidate questions regarding workplace access and arrangements will appear here.') }}</p>
                        </div>
                    @endforelse
                </div>

                <!-- Reply Input -->
                <div class="space-y-2">
                    <textarea wire:model="chatMessageText"
                              wire:keydown.ctrl.enter="sendChatMessage"
                              rows="2"
                              placeholder="{{ __('Type your reply to the candidate (Ctrl+Enter to send)...') }}"
                              class="w-full rounded-xl border border-primary-border px-3.5 py-2.5 text-xs text-dark-neutral placeholder:text-body-muted focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] text-body-muted">
                            {{ __('Press Ctrl+Enter to send') }}
                        </span>
                        <button type="button"
                                wire:click="sendChatMessage"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary-dark text-white text-xs font-bold hover:bg-primary transition-colors shadow-2xs">
                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            <span>{{ __('Send Reply') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Position Filled - Close Job Prompt Modal --}}
    @if ($showCloseJobPromptModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-xs" role="dialog" aria-modal="true" aria-label="{{ __('Position Filled - Close Job Listing') }}">
            <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-2xl bg-white p-6 sm:p-7 shadow-2xl space-y-4" wire:click.stop>
                <div class="flex items-start justify-between border-b border-primary-border pb-3">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 mb-1">
                            {{ __('Hire Completed') }}
                        </div>
                        <h2 class="font-heading text-lg font-bold text-primary-dark">{{ __('Close Filled Job Listing?') }}</h2>
                    </div>
                    <button type="button" wire:click="keepJobListingOpen" class="text-body-muted hover:text-primary-dark text-xl leading-none cursor-pointer">&times;</button>
                </div>

                <p class="text-xs text-body-muted leading-relaxed">
                    {{ __('You have hired :name for :title. Do you want to close this job listing now to stop receiving new applications?', ['name' => $hiredCandidateName, 'title' => $promptCloseJobTitle]) }}
                </p>

                <div class="rounded-xl border border-sky-200 bg-sky-50/70 p-3 text-xs text-sky-900 leading-relaxed">
                    <strong>{{ __('Recruitment Tip:') }}</strong> {{ __('Closing the job removes it from talent search immediately. If you plan to hire multiple candidates for this role, you can keep it open.') }}
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-primary-border">
                    <button type="button" wire:click="keepJobListingOpen" class="rounded-xl border border-primary-border px-4 py-2 text-xs font-semibold text-primary-dark hover:bg-primary-tint transition-colors cursor-pointer">
                        {{ __('Keep Open for More Hires') }}
                    </button>
                    <button type="button" wire:click="closeJobListing" class="rounded-xl bg-primary-dark px-4 py-2 text-xs font-bold text-white hover:bg-primary shadow-xs transition-colors cursor-pointer">
                        {{ __('Close Job Listing') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Request Additional Documents Modal --}}
    @if ($showRequestDocsModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-xs" role="dialog" aria-modal="true" aria-label="{{ __('Request Additional Documents') }}">
            <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl bg-white p-6 sm:p-7 shadow-2xl space-y-4" wire:click.stop>
                <div class="flex items-start justify-between border-b border-primary-border pb-3">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary-tint text-primary-dark border border-primary-border mb-1">
                            {{ __('Pre-Employment & Compliance') }}
                        </div>
                        <h2 class="font-heading text-lg font-bold text-primary-dark">{{ __('Request Documents from Candidate') }}</h2>
                    </div>
                    <button type="button" wire:click="closeRequestDocsModal" class="text-body-muted hover:text-primary-dark text-xl leading-none cursor-pointer">&times;</button>
                </div>

                <p class="text-xs text-body-muted leading-relaxed">
                    {{ __('Select the clearances, government IDs, or certifications you require from this applicant. The candidate will receive a notification and an upload prompt in their applications portal.') }}
                </p>

                <div class="space-y-3">
                    <label class="block text-xs font-bold uppercase tracking-wide text-primary-dark">
                        {{ __('Standard Clearances & Records') }}
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                        @foreach ([
                            'NBI Clearance' => 'National Bureau of Investigation clearance',
                            'Barangay Clearance' => 'Local residence certificate',
                            'Police Clearance' => 'Local police record check',
                            'Medical Certificate (Fit to Work)' => 'Clinical occupational readiness',
                            'Official PWD ID Card (BIR Tax Credit)' => 'Required for BIR RA 10524 tax credits',
                            'Transcript of Records / Diploma' => 'Academic / educational proof',
                            'SSS / PhilHealth / Pag-IBIG Record' => 'Mandatory statutory benefits proof',
                            'BIR Form 1902 / 2316 (TIN)' => 'Tax identification & withholding record',
                        ] as $docName => $docDesc)
                            @php
                                $isAlreadyAttached = in_array($docName, $alreadyAttachedDocTypes ?? [], true);
                            @endphp
                            @if ($isAlreadyAttached)
                                <label aria-disabled="true" class="flex items-start gap-2.5 p-2.5 rounded-xl border border-slate-200 bg-slate-100/90 opacity-65 cursor-not-allowed select-none">
                                    <input type="checkbox" disabled class="mt-0.5 rounded border-slate-300 bg-slate-200 text-slate-400 h-4 w-4 cursor-not-allowed">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-1.5 font-semibold text-slate-500 leading-snug">
                                            <span>{{ $docName }}</span>
                                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-1.5 py-0.5 text-[9px] font-bold text-emerald-800">✓ Already attached</span>
                                        </div>
                                        <div class="text-[10px] text-slate-400 leading-tight mt-0.5">{{ $docDesc }}</div>
                                    </div>
                                </label>
                            @else
                                <label class="flex items-start gap-2.5 p-2.5 rounded-xl border border-primary-border hover:bg-slate-50 cursor-pointer transition-colors {{ in_array($docName, $selectedDocTypes) ? 'bg-primary/5 border-primary ring-1 ring-primary' : 'bg-surface' }}">
                                    <input type="checkbox" wire:model="selectedDocTypes" value="{{ $docName }}" class="mt-0.5 rounded text-primary focus:ring-primary h-4 w-4">
                                    <div class="min-w-0">
                                        <div class="font-semibold text-primary-dark leading-snug">{{ $docName }}</div>
                                        <div class="text-[10px] text-body-muted leading-tight mt-0.5">{{ $docDesc }}</div>
                                    </div>
                                </label>
                            @endif
                        @endforeach
                    </div>

                    {{-- Custom Document Option --}}
                    <div class="space-y-1.5 pt-2">
                        <label for="customDocName" class="block text-xs font-semibold text-primary-dark">
                            {{ __('Other Document (Optional)') }}
                        </label>
                        <input id="customDocName" type="text" wire:model="customDocName"
                               placeholder="{{ __('e.g. Portfolio samples, specialized driver license, training certificates') }}"
                               class="w-full rounded-xl border border-primary-border px-3.5 py-2 text-xs text-primary-dark placeholder:text-body-muted focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    {{-- Notes / Instructions --}}
                    <div class="space-y-1.5">
                        <label for="requestDocsNotes" class="block text-xs font-semibold text-primary-dark">
                            {{ __('Special Instructions / Submission Deadline (Optional)') }}
                        </label>
                        <textarea id="requestDocsNotes" wire:model="requestDocsNotes" rows="2"
                                  placeholder="{{ __('e.g. Please submit clearances valid within the last 6 months before next Monday.') }}"
                                  class="w-full rounded-xl border border-primary-border px-3.5 py-2 text-xs text-primary-dark placeholder:text-body-muted focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-primary-border">
                    <button type="button" wire:click="closeRequestDocsModal" class="rounded-xl border border-primary-border px-4 py-2 text-xs font-semibold text-primary-dark hover:bg-primary-tint transition-colors cursor-pointer">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" wire:click="sendDocumentRequest" class="rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white hover:bg-primary-dark shadow-xs transition-colors cursor-pointer">
                        {{ __('Send Document Request') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
