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

            {{-- Accommodation Filter --}}
            <div class="md:col-span-3">
                <select wire:model.live="selectedAccommodation"
                        class="w-full py-2 px-3 text-xs rounded-xl border border-primary-border bg-surface text-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all">
                    <option value="">{{ __('All Accommodations') }}</option>
                    @foreach($accommodations as $acc)
                        <option value="{{ $acc->name }}">{{ $acc->name }}</option>
                    @endforeach
                </select>
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

        {{-- Second Row: Verified PWD filter & active reset --}}
        <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-primary-border/40 text-xs">
            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                <input type="checkbox"
                       wire:model.live="verifiedOnly"
                       class="size-4 rounded text-secondary focus:ring-secondary border-gray-300">
                <span class="font-semibold text-primary-dark">{{ __('Show RA 10524 Verified PWD Only') }}</span>
                <span class="text-[11px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200 font-bold">Tax-deductible hire</span>
            </label>

            @if($search || $selectedAccommodation || $selectedEducation || $verifiedOnly || $sort !== 'recent')
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

                    {{-- Actions: View Profile & Invite to Apply --}}
                    <div class="pt-3 border-t border-primary-border/60 flex items-center gap-2">
                        <a href="{{ route('employer.candidates.show', $candidate) }}"
                           class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 rounded-xl border border-primary-border bg-white text-xs font-bold text-primary-dark hover:bg-primary-tint hover:border-primary/50 transition-colors shadow-2xs">
                            <svg class="size-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>{{ __('View Profile') }}</span>
                        </a>

                        <button type="button"
                                wire:click="openInviteModal({{ $candidate->id }})"
                                class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 rounded-xl bg-primary-dark text-xs font-bold text-white hover:bg-primary transition-colors shadow-2xs">
                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>{{ __('Invite to Apply') }}</span>
                        </button>
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
</div>
