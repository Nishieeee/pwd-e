<div class="space-y-6 max-w-7xl mx-auto w-full min-w-0">
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

    <!-- Header Banner Card -->
    <div class="w-full relative overflow-hidden rounded-2xl border border-primary-border bg-white p-5 sm:p-6 shadow-sm" style="background: linear-gradient(135deg, #DCEAF4 0%, #FFFFFF 100%); box-shadow: 0 4px 20px rgba(26,40,64,0.06);">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="space-y-2 max-w-3xl min-w-0 flex-1">
                <nav class="inline-flex items-center gap-1.5 rounded-full border border-primary-border bg-white px-3 py-1 text-xs font-semibold text-body-muted shadow-2xs" aria-label="Breadcrumb">
                    <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors rounded focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary">Dashboard</a>
                    <span aria-hidden="true">/</span>
                    <span class="text-primary-dark font-bold" aria-current="page">Applications</span>
                </nav>
                <h1 class="font-heading text-2xl sm:text-3xl font-bold text-primary-dark tracking-tight">Applications</h1>
                <p class="text-xs sm:text-sm text-body-muted leading-relaxed max-w-2xl">
                    Your job search, in one place. Track applications, employer updates, interviews, and offers.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="{{ route('candidate.jobs') }}" class="inline-flex items-center justify-center gap-2 h-11 px-4 rounded-xl bg-primary-dark text-white text-xs sm:text-sm font-semibold hover:bg-primary shadow-xs transition-colors whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-dark focus-visible:ring-offset-2">
                    <span>Find job matches</span>
                    <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Tabbed Navigation -->
    <div class="border-b border-primary-border/80 pb-3.5">
        <div class="flex flex-wrap items-center gap-2 max-w-full" role="tablist" aria-label="Application categories">
            @foreach ([
                ['key' => 'all', 'label' => 'All Applications', 'count' => $allCount],
                ['key' => 'active', 'label' => 'Active Applications', 'count' => $activeCount],
                ['key' => 'interviews', 'label' => 'Interviews', 'count' => $interviewsCount],
                ['key' => 'archived', 'label' => 'Past/Archived', 'count' => $archivedCount],
            ] as $tabItem)
                @php
                    $isActive = $activeTab === $tabItem['key'];
                @endphp
                <button type="button"
                        wire:click="setTab('{{ $tabItem['key'] }}')"
                        role="tab"
                        aria-selected="{{ $isActive ? 'true' : 'false' }}"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold transition-all whitespace-nowrap cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 shrink-0 {{ $isActive ? 'bg-primary-dark text-white shadow-xs' : 'bg-white text-body-muted border border-primary-border/70 hover:bg-primary-tint/50 hover:text-primary-dark' }}">
                    <span>{{ $tabItem['label'] }}</span>
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold tabular-nums {{ $isActive ? 'bg-white/20 text-white' : 'bg-primary-tint text-primary-dark' }}">
                        {{ $tabItem['count'] ?? 0 }}
                    </span>
                </button>
            @endforeach
        </div>
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
                <thead class="bg-[#F8FAFC] text-gray-500 font-bold border-b border-gray-200/80 text-[11px] uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="py-3.5 px-6 whitespace-nowrap">Job Title</th>
                        <th scope="col" class="py-3.5 px-6 whitespace-nowrap">Employer Name</th>
                        <th scope="col" class="py-3.5 px-6 whitespace-nowrap">Application Date</th>
                        <th scope="col" class="py-3.5 px-6 whitespace-nowrap text-center">Accessibility-Fit</th>
                        <th scope="col" class="py-3.5 px-6 whitespace-nowrap">Application Status</th>
                        <th scope="col" class="py-3.5 px-6 whitespace-nowrap text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($applications as $app)
                        <tr class="hover:bg-slate-50/70 transition-colors {{ $app->status === 'interview' ? 'bg-emerald-50/20' : '' }}">
                            <!-- Job Title -->
                            <td class="py-4 px-6 align-middle">
                                <div class="font-bold text-sm text-[#1A2840] hover:text-[#244F73] transition-colors leading-snug">
                                    {{ $app->jobPosting->title }}
                                </div>
                                <div class="text-[11px] text-gray-400 mt-1 flex items-center gap-1.5 whitespace-nowrap">
                                    <span class="size-1.5 rounded-full bg-gray-300"></span>
                                    <span>Updated {{ $app->updated_at ? $app->updated_at->format('M j, Y') : 'Nov 1, 2026' }}</span>
                                </div>
                            </td>

                            <!-- Employer Name -->
                            <td class="py-4 px-6 align-middle">
                                <div class="font-semibold text-xs text-gray-900 leading-snug">
                                    @if($app->jobPosting?->employerProfile)
                                        <a href="{{ route('candidate.employers.show', $app->jobPosting->employerProfile) }}"
                                           class="text-[#1A2840] hover:text-[#244F73] hover:underline inline-flex items-center gap-1 group font-bold"
                                           title="View {{ $app->jobPosting->employerProfile->company_name }} Profile">
                                            <span>{{ $app->jobPosting->employerProfile->company_name }}</span>
                                            <svg class="size-3 text-gray-400 group-hover:text-[#244F73] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @else
                                        {{ $app->jobPosting?->employerProfile?->company_name ?? 'Employer' }}
                                    @endif
                                </div>
                                <div class="text-[11px] text-gray-500 mt-1 flex items-center gap-1 whitespace-nowrap">
                                    <svg class="size-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>{{ $app->jobPosting->location ?? 'Taguig / Remote' }}</span>
                                </div>
                            </td>

                            <!-- Application Date -->
                            <td class="py-4 px-6 align-middle tabular-nums whitespace-nowrap">
                                <div class="font-medium text-xs text-gray-800">
                                    {{ $app->applied_at ? $app->applied_at->format('M j, Y') : 'Oct 25, 2026' }}
                                </div>
                                <div class="text-[11px] text-gray-400 mt-0.5">
                                    {{ $app->applied_at ? $app->applied_at->diffForHumans() : 'Recently' }}
                                </div>
                            </td>

                            <!-- Accessibility-Fit Score -->
                            <td class="py-4 px-6 align-middle text-center whitespace-nowrap">
                                @php
                                    $score = (int) ($app->fit_score_at_apply ?? 95);
                                    $fitBadgeClass = $score >= 90
                                        ? 'bg-[#E8F5E9] text-[#176B47] border-[#A5D6A7] hover:bg-emerald-100/70 hover:border-emerald-300'
                                        : ($score >= 80 ? 'bg-sky-50 text-[#1A2840] border-sky-200 hover:bg-sky-100' : 'bg-amber-50 text-amber-800 border-amber-200 hover:bg-amber-100');
                                @endphp
                                <button type="button"
                                        wire:click="viewFitBreakdown({{ $app->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border tabular-nums transition-all hover:scale-105 focus:outline-none focus:ring-2 focus:ring-[#244F73]/30 cursor-pointer shadow-2xs {{ $fitBadgeClass }}"
                                        title="{{ $score }}% Composite Fit (Click to view breakdown)"
                                        aria-label="{{ $score }}% Composite Fit">
                                    <span class="relative flex size-2 shrink-0">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $score >= 90 ? 'bg-emerald-400' : ($score >= 80 ? 'bg-sky-400' : 'bg-amber-400') }} opacity-75"></span>
                                        <span class="relative inline-flex rounded-full size-2 {{ $score >= 90 ? 'bg-[#176B47]' : ($score >= 80 ? 'bg-[#244F73]' : 'bg-amber-600') }}"></span>
                                    </span>
                                    <span>{{ $score }}% Fit</span>
                                    <span class="sr-only">Composite Fit</span>
                                </button>
                            </td>

                            <!-- Application Status -->
                            <td class="py-4 px-6 align-middle">
                                <div>
                                    @if($app->status === 'interview')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#E8F5E9] text-[#176B47] border border-[#A5D6A7] shadow-2xs">
                                            <svg class="size-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span>Interview Scheduled</span>
                                        </span>
                                    @elseif($app->status === 'under_review')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200 shadow-2xs">
                                            <svg class="size-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Under Review</span>
                                        </span>
                                    @elseif($app->status === 'closed')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                            Closed
                                        </span>
                                    @elseif($app->status === 'rejected')
                                        <div class="inline-flex flex-col items-start gap-1">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200 shadow-2xs">
                                                <svg class="size-3.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span>Declined</span>
                                            </span>
                                            @if($app->rejection_reason)
                                                <div class="text-[11px] text-rose-800 bg-rose-50/70 p-2.5 rounded-xl border border-rose-200 max-w-xs leading-relaxed shadow-2xs">
                                                    <span class="font-bold text-[10px] uppercase tracking-wider text-rose-700 block mb-0.5">{{ __('Employer Remark:') }}</span>
                                                    "{{ $app->rejection_reason }}"
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-sky-50 text-[#1A2840] border border-sky-200 shadow-2xs">
                                            <svg class="size-3.5 text-[#244F73] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>{{ ucfirst($app->status) }}</span>
                                        </span>
                                    @endif

                                    @if($app->status_subtext)
                                        <div class="text-[11px] text-gray-500 mt-1.5 max-w-[200px] leading-tight">
                                            {{ $app->status_subtext }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-6 align-middle">
                                <div class="flex flex-col gap-1.5 w-44">
                                    @if($app->offer_details)
                                        <button type="button"
                                                wire:click="viewOffer({{ $app->id }})"
                                                class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#176B47] text-white text-xs font-bold hover:bg-[#125437] shadow-xs hover:shadow transition-all focus:ring-2 focus:ring-[#176B47]/40">
                                            <svg class="size-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>View Offer</span>
                                        </button>
                                    @endif

                                    <button type="button"
                                            wire:click="viewFitBreakdown({{ $app->id }})"
                                            class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl border border-gray-200 bg-white hover:bg-slate-50 hover:border-gray-300 text-xs font-semibold text-gray-700 hover:text-gray-900 transition-all shadow-2xs hover:shadow-xs focus:ring-2 focus:ring-[#244F73]/30 group">
                                        <svg class="size-3.5 text-emerald-600 group-hover:scale-105 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        <span>View Fit Breakdown</span>
                                    </button>

                                    @php
                                        $unreadChatCount = $app->unreadMessagesFor($user);
                                    @endphp
                                    <button type="button"
                                            wire:click="openChatModal({{ $app->id }})"
                                            class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl border border-[#B8D0E2] bg-gradient-to-r from-sky-50/80 to-[#E8F4F8]/60 hover:from-sky-100 hover:to-[#DCEAF4] text-xs font-semibold text-[#1A2840] transition-all shadow-2xs hover:shadow-xs focus:ring-2 focus:ring-[#244F73]/30 relative group">
                                        <svg class="size-3.5 text-[#244F73] group-hover:scale-105 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                        <span>{{ __('Accommodation Q&A') }}</span>
                                        @if($unreadChatCount > 0)
                                            <span class="inline-flex items-center justify-center size-4 rounded-full bg-red-600 text-white text-[10px] font-bold shadow-2xs animate-pulse">{{ $unreadChatCount }}</span>
                                        @endif
                                    </button>

                                    @if(in_array($app->status, ['interview', 'under_review', 'applied', 'submitted']))
                                        <button type="button"
                                                wire:click="promptWithdraw({{ $app->id }})"
                                                class="w-full inline-flex items-center justify-center gap-1 py-1 rounded-lg text-[11px] font-medium text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-all focus:outline-none focus:ring-1 focus:ring-rose-400 group">
                                            <svg class="size-3 text-gray-400 group-hover:text-rose-500 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            <span>Withdraw Application</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Nested Scheduled Interview Card (Exact Figma match under Interview row) -->
                        @if($app->status === 'interview')
                            @php
                                $progress = $app->interviewChecklistProgress();
                                $checklist = $app->checklist_status;
                                $isAllComplete = $app->isInterviewChecklistComplete();
                            @endphp
                            <tr class="bg-[#F0FDF4]/60">
                                <td colspan="6" class="px-6 py-4 border-t border-[#DCFCE7]">
                                    <div class="rounded-xl bg-[#F0FDF4] border border-[#BBF7D0] p-4 space-y-4">
                                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                            <div class="space-y-1.5">
                                                <div class="flex items-center gap-2 text-xs font-bold text-gray-900">
                                                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    <span>{{ $app->interview_at ? $app->interview_at->format('F j, Y · g:i A') . ' PHT (UTC+08:00)' : 'November 4, 2026 · 2:00 PM PHT (UTC+08:00)' }}</span>
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

                                            <div class="flex flex-wrap items-center gap-2">
                                                @if($app->isInterviewAccepted())
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-emerald-700 text-white text-xs font-bold shadow-2xs">
                                                        <svg class="size-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                        <span>{{ __('Interview Accepted') }}</span>
                                                    </span>

                                                    <a href="{{ $app->googleCalendarUrl() }}" target="_blank" rel="noopener noreferrer"
                                                       class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-[#244F73] text-[#244F73] bg-white text-xs font-semibold hover:bg-[#244F73]/10 transition-colors shadow-2xs"
                                                       title="{{ __('Open Google Calendar Event') }}">
                                                        <svg class="w-3.5 h-3.5 text-[#244F73]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                        <span>{{ __('Google Calendar') }}</span>
                                                    </a>

                                                    <button type="button"
                                                            wire:click="downloadIcs({{ $app->id }})"
                                                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-gray-300 text-gray-700 bg-white text-xs font-semibold hover:bg-gray-50 transition-colors shadow-2xs"
                                                            title="{{ __('Download .ics Calendar File') }}">
                                                        <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                        <span>{{ __('Download .ICS') }}</span>
                                                    </button>
                                                @else
                                                    <button type="button"
                                                            wire:click="acceptAndAddToCalendar({{ $app->id }})"
                                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#244F73] text-white text-xs font-semibold hover:bg-[#1A2840] transition-colors focus:ring-2 focus:ring-[#244F73] shrink-0 shadow-2xs">
                                                        <span>{{ __('Accept & Add to Calendar') }}</span>
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Pre-Interview Assistive Tech Checklist Accordion / Card --}}
                                        <div class="pt-3 border-t border-[#BBF7D0]/80">
                                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-bold text-gray-900 tracking-tight flex items-center gap-1.5">
                                                        <svg class="size-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        Pre-Interview Assistive Tech Checklist
                                                    </span>
                                                    @if($isAllComplete)
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-600 text-white shadow-2xs">
                                                            <svg class="size-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Verified &amp; Ready
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-900 border border-amber-300">
                                                            {{ $progress['completed'] }}/{{ $progress['total'] }} Ready ({{ $progress['percent'] }}%)
                                                        </span>
                                                    @endif
                                                </div>

                                                @if(! $isAllComplete)
                                                    <button type="button"
                                                            wire:click="markChecklistReady({{ $app->id }})"
                                                            class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#244F73] hover:text-[#1A2840] hover:underline self-start sm:self-auto">
                                                        <span>Confirm all 4 ready &rarr;</span>
                                                    </button>
                                                @endif
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5">
                                                @foreach($checklist as $itemKey => $item)
                                                    <button type="button"
                                                            wire:click="toggleChecklistItem({{ $app->id }}, '{{ $itemKey }}')"
                                                            role="checkbox"
                                                            aria-checked="{{ $item['checked'] ? 'true' : 'false' }}"
                                                            class="text-left flex items-start gap-3 p-2.5 rounded-lg border transition-all {{ $item['checked'] ? 'bg-white border-emerald-400 shadow-2xs' : 'bg-white/80 border-gray-200 hover:border-emerald-300' }}">
                                                        <div class="mt-0.5 size-4 rounded flex items-center justify-center shrink-0 {{ $item['checked'] ? 'bg-emerald-600 text-white' : 'border border-gray-400 bg-white' }}">
                                                            @if($item['checked'])
                                                                <svg class="size-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            @endif
                                                        </div>
                                                        <div class="min-w-0">
                                                            <div class="text-xs font-bold text-gray-900 {{ $item['checked'] ? 'text-emerald-950' : '' }} flex items-center gap-1.5">
                                                                <span>{{ $item['label'] }}</span>
                                                                @if($item['checked'])
                                                                    <span class="text-[10px] font-normal text-emerald-700">✓ Done</span>
                                                                @endif
                                                            </div>
                                                            <div class="text-[11px] text-gray-600 leading-snug mt-0.5">
                                                                {{ $item['desc'] }}
                                                            </div>
                                                        </div>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
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

    <!-- 4. Accommodation Inquiries Chat Modal -->
    @if($showChatModal && $chatApp)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="modal-chat-title">
            <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-xl border border-gray-200 space-y-4" wire:click.stop>
                <!-- Modal Header -->
                <div class="flex items-start justify-between pb-3 border-b border-gray-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-bold bg-[#E8F5E9] text-[#176B47] px-2.5 py-0.5 rounded-full border border-emerald-200">
                                <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                <span>{{ __('Accommodation & Logistics') }}</span>
                            </span>
                        </div>
                        <h3 id="modal-chat-title" class="text-base font-bold text-[#1A2840] mt-1">
                            {{ $chatApp->jobPosting->title }}
                        </h3>
                        <p class="text-xs text-gray-500">
                            {{ $chatApp->jobPosting->employerProfile->company_name }}
                        </p>
                    </div>
                    <button type="button" wire:click="closeChatModal" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 focus:ring-2 focus:ring-[#244F73]" aria-label="Close modal">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- 1-Click Accommodation Inquiry Chips -->
                <div class="space-y-1.5 bg-[#F4F7FA] p-3 rounded-xl border border-[#B8D0E2]/60">
                    <span class="text-[11px] font-bold text-[#1A2840] block">
                        {{ __('Quick Accommodation Inquiries (Click to ask):') }}
                    </span>
                    <div class="flex flex-wrap gap-1.5">
                        <button type="button"
                                wire:click="sendChatMessage('wheelchair')"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-white text-[#1A2840] border border-gray-300 hover:border-[#244F73] hover:bg-gray-50 transition-colors shadow-2xs">
                            <svg class="size-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 13v-2a2 2 0 00-2-2h-3l-1.5 4h3.5a1 1 0 011 1v4m-9-2a4 4 0 104 4"/></svg>
                            <span>{{ __('Step-Free Access') }}</span>
                        </button>
                        <button type="button"
                                wire:click="sendChatMessage('fsl')"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-white text-[#1A2840] border border-gray-300 hover:border-[#244F73] hover:bg-gray-50 transition-colors shadow-2xs">
                            <svg class="size-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <span>{{ __('FSL Interpreter') }}</span>
                        </button>
                        <button type="button"
                                wire:click="sendChatMessage('screen_reader')"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-white text-[#1A2840] border border-gray-300 hover:border-[#244F73] hover:bg-gray-50 transition-colors shadow-2xs">
                            <svg class="size-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>{{ __('Screen Reader Test Tasks') }}</span>
                        </button>
                        <button type="button"
                                wire:click="sendChatMessage('schedule')"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-white text-[#1A2840] border border-gray-300 hover:border-[#244F73] hover:bg-gray-50 transition-colors shadow-2xs">
                            <svg class="size-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3"/></svg>
                            <span>{{ __('Rest Breaks / Flex Time') }}</span>
                        </button>
                    </div>
                </div>

                <!-- Messages Log -->
                <div class="h-64 overflow-y-auto space-y-3 p-3.5 bg-gray-50 rounded-xl border border-gray-200" role="log" aria-live="polite" aria-label="Message log">
                    @forelse($chatApp->messages as $msg)
                        @php
                            $isMe = $msg->sender_id === $user->id;
                            $isSystem = $msg->type === 'system';
                            $isInquiry = $msg->type === 'accommodation_inquiry';
                            $resolved = $msg->metadata['resolved'] ?? false;
                        @endphp

                        @if($isSystem)
                            <div class="p-2.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs text-center font-medium">
                                <span>{{ $msg->body }}</span>
                                <span class="block text-[10px] text-emerald-700/80 mt-0.5">{{ $msg->created_at?->diffForHumans() }}</span>
                            </div>
                        @else
                            <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                                <div class="max-w-[85%] rounded-2xl px-3.5 py-2.5 text-xs shadow-2xs {{ $isMe ? 'bg-[#1A2840] text-white rounded-br-xs' : 'bg-white text-gray-800 border border-gray-200 rounded-bl-xs' }}">
                                    @if($isInquiry)
                                        <div class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider mb-1 {{ $isMe ? 'text-sky-300' : 'text-[#244F73]' }}">
                                            <svg class="size-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>{{ __('Accommodation Question') }}</span>
                                            @if($resolved)
                                                <span class="inline-flex items-center px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 font-bold text-[9px]">✓ Confirmed</span>
                                            @endif
                                        </div>
                                    @endif
                                    <p class="leading-relaxed whitespace-pre-wrap">{{ $msg->body }}</p>
                                </div>
                                <span class="text-[10px] text-gray-400 mt-1 px-1">
                                    {{ $isMe ? __('You') : ($msg->sender?->name ?? __('Employer')) }} · {{ $msg->created_at?->format('M d, g:i A') }}
                                </span>
                            </div>
                        @endif
                    @empty
                        <div class="h-full flex flex-col items-center justify-center text-center p-6 text-gray-400">
                            <svg class="size-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <p class="text-xs font-semibold text-gray-600">{{ __('No messages yet') }}</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">{{ __('Ask about venue accessibility, interpreter setups, or interview format.') }}</p>
                        </div>
                    @endforelse
                </div>

                <!-- Compose Input -->
                <div class="space-y-2">
                    <div class="relative">
                        <textarea wire:model="chatMessageText"
                                  wire:keydown.ctrl.enter="sendChatMessage"
                                  rows="2"
                                  placeholder="{{ __('Type your question about accommodations or interview logistics (Ctrl+Enter to send)...') }}"
                                  class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-xs text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-[#244F73] focus:border-transparent"></textarea>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] text-gray-400">
                            {{ __('Press Ctrl+Enter to send') }}
                        </span>
                        <button type="button"
                                wire:click="sendChatMessage"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#1A2840] text-white text-xs font-bold hover:bg-[#244F73] transition-colors shadow-2xs">
                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            <span>{{ __('Send Message') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

