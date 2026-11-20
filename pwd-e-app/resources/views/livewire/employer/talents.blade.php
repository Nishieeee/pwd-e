<div class="space-y-6">
    {{-- Flash Invitation Banner --}}
    @if(session('invitation_success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between gap-4 shadow-xs" role="alert" aria-live="polite">
            <div class="flex items-center gap-3">
                <div class="size-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </div>
                <div class="text-xs sm:text-sm font-semibold">
                    {{ session('invitation_success') }}
                </div>
            </div>
            <button type="button" @click="$el.closest('[role=alert]').remove()" class="text-emerald-700 hover:text-emerald-900 text-xs font-bold" aria-label="Dismiss alert">&times;</button>
        </div>
    @endif

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-primary-border">
        <div>
            <div class="flex items-center gap-2 text-xs text-body-muted">
                <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Employer Portal</a>
                <span>/</span>
                <span class="text-primary-dark font-medium">Talent Discovery</span>
            </div>
            <h1 class="font-heading text-2xl font-bold text-primary-dark mt-1 flex items-center gap-2.5">
                <span>{{ __('Talent Discovery') }}</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary-tint text-primary-dark border border-primary-border">
                    {{ $candidates->total() }} {{ __('Available') }}
                </span>
            </h1>
            <p class="text-xs text-body-muted mt-0.5">
                {{ __('Proactively discover and source verified PWD candidates qualified for your open roles under RA 10524.') }}
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('employer.jobs.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary-dark px-4 py-2.5 text-xs font-bold text-white hover:bg-primary shadow-xs transition-colors">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>{{ __('Post New Job') }}</span>
            </a>
        </div>
    </div>

    {{-- Search & Multi-Filter Control Card --}}
    <div class="rounded-2xl border border-primary-border bg-white p-5 shadow-sm space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
            {{-- Search Bar --}}
            <div class="md:col-span-5 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-body-muted">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="{{ __('Search by role, skills, keywords, or location (e.g., Software Engineer, PHP, Cebu)...') }}"
                       class="w-full pl-10 pr-4 py-2 text-xs rounded-xl border border-primary-border bg-surface text-primary-dark placeholder:text-body-muted/70 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all">
                @if($search)
                    <button type="button" wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-body-muted hover:text-primary-dark text-xs" aria-label="Clear search">
                        ✕
                    </button>
                @endif
            </div>

            {{-- Accommodation Checklist Filter --}}
            <div class="md:col-span-3 relative" x-data="{ open: false, filterText: '' }">
                <button type="button"
                        @click="open = !open"
                        class="w-full py-2 px-3 text-xs rounded-xl border border-primary-border bg-surface text-primary-dark flex items-center justify-between gap-2 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all text-left"
                        :class="{ 'ring-2 ring-primary/40 border-primary bg-white': open || {{ count($selectedAccommodations) > 0 ? 'true' : 'false' }} }"
                        aria-haspopup="true"
                        :aria-expanded="open.toString()">
                    <span class="truncate">
                        @if(count($selectedAccommodations) === 0)
                            <span class="text-primary-dark">{{ __('All Accommodations') }}</span>
                        @elseif(count($selectedAccommodations) === 1)
                            <span class="font-bold text-primary-dark truncate">{{ $selectedAccommodations[0] }}</span>
                        @else
                            <span class="font-bold text-primary-dark">{{ count($selectedAccommodations) }} {{ __('Accommodations') }}</span>
                        @endif
                    </span>
                    <div class="flex items-center gap-1.5 shrink-0 text-body-muted">
                        @if(count($selectedAccommodations) > 0)
                            <span class="size-4 rounded-full bg-secondary text-white text-[10px] font-bold flex items-center justify-center">
                                {{ count($selectedAccommodations) }}
                            </span>
                        @endif
                        <svg class="size-3.5 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </button>

                {{-- Checklist Popover Dropdown Panel --}}
                <div x-show="open"
                     @click.outside="open = false"
                     @keydown.escape.window="open = false"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                     class="absolute left-0 right-0 z-50 mt-1.5 bg-white rounded-2xl border border-primary-border shadow-xl p-3 space-y-2 min-w-[280px]"
                     style="display: none;">

                    {{-- Search inside checklist --}}
                    <div class="relative">
                        <input type="text"
                               x-model="filterText"
                               placeholder="{{ __('Filter accommodations...') }}"
                               class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                        <svg class="size-3.5 text-gray-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    {{-- Header bar inside checklist --}}
                    <div class="flex items-center justify-between text-[11px] text-body-muted px-1 pt-1 border-b border-gray-100 pb-1.5">
                        <span>{{ __('Select required accommodations') }}</span>
                        @if(count($selectedAccommodations) > 0)
                            <button type="button" wire:click="clearAccommodations" class="text-xs font-semibold text-secondary hover:underline">
                                {{ __('Clear all') }}
                            </button>
                        @endif
                    </div>

                    {{-- Checkbox List Items --}}
                    <div class="max-h-60 overflow-y-auto space-y-1 pr-1">
                        @foreach($accommodations as $acc)
                            <label x-show="!filterText || '{{ strtolower(addslashes($acc->name)) }}'.includes(filterText.toLowerCase())"
                                   class="flex items-start gap-2.5 p-2 rounded-lg hover:bg-surface cursor-pointer text-xs transition-colors select-none">
                                <input type="checkbox"
                                       wire:model.live="selectedAccommodations"
                                       value="{{ $acc->name }}"
                                       class="mt-0.5 size-4 rounded text-secondary focus:ring-secondary border-gray-300">
                                <div class="min-w-0 flex-1">
                                    <span class="font-medium text-primary-dark block leading-snug">{{ $acc->name }}</span>
                                    @if($acc->category)
                                        <span class="text-[10px] text-body-muted capitalize">{{ str_replace('_', ' ', $acc->category) }}</span>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>

                    {{-- Bottom Action --}}
                    <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-[11px] text-body-muted">
                            {{ count($selectedAccommodations) }} {{ __('selected') }}
                        </span>
                        <button type="button" @click="open = false" class="px-3 py-1 bg-primary-dark text-white rounded-lg text-xs font-semibold hover:bg-primary transition-colors">
                            {{ __('Done') }}
                        </button>
                    </div>
                </div>
            </div>

            {{-- Education Filter --}}
            <div class="md:col-span-2">
                <select wire:model.live="selectedEducation"
                        class="w-full py-2 px-3 text-xs rounded-xl border border-primary-border bg-surface text-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all">
                    <option value="">{{ __('All Education') }}</option>
                    <option value="College">{{ __('College / Bachelor') }}</option>
                    <option value="Vocational">{{ __('Vocational / Tech') }}</option>
                    <option value="High School">{{ __('High School') }}</option>
                    <option value="Master">{{ __('Master / Postgrad') }}</option>
                </select>
            </div>

            {{-- Sort Order --}}
            <div class="md:col-span-2">
                <select wire:model.live="sort"
                        class="w-full py-2 px-3 text-xs rounded-xl border border-primary-border bg-surface text-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all">
                    <option value="recent">{{ __('Recently Active') }}</option>
                    <option value="name_asc">{{ __('Name (A-Z)') }}</option>
                </select>
            </div>
        </div>

        {{-- Selected Accommodation Tag Chips --}}
        @if(count($selectedAccommodations) > 0)
            <div class="flex flex-wrap items-center gap-1.5 pt-2 border-t border-primary-border/30">
                <span class="text-[11px] font-semibold text-body-muted uppercase tracking-wider mr-1">{{ __('Active Accommodations:') }}</span>
                @foreach($selectedAccommodations as $accName)
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-50 text-sky-800 border border-sky-200 shadow-2xs">
                        <span>{{ $accName }}</span>
                        <button type="button" wire:click="removeAccommodation('{{ addslashes($accName) }}')" class="hover:text-red-600 font-bold ml-0.5 focus:outline-none" aria-label="Remove {{ $accName }}">&times;</button>
                    </span>
                @endforeach
                <button type="button" wire:click="clearAccommodations" class="text-xs font-semibold text-secondary hover:underline ml-1">
                    {{ __('Clear all') }}
                </button>
            </div>
        @endif

        {{-- Second Row: Verified PWD filter & active reset --}}
        <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-primary-border/40 text-xs">
            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                <input type="checkbox"
                       wire:model.live="verifiedOnly"
                       class="size-4 rounded text-secondary focus:ring-secondary border-gray-300">
                <span class="font-semibold text-primary-dark">{{ __('Show RA 10524 Verified PWD Only') }}</span>
                <span class="text-[11px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200 font-bold">Tax-deductible hire</span>
            </label>

            @if($search || count($selectedAccommodations) > 0 || $selectedEducation || $verifiedOnly || $sort !== 'recent')
                <button type="button"
                        wire:click="resetFilters"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-secondary hover:underline">
                    <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>{{ __('Reset all filters') }}</span>
                </button>
            @endif
        </div>
    </div>

    {{-- Talent Grid --}}
    @if($candidates->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($candidates as $candidate)
                @php
                    $user = $candidate->user;
                    $skills = is_array($candidate->skills) ? $candidate->skills : [];
                    $workCount = $candidate->workExperiences->count();
                    $isVerified = $user?->isVerified() ?? false;
                @endphp
                <div class="rounded-2xl border border-primary-border bg-white p-5 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between space-y-4">
                    <div class="space-y-3.5">
                        {{-- Top Bar: Avatar & Verification Badge --}}
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                @if($candidate->avatar_path)
                                    <img src="{{ asset('storage/' . $candidate->avatar_path) }}"
                                         alt="{{ $user->name }}"
                                         class="size-12 rounded-xl object-cover border border-primary-border/60 shrink-0 shadow-2xs">
                                @else
                                    <div class="size-12 rounded-xl bg-primary-tint text-primary-dark font-bold text-sm flex items-center justify-center border border-primary-border/60 shrink-0">
                                        {{ $user->initials() }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <h2 class="font-bold text-sm sm:text-base text-primary-dark truncate leading-snug">
                                        {{ $user->name }}@if($candidate->suffix) <span class="text-body-muted text-xs font-normal">{{ $candidate->suffix }}</span>@endif
                                    </h2>
                                    <p class="text-xs font-semibold text-primary truncate mt-0.5">
                                        {{ $candidate->headline ?: __('Qualified Professional') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Verified PWD Status Badge --}}
                        <div class="flex items-center gap-2">
                            @if($isVerified)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    <svg class="size-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    <span>RA 10524 Verified</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 text-gray-700">
                                    <span>Candidate Profile</span>
                                </span>
                            @endif

                            @if($workCount > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-primary-tint text-primary-dark">
                                    {{ $workCount }} {{ Str::plural('Experience', $workCount) }}
                                </span>
                            @endif
                        </div>

                        {{-- Location & Education Metadata --}}
                        <div class="space-y-1.5 text-xs text-body-muted pt-1">
                            @if($candidate->city || $candidate->location)
                                <div class="flex items-center gap-1.5 truncate">
                                    <svg class="size-3.5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span class="truncate">{{ $candidate->city ?: $candidate->location }}</span>
                                </div>
                            @endif

                            @if($candidate->highest_education)
                                <div class="flex items-center gap-1.5 truncate">
                                    <svg class="size-3.5 text-secondary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                                    <span class="truncate">{{ $candidate->highest_education }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Skills Chips --}}
                        @if(count($skills) > 0)
                            <div class="pt-1">
                                <div class="text-[11px] font-semibold text-body-muted uppercase tracking-wider mb-1.5">{{ __('Skills') }}</div>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach(array_slice($skills, 0, 4) as $skill)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                            {{ $skill }}
                                        </span>
                                    @endforeach
                                    @if(count($skills) > 4)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-semibold text-body-muted bg-gray-50">
                                            +{{ count($skills) - 4 }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Workplace Accommodations --}}
                        @if($candidate->accommodations->isNotEmpty())
                            <div class="pt-1">
                                <div class="text-[11px] font-semibold text-body-muted uppercase tracking-wider mb-1.5">{{ __('Accommodations') }}</div>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($candidate->accommodations->take(3) as $acc)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium bg-sky-50 text-sky-800 border border-sky-200">
                                            <span class="size-1.5 rounded-full bg-sky-500"></span>
                                            <span class="truncate max-w-[130px]">{{ $acc->name }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Actions: View Profile & Invite to Apply / Withdraw --}}
                    <div class="pt-3 border-t border-primary-border/60 flex items-center gap-2">
                        <a href="{{ route('employer.candidates.show', $candidate) }}"
                           class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 rounded-xl border border-primary-border bg-white text-xs font-bold text-primary-dark hover:bg-primary-tint hover:border-primary/50 transition-colors shadow-2xs">
                            <svg class="size-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>{{ __('View Profile') }}</span>
                        </a>

                        @php
                            $pendingInvite = $candidate->jobInvitations?->first();
                        @endphp
                        @if($pendingInvite)
                            <div class="flex-1 flex items-center gap-1.5">
                                <span class="flex-1 inline-flex items-center justify-center gap-1 px-2.5 py-2 rounded-xl bg-amber-50 text-amber-800 border border-amber-200 text-xs font-semibold truncate" title="{{ __('Invited for :job', ['job' => $pendingInvite->jobPosting?->title]) }}">
                                    <span>✉️ {{ __('Invited') }}</span>
                                </span>
                                <button type="button"
                                        wire:click="openWithdrawModal({{ $pendingInvite->id }})"
                                        class="px-2.5 py-2 rounded-xl border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 text-xs font-bold transition-colors"
                                        title="{{ __('Withdraw invitation with remarks') }}">
                                    {{ __('Withdraw') }}
                                </button>
                            </div>
                        @else
                            <button type="button"
                                    wire:click="openInviteModal({{ $candidate->id }})"
                                    class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 rounded-xl bg-primary-dark text-xs font-bold text-white hover:bg-primary transition-colors shadow-2xs">
                                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>{{ __('Invite to Apply') }}</span>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="pt-4">
            {{ $candidates->links() }}
        </div>
    @else
        {{-- Empty State --}}
        <div class="rounded-2xl border border-primary-border bg-white p-12 text-center max-w-xl mx-auto shadow-sm space-y-4">
            <div class="mx-auto size-14 rounded-full bg-primary-tint text-primary flex items-center justify-center">
                <svg class="size-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
            </div>
            <div>
                <h2 class="font-heading text-lg font-bold text-primary-dark">{{ __('No matching candidate profiles found') }}</h2>
                <p class="text-xs text-body-muted mt-1 leading-relaxed">
                    {{ __('Try relaxing your search terms or clearing the accommodation and education filters to discover more talent.') }}
                </p>
            </div>
            <div>
                <button type="button" wire:click="resetFilters" class="inline-flex items-center gap-2 rounded-xl bg-primary-dark px-4 py-2 text-xs font-bold text-white hover:bg-primary transition-colors shadow-xs">
                    <span>{{ __('Clear all search filters') }}</span>
                </button>
            </div>
        </div>
    @endif

    {{-- Invite to Apply Modal --}}
    @if($showInviteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="invite-modal-title">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-5 border border-primary-border shadow-xl">
                <div class="flex items-start justify-between gap-4 pb-3 border-b border-primary-border">
                    <div>
                        <h2 id="invite-modal-title" class="font-heading text-lg font-bold text-primary-dark">
                            {{ __('Invite Candidate to Apply') }}
                        </h2>
                        <p class="text-xs text-body-muted mt-0.5">
                            {{ __('Send a direct invitation to') }} <strong class="text-primary-dark">{{ $invitingCandidateName }}</strong> ({{ $invitingCandidateHeadline }}).
                        </p>
                    </div>
                    <button type="button" wire:click="closeInviteModal" class="text-body-muted hover:text-primary-dark text-lg font-bold" aria-label="Close modal">&times;</button>
                </div>

                @if($activeJobs->isNotEmpty())
                    <form wire:submit="sendInvitation" class="space-y-4">
                        <div>
                            <label for="select-job" class="block text-xs font-bold text-primary-dark mb-1">
                                {{ __('Select Active Job Posting') }} <span class="text-red-500">*</span>
                            </label>
                            <select id="select-job" wire:model="selectedJobId" class="w-full py-2.5 px-3 text-xs rounded-xl border border-primary-border bg-surface text-primary-dark focus:ring-2 focus:ring-primary/40 focus:border-primary">
                                @foreach($activeJobs as $job)
                                    <option value="{{ $job->id }}">
                                        {{ $job->title }} ({{ $job->location ?: 'Flexible / Remote' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('selectedJobId')
                                <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="invite-message" class="block text-xs font-bold text-primary-dark mb-1">
                                {{ __('Personal Note / Message (Optional)') }}
                            </label>
                            <textarea id="invite-message"
                                      wire:model="invitationMessage"
                                      rows="3"
                                      class="w-full p-3 text-xs rounded-xl border border-primary-border bg-surface text-primary-dark focus:ring-2 focus:ring-primary/40 focus:border-primary placeholder:text-body-muted/70"
                                      placeholder="{{ __('Write a brief note explaining why their qualifications match your team...') }}"></textarea>
                            @error('invitationMessage')
                                <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-primary-border">
                            <button type="button" wire:click="closeInviteModal" class="px-4 py-2 rounded-xl border border-primary-border text-xs font-semibold text-primary-dark hover:bg-gray-50 transition-colors">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit" class="px-5 py-2 rounded-xl bg-primary-dark text-xs font-bold text-white hover:bg-primary shadow-xs transition-colors inline-flex items-center gap-1.5">
                                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                <span>{{ __('Send Invitation') }}</span>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs space-y-3">
                        <p class="font-bold">{{ __('No active job postings found') }}</p>
                        <p>{{ __('You must have at least one published job listing before you can invite candidates to apply.') }}</p>
                        <div>
                            <a href="{{ route('employer.jobs.create') }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-primary-dark text-white font-bold text-xs hover:bg-primary transition-colors">
                                <span>{{ __('Create a Job Posting') }} &rarr;</span>
                            </a>
                        </div>
                    </div>
                    <div class="flex justify-end pt-2">
                        <button type="button" wire:click="closeInviteModal" class="px-4 py-2 rounded-xl border border-primary-border text-xs font-semibold text-primary-dark hover:bg-gray-50">
                            {{ __('Close') }}
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Withdraw Invitation Modal --}}
    @if($showWithdrawModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="withdraw-modal-title">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-5 border border-primary-border shadow-xl">
                <div class="flex items-start justify-between gap-4 pb-3 border-b border-primary-border">
                    <div>
                        <h2 id="withdraw-modal-title" class="font-heading text-lg font-bold text-red-700 flex items-center gap-2">
                            <svg class="size-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>{{ __('Withdraw Job Invitation') }}</span>
                        </h2>
                        <p class="text-xs text-body-muted mt-0.5">
                            {{ __('Withdraw invitation for') }} <strong class="text-primary-dark">{{ $withdrawalCandidateName }}</strong> {{ __('for') }} <strong class="text-primary-dark">{{ $withdrawalJobTitle }}</strong>.
                        </p>
                    </div>
                    <button type="button" wire:click="closeWithdrawModal" class="text-body-muted hover:text-primary-dark text-lg font-bold" aria-label="Close modal">&times;</button>
                </div>

                <form wire:submit="confirmWithdrawInvitation" class="space-y-4">
                    <div>
                        <label for="withdraw-reason" class="block text-xs font-bold text-primary-dark mb-1">
                            {{ __('Reason for Withdrawal / Remarks') }} <span class="text-red-500">*</span>
                        </label>
                        <textarea id="withdraw-reason"
                                  wire:model="withdrawalReason"
                                  rows="3"
                                  class="w-full p-3 text-xs rounded-xl border border-primary-border bg-surface text-primary-dark focus:ring-2 focus:ring-red-400 focus:border-red-500 placeholder:text-body-muted/70"
                                  placeholder="{{ __('e.g., Position has been filled internally, role qualifications updated, or requisition closed...') }}"></textarea>
                        @error('withdrawalReason')
                            <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Quick preset remark buttons --}}
                    <div>
                        <span class="text-[11px] text-body-muted block mb-1.5">{{ __('Quick reasons:') }}</span>
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" wire:click="$set('withdrawalReason', 'Position filled internally')" class="text-[11px] px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium">Position filled</button>
                            <button type="button" wire:click="$set('withdrawalReason', 'Role requirements / specifications changed')" class="text-[11px] px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium">Requirements changed</button>
                            <button type="button" wire:click="$set('withdrawalReason', 'Job opening paused or closed')" class="text-[11px] px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium">Opening paused</button>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-primary-border">
                        <button type="button" wire:click="closeWithdrawModal" class="px-4 py-2 rounded-xl border border-primary-border text-xs font-semibold text-primary-dark hover:bg-gray-50 transition-colors">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-red-600 text-xs font-bold text-white hover:bg-red-700 shadow-xs transition-colors inline-flex items-center gap-1.5">
                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>{{ __('Confirm Withdrawal') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
