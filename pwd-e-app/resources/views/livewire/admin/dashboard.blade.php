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
