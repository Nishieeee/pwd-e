<div class="space-y-6">
    {{-- Success Alert --}}
    @if(session('profile_updated_banner'))
        <div class="rounded-xl bg-[#E8F5E9] border border-[#A5D6A7] p-4 text-xs font-semibold text-[#176B47] flex items-center justify-between gap-3 shadow-2xs" role="alert">
            <div class="flex items-center gap-2">
                <svg class="size-4 shrink-0 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                <span>{{ session('profile_updated_banner') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950 font-bold text-sm leading-none">&times;</button>
        </div>
    @endif

    {{-- Company Profile Header Banner --}}
    <div class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-xs" style="background: linear-gradient(135deg, #F0F4F8 0%, #FFFFFF 100%);">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
            <div class="flex flex-col sm:flex-row items-start gap-4">
                <div class="w-16 h-16 rounded-2xl bg-[#244F73] text-white flex items-center justify-center font-bold text-2xl uppercase shadow-md shrink-0">
                    {{ strtoupper(substr($profile->company_name ?? 'CO', 0, 2)) }}
                </div>
                <div class="space-y-1.5">
                    <div class="flex flex-wrap items-center gap-2">
                        @if($profile->verified_at)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                <svg class="size-3 text-emerald-700" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <span>{{ __('Verified Employer') }}</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-900 border border-amber-300">
                                <span>{{ __('Verification Pending') }}</span>
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-sky-100 text-sky-800 border border-sky-300">
                            <span>{{ __('Republic Act No. 10524 Partner') }}</span>
                        </span>
                        @if($profile->isMatrixComplete())
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                <span>{{ __('Accessibility Matrix Certified') }}</span>
                            </span>
                        @endif
                    </div>

                    <h1 class="font-heading text-2xl font-bold text-[#1A2840]">
                        {{ $profile->company_name }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-y-1 gap-x-4 text-xs text-gray-500">
                        @if($profile->industry)
                            <div class="flex items-center gap-1.5">
                                <svg class="size-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>{{ $profile->industry }}</span>
                            </div>
                        @endif

                        @if($profile->location)
                            <div class="flex items-center gap-1.5">
                                <svg class="size-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>{{ $profile->location }}</span>
                            </div>
                        @endif

                        @if($profile->website)
                            <div class="flex items-center gap-1.5">
                                <svg class="size-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                <a href="{{ $profile->website }}" target="_blank" rel="noopener noreferrer" class="text-[#244F73] font-semibold hover:underline">
                                    {{ preg_replace('#^https?://#', '', $profile->website) }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <a href="{{ route('candidate.employers.show', $profile) }}"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-gray-300 text-gray-700 hover:text-[#244F73] hover:border-[#244F73] text-xs font-semibold shadow-2xs transition-colors"
                   title="{{ __('View how candidates and visitors see this profile') }}">
                    <svg class="size-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <span>{{ __('Preview Public Visitor View') }}</span>
                    <svg class="size-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>

                @if($activeTab === 'overview')
                    <button type="button"
                            wire:click="setTab('edit')"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#244F73] text-white text-xs font-semibold hover:bg-[#1A2840] shadow-xs transition-colors">
                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>{{ __('Edit Profile') }}</span>
                    </button>
                @else
                    <button type="button"
                            wire:click="setTab('overview')"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 text-xs font-semibold transition-colors">
                        <span>{{ __('Back to Overview') }}</span>
                    </button>
                @endif
            </div>
        </div>

        {{-- Tab Controls --}}
        <div class="flex items-center gap-2 mt-6 pt-4 border-t border-gray-200/80">
            <button type="button"
                    wire:click="setTab('overview')"
                    class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ $activeTab === 'overview' ? 'bg-[#244F73] text-white shadow-2xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>{{ __('Profile Overview') }}</span>
            </button>

            <button type="button"
                    wire:click="setTab('edit')"
                    class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ $activeTab === 'edit' ? 'bg-[#244F73] text-white shadow-2xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>{{ __('Edit Profile & Accessibility Matrix') }}</span>
            </button>
        </div>
    </div>

    {{-- TAB 1: OVERVIEW MODE --}}
    @if($activeTab === 'overview')
        {{-- Stat Summary Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-2xs">
                <div class="text-xs font-medium text-gray-500">{{ __('Published Job Openings') }}</div>
                <div class="mt-1 flex items-baseline justify-between">
                    <span class="text-2xl font-bold text-[#1A2840]">{{ $activeJobCount }}</span>
                    <a href="{{ route('employer.jobs.index') }}" class="text-xs font-semibold text-[#244F73] hover:underline">{{ __('Manage') }} &rarr;</a>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-2xs">
                <div class="text-xs font-medium text-gray-500">{{ __('Workplace Accommodations') }}</div>
                <div class="mt-1 flex items-baseline justify-between">
                    <span class="text-2xl font-bold text-emerald-700">{{ $profile->accommodations->count() }}</span>
                    <span class="text-xs text-gray-400">{{ __('Active in Matrix') }}</span>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-2xs">
                <div class="text-xs font-medium text-gray-500">{{ __('Declared Total Workforce') }}</div>
                <div class="mt-1 flex items-baseline justify-between">
                    <span class="text-2xl font-bold text-[#1A2840]">{{ $profile->workforce_total ?? 0 }}</span>
                    <a href="{{ route('employer.workforce') }}" class="text-xs font-semibold text-[#244F73] hover:underline">{{ __('Workforce & Tax') }} &rarr;</a>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-2xs">
                <div class="text-xs font-medium text-gray-500">{{ __('Matrix Certified Status') }}</div>
                <div class="mt-1 flex items-baseline justify-between">
                    @if($profile->isMatrixComplete())
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">
                            ✓ {{ __('Certified') }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md">
                            {{ __('Incomplete') }}
                        </span>
                    @endif
                    <button type="button" wire:click="setTab('edit')" class="text-xs font-semibold text-[#244F73] hover:underline">{{ __('Update') }} &rarr;</button>
                </div>
            </div>
        </div>

        {{-- Main Overview Content --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                {{-- About the Company --}}
                <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                        <h2 class="text-base font-bold text-[#1A2840] flex items-center gap-2">
                            <svg class="size-4 text-[#244F73]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span>{{ __('About Company & Inclusive Culture') }}</span>
                        </h2>
                        <button type="button" wire:click="setTab('edit')" class="text-xs font-semibold text-[#244F73] hover:underline">
                            {{ __('Edit') }}
                        </button>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                        {{ $profile->about_summary }}
                    </p>
                </section>

                {{-- Accessibility Highlights --}}
                @if(!empty($profile->accessibility_highlights))
                    <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                            <h2 class="text-base font-bold text-[#1A2840] flex items-center gap-2">
                                <svg class="size-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span>{{ __('Accessibility & Inclusion Highlights') }}</span>
                            </h2>
                            <button type="button" wire:click="setTab('edit')" class="text-xs font-semibold text-[#244F73] hover:underline">
                                {{ __('Edit') }}
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-2 pt-1">
                            @foreach($profile->accessibility_highlights as $highlight)
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-[#E8F5E9] text-[#176B47] border border-[#A5D6A7]">
                                    <svg class="size-3 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    <span>{{ $highlight }}</span>
                                </span>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Workplace Accessibility Matrix --}}
                <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-2xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <div>
                            <h2 class="text-base font-bold text-[#1A2840] flex items-center gap-2">
                                <svg class="size-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span>{{ __('Workplace Accessibility Matrix') }}</span>
                            </h2>
                            <p class="text-xs text-gray-500 mt-0.5">{{ __('Active physical accommodations and assistive software certified for candidate matching.') }}</p>
                        </div>
                        <button type="button" wire:click="setTab('edit')" class="text-xs font-semibold text-[#244F73] hover:underline">
                            {{ __('Update Matrix') }}
                        </button>
                    </div>

                    @php
                        $activeAccommodationsByCategory = $profile->accommodations->groupBy('category');
                        $categoryLabels = [
                            'mobility' => ['title' => 'Mobility & Step-Free Access', 'color' => 'emerald'],
                            'visual' => ['title' => 'Visual Accommodations & High Contrast', 'color' => 'sky'],
                            'hearing' => ['title' => 'Hearing, FSL & Real-Time Captions', 'color' => 'indigo'],
                            'cognitive' => ['title' => 'Cognitive & Neurodivergent Support', 'color' => 'purple'],
                            'flexible_work' => ['title' => 'Flexible Work & Rest Schedules', 'color' => 'amber'],
                            'speech' => ['title' => 'Speech & Alternative Communication', 'color' => 'teal'],
                        ];
                    @endphp

                    @if($activeAccommodationsByCategory->isEmpty())
                        <div class="rounded-xl border border-dashed border-gray-300 p-8 text-center space-y-3">
                            <p class="text-xs text-gray-500">{{ __('No workplace accommodations have been marked yet. Complete your matrix so PWD candidates with matching accommodation needs can discover your job postings.') }}</p>
                            <button type="button" wire:click="setTab('edit')" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#244F73] text-white text-xs font-semibold hover:bg-[#1A2840]">
                                <span>{{ __('Select Accommodations Now') }} &rarr;</span>
                            </button>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($categoryLabels as $catKey => $meta)
                                @php $items = $activeAccommodationsByCategory->get($catKey, collect()); @endphp
                                @if($items->isNotEmpty())
                                    <div class="rounded-lg border border-gray-100 bg-gray-50/70 p-4 space-y-2">
                                        <div class="text-xs font-bold uppercase tracking-wider text-gray-700 flex items-center justify-between">
                                            <span>{{ $meta['title'] }}</span>
                                            <span class="text-[11px] font-normal text-gray-500">{{ $items->count() }} {{ __('verified') }}</span>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                                            @foreach($items as $acc)
                                                <div class="bg-white rounded-md p-2.5 border border-gray-200 text-xs flex items-start gap-2 shadow-2xs">
                                                    <svg class="size-3.5 text-emerald-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                    <div>
                                                        <div class="font-bold text-gray-900 leading-snug">{{ $acc->name }}</div>
                                                        @if($acc->description)
                                                            <div class="text-[11px] text-gray-500 leading-tight mt-0.5">{{ $acc->description }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </section>
            </div>

            {{-- Right Column: Active Jobs & Quick Actions --}}
            <div class="space-y-6">
                {{-- Quick Actions Card --}}
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-2xs space-y-3">
                    <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('Quick Actions') }}</h3>
                    <div class="flex flex-col gap-2">
                        <button type="button" wire:click="setTab('edit')" class="w-full inline-flex items-center justify-center gap-2 px-3.5 py-2.5 rounded-xl bg-[#244F73] text-white text-xs font-semibold hover:bg-[#1A2840] shadow-2xs transition-colors">
                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>{{ __('Edit Company Profile') }}</span>
                        </button>

                        <a href="{{ route('candidate.employers.show', $profile) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-3.5 py-2.5 rounded-xl border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:text-[#244F73] hover:border-[#244F73] shadow-2xs transition-colors">
                            <svg class="size-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>{{ __('View as Candidate / Visitor') }}</span>
                            <svg class="size-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>

                        <a href="{{ route('employer.jobs.create') }}" class="w-full inline-flex items-center justify-center gap-2 px-3.5 py-2.5 rounded-xl border border-emerald-300 bg-emerald-50 text-xs font-semibold text-emerald-800 hover:bg-emerald-100 shadow-2xs transition-colors">
                            <svg class="size-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>{{ __('Post New Job') }}</span>
                        </a>
                    </div>
                </div>

                {{-- Active Job Openings Summary --}}
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                        <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('Job Postings (:count)', ['count' => $profile->jobPostings->count()]) }}</h3>
                        <a href="{{ route('employer.jobs.index') }}" class="text-xs font-semibold text-[#244F73] hover:underline">{{ __('View All') }}</a>
                    </div>

                    @forelse($profile->jobPostings->take(4) as $job)
                        <div class="p-3 rounded-lg border border-gray-100 bg-gray-50/60 hover:bg-gray-100/70 transition-colors space-y-1">
                            <div class="flex items-start justify-between gap-2">
                                <div class="text-xs font-bold text-gray-900 leading-snug">{{ $job->title }}</div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $job->status === 'published' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-700' }}">
                                    {{ ucfirst($job->status) }}
                                </span>
                            </div>
                            <div class="text-[11px] text-gray-500 flex items-center justify-between">
                                <span>{{ $job->location ?? 'Taguig / Remote' }}</span>
                                <a href="{{ route('employer.jobs.edit', $job) }}" class="text-[#244F73] hover:underline font-medium">{{ __('Edit') }}</a>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 py-3 text-center">{{ __('No job postings created yet.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    {{-- TAB 2: EDIT PROFILE & ACCOMMODATIONS FORM --}}
    @if($activeTab === 'edit')
        <form wire:submit.prevent="save" class="space-y-6">
            {{-- Basic Company Details --}}
            <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-2xs space-y-5">
                <div class="pb-3 border-b border-gray-100">
                    <h2 class="text-base font-bold text-[#1A2840] flex items-center gap-2">
                        <svg class="size-4 text-[#244F73]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span>{{ __('Basic Company Information') }}</span>
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">{{ __('Update your company branding, primary workplace location, and public website.') }}</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Company Name --}}
                    <div>
                        <label for="company_name" class="block text-xs font-bold text-gray-700 mb-1">
                            {{ __('Company Name') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="company_name"
                               wire:model="company_name"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-xs text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#244F73] @error('company_name') border-red-500 @enderror"
                               required>
                        @error('company_name') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Industry --}}
                    <div>
                        <label for="industry" class="block text-xs font-bold text-gray-700 mb-1">
                            {{ __('Industry Sector') }}
                        </label>
                        <select id="industry"
                                wire:model="industry"
                                class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-xs text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#244F73]">
                            <option value="">{{ __('Select Industry Sector...') }}</option>
                            <option value="Information Technology & Software">Information Technology & Software</option>
                            <option value="BPO & Customer Experience">BPO & Customer Experience</option>
                            <option value="Healthcare & Medical Services">Healthcare & Medical Services</option>
                            <option value="Banking, Financial Services & FinTech">Banking, Financial Services & FinTech</option>
                            <option value="Education & Academic Institutions">Education & Academic Institutions</option>
                            <option value="Manufacturing & Logistics">Manufacturing & Logistics</option>
                            <option value="Retail, E-Commerce & Hospitality">Retail, E-Commerce & Hospitality</option>
                            <option value="Government & Non-Profit Services">Government & Non-Profit Services</option>
                            <option value="Creative, Media & Design">Creative, Media & Design</option>
                            <option value="Other">Other</option>
                        </select>
                        @error('industry') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Primary Location --}}
                    <div>
                        <label for="location" class="block text-xs font-bold text-gray-700 mb-1">
                            {{ __('Primary Workplace Location') }}
                        </label>
                        <input type="text"
                               id="location"
                               wire:model="location"
                               placeholder="e.g. Bonifacio Global City, Taguig / Remote Philippines"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-xs text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#244F73]">
                        @error('location') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Company Website --}}
                    <div>
                        <label for="website" class="block text-xs font-bold text-gray-700 mb-1">
                            {{ __('Company Website URL') }}
                        </label>
                        <input type="url"
                               id="website"
                               wire:model="website"
                               placeholder="https://example.com"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-xs text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#244F73]">
                        @error('website') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- About the Company & Culture --}}
                <div>
                    <label for="about" class="block text-xs font-bold text-gray-700 mb-1">
                        {{ __('About the Company & Inclusive Culture') }}
                    </label>
                    <textarea id="about"
                              wire:model="about"
                              rows="4"
                              placeholder="Describe your organization's mission, team culture, and proactive policies supporting employees with disabilities in the Philippines..."
                              class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-xs text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#244F73]"></textarea>
                    <p class="text-[11px] text-gray-500 mt-1">{{ __('This description is prominently presented on your public page to candidates evaluating your accessibility credentials.') }}</p>
                    @error('about') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </section>

            {{-- Accessibility Highlights Builder --}}
            <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-2xs space-y-4">
                <div class="pb-3 border-b border-gray-100">
                    <h2 class="text-base font-bold text-[#1A2840] flex items-center gap-2">
                        <svg class="size-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>{{ __('Key Accessibility Highlights') }}</span>
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">{{ __('Add short bullet highlights (e.g., "Full Wheelchair Ramp Access", "FSL Sign Language Interpreter On-Call", "Ergonomic Adjustable Desks").') }}</p>
                </div>

                {{-- Add new highlight --}}
                <div class="flex items-center gap-2">
                    <input type="text"
                           wire:model="newHighlight"
                           wire:keydown.enter.prevent="addHighlight"
                           placeholder="Type an accessibility highlight and press Add..."
                           class="flex-1 px-3.5 py-2 rounded-lg border border-gray-300 text-xs text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#244F73]">
                    <button type="button"
                            wire:click="addHighlight"
                            class="px-4 py-2 rounded-lg bg-[#244F73] text-white text-xs font-semibold hover:bg-[#1A2840] transition-colors">
                        + {{ __('Add') }}
                    </button>
                </div>

                {{-- Pills display --}}
                <div class="flex flex-wrap gap-2 pt-1">
                    @forelse($accessibility_highlights as $index => $h)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-[#E8F5E9] text-[#176B47] border border-[#A5D6A7]">
                            <span>{{ $h }}</span>
                            <button type="button" wire:click="removeHighlight({{ $index }})" class="size-4 rounded-full hover:bg-emerald-200 text-emerald-800 flex items-center justify-center font-bold text-xs" title="Remove">&times;</button>
                        </span>
                    @empty
                        <span class="text-xs text-gray-400 italic">{{ __('No highlights added yet. Type above and click Add.') }}</span>
                    @endforelse
                </div>
            </section>

            {{-- Workplace Accessibility Matrix Checkboxes --}}
            <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-2xs space-y-5">
                <div class="pb-3 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h2 class="text-base font-bold text-[#1A2840] flex items-center gap-2">
                            <svg class="size-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>{{ __('Workplace Accessibility Matrix Certifications') }}</span>
                        </h2>
                        <p class="text-xs text-gray-500 mt-0.5">{{ __('Select all workplace features and accommodations provided at your office or remote operations.') }}</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-[#DCEAF4] text-[#1A2840] shrink-0">
                        {{ count($selectedAccommodations) }} {{ __('Selected') }}
                    </span>
                </div>

                @php
                    $categoryLabels = [
                        'mobility' => ['title' => 'Mobility & Physical Access', 'desc' => 'Step-free routes, accessible restrooms, elevators, ramps'],
                        'visual' => ['title' => 'Visual Accommodations', 'desc' => 'Screen reader software, high contrast, tactile signs, braille'],
                        'hearing' => ['title' => 'Hearing & Sign Language', 'desc' => 'FSL interpreters, live captioning, visual alarms'],
                        'cognitive' => ['title' => 'Cognitive & Neurodiversity Support', 'desc' => 'Quiet focus rooms, plain-text tasks, sensory lighting'],
                        'flexible_work' => ['title' => 'Flexible Work & Rest Schedules', 'desc' => 'WFH options, scheduled medical breaks, flexible pacing'],
                        'speech' => ['title' => 'Speech & Alternative Communication', 'desc' => 'Written chat alternatives, non-verbal collaboration tools'],
                    ];
                @endphp

                <div class="space-y-5">
                    @foreach($categoryLabels as $catKey => $catInfo)
                        @php $catItems = $allAccommodationsByCategory->get($catKey, collect()); @endphp
                        @if($catItems->isNotEmpty())
                            <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider">{{ $catInfo['title'] }}</h3>
                                        <p class="text-[11px] text-gray-500">{{ $catInfo['desc'] }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5 pt-1">
                                    @foreach($catItems as $item)
                                        @php $checked = in_array($item->id, $selectedAccommodations); @endphp
                                        <label class="flex items-start gap-3 p-3 rounded-lg border transition-all cursor-pointer {{ $checked ? 'bg-[#F0FDF4] border-emerald-300 shadow-2xs' : 'bg-white border-gray-200 hover:border-gray-300' }}">
                                            <input type="checkbox"
                                                   value="{{ $item->id }}"
                                                   wire:model="selectedAccommodations"
                                                   class="mt-0.5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 size-4">
                                            <div class="min-w-0">
                                                <div class="text-xs font-bold text-gray-900 {{ $checked ? 'text-emerald-950' : '' }}">
                                                    {{ $item->name }}
                                                </div>
                                                @if($item->description)
                                                    <div class="text-[11px] text-gray-500 leading-snug mt-0.5">
                                                        {{ $item->description }}
                                                    </div>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </section>

            {{-- Sticky Save Action Footer --}}
            <div class="sticky bottom-4 z-20 rounded-xl bg-white/95 backdrop-blur border border-gray-200 p-4 shadow-lg flex items-center justify-between gap-4">
                <div class="text-xs text-gray-500">
                    <span>{{ __('Save your updates to refresh candidate match scoring and your public profile.') }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button"
                            wire:click="setTab('overview')"
                            class="px-4 py-2 rounded-xl border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 px-6 py-2 rounded-xl bg-[#244F73] text-white text-xs font-bold hover:bg-[#1A2840] shadow-xs transition-colors disabled:opacity-50">
                        <span wire:loading.remove>{{ __('Save Profile & Matrix') }}</span>
                        <span wire:loading class="inline-flex items-center gap-1.5">
                            <svg class="animate-spin size-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>{{ __('Saving...') }}</span>
                        </span>
                    </button>
                </div>
            </div>
        </form>
    @endif
</div>
