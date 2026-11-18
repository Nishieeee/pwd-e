<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Breadcrumb / Eyebrow -->
    <nav class="flex items-center text-xs text-gray-500 gap-1.5" aria-label="Breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:text-gray-900">Your workspace</a>
        <span aria-hidden="true">/</span>
        <span class="text-gray-900 font-medium" aria-current="page">Job Matches &amp; Search</span>
    </nav>

    <!-- Header Section -->
    <div>
        <h1 class="text-2xl lg:text-3xl font-bold text-[#1A2840] tracking-tight">Job Matches &amp; Search</h1>
        <p class="text-sm text-[#4E5F6E] mt-1">
            Find work that fits your skills and supports your needs. Your saved profile has already done the matching.
        </p>
    </div>

    <!-- Alert / Application Confirmation Banner -->
    @if($applicationSuccessMessage)
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between" role="alert">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium">{{ $applicationSuccessMessage }}</span>
            </div>
            <button type="button" wire:click="$set('applicationSuccessMessage', null)" class="text-emerald-700 hover:text-emerald-900 text-xs font-semibold" aria-label="Dismiss alert">
                Dismiss
            </button>
        </div>
    @endif

    <!-- Search & Filter Card -->
    <section class="bg-white rounded-xl border border-gray-200 p-5 shadow-xs space-y-4" aria-labelledby="search-filters-heading">
        <h2 id="search-filters-heading" class="sr-only">Job Search and Accommodation Filters</h2>

        <!-- Row 1: Search and Work Arrangement -->
        <div class="flex flex-col md:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Search by title, company or keyword"
                       class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 text-xs text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#244F73] focus:border-transparent"
                       aria-label="Search by job title, company or keyword">
            </div>

            <div class="w-full md:w-72">
                <select wire:model.live="workArrangement"
                        class="w-full py-2.5 px-3 rounded-lg border border-gray-300 text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#244F73]"
                        aria-label="Filter by location and work arrangement">
                    <option value="all">Remote · Zamboanga City · Hybrid</option>
                    <option value="remote">Remote Only</option>
                    <option value="hybrid">Hybrid Work</option>
                    <option value="onsite">Onsite / Zamboanga City</option>
                </select>
            </div>

            <button type="button"
                    class="w-full md:w-auto px-6 py-2.5 rounded-lg bg-[#244F73] text-white text-xs font-semibold hover:bg-[#1A2840] transition-colors focus:ring-2 focus:ring-[#244F73] shrink-0">
                Search jobs
            </button>
        </div>

        <!-- Row 2: Smart Match Banner -->
        <div class="flex items-center justify-between px-4 py-2.5 rounded-lg bg-[#DCEAF4]/50 border border-[#B8D0E2] text-xs">
            <div class="flex items-center gap-3">
                <button type="button"
                        wire:click="toggleSmartMatch"
                        role="switch"
                        :aria-checked="{{ $smartMatch ? 'true' : 'false' }}"
                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-[#244F73] {{ $smartMatch ? 'bg-[#244F73]' : 'bg-gray-300' }}"
                        aria-label="Toggle Smart Match algorithm">
                    <span class="sr-only">Toggle Smart Match</span>
                    <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $smartMatch ? 'translate-x-4' : 'translate-x-0' }}"></span>
                </button>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-[#1A2840]">Smart Match</span>
                    <span class="text-gray-600">Only show 85%+ Accessibility Matches</span>
                </div>
            </div>
            <span class="font-bold text-xs {{ $smartMatch ? 'text-[#244F73]' : 'text-gray-500' }}">
                {{ $smartMatch ? 'ON' : 'OFF' }}
            </span>
        </div>

        <!-- Row 3: Accommodation Filters -->
        <div class="space-y-2 pt-1">
            <div class="flex items-center justify-between text-xs">
                <span class="font-semibold text-gray-700">
                    Accommodation filters &middot; {{ count($selectedAccommodations) }} selected from your saved profile
                </span>
                <button type="button"
                        wire:click="resetToSavedProfile"
                        class="text-[#244F73] hover:underline font-semibold focus:outline-none focus:ring-1 focus:ring-[#244F73]">
                    Reset to saved profile
                </button>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @foreach($candidateProfile->accessibilityNeeds as $cNeed)
                    @php
                        $acc = $cNeed->accommodation;
                        $isSelected = in_array((string) $acc->id, $selectedAccommodations);
                    @endphp
                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs cursor-pointer transition-colors {{ $isSelected ? 'bg-sky-50 text-[#1A2840] border-[#244F73] font-medium' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100' }}">
                        <input type="checkbox"
                               value="{{ $acc->id }}"
                               wire:model.live="selectedAccommodations"
                               class="rounded border-gray-300 text-[#244F73] focus:ring-[#244F73] h-3.5 w-3.5">
                        <span>{{ $acc->name }}</span>
                    </label>
                @endforeach
            </div>

            <p class="text-[11px] text-gray-500">
                Adjust your needs for this search anytime. Your saved profile stays unchanged.
            </p>
        </div>
    </section>

    <!-- 2-Column List & Detail View -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left Column: Matching Jobs List (Independently Scrollable) -->
        <div class="{{ $isFullScreen ? 'hidden' : 'lg:col-span-5' }} flex flex-col lg:h-[calc(100vh-5.5rem)] space-y-4">
            <div class="flex items-center justify-between pb-1 shrink-0">
                <div>
                    <h2 class="text-sm font-bold text-[#1A2840]">
                        {{ count($jobs) }} matching jobs
                    </h2>
                    <p class="text-[11px] text-gray-500">
                        Based on your skills &amp; {{ count($selectedAccommodations) }} accommodation needs
                    </p>
                </div>
                <div class="text-xs text-gray-500 flex items-center gap-1 font-medium">
                    <span>Most match</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>

            <!-- Job Cards List -->
            <div class="space-y-3 flex-1 overflow-y-auto pr-1.5 py-0.5 focus:outline-none focus:ring-2 focus:ring-[#244F73] rounded-xl"
                 role="listbox"
                 aria-label="Matching job postings"
                 tabindex="0">
                @forelse($jobs as $job)
                    @php
                        $isSelected = (int) $selectedJobId === (int) $job->id;
                    @endphp
                    <article wire:key="job-card-{{ $job->id }}"
                             wire:click="selectJob({{ $job->id }})"
                             class="group relative w-full text-left rounded-xl p-4 cursor-pointer select-none transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-[#244F73] focus:ring-offset-1 {{ $isSelected ? 'border-2 border-[#244F73] ring-2 ring-[#244F73]/20 bg-[#F0F7FF] shadow-md' : 'bg-white border border-gray-200 hover:border-[#244F73]/60 hover:bg-sky-50/40 hover:shadow-sm' }}"
                             tabindex="0"
                             role="option"
                             aria-selected="{{ $isSelected ? 'true' : 'false' }}"
                             aria-label="{{ $job->title }} at {{ $job->employerProfile->company_name }}"
                             wire:keydown.enter="selectJob({{ $job->id }})"
                             wire:keydown.space.prevent="selectJob({{ $job->id }})">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-lg bg-[#E0E7FF] text-[#1E1B4B] flex items-center justify-center font-bold text-xs uppercase shrink-0 mt-0.5">
                                    {{ substr($job->employerProfile->company_name ?? 'CO', 0, 2) }}
                                </div>
                                <div>
                                    <div class="font-bold text-xs text-[#1A2840] group-hover:text-[#244F73] transition-colors leading-snug">
                                        {{ $job->employerProfile->company_name }}
                                    </div>
                                    <div class="text-[11px] text-gray-500">
                                        {{ $job->location }}
                                    </div>
                                </div>
                            </div>

                            @if($isSelected)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-[#244F73] text-white">
                                    Selected
                                </span>
                            @endif
                        </div>

                        <div class="mt-2.5">
                            <h3 class="font-bold text-sm text-[#1A2840] group-hover:text-[#244F73] transition-colors">
                                {{ $job->title }}
                            </h3>
                            <div class="text-xs text-gray-600 mt-1 flex flex-wrap items-center gap-1.5 font-medium">
                                <span class="tabular-nums font-semibold text-gray-800">₱{{ number_format($job->salary_min) }}–₱{{ number_format($job->salary_max) }}</span>
                                <span class="text-gray-400">&middot;</span>
                                <span>/month</span>
                                <span class="text-gray-400">&middot;</span>
                                <span>{{ ucfirst(str_replace('_', '-', $job->employment_type)) }}</span>
                                <span class="text-gray-400">&middot;</span>
                                <span>{{ $job->experience_level ?? 'Entry level' }}</span>
                            </div>
                        </div>

                        <!-- Match score pill -->
                        <div class="mt-2 flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#E8F5E9] text-[#176B47] border border-[#A5D6A7]">
                                {{ $job->score }}% Accessibility fit
                            </span>
                        </div>

                        <!-- Skills Pills -->
                        <div class="flex flex-wrap gap-1.5 mt-2.5">
                            @if(is_array($job->required_skills))
                                @foreach(array_slice($job->required_skills, 0, 3) as $skill)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-[#DCEAF4] text-[#1A2840]">
                                        {{ $skill }}
                                    </span>
                                @endforeach
                            @endif
                        </div>

                        <!-- Accommodation Supported Note -->
                        @if($job->all_selected_supported ?? true)
                            <div class="flex items-center gap-1.5 mt-3 text-[11px] text-[#176B47]">
                                <svg class="w-3.5 h-3.5 text-[#176B47] shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-medium">All {{ $job->total_selected_accommodations_count ?? count($selectedAccommodations) }} selected accommodation needs supported</span>
                            </div>
                        @else
                            <div class="flex items-center gap-1.5 mt-3 text-[11px] text-amber-800">
                                <span class="font-medium">Good for some needs &middot; {{ $job->supported_accommodations_count }}/{{ $job->total_selected_accommodations_count }} from your filters</span>
                            </div>
                        @endif

                        <!-- Card Footer -->
                        <div class="flex items-center justify-between mt-3 pt-2.5 border-t {{ $isSelected ? 'border-[#244F73]/20' : 'border-gray-100' }} text-[11px] text-gray-400">
                            <span>Posted {{ $job->published_at ? $job->published_at->diffForHumans() : '2 days ago' }}</span>
                            <span class="font-semibold text-[#244F73] group-hover:underline flex items-center gap-0.5">
                                {{ $isSelected ? 'Viewing details' : 'View job' }} &rarr;
                            </span>
                        </div>
                    </article>
                @empty
                    <div class="bg-white rounded-xl p-8 border border-gray-200 text-center text-gray-500 text-xs">
                        No jobs matched your filter criteria. Try toggling Smart Match or clearing keyword search.
                    </div>
                @endforelse
            </div>

            <!-- Footer summary note -->
            <div class="p-3 rounded-lg bg-gray-50 border border-gray-200 text-[11px] text-gray-500 flex items-center justify-between shrink-0">
                <span>About our match score &middot; {{ count($jobs) }} jobs meet your filters</span>
                <span class="font-semibold text-emerald-700">96-100% Strong accessibility fit</span>
            </div>
        </div>

        <!-- Right Column: Job Details or Empty Selection View (Independently Scrollable) -->
        <div wire:key="job-details-{{ $selectedJob?->id ?? 'empty' }}"
             class="{{ $isFullScreen ? 'lg:col-span-12' : 'lg:col-span-7' }} bg-white rounded-xl border border-gray-200 p-6 shadow-xs min-h-[500px] lg:h-[calc(100vh-5.5rem)] overflow-y-auto focus:outline-none focus:ring-2 focus:ring-[#244F73]"
             role="region"
             aria-label="Selected job details and Workspace Accessibility Matrix"
             tabindex="0">
            @if($selectedJob)
                <!-- Top Toolbar -->
                <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Job Details</span>
                    <button type="button"
                            wire:click="toggleFullScreen"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-600 hover:text-gray-900 border border-gray-200 rounded-md px-2.5 py-1 focus:ring-2 focus:ring-[#244F73]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                        </svg>
                        <span>{{ $isFullScreen ? 'Exit Full Screen' : 'Full Screen' }}</span>
                    </button>
                </div>

                <!-- Job Header Card -->
                <div class="pt-5 space-y-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 rounded-xl bg-[#E0E7FF] text-[#1E1B4B] flex items-center justify-center font-bold text-base uppercase shrink-0">
                                {{ substr($selectedJob->employerProfile->company_name ?? 'CO', 0, 2) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-[#1A2840]">
                                    {{ $selectedJob->employerProfile->company_name }}
                                </h3>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    Posted {{ $selectedJob->published_at ? $selectedJob->published_at->diffForHumans() : '2 days ago' }}
                                </div>
                            </div>
                        </div>

                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#E8F5E9] text-[#176B47] border border-[#A5D6A7]">
                            {{ $selectedJob->score }}% Accessibility fit
                        </span>
                    </div>

                    <div>
                        <h2 class="text-xl font-bold text-[#1A2840] tracking-tight">
                            {{ $selectedJob->title }}
                        </h2>
                        <div class="flex flex-wrap items-center gap-2 text-xs text-gray-600 mt-1 font-medium">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>{{ $selectedJob->location }}</span>
                            </span>
                            <span class="text-gray-300">|</span>
                            <span>{{ ucfirst(str_replace('_', '-', $selectedJob->employment_type)) }} &middot; {{ $selectedJob->experience_level ?? 'Entry level' }}</span>
                        </div>

                        <div class="text-base font-bold text-gray-900 mt-2">
                            ₱{{ number_format($selectedJob->salary_min) }}–₱{{ number_format($selectedJob->salary_max) }} <span class="text-xs font-normal text-gray-500">/month</span>
                        </div>
                    </div>

                    <!-- Skills Pills -->
                    <div class="flex flex-wrap gap-2">
                        @if(is_array($selectedJob->required_skills))
                            @foreach($selectedJob->required_skills as $skill)
                                <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-medium bg-[#DCEAF4] text-[#1A2840]">
                                    {{ $skill }}
                                </span>
                            @endforeach
                        @endif
                    </div>

                    <!-- Accommodation notice -->
                    <div class="flex items-center gap-1.5 text-xs text-[#176B47] font-medium">
                        <svg class="w-4 h-4 text-[#176B47] shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>All 3 selected accommodation needs supported</span>
                    </div>

                    <!-- Apply Action Box -->
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 pt-2">
                        <button type="button"
                                wire:click="applyForJob({{ $selectedJob->id }})"
                                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg bg-[#244F73] text-white text-xs font-bold hover:bg-[#1A2840] shadow-sm transition-colors focus:ring-2 focus:ring-[#244F73]">
                            <span>Apply Now</span>
                            <span aria-hidden="true">&rarr;</span>
                        </button>
                        <span class="text-[11px] text-gray-500">
                            Includes your WMSU profile, requirement ID, introductory video/FSL from your saved profile.
                        </span>
                    </div>
                </div>

                <!-- Description / Overview Section -->
                <div class="mt-8 pt-6 border-t border-gray-100 space-y-3">
                    <h3 class="text-base font-bold text-[#1A2840]">
                        {{ $selectedJob->overview_headline ?? 'Build a more inclusive web' }}
                    </h3>
                    <p class="text-xs text-gray-600 leading-relaxed">
                        {{ $selectedJob->description }}
                    </p>
                </div>

                <!-- WORKSPACE ACCESSIBILITY MATRIX (Exact Figma Match) -->
                <section class="mt-8 rounded-xl bg-[#F0FDF4] border border-[#BBF7D0] p-5 shadow-xs space-y-4" aria-labelledby="matrix-title">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#176B47]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <h3 id="matrix-title" class="font-bold text-sm text-[#1A2840]">
                                Workspace Accessibility Matrix
                            </h3>
                        </div>
                        <p class="text-xs text-[#176B47] font-medium">
                            Employer guaranteed support &middot; All 3 needs from your saved profile are supported, plus 2 additional interview and assessment resources.
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs" role="table">
                            <thead class="text-[11px] font-bold text-gray-500 uppercase tracking-wider border-b border-[#DCFCE7]">
                                <tr>
                                    <th scope="col" class="pb-2.5">ACCOMMODATION &amp; SUPPORT</th>
                                    <th scope="col" class="pb-2.5 text-right">AVAILABILITY</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#DCFCE7]">
                                @foreach($selectedJob->jobAccommodations as $jAcc)
                                    <tr>
                                        <td class="py-3 pr-4">
                                            <div class="font-bold text-xs text-[#1A2840]">
                                                {{ $jAcc->accommodation->name }}
                                            </div>
                                            @if($jAcc->scope)
                                                <div class="text-[11px] font-medium text-emerald-800 mt-0.5">
                                                    {{ $jAcc->scope }}
                                                </div>
                                            @endif
                                            @if($jAcc->description)
                                                <div class="text-[11px] text-gray-600 mt-0.5 leading-snug">
                                                    {{ $jAcc->description }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-3 text-right whitespace-nowrap align-top">
                                            <span class="inline-flex items-center text-xs font-bold text-[#176B47]">
                                                {{ $jAcc->availability ?? '+ Guaranteed' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- What you'll do -->
                @if(!empty($selectedJob->what_you_will_do))
                    <div class="mt-8 space-y-3">
                        <h3 class="text-sm font-bold text-[#1A2840]">What you'll do</h3>
                        <ul class="space-y-2 text-xs text-gray-600 list-disc list-inside">
                            @foreach($selectedJob->what_you_will_do as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- What you'll bring -->
                @if(!empty($selectedJob->what_you_will_bring))
                    <div class="mt-6 space-y-3">
                        <h3 class="text-sm font-bold text-[#1A2840]">What you'll bring</h3>
                        <ul class="space-y-2 text-xs text-gray-600 list-disc list-inside">
                            @foreach($selectedJob->what_you_will_bring as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Benefits & support -->
                @if(!empty($selectedJob->benefits))
                    <div class="mt-6 space-y-3">
                        <h3 class="text-sm font-bold text-[#1A2840]">Benefits &amp; support</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($selectedJob->benefits as $benefit)
                                <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-semibold bg-[#DCEAF4] text-[#1A2840]">
                                    {{ $benefit }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- About Employer -->
                <div class="mt-6 space-y-2">
                    <h3 class="text-sm font-bold text-[#1A2840]">
                        About {{ $selectedJob->employerProfile->company_name }}
                    </h3>
                    <p class="text-xs text-gray-600 leading-relaxed">
                        A Philippine digital product studio building practical, modern web experiences. Our remote-first team values clear communication, accessible tools, and equal opportunities for early-career talent.
                    </p>
                </div>

                <!-- Footer Application Box -->
                <div class="mt-8 rounded-xl bg-[#F4F7FA] border border-[#B8D0E2] p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-sm text-[#1A2840]">
                            Your application is ready, {{ $user->first_name ?? 'Maria' }}
                        </h4>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#E8F5E9] text-[#176B47] border border-[#A5D6A7]">
                            100% profile complete
                        </span>
                    </div>

                    <p class="text-xs text-gray-600 leading-relaxed">
                        Your application uses your verified government digital account to share your resume &amp; credentials, FSL introduction and accommodations.
                    </p>

                    <div class="flex flex-wrap items-center gap-4 text-xs text-gray-700">
                        <div class="flex items-center gap-1.5 text-[#176B47] font-medium">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>Digital profile</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-[#176B47] font-medium">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>Resume on talent</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-[#176B47] font-medium">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>PWD ID &amp; accommodation sheet</span>
                        </div>
                    </div>

                    <a href="{{ route('candidate.apply', $selectedJob) }}"
                            class="block w-full py-3 rounded-lg bg-[#244F73] text-white text-xs font-bold hover:bg-[#1A2840] shadow-sm transition-colors focus:ring-2 focus:ring-[#244F73] text-center">
                        Apply Now &rarr;
                    </a>

                    <p class="text-[11px] text-center text-gray-500">
                        Your profile is shared with the employer only when you apply.
                    </p>
                </div>
            @else
                <!-- State A: Empty Selection View (Image 2) -->
                <div class="h-full flex flex-col items-center justify-center text-center p-8 space-y-4 my-auto min-h-[450px]">
                    <div class="w-16 h-16 rounded-full bg-sky-50 text-[#244F73] flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>

                    <div class="max-w-xs space-y-2">
                        <h3 class="text-base font-bold text-[#1A2840]">
                            Select a job to view details
                        </h3>
                        <p class="text-xs text-gray-500 leading-relaxed">
                            Explore your matches on the left. Select a job to see the role, pay, and accessibility support.
                        </p>
                    </div>

                    <div class="flex items-center gap-1.5 text-xs text-gray-400">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Your profile is shared only when you apply.</span>
                    </div>

                    <button type="button"
                            wire:click="toggleFullScreen"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-600 hover:text-gray-900 border border-gray-300 rounded-md px-4 py-2 mt-2 focus:ring-2 focus:ring-[#244F73]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                        <span>Full Screen</span>
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

