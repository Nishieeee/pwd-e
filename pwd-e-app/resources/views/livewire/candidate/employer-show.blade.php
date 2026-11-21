<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    {{-- Back Navigation --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('candidate.jobs') }}" class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-primary hover:text-primary-dark transition-colors">
            <span aria-hidden="true">&larr;</span>
            <span>{{ __('Back to Job Matches') }}</span>
        </a>
    </div>

    {{-- Employer Hero Banner --}}
    <div class="relative overflow-hidden rounded-2xl border border-primary-border bg-white p-6 sm:p-8 shadow-sm" style="background: linear-gradient(135deg, #DCEAF4 0%, #FFFFFF 100%); box-shadow: 0 4px 20px rgba(26,40,64,0.06);">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
            <div class="flex flex-col sm:flex-row items-start gap-5">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-[#244F73] text-white flex items-center justify-center font-bold text-2xl uppercase shadow-md shrink-0">
                    {{ strtoupper(substr($employer->company_name, 0, 2)) }}
                </div>
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        @if($employer->verified_at)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                <svg class="size-3 text-emerald-700" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <span>{{ __('Verified Employer') }}</span>
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-sky-100 text-sky-800 border border-sky-300">
                            <span>{{ __('Republic Act No. 10524 Partner') }}</span>
                        </span>
                        @if($employer->isMatrixComplete())
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                <span>{{ __('Accessibility Matrix Certified') }}</span>
                            </span>
                        @endif
                    </div>

                    <h1 class="font-heading text-2xl sm:text-3xl font-bold text-primary-dark">
                        {{ $employer->company_name }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-y-1 gap-x-4 text-xs sm:text-sm text-body-muted">
                        @if($employer->industry)
                            <div class="flex items-center gap-1.5">
                                <svg class="size-4 text-body-muted/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>{{ $employer->industry }}</span>
                            </div>
                        @endif

                        @if($employer->location)
                            <div class="flex items-center gap-1.5">
                                <svg class="size-4 text-body-muted/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>{{ $employer->location }}</span>
                            </div>
                        @endif

                        @if($employer->website)
                            <div class="flex items-center gap-1.5">
                                <svg class="size-4 text-body-muted/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                <a href="{{ $employer->website }}" target="_blank" rel="noopener noreferrer" class="text-primary font-semibold hover:underline">
                                    {{ preg_replace('#^https?://#', '', $employer->website) }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a href="#active-jobs" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary-dark text-white text-xs sm:text-sm font-semibold hover:bg-primary shadow-xs transition-colors">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>{{ __('View :count Openings', ['count' => $jobs->count()]) }}</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Main Profile Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left 2 Columns: About & Accessibility Matrix --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- About Employer --}}
            <section class="rounded-2xl border border-primary-border bg-white p-6 sm:p-7 shadow-sm space-y-3">
                <h2 class="font-heading text-lg font-bold text-primary-dark flex items-center gap-2">
                    <svg class="size-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>{{ __('About the Company & Inclusive Culture') }}</span>
                </h2>
                <p class="text-sm text-body-muted leading-relaxed">
                    {{ $employer->about_summary }}
                </p>
            </section>

            {{-- Workplace Accessibility Matrix --}}
            <section class="rounded-2xl border border-primary-border bg-white p-6 sm:p-7 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div>
                        <h2 class="font-heading text-lg font-bold text-primary-dark flex items-center gap-2">
                            <svg class="size-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>{{ __('Workplace Accessibility Matrix') }}</span>
                        </h2>
                        <p class="text-xs text-body-muted mt-0.5">{{ __('Verified physical accommodations, assistive technology tools, and workplace policies available at this company.') }}</p>
                    </div>
                </div>

                @if($employer->accommodations->isEmpty())
                    <div class="p-6 rounded-xl bg-surface border border-primary-border text-center">
                        <p class="text-sm font-semibold text-primary-dark">{{ __('Accessibility Matrix is being updated') }}</p>
                        <p class="text-xs text-body-muted mt-1">{{ __('This employer provides standard accommodations upon request for candidate interviews.') }}</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($accommodationsByCategory as $category => $items)
                            <div class="space-y-2">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-primary-dark flex items-center gap-1.5">
                                    <span class="size-2 rounded-full bg-emerald-500"></span>
                                    <span>{{ ucwords(str_replace('_', ' ', $category ?: 'General Accommodations')) }}</span>
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                    @foreach($items as $acc)
                                        <div class="flex items-start gap-2.5 p-3 rounded-xl border border-emerald-200 bg-emerald-50/40 text-xs">
                                            <span class="mt-0.5 size-4 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0">
                                                <svg class="size-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            </span>
                                            <div>
                                                <strong class="font-bold text-emerald-950 block">{{ $acc->name }}</strong>
                                                @if($acc->description)
                                                    <span class="text-[11px] text-emerald-800/80 leading-snug">{{ $acc->description }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        {{-- Right 1 Column: Quick Facts & Active Jobs --}}
        <div class="space-y-6">
            {{-- Inclusivity Summary Card --}}
            <div class="rounded-2xl border border-primary-border bg-white p-6 shadow-sm space-y-4">
                <h3 class="font-heading text-sm font-bold text-primary-dark">{{ __('Statutory & Inclusivity Standards') }}</h3>
                <dl class="space-y-3 text-xs">
                    <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                        <dt class="text-body-muted">{{ __('Philippine Statutory Partner') }}</dt>
                        <dd class="font-bold text-emerald-700">RA 10524 Compliant</dd>
                    </div>
                    <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                        <dt class="text-body-muted">{{ __('Identity Verification') }}</dt>
                        <dd class="font-bold text-primary-dark">{{ $employer->verified_at ? 'DOLE/BIR Verified' : 'Registered' }}</dd>
                    </div>
                    <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                        <dt class="text-body-muted">{{ __('Accommodations Offered') }}</dt>
                        <dd class="font-bold text-primary-dark tabular-nums">{{ $employer->accommodations->count() }} Verified</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-body-muted">{{ __('Active Career Openings') }}</dt>
                        <dd class="font-bold text-primary tabular-nums">{{ $jobs->count() }} Roles</dd>
                    </div>
                </dl>
            </div>

            {{-- Active Job Openings Stream --}}
            <section id="active-jobs" class="rounded-2xl border border-primary-border bg-white p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-heading text-sm font-bold text-primary-dark flex items-center gap-1.5">
                        <svg class="size-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>{{ __('Active Job Openings (:count)', ['count' => $jobs->count()]) }}</span>
                    </h3>
                </div>

                @if($jobs->isEmpty())
                    <p class="text-xs text-body-muted text-center py-4">{{ __('No active job openings at this time. Check back soon.') }}</p>
                @else
                    <div class="space-y-3">
                        @foreach($jobs as $job)
                            <div class="p-3.5 rounded-xl border border-primary-border bg-surface hover:border-primary/50 transition-colors space-y-2">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <h4 class="font-bold text-xs sm:text-sm text-primary-dark hover:text-primary">
                                            {{ $job->title }}
                                        </h4>
                                        <div class="text-[11px] text-body-muted flex items-center gap-2 mt-0.5">
                                            <span>{{ $job->location }}</span>
                                            @if($job->type)
                                                <span>&middot;</span>
                                                <span class="capitalize">{{ str_replace('_', ' ', $job->type) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if($job->accommodations->isNotEmpty())
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($job->accommodations->take(2) as $acc)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                {{ $acc->name }}
                                            </span>
                                        @endforeach
                                        @if($job->accommodations->count() > 2)
                                            <span class="text-[10px] text-body-muted self-center">+{{ $job->accommodations->count() - 2 }} more</span>
                                        @endif
                                    </div>
                                @endif

                                <div class="pt-1 flex items-center justify-between">
                                    <span class="text-[10px] text-body-muted">{{ $job->created_at?->diffForHumans() }}</span>
                                    <a href="{{ route('candidate.apply', $job->id) }}" class="inline-flex items-center gap-1 text-xs font-bold text-primary hover:text-primary-dark">
                                        <span>{{ __('Apply Now') }}</span>
                                        <span aria-hidden="true">&rarr;</span>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>
