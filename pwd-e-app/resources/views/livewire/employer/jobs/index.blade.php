<div class="space-y-6">
    <!-- Hero Header with Landing Page Design -->
    <div class="relative overflow-hidden rounded-2xl border border-primary-border bg-white p-6 lg:p-7 shadow-sm" style="background: linear-gradient(135deg, #DCEAF4 0%, #FFFFFF 100%); box-shadow: 0 4px 20px rgba(26,40,64,0.06);">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-2.5 max-w-3xl min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-primary-border bg-white px-3 py-1 text-xs font-bold text-secondary shadow-2xs whitespace-nowrap">
                        <svg class="size-3.5 text-secondary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ __('Inclusive Employer Portal') }}</span>
                    </span>
                    @if (auth()->user()->employerProfile?->company_name)
                        <span class="rounded-full bg-primary-tint px-3 py-1 text-xs font-bold text-primary-dark border border-primary-border/70 whitespace-nowrap">
                            {{ auth()->user()->employerProfile->company_name }}
                        </span>
                    @endif
                </div>

                <h1 class="font-heading text-2xl sm:text-3xl font-bold text-primary-dark tracking-tight">
                    {{ __('Job Postings Management') }}
                </h1>
                <p class="text-xs sm:text-sm text-body-muted leading-relaxed max-w-2xl">
                    {{ __('Create, review, and manage your barrier-free job listings accredited under Republic Act 10524.') }}
                </p>
            </div>

            <!-- Header CTA -->
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a
                    href="{{ route('employer.jobs.create') }}"
                    class="inline-flex items-center justify-center gap-2 h-11 rounded-xl bg-primary-dark px-4 text-xs sm:text-sm font-semibold text-white shadow-xs transition hover:bg-primary whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-dark focus-visible:ring-offset-2"
                >
                    <svg class="size-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>{{ __('Post a New Opportunity') }}</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Metrics Bar with PWD-E Styled Stat Cards -->
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <!-- Stat: Total -->
        <div class="rounded-2xl border border-primary-border bg-white p-5 shadow-sm transition hover:shadow-md" style="box-shadow: 0 4px 16px rgba(26,40,64,0.06);">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-body-muted">{{ __('Total Postings') }}</p>
                <div class="size-8 rounded-lg bg-surface flex items-center justify-center text-primary-dark">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
            </div>
            <p class="mt-3 font-heading text-3xl font-bold text-primary-dark">{{ $counts['all'] }}</p>
            <p class="mt-1 text-xs text-body-muted">{{ __('All opportunities') }}</p>
        </div>

        <!-- Stat: Active (Published) -->
        <div class="rounded-2xl border border-primary-border border-t-4 border-t-secondary bg-white p-5 shadow-sm transition hover:shadow-md" style="box-shadow: 0 4px 16px rgba(26,40,64,0.06);">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-secondary">{{ __('Published (Active)') }}</p>
                <div class="size-8 rounded-lg bg-secondary-tint flex items-center justify-center text-secondary">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
            <p class="mt-3 font-heading text-3xl font-bold text-secondary">{{ $counts['published'] }}</p>
            <p class="mt-1 text-xs text-body-muted">{{ __('Live for candidates') }}</p>
        </div>

        <!-- Stat: Drafts -->
        <div class="rounded-2xl border border-primary-border border-t-4 border-t-amber-500 bg-white p-5 shadow-sm transition hover:shadow-md" style="box-shadow: 0 4px 16px rgba(26,40,64,0.06);">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-amber-700">{{ __('Drafts') }}</p>
                <div class="size-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
            </div>
            <p class="mt-3 font-heading text-3xl font-bold text-amber-700">{{ $counts['draft'] }}</p>
            <p class="mt-1 text-xs text-body-muted">{{ __('Incomplete listings') }}</p>
        </div>

        <!-- Stat: Closed -->
        <div class="rounded-2xl border border-primary-border bg-white p-5 shadow-sm transition hover:shadow-md" style="box-shadow: 0 4px 16px rgba(26,40,64,0.06);">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-body-muted">{{ __('Closed') }}</p>
                <div class="size-8 rounded-lg bg-surface flex items-center justify-center text-body-muted">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="mt-3 font-heading text-3xl font-bold text-dark-neutral">{{ $counts['closed'] }}</p>
            <p class="mt-1 text-xs text-body-muted">{{ __('Completed or archived') }}</p>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="rounded-2xl border border-primary-border bg-white p-4 shadow-sm" style="box-shadow: 0 4px 16px rgba(26,40,64,0.06);">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <!-- Filter Tabs -->
            <div class="flex flex-wrap items-center gap-1.5">
                <button
                    type="button"
                    wire:click="setStatusFilter('all')"
                    class="rounded-xl px-4 py-2 text-xs font-bold transition {{ $status === 'all' ? 'bg-primary-dark text-white shadow-xs' : 'bg-surface text-primary-dark hover:bg-primary-tint border border-primary-border' }}"
                >
                    {{ __('All Postings') }} ({{ $counts['all'] }})
                </button>
                <button
                    type="button"
                    wire:click="setStatusFilter('published')"
                    class="rounded-xl px-4 py-2 text-xs font-bold transition {{ $status === 'published' ? 'bg-secondary text-white shadow-xs' : 'bg-surface text-primary-dark hover:bg-primary-tint border border-primary-border' }}"
                >
                    {{ __('Published') }} ({{ $counts['published'] }})
                </button>
                <button
                    type="button"
                    wire:click="setStatusFilter('draft')"
                    class="rounded-xl px-4 py-2 text-xs font-bold transition {{ $status === 'draft' ? 'bg-amber-600 text-white shadow-xs' : 'bg-surface text-primary-dark hover:bg-primary-tint border border-primary-border' }}"
                >
                    {{ __('Drafts') }} ({{ $counts['draft'] }})
                </button>
                <button
                    type="button"
                    wire:click="setStatusFilter('closed')"
                    class="rounded-xl px-4 py-2 text-xs font-bold transition {{ $status === 'closed' ? 'bg-dark-neutral text-white shadow-xs' : 'bg-surface text-primary-dark hover:bg-primary-tint border border-primary-border' }}"
                >
                    {{ __('Closed') }} ({{ $counts['closed'] }})
                </button>
            </div>

            <!-- Search Field -->
            <div class="w-full sm:w-80">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-body-muted">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('Search title or location...') }}"
                        class="w-full rounded-xl border border-primary-border bg-surface pl-10 pr-4 py-2 text-sm text-dark-neutral placeholder-body-muted focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition"
                    />
                </div>
            </div>
        </div>
    </div>

    <!-- Job Postings List Cards matching jobs.blade.php Style -->
    @if ($jobs->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-primary-border bg-white p-12 text-center shadow-sm">
            <div class="size-16 rounded-2xl bg-primary-tint flex items-center justify-center text-primary-dark">
                <svg class="size-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="mt-4 font-heading text-lg font-bold text-primary-dark">
                @if (filled($search) || $status !== 'all')
                    {{ __('No matching job postings found') }}
                @else
                    {{ __('No job postings yet') }}
                @endif
            </h3>
            <p class="mt-1 text-sm text-body-muted max-w-md">
                @if (filled($search) || $status !== 'all')
                    {{ __('Try clearing your search query or selecting a different status filter above.') }}
                @else
                    {{ __('Start connecting with qualified candidates with disabilities by publishing your first verified inclusive opening.') }}
                @endif
            </p>
            <div class="mt-6">
                <a
                    href="{{ route('employer.jobs.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary-dark px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary"
                >
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    {{ __('Post Your First Job') }}
                </a>
            </div>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($jobs as $job)
                <article class="rounded-2xl border border-primary-border bg-white p-6 shadow-sm transition hover:shadow-md" style="box-shadow: 0 4px 16px rgba(26,40,64,0.08);">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-stretch lg:justify-between">
                        <!-- Left Details -->
                        <div class="space-y-3 flex-1 min-w-0">
                            <!-- Title & Status Badge -->
                            <div class="flex flex-wrap items-center gap-3">
                                <h2 class="font-heading text-xl font-bold text-primary-dark">
                                    {{ $job->title }}
                                </h2>

                                @if ($job->status === 'published')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-secondary-tint px-3 py-1 text-xs font-bold text-secondary border border-secondary/20">
                                        <span class="size-1.5 rounded-full bg-secondary"></span>
                                        {{ __('Published & Active') }}
                                    </span>
                                @elseif ($job->status === 'draft')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-800 border border-amber-300">
                                        <span class="size-1.5 rounded-full bg-amber-500"></span>
                                        {{ __('Draft') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-surface px-3 py-1 text-xs font-bold text-body-muted border border-primary-border">
                                        {{ __('Closed') }}
                                    </span>
                                @endif

                                <span class="rounded-full bg-primary-tint px-2.5 py-0.5 text-xs font-semibold text-primary-dark">
                                    {{ __('RA 10524') }}
                                </span>
                            </div>

                            <!-- Meta Details row -->
                            <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-body-muted">
                                @if ($job->location)
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="size-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><circle cx="12" cy="11" r="3"/></svg>
                                        {{ $job->location }}
                                    </span>
                                @endif

                                <span class="capitalize inline-flex items-center gap-1.5">
                                    <svg class="size-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/></svg>
                                    {{ str_replace('_', ' ', $job->employment_type) }} &bull; {{ ucfirst($job->work_arrangement) }}
                                </span>

                                @if ($job->salary_min || $job->salary_max)
                                    <span class="inline-flex items-center gap-1.5 font-medium text-primary-dark">
                                        <svg class="size-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                                        ₱{{ number_format((float) $job->salary_min, 0) }} - ₱{{ number_format((float) $job->salary_max, 0) }} / mo
                                    </span>
                                @endif

                                <span class="text-xs text-body-muted">
                                    {{ __('Created on') }} {{ $job->created_at->format('M d, Y') }}
                                </span>
                            </div>

                            <!-- Job Description Excerpt -->
                            <p class="text-sm text-body-muted line-clamp-2 leading-relaxed">
                                {{ $job->description }}
                            </p>

                            <!-- Verified Accommodations Section -->
                            @if ($job->accommodations->isNotEmpty())
                                <div class="pt-1">
                                    <span class="text-xs font-bold text-primary-dark mr-1">{{ __('Verified Accommodations:') }}</span>
                                    <div class="mt-1 flex flex-wrap gap-1.5">
                                        @foreach ($job->accommodations->take(4) as $acc)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-primary-tint px-2.5 py-1 text-xs font-semibold text-primary-dark border border-primary-border/70">
                                                <span class="text-secondary font-bold">✓</span> {{ $acc->name }}
                                            </span>
                                        @endforeach
                                        @if ($job->accommodations->count() > 4)
                                            <span class="rounded-full bg-surface px-2.5 py-1 text-xs font-medium text-body-muted border border-primary-border">
                                                +{{ $job->accommodations->count() - 4 }} {{ __('more') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Skills Chips -->
                            @if (!empty($job->required_skills))
                                <div class="flex flex-wrap items-center gap-1.5 pt-1">
                                    <span class="text-xs font-bold text-primary-dark mr-1">{{ __('Skills:') }}</span>
                                    @foreach ($job->required_skills as $skill)
                                        <span class="rounded-lg bg-surface px-2 py-0.5 text-xs text-dark-neutral border border-primary-border/60">
                                            {{ $skill }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Right Stats Box & Action Controls -->
                        <div class="flex flex-wrap items-center gap-3 lg:flex-col lg:justify-center lg:items-end lg:self-center shrink-0">
                            <!-- Applicant Counter Box -->
                            <div class="rounded-xl border border-primary-border bg-surface p-3 text-center min-w-[130px]">
                                <span class="block font-heading text-xl font-bold text-primary-dark">
                                    {{ $job->applications_count }}
                                </span>
                                <span class="block text-xs font-medium text-body-muted">
                                    {{ Str::plural('Applicant', $job->applications_count) }}
                                </span>
                            </div>

                            <!-- Actions Group -->
                            <div class="flex flex-wrap items-center gap-2">
                                <a
                                    href="{{ route('employer.jobs.applicants', $job->id) }}"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-primary-border bg-white px-3.5 py-2 text-xs font-bold text-primary-dark shadow-xs transition hover:bg-primary-tint whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2"
                                >
                                    {{ __('Applicants') }}
                                </a>

                                <a
                                    href="{{ route('employer.jobs.edit', $job->id) }}"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-primary-border bg-white px-3.5 py-2 text-xs font-bold text-primary-dark shadow-xs transition hover:bg-primary-tint whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2"
                                >
                                    <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    {{ __('Edit') }}
                                </a>

                                @if ($job->status === 'published')
                                    <button
                                        type="button"
                                        wire:click="openConfirmModal('closeJob', {{ $job->id }}, '{{ __('Close Job Posting?') }}', '{{ __('Are you sure you want to close :title? Candidates will no longer be able to submit applications.', ['title' => $job->title]) }}', '{{ __('Close Posting') }}', 'bg-rose-600')"
                                        class="rounded-xl border border-rose-200 bg-rose-50/50 px-3.5 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100 transition whitespace-nowrap cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-600 focus-visible:ring-offset-2"
                                    >
                                        {{ __('Close') }}
                                    </button>
                                @elseif ($job->status === 'closed')
                                    <button
                                        type="button"
                                        wire:click="reopenJob({{ $job->id }})"
                                        class="rounded-xl bg-secondary px-3.5 py-2 text-xs font-bold text-white shadow-xs hover:bg-secondary/90 transition whitespace-nowrap cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary focus-visible:ring-offset-2"
                                    >
                                        {{ __('Republish') }}
                                    </button>
                                @endif

                                @if ($job->status === 'draft')
                                    <button
                                        type="button"
                                        wire:click="openConfirmModal('deleteJob', {{ $job->id }}, '{{ __('Delete Draft?') }}', '{{ __('Are you sure you want to delete this draft job posting? This cannot be undone.') }}', '{{ __('Delete Draft') }}', 'bg-rose-600')"
                                        class="rounded-xl border border-rose-300 bg-white px-3.5 py-2 text-xs font-bold text-rose-700 hover:bg-rose-50 transition whitespace-nowrap cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-600 focus-visible:ring-offset-2"
                                    >
                                        {{ __('Delete') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach

            <!-- Pagination Container -->
            <div class="pt-4">
                {{ $jobs->links() }}
            </div>
        </div>
    @endif

    {{-- Job Action Confirmation Modal --}}
    @if ($showConfirmModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-xs" role="dialog" aria-modal="true" aria-label="{{ $confirmTitle }}">
            <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-2xl bg-white p-6 sm:p-7 shadow-2xl space-y-4" wire:click.stop>
                <div class="flex items-start justify-between border-b border-primary-border pb-3">
                    <h2 class="font-heading text-lg font-bold text-primary-dark">{{ $confirmTitle }}</h2>
                    <button type="button" wire:click="closeConfirmModal" class="text-body-muted hover:text-primary-dark text-xl leading-none cursor-pointer">&times;</button>
                </div>

                <p class="text-xs text-body-muted leading-relaxed">
                    {{ $confirmDescription }}
                </p>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-primary-border">
                    <button type="button" wire:click="closeConfirmModal" class="rounded-xl border border-primary-border px-4 py-2 text-xs font-semibold text-primary-dark hover:bg-primary-tint transition-colors cursor-pointer">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" wire:click="executeConfirmedAction" class="rounded-xl {{ $confirmButtonClass }} px-4 py-2 text-xs font-bold text-white shadow-xs transition-colors cursor-pointer">
                        {{ $confirmButtonText }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
