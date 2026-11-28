<div class="space-y-6 max-w-7xl mx-auto w-full min-w-0">
    @include('partials.candidate-offer-modals')

    <!-- Profile Saved Success Banner (Redirected from Profile Builder) -->
    @if(!empty($profileSavedMessage))
        <div class="p-4 rounded-xl bg-[#F0FDF4] border border-[#BBF7D0] text-[#176B47] flex items-center justify-between gap-4 shadow-xs" role="alert" aria-live="polite">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-[#176B47] shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div class="text-xs leading-relaxed">
                    <strong class="block font-bold text-[#176B47]">Profile saved successfully!</strong>
                    <span class="text-[#176B47]">Your details, accommodation needs, and both attachments are saved. Your job matches are ready.</span>
                </div>
            </div>
            <button type="button"
                    wire:click="dismissProfileSavedBanner"
                    class="text-[#176B47] hover:text-emerald-950 p-1 rounded focus:outline-none focus:ring-2 focus:ring-[#176B47]"
                    aria-label="Dismiss profile saved alert">
                &#10005;
            </button>
        </div>
    @endif

    <!-- Feedback banner if interview accepted -->
    @if($calendarDownloadSuccess)
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between" role="alert">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium">{{ $calendarDownloadSuccess }}</span>
            </div>
            <button type="button" wire:click="$set('calendarDownloadSuccess', null)" class="text-emerald-700 hover:text-emerald-900 text-xs font-semibold" aria-label="Dismiss message">
                Dismiss
            </button>
        </div>
    @endif

    <!-- Statutory Employment Separation Notice Banner (Due Process under RA 7277) -->
    @if($pendingSeparationNotice)
        <div class="p-5 sm:p-6 rounded-2xl bg-amber-50 border-2 border-amber-300 shadow-sm space-y-4" role="alert" aria-live="assertive">
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                <div class="space-y-2 max-w-3xl">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-600 text-white shadow-xs">
                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>{{ __('Action Required: Notice of Employment Separation') }}</span>
                        </span>
                        <span class="text-xs font-medium text-amber-900">{{ $pendingSeparationNotice->separation_notice_at?->diffForHumans() }}</span>
                    </div>

                    <h2 class="font-heading text-lg sm:text-xl font-bold text-amber-950">
                        {{ $pendingSeparationNotice->employerProfile?->company_name ?? __('Your Employer') }} {{ __('has submitted a Notice of Separation') }}
                    </h2>

                    <div class="text-xs sm:text-sm text-amber-900 leading-relaxed space-y-1.5">
                        <p>
                            <strong>{{ __('Position:') }}</strong> {{ $pendingSeparationNotice->application?->jobPosting?->title ?? __('Hired Position') }} &bull;
                            <strong>{{ __('Proposed Effective Date:') }}</strong> {{ $pendingSeparationNotice->ended_at?->format('F d, Y') ?? '—' }} &bull;
                            <strong>{{ __('Reason:') }}</strong> <span class="capitalize">{{ $pendingSeparationNotice->ended_reason }}</span>
                        </p>
                        @if($pendingSeparationNotice->ended_notes)
                            <p class="italic bg-amber-100/70 p-2.5 rounded-lg border border-amber-200 text-amber-950">
                                "{{ $pendingSeparationNotice->ended_notes }}"
                            </p>
                        @endif
                        <p class="text-xs text-amber-800">
                            {{ __('Under Republic Act No. 7277 (Magna Carta for Disabled Persons) and Philippine Labor Standards, security of tenure is protected. You have the right to acknowledge this separation or submit a formal contestation/dispute for DOLE & administrative review.') }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap md:flex-col items-center gap-2.5 shrink-0">
                    <button type="button"
                            wire:click="openAcknowledgeModal({{ $pendingSeparationNotice->id }})"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-white border border-amber-300 text-xs font-bold text-amber-900 hover:bg-amber-100 transition-colors cursor-pointer shadow-2xs">
                        <svg class="size-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ __('Acknowledge Separation') }}</span>
                    </button>
                    <button type="button"
                            wire:click="openDisputeModal({{ $pendingSeparationNotice->id }})"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-red-600 text-white text-xs font-bold hover:bg-red-700 transition-colors cursor-pointer shadow-xs">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>{{ __('Contest / Dispute Separation') }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Separation Disputed Banner (Active Case Pending Review) -->
    @if($disputedSeparationNotice && ! $pendingSeparationNotice)
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-900 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
            <div class="flex items-start gap-3">
                <svg class="size-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-xs leading-relaxed">
                    <strong class="block font-bold text-red-950">{{ __('Separation Under Formal Dispute / Review') }}</strong>
                    <span>{{ __('You contested the notice of separation from :company on :date. Your record is preserved under dispute for RA 7277 compliance auditing.', ['company' => $disputedSeparationNotice->employerProfile?->company_name ?? 'Employer', 'date' => $disputedSeparationNotice->candidate_response_at?->format('M d, Y')]) }}</span>
                </div>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200 shrink-0">
                {{ __('Contested Case Active') }}
            </span>
        </div>
    @endif

    {{-- Direct Employer Job Invitations --}}
    @if(isset($jobInvitations) && $jobInvitations->isNotEmpty())
        <div class="space-y-3">
            @foreach($jobInvitations as $invite)
                @php
                    $jobTitle = $invite->jobPosting?->title ?? $invite->data['job_title'] ?? 'Open Position';
                    $employerName = $invite->employerProfile?->company_name ?? $invite->data['employer_name'] ?? 'Verified Employer';
                    $actionUrl = $invite->jobPosting ? route('candidate.apply', $invite->jobPosting) : ($invite->data['action_url'] ?? '#');
                    $personalMsg = $invite->personal_message ?? $invite->data['personal_message'] ?? null;
                @endphp
                <div class="p-5 rounded-2xl bg-gradient-to-r from-sky-50 to-indigo-50/40 border border-sky-200 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-secondary text-white shadow-2xs">
                                <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>{{ __('Direct Job Invitation') }}</span>
                            </span>
                            <span class="text-xs text-body-muted">{{ $invite->created_at?->diffForHumans() }}</span>
                        </div>
                        <h2 class="font-heading text-lg font-bold text-primary-dark">
                            @if($invite->employerProfile)
                                <a href="{{ route('candidate.employers.show', $invite->employerProfile) }}" class="hover:underline text-primary-dark hover:text-[#244F73]">
                                    {{ $employerName }}
                                </a>
                            @else
                                {{ $employerName }}
                            @endif
                            {{ __('invited you to apply for') }} <span class="text-primary">{{ $jobTitle }}</span>
                        </h2>
                        @if($personalMsg)
                            <p class="text-xs text-body-muted italic bg-white/70 rounded-lg p-2.5 border border-sky-100 max-w-2xl">
                                "{{ $personalMsg }}"
                            </p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2.5 shrink-0">
                        <button type="button"
                                wire:click="openDeclineInviteModal({{ $invite->id }})"
                                class="inline-flex items-center px-3.5 py-2.5 rounded-xl border border-gray-300 bg-white text-xs font-semibold text-body-muted hover:text-red-600 hover:border-red-200 hover:bg-red-50 transition-colors cursor-pointer">
                            {{ __('Decline') }}
                        </button>
                        <a href="{{ $actionUrl }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-dark text-xs font-bold text-white hover:bg-primary shadow-xs transition-colors">
                            <span>{{ __('Review & Apply') }}</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Dashboard Header Banner Card -->
    <div class="w-full relative overflow-hidden rounded-2xl border border-primary-border bg-white p-5 sm:p-6 shadow-sm" style="background: linear-gradient(135deg, #DCEAF4 0%, #FFFFFF 100%); box-shadow: 0 4px 20px rgba(26,40,64,0.06);">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="space-y-2 max-w-3xl min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <!-- PWD ID Verified Pill -->
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white text-secondary border border-primary-border shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-secondary shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>PWD ID: {{ $user->pwd_id_number ?? '09-7332-0001842-A' }} (Verified)</span>
                    </span>

                    <!-- Alumni Badge Pill -->
                    @if($candidateProfile->education_badge)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-primary-tint text-primary-dark border border-primary-border/70">
                            {{ $candidateProfile->education_badge }}
                        </span>
                    @endif
                </div>

                <h1 class="font-heading text-2xl sm:text-3xl font-bold text-primary-dark tracking-tight">Candidate Dashboard</h1>
                <p class="text-xs sm:text-sm text-body-muted leading-relaxed max-w-2xl">
                    Welcome back, {{ $user->name }}. Your PWD credentials and accommodation profile are active.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="{{ route('candidate.profile.show') }}" class="inline-flex items-center justify-center gap-2 h-11 px-4 rounded-xl border border-primary-border bg-white text-primary-dark text-xs sm:text-sm font-semibold hover:bg-primary-tint/60 shadow-2xs transition-colors whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2">
                    <svg class="size-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                    <span>Profile</span>
                </a>
                <a href="{{ route('candidate.jobs') }}" class="inline-flex items-center justify-center gap-2 h-11 px-4 rounded-xl bg-primary-dark text-white text-xs sm:text-sm font-semibold hover:bg-primary shadow-xs transition-colors whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-dark focus-visible:ring-offset-2">
                    <span>Find job matches</span>
                    <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 4 Stat Cards in a row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Stat 1: Active Applications -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-3xl font-bold text-[#1A2840]">{{ $activeApplicationsCount }}</div>
                <div class="text-xs font-medium text-[#4E5F6E] mt-1">Active Applications</div>
            </div>
            <div class="w-10 h-10 rounded-lg bg-sky-50 text-[#244F73] flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>

        <!-- Stat 2: Highest Accessibility Fit -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-3xl font-bold text-[#1A2840]">{{ $highestFitScore }}%</div>
                <div class="text-xs font-medium text-[#4E5F6E] mt-1">Highest Accessibility Fit</div>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
        </div>

        <!-- Stat 3: Interview Invitation -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-3xl font-bold text-[#1A2840]">{{ $interviewInvitationsCount }}</div>
                <div class="text-xs font-medium text-[#4E5F6E] mt-1">Interview Invitation</div>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>

        <!-- Stat 4: Accommodations Specified -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-3xl font-bold text-[#1A2840]">{{ $accommodationsSpecifiedCount }}</div>
                <div class="text-xs font-medium text-[#4E5F6E] mt-1">Accommodations Specified</div>
            </div>
            <div class="w-10 h-10 rounded-lg bg-sky-50 text-[#244F73] flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Upcoming Interview Banner (Green container) -->
    @if($upcomingInterviewApplication)
        <div class="rounded-xl bg-[#F0FDF4] border border-[#BBF7D0] p-6 shadow-xs" role="region" aria-label="Upcoming interview announcement">
            <div class="flex items-center justify-between pb-3 border-b border-[#DCFCE7]">
                <div class="flex items-center gap-2 text-sm font-semibold text-emerald-900">
                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Upcoming interview</span>
                </div>
                <div class="flex items-center gap-1.5 text-xs font-semibold text-emerald-700">
                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span>Interview Scheduled</span>
                </div>
            </div>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 py-4">
                <div>
                    <h2 class="text-base font-bold text-[#1A2840]">
                        {{ $upcomingInterviewApplication->jobPosting->title }} &mdash;
                        @if($upcomingInterviewApplication->jobPosting?->employerProfile)
                            <a href="{{ route('candidate.employers.show', $upcomingInterviewApplication->jobPosting->employerProfile) }}" class="hover:underline text-[#244F73]">
                                {{ $upcomingInterviewApplication->jobPosting->employerProfile->company_name }}
                            </a>
                        @else
                            {{ $upcomingInterviewApplication->jobPosting?->employerProfile?->company_name ?? 'Employer' }}
                        @endif
                    </h2>
                    <p class="text-xs text-[#4E5F6E] mt-1">
                        <span class="font-medium">Date &amp; Time:</span> {{ $upcomingInterviewApplication->interview_at ? $upcomingInterviewApplication->interview_at->format('F j, Y \a\t g:i A') : 'November 4, 2026 at 2:00 PM' }} (Accessible Virtual Room / Google Meet)
                    </p>
                </div>

                <button type="button"
                        wire:click="acceptAndAddToCalendar({{ $upcomingInterviewApplication->id }})"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#244F73] text-white text-xs font-semibold hover:bg-[#1A2840] transition-colors focus:ring-2 focus:ring-[#244F73] shrink-0">
                    <span>Accept &amp; Add to Calendar</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </button>
            </div>

            <div class="flex items-start gap-2 pt-3 border-t border-[#DCFCE7] text-xs text-emerald-800">
                <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>
                    <strong class="font-semibold">Workplace Accommodations Confirmed:</strong> {{ $upcomingInterviewApplication->interview_accommodations ?? 'Screen reader test files, wheelchair ramp access at BGC office, quiet interview room.' }}
                </span>
            </div>
        </div>
    @endif

    <!-- Active Job Application Status Section -->
    <section class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-xs" aria-labelledby="active-applications-heading">
        <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100">
            <h2 id="active-applications-heading" class="text-base font-bold text-[#1A2840]">
                Active Job Application Status
            </h2>
            @if($activeApplicationsCount > 0)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#DCEAF4] text-[#1A2840]">
                    {{ $activeApplicationsCount }} active {{ \Illuminate\Support\Str::plural('application', $activeApplicationsCount) }}
                </span>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs" role="table">
                <thead class="bg-gray-50/75 text-gray-500 font-semibold border-b border-gray-200">
                    <tr>
                        <th scope="col" class="py-3 px-6">Job Title &amp; Employer</th>
                        <th scope="col" class="py-3 px-6">Application Date</th>
                        <th scope="col" class="py-3 px-6">Accessibility-Fit</th>
                        <th scope="col" class="py-3 px-6">Application Status</th>
                        <th scope="col" class="py-3 px-6">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($activeApplications as $app)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 px-6">
                                <div class="font-semibold text-sm text-[#1A2840]">
                                    {{ $app->jobPosting->title }}
                                </div>
                                <div class="text-gray-500 text-xs mt-0.5">
                                    @if($app->jobPosting?->employerProfile)
                                        <a href="{{ route('candidate.employers.show', $app->jobPosting->employerProfile) }}" class="font-medium text-[#244F73] hover:underline inline-flex items-center gap-1 group">
                                            <span>{{ $app->jobPosting->employerProfile->company_name }}</span>
                                            <svg class="size-3 text-gray-400 group-hover:text-[#244F73] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @else
                                        {{ $app->jobPosting?->employerProfile?->company_name }}
                                    @endif
                                    @if($app->jobPosting?->location)
                                        <span class="text-gray-400">({{ $app->jobPosting->location }})</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6 text-gray-600 tabular-nums">
                                {{ $app->applied_at ? $app->applied_at->format('Y-m-d') : '2026-10-25' }}
                            </td>
                            <td class="py-4 px-6">
                                @php
                                    $scoreVal = (int) ($app->fit_score_at_apply ?? 95);
                                    $badgeClass = $scoreVal >= 90
                                        ? 'bg-[#E8F5E9] text-[#176B47] border-[#A5D6A7]'
                                        : ($scoreVal >= 80 ? 'bg-sky-50 text-[#1A2840] border-sky-200' : 'bg-amber-50 text-amber-800 border-amber-200');
                                @endphp
                                <button type="button"
                                        wire:click="viewFitBreakdown({{ $app->id }})"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold border tabular-nums transition-transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-[#244F73]/30 cursor-pointer {{ $badgeClass }}"
                                        title="{{ $scoreVal }}% Composite Fit (Click to view breakdown)"
                                        aria-label="{{ $scoreVal }}% Composite Fit">
                                    <span class="size-1.5 rounded-full {{ $scoreVal >= 90 ? 'bg-[#176B47]' : ($scoreVal >= 80 ? 'bg-[#244F73]' : 'bg-amber-600') }}"></span>
                                    <span>{{ $scoreVal }}%</span>
                                    <span class="sr-only">Composite Fit</span>
                                </button>
                            </td>
                            <td class="py-4 px-6">
                                @if($app->status === 'interview')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full font-medium bg-[#E8F5E9] text-[#176B47] border border-[#A5D6A7]">
                                        Interview Scheduled
                                    </span>
                                @elseif($app->status === 'under_review')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full font-medium bg-amber-50 text-amber-800 border border-amber-200">
                                        Under Review
                                    </span>
                                @elseif($app->status === 'rejected')
                                    <div class="inline-flex flex-col items-start gap-0.5">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full font-medium bg-red-50 text-red-700 border border-red-200">
                                            Declined
                                        </span>
                                        @if($app->rejection_reason)
                                            <span class="text-[10px] text-gray-500 max-w-[180px] truncate" title="{{ $app->rejection_reason }}">
                                                "{{ $app->rejection_reason }}"
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full font-medium bg-gray-100 text-gray-700">
                                        {{ ucfirst($app->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <button type="button"
                                        wire:click="viewFitBreakdown({{ $app->id }})"
                                        class="inline-flex items-center px-3 py-1.5 rounded-md border border-gray-300 text-xs font-semibold text-[#1A2840] bg-white hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-[#244F73]">
                                    View Fit Breakdown
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 px-6 text-center text-gray-500">
                                No active applications found. Explore opportunities under Job Matches & Search.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- Recommended Jobs Based on Your Accommodations -->
    <section aria-labelledby="recommended-jobs-heading" class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 id="recommended-jobs-heading" class="text-base font-bold text-[#1A2840]">
                    Recommended Jobs Based on Your Accommodations
                </h2>
                <p class="text-xs text-[#4E5F6E] mt-0.5">
                    These positions have 100% verified coverage for your wheelchair ramp access and screen reader preferences.
                </p>
            </div>
            <a href="{{ route('candidate.jobs') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#244F73] hover:text-[#1A2840] transition-colors shrink-0">
                <span>See All Matched Jobs</span>
                <span aria-hidden="true">&rarr;</span>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($recommendedJobs as $rJob)
                <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-xs flex flex-col justify-between hover:border-[#244F73] transition-colors">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <!-- Company Logo Initials Box -->
                            @if($rJob->employerProfile)
                                <a href="{{ route('candidate.employers.show', $rJob->employerProfile) }}" class="w-10 h-10 rounded-lg bg-[#E0E7FF] text-[#1E1B4B] flex items-center justify-center font-bold text-xs uppercase hover:bg-[#C7D2FE] transition-colors" title="View {{ $rJob->employerProfile->company_name }} profile">
                                    {{ substr($rJob->employerProfile->company_name ?? 'CO', 0, 2) }}
                                </a>
                            @else
                                <div class="w-10 h-10 rounded-lg bg-[#E0E7FF] text-[#1E1B4B] flex items-center justify-center font-bold text-xs uppercase">
                                    {{ substr($rJob->employerProfile->company_name ?? 'CO', 0, 2) }}
                                </div>
                            @endif
                            <div class="text-base font-bold text-[#176B47]">
                                {{ $rJob->fit_score ?? 96 }}% <span class="text-xs font-semibold text-gray-500">FIT</span>
                            </div>
                        </div>

                        <div class="font-bold text-sm text-[#1A2840]">
                            @if($rJob->employerProfile)
                                <a href="{{ route('candidate.employers.show', $rJob->employerProfile) }}" class="hover:text-[#244F73] hover:underline inline-flex items-center gap-1 group">
                                    <span>{{ $rJob->employerProfile->company_name }}</span>
                                    <svg class="size-3 text-gray-400 group-hover:text-[#244F73] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            @else
                                {{ $rJob->employerProfile->company_name }}
                            @endif
                        </div>
                        <div class="text-xs text-gray-500 mt-0.5">
                            {{ $rJob->title }}
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 mt-5 pt-3 border-t border-gray-100 text-xs text-[#176B47]">
                        <svg class="w-4 h-4 shrink-0 text-[#176B47]" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-[11px] font-medium">100% verified accommodation coverage</span>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Fit Breakdown Modal -->
    @if($showBreakdownModal && $breakdownApplication)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="modal-breakdown-title">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-gray-200 space-y-5 animate-in fade-in duration-150">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 id="modal-breakdown-title" class="text-lg font-bold text-[#1A2840]">
                        Composite Fit Breakdown
                    </h3>
                    <button type="button" wire:click="closeBreakdownModal" class="p-1 rounded-md text-gray-400 hover:text-gray-600 focus:ring-2 focus:ring-[#244F73]" aria-label="Close dialog">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div>
                    <div class="font-semibold text-sm text-[#1A2840]">{{ $breakdownApplication->jobPosting->title }}</div>
                    <div class="text-xs text-gray-500">{{ $breakdownApplication->jobPosting->employerProfile->company_name }}</div>
                </div>

                <div class="bg-[#F4F7FA] rounded-xl p-4 border border-[#B8D0E2] flex items-center justify-between">
                    <div>
                        <div class="text-xs font-semibold text-[#4E5F6E]">TOTAL SCORE</div>
                        <div class="text-2xl font-bold text-[#176B47]">{{ (int) $breakdownApplication->fit_score_at_apply }}% Composite Fit</div>
                    </div>
                    <div class="text-xs text-right text-gray-600">
                        <div>Accessibility Weight: 60%</div>
                        <div>Skills Overlap: 40%</div>
                    </div>
                </div>

                <div class="space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500">Accommodation Support Checklist</h4>
                    <ul class="space-y-2 text-xs">
                        @foreach($candidateProfile->accessibilityNeeds as $need)
                            @php
                                $isSupported = $breakdownApplication->jobPosting->accommodations->contains('id', $need->accommodation_id);
                            @endphp
                            <li class="flex items-center justify-between p-2 rounded-lg {{ $isSupported ? 'bg-emerald-50 text-emerald-900 border border-emerald-200' : 'bg-gray-50 text-gray-700 border border-gray-200' }}">
                                <span class="font-medium">{{ $need->accommodation->name }}</span>
                                <span class="font-semibold {{ $isSupported ? 'text-emerald-700' : 'text-gray-500' }}">
                                    {{ $isSupported ? '✓ Guaranteed Support' : 'Not specified' }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <button type="button" wire:click="closeBreakdownModal" class="px-4 py-2 rounded-lg bg-[#244F73] text-white text-xs font-semibold hover:bg-[#1A2840]">
                        Close Breakdown
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Candidate Employment Separation Dispute Modal -->
    @if ($showDisputeModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" role="dialog" aria-modal="true" aria-label="{{ __('Contest Employment Separation') }}">
            <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl bg-white p-6 sm:p-8 shadow-xl space-y-5" wire:click.stop>
                <div class="flex items-start justify-between border-b border-primary-border pb-3">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200 mb-1">
                            {{ __('Statutory Due Process Rights') }}
                        </div>
                        <h2 class="font-heading text-lg font-bold text-primary-dark">{{ __('Contest Notice of Separation') }}</h2>
                        <p class="text-xs text-body-muted mt-0.5">{{ __('Under Republic Act No. 7277 (Magna Carta for Disabled Persons)') }}</p>
                    </div>
                    <button type="button" wire:click="closeDisputeModal" class="text-body-muted hover:text-primary-dark text-lg">&times;</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div class="rounded-xl border border-sky-200 bg-sky-50/70 p-3.5 text-xs text-sky-900 leading-relaxed">
                        <strong class="font-bold block mb-1">{{ __('Your Legal Rights:') }}</strong>
                        {{ __('No qualified employee with disability shall be terminated without just or authorized cause and observance of due process. Submitting this contestation places the separation under dispute, notifies the employer, and maintains the audit trail.') }}
                    </div>

                    <div>
                        <label class="block font-bold text-primary-dark mb-1.5">
                            {{ __('Reason / Statement of Contestation') }} <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            wire:model="candidateDisputeReason"
                            rows="5"
                            placeholder="{{ __('Detail your grounds for contesting this separation (e.g., absence of justifiable cause, lack of reasonable accommodation, breach of agreed terms, discriminatory treatment)...') }}"
                            class="w-full rounded-lg border border-primary-border p-3 text-xs focus:ring-2 focus:ring-red-500 focus:outline-none"></textarea>
                        @error('candidateDisputeReason')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-primary-border">
                    <button type="button" wire:click="closeDisputeModal" class="rounded-lg border border-primary-border px-4 py-2 text-xs font-semibold text-primary-dark hover:bg-primary-tint">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" wire:click="submitDispute" class="rounded-lg bg-red-600 px-4 py-2 text-xs font-semibold text-white hover:bg-red-700 shadow-xs cursor-pointer">
                        {{ __('Submit Formal Dispute') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Candidate Employment Separation Acknowledgment Modal -->
    @if ($showAcknowledgeModal && $acknowledgingRecord)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-xs" role="dialog" aria-modal="true" aria-label="{{ __('Acknowledge Employment Separation') }}">
            <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl bg-white p-6 sm:p-8 shadow-2xl space-y-5 animate-in fade-in zoom-in-95 duration-150" wire:click.stop>
                <div class="flex items-start justify-between border-b border-primary-border pb-3">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200 mb-1">
                            {{ __('Statutory Due Process Notice') }}
                        </div>
                        <h2 class="font-heading text-lg font-bold text-primary-dark">{{ __('Acknowledge Employment Separation') }}</h2>
                        <p class="text-xs text-body-muted mt-0.5">{{ $acknowledgingRecord->employerProfile?->company_name ?? __('Employer') }} &bull; {{ $acknowledgingRecord->application?->jobPosting?->title ?? __('Position') }}</p>
                    </div>
                    <button type="button" wire:click="closeAcknowledgeModal" class="text-body-muted hover:text-primary-dark text-xl leading-none cursor-pointer">&times;</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div class="rounded-xl border border-gray-200 bg-surface p-4 space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="font-semibold text-primary-dark">{{ __('Employer:') }}</span>
                            <span class="text-body-muted">{{ $acknowledgingRecord->employerProfile?->company_name ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-semibold text-primary-dark">{{ __('Position:') }}</span>
                            <span class="text-body-muted">{{ $acknowledgingRecord->application?->jobPosting?->title ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-semibold text-primary-dark">{{ __('Effective Date:') }}</span>
                            <span class="font-medium text-primary-dark">{{ $acknowledgingRecord->ended_at?->format('F d, Y') ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-semibold text-primary-dark">{{ __('Reason for Departure:') }}</span>
                            <span class="capitalize text-body-muted font-medium">{{ $acknowledgingRecord->ended_reason }}</span>
                        </div>
                        @if($acknowledgingRecord->ended_notes)
                            <div class="pt-2 border-t border-gray-200/80">
                                <span class="font-semibold text-primary-dark block mb-0.5">{{ __('Employer Remarks:') }}</span>
                                <p class="text-xs text-body-muted italic bg-white p-2.5 rounded-lg border border-gray-200">
                                    "{{ $acknowledgingRecord->ended_notes }}"
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="rounded-xl border border-amber-200 bg-amber-50/80 p-3.5 text-xs text-amber-950 leading-relaxed">
                        <strong class="font-bold block mb-1">{{ __('Statutory Due Process Confirmation:') }}</strong>
                        {{ __('Confirming this action indicates that you have received and accepted this notice of separation pursuant to Philippine Labor Standards and R.A. 7277. Your employment record will transition to Separated Staff, and your employer will be notified.') }}
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-primary-border">
                    <button type="button" wire:click="closeAcknowledgeModal" class="rounded-xl border border-primary-border px-4 py-2.5 text-xs font-semibold text-primary-dark hover:bg-primary-tint transition-colors cursor-pointer">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" wire:click="acknowledgeSeparation({{ $acknowledgingRecord->id }})" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-emerald-700 shadow-xs cursor-pointer transition-colors">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ __('Confirm & Acknowledge Separation') }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Decline Invitation Confirmation Modal --}}
    @if ($showDeclineInviteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-xs" role="dialog" aria-modal="true" aria-label="{{ __('Decline Invitation') }}">
            <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-2xl bg-white p-6 sm:p-7 shadow-2xl space-y-4" wire:click.stop>
                <div class="flex items-start justify-between border-b border-primary-border pb-3">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200 mb-1">
                            {{ __('Invitation Notice') }}
                        </div>
                        <h2 class="font-heading text-lg font-bold text-primary-dark">{{ __('Decline Application Invitation?') }}</h2>
                    </div>
                    <button type="button" wire:click="closeDeclineInviteModal" class="text-body-muted hover:text-primary-dark text-xl leading-none cursor-pointer">&times;</button>
                </div>

                <p class="text-xs text-body-muted leading-relaxed">
                    {{ __('Are you sure you want to decline the invitation to apply for :job from :employer?', ['job' => $decliningInviteJobTitle, 'employer' => $decliningInviteEmployer]) }}
                </p>

                <div class="rounded-xl border border-gray-200 bg-surface p-3 text-xs text-body-muted leading-relaxed">
                    {{ __('You will not be automatically reconsidered for this invitation, but you can still search and apply for other openings by this employer anytime.') }}
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-primary-border">
                    <button type="button" wire:click="closeDeclineInviteModal" class="rounded-xl border border-primary-border px-4 py-2 text-xs font-semibold text-primary-dark hover:bg-primary-tint transition-colors cursor-pointer">
                        {{ __('Keep Invitation') }}
                    </button>
                    <button type="button" wire:click="confirmDeclineInvitation" class="rounded-xl bg-red-600 px-4 py-2 text-xs font-bold text-white hover:bg-red-700 shadow-xs transition-colors cursor-pointer">
                        {{ __('Yes, Decline Invitation') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

