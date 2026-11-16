<x-layouts::app :title="__('Employer Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        @if (auth()->user()?->isEmployer())
            <!-- Employer Hero Banner matching Landing Page Design -->
            <div class="relative overflow-hidden rounded-2xl border border-primary-border bg-white p-6 sm:p-8 shadow-sm" style="background: linear-gradient(135deg, #DCEAF4 0%, #FFFFFF 100%); box-shadow: 0 4px 20px rgba(26,40,64,0.06);">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div class="space-y-3 max-w-2xl">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-primary-border bg-white px-3 py-1 text-xs font-bold text-secondary shadow-xs">
                                <svg class="size-3.5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                {{ __('Republic Act No. 10524 Accredited Partner') }}
                            </span>
                            @if (auth()->user()->employerProfile?->matrix_completed_at)
                                <span class="rounded-full bg-secondary-tint px-3 py-1 text-xs font-semibold text-secondary">
                                    {{ __('Accessibility Matrix Verified') }}
                                </span>
                            @endif
                        </div>

                        <h1 class="font-heading text-2xl sm:text-3xl font-bold text-primary-dark">
                            {{ __('Welcome back,') }} {{ auth()->user()->employerProfile?->company_name ?? auth()->user()->name }}!
                        </h1>

                        <p class="text-sm sm:text-base text-body-muted leading-relaxed">
                            {{ __('Connect with skilled PWD talent across the Philippines, maintain your barrier-free workplace accommodations, and manage accredited opportunities.') }}
                        </p>
                    </div>

                    <!-- Quick Actions -->
                    <div class="flex flex-wrap items-center gap-3 shrink-0">
                        <a
                            href="{{ route('employer.jobs.create') }}"
                            wire:navigate
                            class="inline-flex items-center gap-2 rounded-lg bg-primary-dark px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary"
                        >
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            {{ __('Post a New Job') }}
                        </a>

                        <a
                            href="{{ route('employer.jobs.index') }}"
                            wire:navigate
                            class="inline-flex items-center gap-2 rounded-lg border border-primary-border bg-white px-5 py-3 text-sm font-semibold text-primary-dark shadow-xs transition hover:bg-primary-tint"
                        >
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            {{ __('Manage Jobs') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Overview Feature Cards -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <!-- Card 1: Job Management -->
                <div class="rounded-2xl border border-primary-border border-t-4 border-t-primary bg-white p-6 shadow-sm flex flex-col justify-between" style="box-shadow: 0 4px 16px rgba(26,40,64,0.06);">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="size-10 rounded-xl bg-primary-tint flex items-center justify-center text-primary-dark">
                                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <span class="rounded-full bg-primary-tint px-2.5 py-0.5 text-xs font-semibold text-primary-dark">
                                {{ auth()->user()->employerProfile?->jobPostings()->count() ?? 0 }} {{ __('Postings') }}
                            </span>
                        </div>
                        <h2 class="font-heading text-lg font-bold text-primary-dark">{{ __('Job Opportunities') }}</h2>
                        <p class="text-xs text-body-muted leading-relaxed">
                            {{ __('Create barrier-free job listings, scan for inclusive language, and specify needed workplace accommodations.') }}
                        </p>
                    </div>
                    <div class="mt-5 pt-4 border-t border-primary-border/60">
                        <a href="{{ route('employer.jobs.index') }}" wire:navigate class="text-xs font-bold text-primary hover:underline inline-flex items-center gap-1">
                            {{ __('View Active & Draft Postings') }} &rarr;
                        </a>
                    </div>
                </div>

                <!-- Card 2: Workplace Matrix -->
                <div class="rounded-2xl border border-primary-border border-t-4 border-t-secondary bg-white p-6 shadow-sm flex flex-col justify-between" style="box-shadow: 0 4px 16px rgba(26,40,64,0.06);">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="size-10 rounded-xl bg-secondary-tint flex items-center justify-center text-secondary">
                                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <span class="rounded-full bg-secondary-tint px-2.5 py-0.5 text-xs font-semibold text-secondary">
                                {{ __('Matrix Ready') }}
                            </span>
                        </div>
                        <h2 class="font-heading text-lg font-bold text-primary-dark">{{ __('Accessibility Matrix') }}</h2>
                        <p class="text-xs text-body-muted leading-relaxed">
                            {{ __('Your declared accommodations (ramps, screen readers, sign language) pre-populate role postings and boost candidate match fit.') }}
                        </p>
                    </div>
                    <div class="mt-5 pt-4 border-t border-primary-border/60">
                        <span class="text-xs font-semibold text-secondary inline-flex items-center gap-1">
                            ✓ {{ __('Verified for Candidate Matching') }}
                        </span>
                    </div>
                </div>

                <!-- Card 3: Statutory Tax Incentives (RA 10524) -->
                <div class="rounded-2xl border border-primary-border border-t-4 border-t-primary-dark bg-white p-6 shadow-sm flex flex-col justify-between" style="box-shadow: 0 4px 16px rgba(26,40,64,0.06);">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="size-10 rounded-xl bg-surface flex items-center justify-center text-primary-dark">
                                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                            </div>
                            <span class="rounded-full bg-surface px-2.5 py-0.5 text-xs font-semibold text-primary-dark border border-primary-border">
                                {{ __('RA 10524') }}
                            </span>
                        </div>
                        <h2 class="font-heading text-lg font-bold text-primary-dark">{{ __('Tax Incentives Tracker') }}</h2>
                        <p class="text-xs text-body-muted leading-relaxed">
                            {{ __('Eligible employers may claim an additional 25% gross deduction on wages paid to qualified PWD hires under Republic Act No. 10524.') }}
                        </p>
                    </div>
                    <div class="mt-5 pt-4 border-t border-primary-border/60">
                        <span class="text-xs text-body-muted">
                            {{ __('Tracks automatically on verified hires') }}
                        </span>
                    </div>
                </div>
            </div>
        @else
            <!-- Non-employer fallback / General placeholder -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                    <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                    <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                    <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                </div>
            </div>
        @endif
    </div>
</x-layouts::app>
