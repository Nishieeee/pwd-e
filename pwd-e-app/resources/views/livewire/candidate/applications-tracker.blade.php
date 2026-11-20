<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Notification Banner -->
    @if($actionNotification)
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between" role="alert">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium">{{ $actionNotification }}</span>
            </div>
            <button type="button" wire:click="$set('actionNotification', null)" class="text-emerald-700 hover:text-emerald-900 text-xs font-semibold" aria-label="Dismiss message">
                Dismiss
            </button>
        </div>
    @endif

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-[#1A2840] tracking-tight">Applications</h1>
            <p class="text-sm text-[#4E5F6E] mt-1">
                Your job search, in one place. Track applications, employer updates, interviews, and offers.
            </p>
        </div>

        <a href="{{ route('candidate.jobs') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-[#244F73] text-white text-sm font-semibold hover:bg-[#1A2840] shadow-sm transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-[#244F73] shrink-0">
            <span>Find job matches</span>
            <span aria-hidden="true">&rarr;</span>
        </a>
    </div>

    <!-- Tabbed Navigation -->
    <div class="border-b border-gray-200" role="tablist" aria-label="Application categories">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <!-- Tab: All Applications -->
            <button type="button"
                    wire:click="setTab('all')"
                    role="tab"
                    aria-selected="{{ $activeTab === 'all' ? 'true' : 'false' }}"
                    class="whitespace-nowrap py-3 px-1 border-b-2 font-semibold text-xs flex items-center gap-2 transition-colors {{ $activeTab === 'all' ? 'border-[#244F73] text-[#244F73]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <span>All Applications</span>
                @if($allCount > 0)
                    <span class="rounded-full px-2 py-0.5 text-[11px] {{ $activeTab === 'all' ? 'bg-[#DCEAF4] text-[#1A2840]' : 'bg-gray-100 text-gray-600' }}">
                        {{ $allCount }}
                    </span>
                @endif
            </button>

            <!-- Tab: Active Applications -->
            <button type="button"
                    wire:click="setTab('active')"
                    role="tab"
                    aria-selected="{{ $activeTab === 'active' ? 'true' : 'false' }}"
                    class="whitespace-nowrap py-3 px-1 border-b-2 font-semibold text-xs flex items-center gap-2 transition-colors {{ $activeTab === 'active' ? 'border-[#244F73] text-[#244F73]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <span>Active Applications</span>
                @if($activeCount > 0)
                    <span class="rounded-full px-2 py-0.5 text-[11px] {{ $activeTab === 'active' ? 'bg-[#DCEAF4] text-[#1A2840]' : 'bg-gray-100 text-gray-600' }}">
                        {{ $activeCount }}
                    </span>
                @endif
            </button>

            <!-- Tab: Interviews -->
            <button type="button"
                    wire:click="setTab('interviews')"
                    role="tab"
                    aria-selected="{{ $activeTab === 'interviews' ? 'true' : 'false' }}"
                    class="whitespace-nowrap py-3 px-1 border-b-2 font-semibold text-xs flex items-center gap-2 transition-colors {{ $activeTab === 'interviews' ? 'border-[#244F73] text-[#244F73]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <span>Interviews</span>
                @if($interviewsCount > 0)
                    <span class="rounded-full px-2 py-0.5 text-[11px] {{ $activeTab === 'interviews' ? 'bg-[#DCEAF4] text-[#1A2840]' : 'bg-gray-100 text-gray-600' }}">
                        {{ $interviewsCount }}
                    </span>
                @endif
            </button>

            <!-- Tab: Past/Archived -->
            <button type="button"
                    wire:click="setTab('archived')"
                    role="tab"
                    aria-selected="{{ $activeTab === 'archived' ? 'true' : 'false' }}"
                    class="whitespace-nowrap py-3 px-1 border-b-2 font-semibold text-xs flex items-center gap-2 transition-colors {{ $activeTab === 'archived' ? 'border-[#244F73] text-[#244F73]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <span>Past/Archived</span>
                @if($archivedCount > 0)
                    <span class="rounded-full px-2 py-0.5 text-[11px] {{ $activeTab === 'archived' ? 'bg-[#DCEAF4] text-[#1A2840]' : 'bg-gray-100 text-gray-600' }}">
                        {{ $archivedCount }}
                    </span>
                @endif
            </button>
        </nav>
    </div>

    <!-- Search & Filter Controls -->
    <div class="flex flex-col sm:flex-row items-center gap-3">
        <div class="relative flex-1 w-full">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Search by job title or employer"
                   class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 text-xs text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#244F73] bg-white"
                   aria-label="Search by job title or employer">
        </div>

        <div class="w-full sm:w-44">
            <select wire:model.live="statusFilter"
                    class="w-full py-2.5 px-3 rounded-lg border border-gray-300 text-xs text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-[#244F73]"
                    aria-label="Filter by application status">
                <option value="all">All statuses</option>
                <option value="interview">Interview Scheduled</option>
                <option value="under_review">Under Review</option>
                <option value="closed">Closed</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>

        <div class="w-full sm:w-40">
            <select wire:model.live="dateFilter"
                    class="w-full py-2.5 px-3 rounded-lg border border-gray-300 text-xs text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-[#244F73]"
                    aria-label="Filter by application date range">
                <option value="all">All dates</option>
                <option value="30">Past 30 days</option>
                <option value="60">Past 60 days</option>
                <option value="90">Past 90 days</option>
            </select>
        </div>
    </div>

    <!-- Summary Row -->
    <div class="flex items-center justify-between text-xs text-gray-500">
        <div>
            <strong class="font-bold text-gray-800">{{ count($applications) }} applications</strong> &middot; Your complete employer interaction history
        </div>
        <div class="flex items-center gap-1.5 font-medium">
            <span>Sort by:</span>
            <select wire:model.live="sortBy" class="border-0 bg-transparent py-0 pl-1 pr-6 text-xs font-semibold text-gray-800 focus:ring-0 cursor-pointer">
                <option value="latest">Latest update</option>
                <option value="oldest">Oldest update</option>
                <option value="fit_score">Highest Fit Score</option>
            </select>
        </div>
    </div>

    <!-- Applications Table -->
    <section class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden" aria-label="Applications table">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs" role="table">
                <thead class="bg-gray-50/75 text-gray-500 font-semibold border-b border-gray-200">
                    <tr>
                        <th scope="col" class="py-3 px-6">Job Title</th>
                        <th scope="col" class="py-3 px-6">Employer Name</th>
                        <th scope="col" class="py-3 px-6">Application Date</th>
                        <th scope="col" class="py-3 px-6">Accessibility-Fit</th>
                        <th scope="col" class="py-3 px-6">Application Status</th>
                        <th scope="col" class="py-3 px-6">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($applications as $app)
                        <tr class="hover:bg-gray-50/50 transition-colors {{ $app->status === 'interview' ? 'bg-emerald-50/15' : '' }}">
                            <!-- Job Title -->
                            <td class="py-4 px-6 align-top">
                                <div class="font-bold text-sm text-[#1A2840]">
                                    {{ $app->jobPosting->title }}
                                </div>
                                <div class="text-[11px] text-gray-400 mt-0.5">
                                    Updated {{ $app->updated_at ? $app->updated_at->format('M j, Y') : 'Nov 1, 2026' }}
                                </div>
                            </td>

                            <!-- Employer Name -->
                            <td class="py-4 px-6 align-top">
                                <div class="font-semibold text-xs text-gray-800">
                                    {{ $app->jobPosting->employerProfile->company_name }}
                                </div>
                                <div class="text-[11px] text-gray-500 mt-0.5">
                                    {{ $app->jobPosting->location ?? 'Taguig / Remote' }}
                                </div>
                            </td>

                            <!-- Application Date -->
                            <td class="py-4 px-6 align-top text-gray-600 tabular-nums">
                                {{ $app->applied_at ? $app->applied_at->format('Y-m-d') : '2026-10-25' }}
                            </td>

                            <!-- Accessibility-Fit Score -->
                            <td class="py-4 px-6 align-top">
                                @php
                                    $score = (int) ($app->fit_score_at_apply ?? 95);
                                    $fitBadgeClass = $score >= 90
                                        ? 'bg-[#E8F5E9] text-[#176B47] border-[#A5D6A7]'
                                        : ($score >= 80 ? 'bg-sky-50 text-[#1A2840] border-sky-200' : 'bg-amber-50 text-amber-800 border-amber-200');
                                @endphp
                                <button type="button"
                                        wire:click="viewFitBreakdown({{ $app->id }})"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold border tabular-nums transition-transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-[#244F73]/30 cursor-pointer {{ $fitBadgeClass }}"
                                        title="{{ $score }}% Composite Fit (Click to view breakdown)"
                                        aria-label="{{ $score }}% Composite Fit">
                                    <span class="size-1.5 rounded-full {{ $score >= 90 ? 'bg-[#176B47]' : ($score >= 80 ? 'bg-[#244F73]' : 'bg-amber-600') }}"></span>
                                    <span>{{ $score }}%</span>
                                    <span class="sr-only">Composite Fit</span>
                                </button>
                            </td>

                            <!-- Application Status -->
                            <td class="py-4 px-6 align-top">
                                <div>
                                    @if($app->status === 'interview')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full font-medium bg-[#E8F5E9] text-[#176B47] border border-[#A5D6A7]">
                                            Interview Scheduled
                                        </span>
                                    @elseif($app->status === 'under_review')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full font-medium bg-amber-50 text-amber-800 border border-amber-200">
                                            Under Review
                                        </span>
                                    @elseif($app->status === 'closed')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full font-medium bg-gray-100 text-gray-700">
                                            Closed
                                        </span>
                                    @elseif($app->status === 'rejected')
                                        <div class="inline-flex flex-col items-start gap-1">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full font-medium bg-red-50 text-red-700 border border-red-200">
                                                Declined
                                            </span>
                                            @if($app->rejection_reason)
                                                <div class="text-[11px] text-red-800 bg-red-50/70 p-2 rounded-lg border border-red-100 max-w-xs leading-relaxed">
                                                    <span class="font-bold text-[10px] uppercase tracking-wider text-red-600 block">{{ __('Employer Remark:') }}</span>
                                                    "{{ $app->rejection_reason }}"
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full font-medium bg-gray-100 text-gray-700">
                                            {{ ucfirst($app->status) }}
                                        </span>
                                    @endif

                                    @if($app->status_subtext)
                                        <div class="text-[11px] text-gray-500 mt-1">
                                            {{ $app->status_subtext }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-6 align-top">
                                <div class="space-y-1.5">
                                    @if($app->offer_details)
                                        <div>
                                            <button type="button"
                                                    wire:click="viewOffer({{ $app->id }})"
                                                    class="inline-flex items-center px-3 py-1.5 rounded-md bg-[#244F73] text-white text-xs font-semibold hover:bg-[#1A2840] shadow-xs focus:ring-2 focus:ring-[#244F73]">
                                                View Offer
                                            </button>
                                        </div>
                                    @endif

                                    <div>
                                        <button type="button"
                                                wire:click="viewFitBreakdown({{ $app->id }})"
                                                class="inline-flex items-center px-3 py-1.5 rounded-md border border-gray-300 text-xs font-semibold text-[#1A2840] bg-white hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-[#244F73]">
                                            View Fit Breakdown
                                        </button>
                                    </div>

                                    @if(in_array($app->status, ['interview', 'under_review', 'applied', 'submitted']))
                                        <div>
                                            <button type="button"
                                                    wire:click="promptWithdraw({{ $app->id }})"
                                                    class="text-[11px] text-gray-500 hover:text-red-600 underline transition-colors focus:outline-none focus:ring-1 focus:ring-red-500 rounded">
                                                Withdraw Application
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Nested Scheduled Interview Card (Exact Figma match under Interview row) -->
                        @if($app->status === 'interview')
                            <tr class="bg-[#F0FDF4]/60">
                                <td colspan="6" class="px-6 py-4 border-t border-[#DCFCE7]">
                                    <div class="rounded-xl bg-[#F0FDF4] border border-[#BBF7D0] p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                        <div class="space-y-1.5">
                                            <div class="flex items-center gap-2 text-xs font-bold text-gray-900">
                                                <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span>{{ $app->interview_at ? $app->interview_at->format('F j, Y \· g:i A \P\H\T \(U\T\C+08:00\)') : 'November 4, 2026 · 2:00 PM PHT (UTC+08:00)' }}</span>
                                            </div>

                                            <div class="text-xs">
                                                <a href="{{ $app->interview_url ?? 'https://meet.google.com' }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 font-semibold text-[#244F73] hover:underline">
                                                    <span>{{ $app->interview_location ?? 'Accessible Virtual Room · Google Meet' }}</span>
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                </a>
                                            </div>

                                            <div class="flex items-start gap-1.5 text-xs text-emerald-800 pt-1">
                                                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                <span><strong class="font-semibold">Accommodations confirmed:</strong> {{ $app->interview_accommodations ?? 'Screen reader test files, wheelchair ramp access at BGC office, quiet interview room.' }}</span>
                                            </div>
                                        </div>

                                        <button type="button"
                                                wire:click="acceptAndAddToCalendar({{ $app->id }})"
                                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#244F73] text-white text-xs font-semibold hover:bg-[#1A2840] transition-colors focus:ring-2 focus:ring-[#244F73] shrink-0">
                                            <span>Accept &amp; Add to Calendar</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 px-6 text-center">
                                <div class="max-w-md mx-auto space-y-3" role="status" aria-live="polite">
                                    <p class="text-sm font-semibold text-[#1A2840]">
                                        {{ $emptyStateMessage ?? 'You have not submitted any job applications yet.' }}
                                    </p>
                                    <div class="pt-1">
                                        <a href="{{ route('candidate.jobs') }}"
                                           class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-[#244F73] text-white text-xs font-semibold hover:bg-[#1A2840] shadow-xs transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#244F73]">
                                            <span>Find job matches</span>
                                            <span aria-hidden="true">&rarr;</span>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination & Footer Summary -->
        @if($applications->isNotEmpty())
            <div class="px-6 py-4 bg-gray-50/75 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
                <div>
                    Showing 1-{{ $applications->count() }} of {{ $applications->count() }} applications
                </div>
                <div class="inline-flex items-center gap-1 font-medium" role="navigation" aria-label="Pagination">
                    <button type="button" class="px-2.5 py-1 rounded border border-gray-300 text-gray-400 bg-white cursor-not-allowed" disabled>
                        Previous
                    </button>
                    <span class="px-2.5 py-1 rounded bg-[#244F73] text-white font-bold" aria-current="page">
                        1
                    </span>
                    <button type="button" class="px-2.5 py-1 rounded border border-gray-300 text-gray-400 bg-white cursor-not-allowed" disabled>
                        Next
                    </button>
                </div>
            </div>
        @endif
    </section>

    <!-- Bottom Helper Note -->
    <div class="flex flex-wrap items-center gap-2 text-xs text-[#25313B]" role="note" aria-label="Composite Fit score explanation">
        <svg class="w-4 h-4 text-[#244F73] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <circle cx="12" cy="12" r="10" stroke-width="2"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16v-4m0-4h.01"/>
        </svg>
        <span>
            Composite Fit measures how a job matches your skills and accessibility needs. Select
        </span>
        <flux:button variant="subtle"
                     size="xs"
                     wire:click="openDefaultFitBreakdown"
                     class="!px-2.5 !py-1 !h-auto font-bold !text-[#1A2840] !bg-[#DCEAF4] hover:!bg-[#B8D0E2] border border-[#244F73]/40 underline decoration-[#244F73] underline-offset-2 rounded-md focus:ring-2 focus:ring-[#244F73]"
                     aria-label="Open Fit Breakdown for accommodation-level details">
            Fit Breakdown
        </flux:button>
        <span>
            to view accommodation-level details.
        </span>
    </div>

    <!-- Modals: Breakdown, Offer, Withdraw Confirmation -->

    <!-- 1. Fit Breakdown Modal -->
    @if($showBreakdownModal && $breakdownApp)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="modal-fit-title">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-gray-200 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 id="modal-fit-title" class="text-base font-bold text-[#1A2840]">
                        Composite Fit Breakdown
                    </h3>
                    <button type="button" wire:click="closeBreakdownModal" class="p-1 rounded-md text-gray-400 hover:text-gray-600 focus:ring-2 focus:ring-[#244F73]" aria-label="Close modal">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div>
                    <div class="font-bold text-sm text-[#1A2840]">{{ $breakdownApp->jobPosting->title }}</div>
                    <div class="text-xs text-gray-500">{{ $breakdownApp->jobPosting->employerProfile->company_name }}</div>
                </div>

                <div class="bg-[#F4F7FA] rounded-xl p-4 border border-[#B8D0E2] flex items-center justify-between">
                    <div>
                        <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">COMPOSITE FIT SCORE</div>
                        <div class="text-2xl font-bold text-[#176B47]">{{ (int) $breakdownApp->fit_score_at_apply }}% Fit</div>
                    </div>
                    <div class="text-xs text-right text-gray-600">
                        <div>60% Accessibility Match</div>
                        <div>40% Skills Overlap</div>
                    </div>
                </div>

                <div class="space-y-2">
                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Accommodation Requirements</h4>
                    <ul class="space-y-1.5 text-xs">
                        @foreach($candidateProfile->accessibilityNeeds as $need)
                            @php
                                $supported = $breakdownApp->jobPosting->accommodations->contains('id', $need->accommodation_id);
                            @endphp
                            <li class="flex items-center justify-between p-2 rounded-lg {{ $supported ? 'bg-emerald-50 text-emerald-900 border border-emerald-200' : 'bg-gray-50 text-gray-700' }}">
                                <span class="font-medium">{{ $need->accommodation->name }}</span>
                                <span class="font-bold {{ $supported ? 'text-emerald-700' : 'text-gray-400' }}">
                                    {{ $supported ? '✓ Supported' : 'Not declared' }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <button type="button" wire:click="closeBreakdownModal" class="px-4 py-2 rounded-lg bg-[#244F73] text-white text-xs font-semibold hover:bg-[#1A2840]">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- 2. View Offer Modal -->
    @if($showOfferModal && $offerApp)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="modal-offer-title">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-gray-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 id="modal-offer-title" class="text-base font-bold text-[#1A2840]">
                        Job Offer Details
                    </h3>
                    <button type="button" wire:click="closeOfferModal" class="p-1 rounded-md text-gray-400 hover:text-gray-600 focus:ring-2 focus:ring-[#244F73]" aria-label="Close modal">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="space-y-1">
                    <div class="font-bold text-sm text-[#1A2840]">{{ $offerApp->jobPosting->title }}</div>
                    <div class="text-xs text-gray-500">{{ $offerApp->jobPosting->employerProfile->company_name }}</div>
                </div>

                <div class="p-4 rounded-xl bg-sky-50 border border-sky-200 text-xs text-sky-900 space-y-2">
                    <div class="flex justify-between font-medium">
                        <span>Offered Salary:</span>
                        <strong class="font-bold text-sm text-gray-900">₱29,000 /month</strong>
                    </div>
                    <div class="flex justify-between">
                        <span>Work Arrangement:</span>
                        <span class="font-semibold">{{ ucfirst($offerApp->jobPosting->work_arrangement) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Offer Date:</span>
                        <span>October 22, 2026</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Status:</span>
                        <span class="font-bold text-amber-700">Declined by candidate (Oct 24, 2026)</span>
                    </div>
                </div>

                <div class="pt-3 flex justify-end">
                    <button type="button" wire:click="closeOfferModal" class="px-4 py-2 rounded-lg bg-[#244F73] text-white text-xs font-semibold hover:bg-[#1A2840]">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- 3. Withdraw Confirmation Modal (UX-08) -->
    @if($showWithdrawConfirmModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="modal-withdraw-title">
            <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl border border-gray-200 space-y-4">
                <div class="flex items-center gap-3 text-amber-600">
                    <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 id="modal-withdraw-title" class="text-base font-bold text-gray-900">
                        Withdraw Application?
                    </h3>
                </div>

                <p class="text-xs text-gray-600 leading-relaxed">
                    Are you sure you want to withdraw this application? The employer will be notified that you are no longer in consideration.
                </p>

                <div class="pt-3 flex items-center justify-end gap-2">
                    <button type="button"
                            wire:click="closeWithdrawModal"
                            class="px-4 py-2 rounded-lg border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-[#244F73]">
                        Cancel
                    </button>
                    <button type="button"
                            wire:click="confirmWithdraw"
                            class="px-4 py-2 rounded-lg bg-red-600 text-white text-xs font-semibold hover:bg-red-700 focus:ring-2 focus:ring-red-500">
                        Withdraw
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

