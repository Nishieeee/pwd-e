<!DOCTYPE html>
<html lang="en" x-data="{ readable: false, a11yOpen: false }" :data-readable-font="readable ? 'true' : null">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PWD-E — Inclusive Job Directory</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-white text-dark-neutral antialiased">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-50 focus:bg-primary-dark focus:text-white focus:px-3 focus:py-2 focus:rounded">Skip to main content</a>
    <x-site-header />
    <x-accessibility-panel />
    <main id="main">
        {{-- Hero / Search --}}
        <section class="relative" style="background: linear-gradient(180deg, #DCEAF4 0%, #FFFFFF 100%);">
            <div class="max-w-[1200px] mx-auto px-6 sm:px-10 lg:px-12 pt-24 pb-12 text-center">
                <h1 class="font-heading text-primary-dark text-3xl sm:text-4xl font-bold">Inclusive Job Directory</h1>
                <p class="text-body-muted mt-3 max-w-2xl mx-auto">Every job opening below has been verified under our <strong>Workspace Accessibility Matrix</strong> and is accredited under Republic Act 10524.</p>
                <div class="mt-8 bg-white rounded-2xl shadow-sm border border-primary-border p-4 max-w-5xl mx-auto">
                    <label for="search" class="block text-sm font-bold text-primary-dark mb-2">Search for Inclusive Opportunities (Skill, Location, and Accommodation)</label>
                    <div class="flex flex-col md:flex-row gap-2">
                        <input
                            id="search"
                            type="search"
                            inputmode="search"
                            enterkeyhint="search"
                            wire:model.live.debounce.400ms="search"
                            class="flex-1 rounded-lg border border-primary-border bg-surface px-4 py-3 text-base min-h-[48px] focus:outline-none focus:ring-2 focus:ring-primary"
                            placeholder="e.g. Accessibility in Manila with Ramp Access or Screen Reader"
                        >
                        <button type="button" wire:click="$set('search', '')" class="bg-primary-dark text-white font-semibold px-6 py-3 rounded-lg min-h-[48px] hover:bg-primary md:w-auto w-full">
                            Clear
                        </button>
                    </div>
                    @if($popularAccommodations->isNotEmpty())
                    <div class="mt-4 pt-4 border-t border-primary-border flex flex-wrap items-center gap-2">
                        <span class="text-sm text-body-muted">Popular Accommodations:</span>
                        @foreach ($popularAccommodations as $pill)
                            <button
                                type="button"
                                wire:click="filterByAccommodation('{{ $pill }}')"
                                class="text-sm border rounded-full px-3 py-1.5 transition {{ $accommodationFilter === $pill ? 'bg-primary-dark text-white border-primary-dark' : 'border-primary-border text-primary-dark bg-white hover:bg-primary-tint' }}"
                            >{{ $pill }}</button>
                        @endforeach
                        @if($accommodationFilter)
                            <button type="button" wire:click="$set('accommodationFilter', '')" class="text-sm text-secondary underline ml-2">Clear filter</button>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- Job Listings --}}
        <section class="max-w-[1200px] mx-auto px-6 sm:px-10 lg:px-12 py-12">
            <div wire:loading.class="opacity-50 pointer-events-none" class="transition-opacity duration-200">
                @if ($jobs->isEmpty())
                    <div class="text-center py-24">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-primary-tint rounded-full mb-6">
                            <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h2 class="font-heading text-primary-dark text-2xl font-bold">No job postings found</h2>
                        <p class="text-body-muted mt-2 max-w-md mx-auto">
                            @if($search || $accommodationFilter)
                                Try adjusting your search or clearing the filter.
                            @else
                                Check back soon — inclusive employers are onboarding now.
                            @endif
                        </p>
                        @if($search || $accommodationFilter)
                        <button wire:click="$set('search', ''); $set('accommodationFilter', '')" class="mt-6 inline-flex items-center gap-2 bg-primary-dark text-white font-semibold px-6 py-3 rounded-lg hover:bg-primary">
                            Clear search &amp; filters
                        </button>
                        @endif
                    </div>
                @else
                    <div class="flex items-center justify-between mb-6">
                        <p class="text-sm text-body-muted">
                            Showing <span class="font-semibold text-primary-dark">{{ $jobs->total() }}</span> inclusive {{ Str::plural('opportunity', $jobs->total()) }}
                        </p>
                        <div wire:loading>
                            <svg class="animate-spin w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </div>
                    </div>

                    <ul class="space-y-4 md:space-y-6 list-none p-0">
                        @foreach ($jobs as $job)
                        <li>
                            <article class="bg-white rounded-2xl border border-primary-border shadow-sm p-5 md:p-6" style="box-shadow: 0 4px 16px rgba(26,40,64,0.08);">
                                <div class="md:flex md:gap-6">
                                    <div class="flex-1 min-w-0">
                                        <h2 class="font-heading text-primary-dark text-xl font-bold">{{ $job->title }}</h2>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <span class="text-xs font-medium bg-secondary-tint text-secondary px-3 py-1 rounded-full">Verified Inclusive</span>
                                            <span class="text-xs font-medium bg-primary-tint text-primary-dark px-3 py-1 rounded-full">RA 10524 Accredited</span>
                                        </div>
                                        <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-body-muted">
                                            <span class="font-medium text-primary-dark">{{ $job->employerProfile->company_name ?? 'Unknown Employer' }}</span>
                                            <span class="inline-flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>
                                                {{ $job->location }}
                                            </span>
                                            @if($job->employment_type || $job->work_arrangement)
                                            <span class="inline-flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/></svg>
                                                {{ str_replace('_', ' ', $job->employment_type) }}{{ $job->work_arrangement ? ' · ' . str_replace('_', ' ', $job->work_arrangement) : '' }}
                                            </span>
                                            @endif
                                            @if($job->salary_min || $job->salary_max)
                                            <span class="inline-flex items-center gap-1" style="overflow-wrap: anywhere;">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                                                @if($job->salary_min && $job->salary_max)
                                                    ₱{{ number_format($job->salary_min) }} – ₱{{ number_format($job->salary_max) }} / month
                                                @elseif($job->salary_min)
                                                    From ₱{{ number_format($job->salary_min) }} / month
                                                @else
                                                    Up to ₱{{ number_format($job->salary_max) }} / month
                                                @endif
                                            </span>
                                            @endif
                                        </div>
                                        <p class="mt-3 text-body-muted text-sm md:text-base line-clamp-3">{{ $job->description }}</p>
                                        @if($job->accommodations->isNotEmpty())
                                        <div class="mt-3">
                                            <span class="text-sm font-bold text-primary-dark">Verified Accommodations:</span>
                                            <div class="mt-1 flex flex-wrap gap-1.5">
                                                @foreach ($job->accommodations as $acc)
                                                    <span class="text-xs bg-primary-tint text-primary-dark px-2.5 py-1 rounded-full">✓ {{ $acc->name }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="mt-4 md:mt-0 md:w-48 shrink-0">
                                        <div class="bg-surface rounded-xl border border-primary-border p-4 flex flex-col gap-2">
                                            <span class="text-center text-sm font-medium text-primary-dark bg-primary-tint rounded-full px-3 py-1">Inclusive Employer</span>
                                            @auth
                                                <a href="#" class="block text-center bg-primary-dark text-white font-semibold py-2.5 rounded-lg min-h-[40px] flex items-center justify-center hover:bg-primary">Apply via Portal</a>
                                            @else
                                                <a href="{{ route('login') }}" class="block text-center bg-primary-dark text-white font-semibold py-2.5 rounded-lg min-h-[40px] flex items-center justify-center hover:bg-primary">Log in to Apply</a>
                                            @endauth
                                        </div>
                                        @if($job->published_at)
                                        <p class="text-xs text-center text-body-muted mt-2">Posted {{ $job->published_at->diffForHumans() }}</p>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        </li>
                        @endforeach
                    </ul>

                    {{-- Pagination --}}
                    @if ($jobs->hasPages())
                    <div class="mt-10">
                        {{ $jobs->links() }}
                    </div>
                    @endif
                @endif
            </div>
        </section>
    </main>
    <x-site-footer />
</body>
</html>
