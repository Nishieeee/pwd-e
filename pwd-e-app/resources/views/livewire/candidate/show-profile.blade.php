<div class="space-y-8 max-w-6xl mx-auto w-full min-w-0">
    <style>
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
                font-size: 11pt !important;
            }
            header, aside, nav, .no-print, [role="banner"], [role="navigation"] {
                display: none !important;
            }
            .print-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }
            .page-break-inside-avoid {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }
            .print-signature-block {
                display: block !important;
            }
        }
    </style>

    {{-- Profile Saved Banner --}}
    @if(session('profile_saved_banner'))
        <div class="no-print p-4 rounded-xl bg-[#F0FDF4] border border-[#BBF7D0] text-[#176B47] flex items-center justify-between gap-4 shadow-xs" role="alert" aria-live="polite">
            <div class="flex items-start gap-3">
                <svg class="size-5 text-[#176B47] shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <div class="text-xs leading-relaxed">
                    <strong class="block font-bold text-[#176B47]">Profile saved successfully!</strong>
                    <span>{{ session('profile_saved_banner') }}</span>
                </div>
            </div>
            <a href="{{ route('candidate.jobs') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#176B47] text-white font-bold text-xs hover:bg-[#125336] transition-colors shrink-0">
                <span>Find job matches</span>
                <span>&rarr;</span>
            </a>
        </div>
    @endif

    {{-- Top Header Banner Card (Screen Only) --}}
    <div class="no-print w-full relative overflow-hidden rounded-2xl border border-primary-border bg-white p-5 sm:p-6 shadow-sm" style="background: linear-gradient(135deg, #DCEAF4 0%, #FFFFFF 100%); box-shadow: 0 4px 20px rgba(26,40,64,0.06);">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="space-y-2 max-w-3xl min-w-0 flex-1">
                <nav class="inline-flex items-center gap-1.5 rounded-full border border-primary-border bg-white px-3 py-1 text-xs font-semibold text-body-muted shadow-2xs" aria-label="Breadcrumb">
                    @if($isEmployer)
                        <a href="{{ route('employer.jobs.index') }}" class="hover:text-primary transition-colors rounded focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary">Employer Portal</a>
                        <span aria-hidden="true">/</span>
                        <span class="text-primary-dark font-bold" aria-current="page">Candidate Profile</span>
                    @else
                        <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors rounded focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary">Dashboard</a>
                        <span aria-hidden="true">/</span>
                        <span class="text-primary-dark font-bold" aria-current="page">{{ __('View my Profile') }}</span>
                    @endif
                </nav>
                <h1 class="font-heading text-2xl sm:text-3xl font-bold text-primary-dark tracking-tight">
                    {{ $isOwner ? __('Profile') : __('Candidate Digital Profile') }}
                </h1>
                <p class="text-xs sm:text-sm text-body-muted leading-relaxed max-w-2xl">
                    {{ __('Official multimodal employment profile and Philippine Digital Profile format under RA 10524.') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                @if($isOwner)
                    {{-- Mode Switcher Tabs --}}
                    <div class="inline-flex rounded-xl p-1 bg-white/90 border border-primary-border shadow-2xs" role="tablist" aria-label="Profile navigation">
                        <button type="button"
                                wire:click="setTab('view')"
                                role="tab"
                                aria-selected="{{ $tab === 'view' ? 'true' : 'false' }}"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-bold transition-all whitespace-nowrap cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 {{ $tab === 'view' ? 'bg-primary-dark text-white shadow-xs' : 'text-body-muted hover:text-primary-dark hover:bg-primary-tint/50' }}">
                            <svg class="size-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>{{ __('View Profile') }}</span>
                        </button>
                        <button type="button"
                                wire:click="setTab('edit')"
                                role="tab"
                                aria-selected="{{ $tab === 'edit' ? 'true' : 'false' }}"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-bold transition-all whitespace-nowrap cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 {{ $tab === 'edit' ? 'bg-primary-dark text-white shadow-xs' : 'text-body-muted hover:text-primary-dark hover:bg-primary-tint/50' }}">
                            <svg class="size-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            <span>{{ __('Edit Profile') }}</span>
                        </button>
                    </div>

                    @if($tab === 'view')
                        <button type="button" onclick="window.print()"
                                class="inline-flex items-center justify-center gap-2 h-11 px-4 rounded-xl bg-primary-dark text-xs sm:text-sm font-semibold text-white hover:bg-primary shadow-xs transition-colors whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-dark focus-visible:ring-offset-2 cursor-pointer">
                            <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            <span>{{ __('Print / Save Profile') }}</span>
                        </button>
                    @endif
                @else
                    <button type="button" onclick="history.back()"
                            class="inline-flex items-center justify-center gap-1.5 h-11 px-4 rounded-xl border border-primary-border bg-white text-xs sm:text-sm font-semibold text-primary-dark hover:bg-primary-tint/60 shadow-2xs transition-colors whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 cursor-pointer">
                        <svg class="size-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>{{ __('Back') }}</span>
                    </button>
                    <button type="button" onclick="window.print()"
                            class="inline-flex items-center justify-center gap-2 h-11 px-4 rounded-xl bg-primary-dark text-xs sm:text-sm font-semibold text-white hover:bg-primary shadow-xs transition-colors whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-dark focus-visible:ring-offset-2 cursor-pointer">
                        <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>{{ __('Print / Save Profile') }}</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    @if ($isOwner && $tab === 'edit')
        {{-- Unified Profile Editor --}}
        <div class="no-print space-y-6 w-full min-w-0">
            <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-primary-border/60">
                <div>
                    <h2 class="text-lg font-bold text-primary-dark">{{ __('Edit Profile & Credentials') }}</h2>
                    <p class="text-sm text-gray-500 mt-1 leading-relaxed">{{ __('Update your personal information, work history, education, skills, and workplace accommodations.') }}</p>
                </div>
                <button type="button" wire:click="setTab('view')"
                        class="text-xs font-semibold text-primary hover:underline inline-flex items-center gap-1 rounded px-2 py-1 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-2">
                    <span>{{ __('View Profile Preview') }}</span>
                    <span aria-hidden="true">&rarr;</span>
                </button>
            </div>

            <livewire:candidate.profile-builder :is-embedded="true" wire:key="embedded-profile-builder-{{ $profile->id }}" />
        </div>
    @else
        {{-- Main Hybrid Profile Card --}}
        <article class="print-container bg-white rounded-2xl border border-primary-border shadow-sm p-6 sm:p-10 space-y-8 w-full min-w-0 overflow-hidden" aria-label="{{ __('Candidate Employment Profile') }}">

        {{-- Profile Official Header --}}
        <header class="flex flex-col sm:flex-row items-start justify-between gap-6 pb-6 border-b-2 border-primary-dark/20 page-break-inside-avoid w-full min-w-0">
            <div class="flex flex-col sm:flex-row items-start gap-6 min-w-0 flex-1">
                {{-- 2x2 Photo Frame --}}
                <div class="relative w-28 h-28 sm:w-32 sm:h-32 rounded-xl border-2 border-primary-border bg-primary-tint/30 flex items-center justify-center overflow-hidden shrink-0 shadow-xs">
                    @if($profile->avatar_path)
                        <img src="{{ Storage::url($profile->avatar_path) }}"
                             alt="{{ $candidateUser->name }} 2x2 Photo"
                             class="w-full h-full object-cover"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="flex-col items-center justify-center text-center p-2" style="display: none;">
                            <span class="font-heading text-2xl font-bold text-primary">{{ $candidateUser?->initials() ?? 'MS' }}</span>
                            <span class="text-[10px] uppercase font-semibold text-body-muted mt-1 tracking-wider">2x2 Photo</span>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center text-center p-2">
                            <span class="font-heading text-2xl font-bold text-primary">{{ $candidateUser?->initials() ?? 'MS' }}</span>
                            <span class="text-[10px] uppercase font-semibold text-body-muted mt-1 tracking-wider">2x2 Photo</span>
                        </div>
                    @endif
                    <div class="absolute inset-0 border border-black/5 pointer-events-none rounded-xl"></div>
                </div>

                {{-- Name & Identity Meta --}}
                <div class="space-y-1.5 min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#E8F5E9] text-[#176B47] border border-[#176B47]/20">
                            <svg class="size-3.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            RA 7277 Verified PWD
                        </span>
                        @if($profile->disability_type)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary-tint text-primary-dark border border-primary-border">
                                Category: {{ $profile->disability_type }}
                            </span>
                        @endif
                    </div>

                    <h2 class="font-heading text-2xl sm:text-3xl font-extrabold text-primary-dark tracking-tight break-words">
                        {{ $candidateUser->name }}@if($profile->suffix) {{ $profile->suffix }}@endif
                    </h2>

                    <p class="text-sm font-semibold text-primary break-words">
                        {{ $profile->headline ?: __('Professional seeking inclusive and barrier-free opportunities') }}
                    </p>

                    <div class="flex flex-wrap items-center gap-y-1 gap-x-4 pt-1 text-xs text-body-muted min-w-0">
                        @if($profile->address || $profile->city || $profile->province || $profile->country)
                            <span class="flex items-center gap-1">
                                <svg class="size-3.5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="break-words">{{ implode(', ', array_filter([$profile->address, $profile->barangay, $profile->city, $profile->province, $profile->country])) }}</span>
                            </span>
                        @endif

                        <span class="flex items-center gap-1 min-w-0 break-all">
                            <svg class="size-3.5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>{{ $candidateUser->email }}</span>
                        </span>

                        @if($profile->phone)
                            <span class="flex items-center gap-1 shrink-0">
                                <svg class="size-3.5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span>{{ $profile->phone }}</span>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Document Stamp / Meta --}}
            <div class="sm:text-right space-y-1 text-xs shrink-0 pt-2 sm:pt-0">
                <span class="inline-block uppercase tracking-wider text-[10px] font-bold text-primary bg-primary-tint px-2.5 py-1 rounded">
                    Digital Profile
                </span>
                <p class="font-mono text-[11px] text-body-muted">
                    ID: PWD-{{ str_pad((string) $candidateUser->id, 5, '0', STR_PAD_LEFT) }}
                </p>
                <p class="text-[11px] text-body-muted">
                    Updated: {{ $profile->updated_at ? $profile->updated_at->format('M d, Y') : now()->format('M d, Y') }}
                </p>
            </div>
        </header>

        {{-- Section 1: Executive Summary / Bio --}}
        @if($profile->bio)
            <section class="space-y-2 page-break-inside-avoid">
                <h3 class="font-heading text-xs font-bold uppercase tracking-wider text-primary-dark pb-1.5 border-b border-gray-100 flex items-center gap-2">
                    <svg class="size-4 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>{{ __('Personal Summary & Career Objective') }}</span>
                </h3>
                <p class="text-sm text-primary-dark leading-relaxed whitespace-pre-line">
                    {{ $profile->bio }}
                </p>
            </section>
        @endif

        {{-- Section 2: Workplace Accommodations & Accessibility Needs (RA 7277 / RA 10524) --}}
        <section class="rounded-xl border border-primary-border bg-primary-tint/30 p-5 space-y-3 page-break-inside-avoid">
            <div class="flex items-center justify-between gap-2">
                <h3 class="font-heading text-xs font-bold uppercase tracking-wider text-primary-dark flex items-center gap-2">
                    <svg class="size-4 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="4" r="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0l-3 6m3-6l3 6M9 9h6"/></svg>
                    <span>{{ __('Required Workplace Accommodations (RA 7277 / RA 10524)') }}</span>
                </h3>
                <span class="text-[11px] font-semibold text-primary">Incentive Eligible</span>
            </div>

            <p class="text-xs text-body-muted">
                {{ __('Accommodations required by candidate to perform essential job duties efficiently without physical or communication barriers:') }}
            </p>

            <div class="flex flex-wrap gap-2 pt-1">
                @forelse($profile->accommodations as $acc)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white text-primary-dark border border-primary-border shadow-2xs">
                        <svg class="size-3.5 text-secondary" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ $acc->name }}</span>
                    </span>
                @empty
                    @if($profile->accommodation_notes)
                        <span class="text-xs text-primary-dark font-medium">{{ $profile->accommodation_notes }}</span>
                    @else
                        <span class="text-xs text-body-muted italic">{{ __('Standard workplace accommodations indicated upon application.') }}</span>
                    @endif
                @endforelse
            </div>

            @if($profile->accommodation_notes && $profile->accommodations->isNotEmpty())
                <p class="text-xs text-primary-dark pt-1">
                    <strong>{{ __('Candidate notes:') }}</strong> {{ $profile->accommodation_notes }}
                </p>
            @endif
        </section>

        {{-- Section 3: Work Experience & Employment History --}}
        <section class="space-y-4 page-break-inside-avoid">
            <h3 class="font-heading text-xs font-bold uppercase tracking-wider text-primary-dark pb-1.5 border-b border-gray-100 flex items-center gap-2">
                <svg class="size-4 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span>{{ __('Work Experience & Employment History') }}</span>
            </h3>

            <div class="space-y-4">
                @forelse($profile->workExperiences as $exp)
                    <div class="space-y-1 text-sm border-l-2 border-primary/40 pl-3.5 py-0.5">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <h4 class="font-bold text-primary-dark text-base">{{ $exp->job_title }}</h4>
                            <span class="text-xs text-body-muted font-medium">
                                {{ $exp->start_date ? $exp->start_date->format('M Y') : 'Start' }} —
                                {{ $exp->is_current ? 'Present' : ($exp->end_date ? $exp->end_date->format('M Y') : 'End') }}
                            </span>
                        </div>
                        <p class="text-xs font-semibold text-primary">
                            {{ $exp->company_name }}@if($exp->location) · {{ $exp->location }}@endif
                        </p>
                        @if($exp->description)
                            <p class="text-xs text-body-muted leading-relaxed whitespace-pre-line pt-1">
                                {{ $exp->description }}
                            </p>
                        @endif
                    </div>
                @empty
                    <p class="text-xs text-body-muted italic">{{ __('No formal work experience added yet. Entry-level & vocational training candidate.') }}</p>
                @endforelse
            </div>
        </section>

        {{-- Section 4: Educational Attainment --}}
        <section class="space-y-4 page-break-inside-avoid">
            <h3 class="font-heading text-xs font-bold uppercase tracking-wider text-primary-dark pb-1.5 border-b border-gray-100 flex items-center gap-2">
                <svg class="size-4 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                <span>{{ __('Educational Attainment') }}</span>
            </h3>

            <div class="space-y-3">
                @forelse($profile->education as $edu)
                    <div class="space-y-0.5 text-sm border-l-2 border-secondary/40 pl-3.5 py-0.5">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <h4 class="font-bold text-primary-dark">{{ $edu->degree }}@if($edu->field_of_study) in {{ $edu->field_of_study }}@endif</h4>
                            <span class="text-xs text-body-muted font-medium">
                                {{ $edu->start_date ? $edu->start_date->format('Y') : '' }} —
                                {{ $edu->is_current ? 'Present' : ($edu->end_date ? $edu->end_date->format('Y') : 'Completed') }}
                            </span>
                        </div>
                        <p class="text-xs font-semibold text-secondary">
                            {{ $edu->institution_name }}
                        </p>
                        @if($edu->description)
                            <p class="text-xs text-body-muted pt-0.5">{{ $edu->description }}</p>
                        @endif
                    </div>
                @empty
                    @if($profile->highest_education)
                        <p class="text-xs text-primary-dark font-medium">{{ $profile->highest_education }}</p>
                    @else
                        <p class="text-xs text-body-muted italic">{{ __('Educational background details available upon request.') }}</p>
                    @endif
                @endforelse
            </div>
        </section>

        {{-- Section 5: Skills & Languages --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 page-break-inside-avoid">
            {{-- Skills --}}
            <section class="space-y-2.5">
                <h3 class="font-heading text-xs font-bold uppercase tracking-wider text-primary-dark pb-1.5 border-b border-gray-100">
                    {{ __('Skills & Core Competencies') }}
                </h3>
                <div class="flex flex-wrap gap-1.5">
                    @forelse((array) ($profile->skills ?? []) as $skill)
                        <span class="px-2.5 py-1 rounded-md bg-gray-100 text-primary-dark text-xs font-medium">
                            {{ $skill }}
                        </span>
                    @empty
                        <span class="text-xs text-body-muted italic">{{ __('Skills documented during onboarding.') }}</span>
                    @endforelse
                </div>
            </section>

            {{-- Spoken & Sign Languages --}}
            <section class="space-y-2.5">
                <h3 class="font-heading text-xs font-bold uppercase tracking-wider text-primary-dark pb-1.5 border-b border-gray-100">
                    {{ __('Communication & Languages') }}
                </h3>
                <div class="flex flex-wrap gap-1.5">
                    @forelse((array) ($profile->spoken_languages ?? ['Filipino', 'English']) as $lang)
                        <span class="px-2.5 py-1 rounded-md bg-primary-tint/60 text-primary-dark text-xs font-medium">
                            {{ $lang }}
                        </span>
                    @empty
                        <span class="text-xs text-body-muted italic">{{ __('Filipino, English') }}</span>
                    @endforelse
                </div>
            </section>
        </div>

        {{-- Section 6: Certifications & Licenses --}}
        @if($profile->certifications->isNotEmpty())
            <section class="space-y-3 page-break-inside-avoid">
                <h3 class="font-heading text-xs font-bold uppercase tracking-wider text-primary-dark pb-1.5 border-b border-gray-100 flex items-center gap-2">
                    <svg class="size-4 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    <span>{{ __('Certifications & Credentials') }}</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    @foreach($profile->certifications as $cert)
                        <div class="rounded-lg border border-gray-200 p-3 space-y-1">
                            <p class="font-bold text-primary-dark">{{ $cert->name }}</p>
                            <p class="text-body-muted">{{ $cert->issuing_organization }}</p>
                            @if($cert->issued_at)
                                <p class="text-[11px] text-body-muted">Issued: {{ $cert->issued_at->format('M Y') }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Section 7: Multimodal Artifacts (Screen Only) --}}
        @if($profile->fsl_video_path || $profile->resume_pdf_path)
            <section class="no-print space-y-3 pt-4 border-t border-gray-100">
                <h3 class="font-heading text-xs font-bold uppercase tracking-wider text-primary-dark flex items-center gap-2">
                    <svg class="size-4 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <span>{{ __('Multimodal Attachments') }}</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @if($profile->fsl_video_path)
                        <div class="rounded-xl border border-primary-border p-4 bg-primary-tint/20 space-y-2">
                            <div class="flex items-center gap-2 text-xs font-bold text-primary-dark">
                                <span class="size-2 rounded-full bg-[#176B47]"></span>
                                <span>Filipino Sign Language (FSL) Video Resume</span>
                            </div>
                            <video controls class="w-full rounded-lg bg-black aspect-video">
                                <source src="{{ Storage::url($profile->fsl_video_path) }}" type="video/mp4">
                                Your browser does not support video playback.
                            </video>
                        </div>
                    @endif

                    @if($profile->resume_pdf_path)
                        <div class="rounded-xl border border-primary-border p-4 bg-primary-tint/20 flex flex-col justify-between space-y-3">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 text-xs font-bold text-primary-dark">
                                    <svg class="size-4 text-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                                    <span>Attached Original Curriculum Vitae (PDF)</span>
                                </div>
                                <p class="text-xs text-body-muted">Optional detailed external resume file uploaded by candidate.</p>
                            </div>
                            <a href="{{ Storage::url($profile->resume_pdf_path) }}" target="_blank"
                               class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-lg bg-white border border-primary-border text-xs font-semibold text-primary hover:bg-primary-tint transition-colors">
                                <span>Open PDF Document</span>
                                <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </div>
                    @endif
                </div>
            </section>
        @endif

        {{-- Formal Bio-Data Certification Block (Visible in Print & Screen) --}}
        <footer class="pt-6 border-t-2 border-primary-dark/20 space-y-6 page-break-inside-avoid">
            <p class="text-xs text-body-muted italic leading-relaxed text-center sm:text-left">
                "I hereby certify that all information stated herein is true, correct, and complete to the best of my knowledge and belief. Prepared in compliance with Republic Act No. 7277, RA 10524, and the Data Privacy Act of 2012 (RA 10173)."
            </p>

            <div class="flex flex-col sm:flex-row items-center sm:items-end justify-between gap-6 pt-4 text-xs text-primary-dark w-full min-w-0">
                <div class="space-y-1 text-center sm:text-left min-w-0 flex-1">
                    <p class="text-[11px] text-body-muted">Accredited by PESO &amp; Republic Act No. 10524 Partner</p>
                    <p class="font-bold text-xs">PWD-E Multimodal Employment Platform</p>
                    <p class="text-[10px] text-body-muted">Western Mindanao State University · College of Computing Studies</p>
                </div>

                <div class="text-center sm:text-right space-y-1 shrink-0 w-full sm:w-auto flex flex-col items-center sm:items-end">
                    <div class="w-56 border-b border-primary-dark pb-1">
                        <span class="font-bold uppercase tracking-wider text-xs">{{ $candidateUser->name }}</span>
                    </div>
                    <p class="text-[10px] text-body-muted uppercase tracking-wider">Candidate Signature Over Printed Name</p>
                    <p class="text-[10px] text-body-muted">Date: {{ now()->format('F d, Y') }}</p>
                </div>
            </div>
        </footer>

    </article>
    @endif
</div>
