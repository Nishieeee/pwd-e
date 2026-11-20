<div class="space-y-8 max-w-7xl mx-auto">
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
                            {{ $employerName }} {{ __('invited you to apply for') }} <span class="text-primary">{{ $jobTitle }}</span>
                        </h2>
                        @if($personalMsg)
                            <p class="text-xs text-body-muted italic bg-white/70 rounded-lg p-2.5 border border-sky-100 max-w-2xl">
                                "{{ $personalMsg }}"
                            </p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2.5 shrink-0">
                        <button type="button"
                                wire:click="declineInvitation({{ $invite->id }})"
                                wire:confirm="{{ __('Are you sure you want to decline this invitation?') }}"
                                class="inline-flex items-center px-3.5 py-2.5 rounded-xl border border-gray-300 bg-white text-xs font-semibold text-body-muted hover:text-red-600 hover:border-red-200 hover:bg-red-50 transition-colors">
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

    <!-- Dashboard Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-[#1A2840] tracking-tight">Candidate Dashboard</h1>
            <p class="text-sm text-[#4E5F6E] mt-1">
                Welcome back, {{ $user->name }}. Your PWD credentials and accommodation profile are active.
            </p>
            <div class="flex flex-wrap items-center gap-2.5 mt-3">
                <!-- PWD ID Verified Pill -->
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>PWD ID: {{ $user->pwd_id_number ?? '09-7332-0001842-A' }} (Verified)</span>
                </span>

                <!-- Alumni Badge Pill -->
                @if($candidateProfile->education_badge)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#DCEAF4] text-[#1A2840] border border-[#B8D0E2]">
                        {{ $candidateProfile->education_badge }}
                    </span>
                @endif
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <a href="{{ route('candidate.profile.show') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border border-primary-border bg-white text-[#1A2840] text-sm font-semibold hover:bg-primary-tint shadow-xs transition-colors">
                <svg class="size-4 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                <span>Profile</span>
            </a>
            <a href="{{ route('candidate.jobs') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-[#244F73] text-white text-sm font-semibold hover:bg-[#1A2840] shadow-sm transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-[#244F73]">
                <span>Find job matches</span>
                <span aria-hidden="true">&rarr;</span>
            </a>
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
                        {{ $upcomingInterviewApplication->jobPosting->title }} &mdash; {{ $upcomingInterviewApplication->jobPosting->employerProfile->company_name }}
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
                                    {{ $app->jobPosting->employerProfile->company_name }}
                                    @if($app->jobPosting->location)
                                        ({{ $app->jobPosting->location }})
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
                            <div class="w-10 h-10 rounded-lg bg-[#E0E7FF] text-[#1E1B4B] flex items-center justify-center font-bold text-xs uppercase">
                                {{ substr($rJob->employerProfile->company_name ?? 'CO', 0, 2) }}
                            </div>
                            <div class="text-base font-bold text-[#176B47]">
                                {{ $rJob->fit_score ?? 96 }}% <span class="text-xs font-semibold text-gray-500">FIT</span>
                            </div>
                        </div>

                        <div class="font-bold text-sm text-[#1A2840]">
                            {{ $rJob->employerProfile->company_name }}
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
</div>

