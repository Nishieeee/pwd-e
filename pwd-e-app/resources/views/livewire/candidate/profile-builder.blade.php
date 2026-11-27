<div id="profile-builder-top"
     x-data="{
         scrollToTop() {
             this.$nextTick(() => {
                 const scrollable = document.querySelector('.lg\\:overflow-y-auto') || document.querySelector('.overflow-y-auto') || document.querySelector('main');
                 if (scrollable) {
                     scrollable.scrollTo({ top: 0, behavior: 'smooth' });
                 }
                 window.scrollTo(0, 0);
             });
         }
     }"
     x-init="
         $wire.on('profile-step-changed', () => scrollToTop());
     "
     class="space-y-8 max-w-6xl mx-auto pb-12 w-full min-w-0">
    @if(!$isEmbedded)
        <!-- Page Header Banner Card -->
        <div class="w-full relative overflow-hidden rounded-2xl border border-primary-border bg-white p-5 sm:p-6 shadow-sm" style="background: linear-gradient(135deg, #DCEAF4 0%, #FFFFFF 100%); box-shadow: 0 4px 20px rgba(26,40,64,0.06);">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="space-y-2 max-w-3xl min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <nav class="inline-flex items-center gap-1.5 rounded-full border border-primary-border bg-white px-3 py-1 text-xs font-semibold text-body-muted shadow-2xs" aria-label="Breadcrumb">
                            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors rounded focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary">Dashboard</a>
                            <span aria-hidden="true">/</span>
                            <span class="text-primary-dark font-bold" aria-current="page">Profile Builder</span>
                        </nav>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-primary-tint text-primary-dark border border-primary-border/70">
                            Private draft
                        </span>
                    </div>
                    <h1 class="font-heading text-2xl sm:text-3xl font-bold text-primary-dark tracking-tight">Profile Builder</h1>
                    <p class="text-xs sm:text-sm text-body-muted leading-relaxed max-w-2xl">
                        @if($currentStep === 1)
                            Your experience, in your own words. Speak or type an answer, then review the text that becomes your employment profile.
                        @elseif($currentStep === 2)
                            Tell us about your background. All experience counts, including projects, internships, and volunteer work.
                        @elseif($currentStep === 3)
                            Let employers know what helps you do your best work. Choose the support you need, not a diagnosis.
                        @elseif($currentStep === 4)
                            Bring your story to life. Add your resume and a Filipino Sign Language introduction to complete your profile.
                        @else
                            Take one last look, {{ $firstName ?: 'Maria' }}. This is the profile we'll use to find work that fits your skills and support needs.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- 5-Step Progress Indicator Card -->
    <section class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-7 shadow-xs space-y-5" aria-label="Profile Builder Progress">
        <div class="flex flex-wrap items-center justify-between gap-3 text-xs sm:text-sm">
            <span class="font-bold text-[#1A2840]">
                @if($currentStep === 1)
                    Step 1 of 5 &middot; Input mode selection
                @elseif($currentStep === 2)
                    Step 2 of 5 &middot; Professional details
                @elseif($currentStep === 3)
                    Step 3 of 5 &middot; Accommodation checklist
                @elseif($currentStep === 4)
                    Step 4 of 5 &middot; Resume &amp; FSL introduction
                @else
                    Step 5 of 5 &middot; Review &amp; save
                @endif
            </span>
            <span class="text-xs text-gray-500">Your draft is saved &middot; Take your time</span>
        </div>

        @php
            $steps = [
                1 => 'Input mode',
                2 => 'Professional details',
                3 => 'Accommodations',
                4 => 'Attachments',
                5 => 'Review & save',
            ];
        @endphp

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4" role="list">
            @foreach($steps as $stepNumber => $stepLabel)
                @php
                    $isCompleted = $stepNumber < $currentStep;
                    $isCurrent = $stepNumber === $currentStep;
                @endphp
                <button type="button"
                        wire:click="goToStep({{ $stepNumber }})"
                        role="listitem"
                        aria-current="{{ $isCurrent ? 'step' : 'false' }}"
                        class="text-left group cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-2 rounded-xl p-2 hover:bg-gray-50 transition-colors min-w-0">
                    <div class="h-1.5 w-full rounded-full mb-3 {{ ($isCompleted || $isCurrent) ? 'bg-[#244F73]' : 'bg-gray-200' }}"></div>
                    <div class="flex items-center gap-2.5 min-w-0">
                        @if($isCompleted)
                            <span class="w-6 h-6 rounded-full border border-[#244F73] text-[#244F73] bg-sky-50 flex items-center justify-center text-[11px] font-bold shrink-0">
                                &#10003;
                            </span>
                        @elseif($isCurrent)
                            <span class="w-6 h-6 rounded-full bg-[#244F73] text-white flex items-center justify-center text-[11px] font-bold shrink-0">
                                {{ $stepNumber }}
                            </span>
                        @else
                            <span class="w-6 h-6 rounded-full border border-gray-300 text-gray-500 flex items-center justify-center text-[11px] font-semibold shrink-0">
                                {{ $stepNumber }}
                            </span>
                        @endif
                        <span class="text-xs sm:text-[13px] truncate min-w-0 {{ $isCurrent ? 'font-bold text-[#1A2840]' : ($isCompleted ? 'font-medium text-[#1A2840]' : 'text-gray-500 group-hover:text-gray-700') }}">
                            {{ $stepLabel }}
                        </span>
                    </div>
                </button>
            @endforeach
        </div>
    </section>

    {{-- ===================================================================== --}}
    {{-- STEP 1: INPUT MODE SELECTION                                          --}}
    {{-- ===================================================================== --}}
    @if($currentStep === 1)
        <section class="bg-white rounded-xl border border-gray-200 p-6 shadow-xs space-y-6" aria-labelledby="step1-heading">
            <div>
                <h2 id="step1-heading" class="text-lg font-bold text-[#1A2840]">How would you like to build your profile?</h2>
                <p class="text-xs text-[#4E5F6E] mt-1">
                    Choose what feels comfortable. You can switch between speaking and typing at any time.
                </p>
            </div>

            <!-- 3 Profile Building Mode Selection Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4" role="radiogroup" aria-label="Profile input mode">
                <!-- Standard Typing Card -->
                <button type="button"
                        wire:click="selectInputMode('standard')"
                        role="radio"
                        aria-checked="{{ $inputMode === 'standard' ? 'true' : 'false' }}"
                        class="text-left rounded-xl p-5 transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#244F73] {{ $inputMode === 'standard' ? 'border-2 border-[#244F73] bg-[#EBF3FA]/60 shadow-sm' : 'border border-gray-200 bg-white hover:border-[#244F73]/50' }}">
                    <div class="flex items-start justify-between gap-3">
                        <div class="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-[#244F73] shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="3" y="5" width="18" height="14" rx="2" stroke-width="2"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 9h2m3 0h2m3 0h1M7 13h10"/>
                            </svg>
                        </div>
                        @if($inputMode === 'standard')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#DCEAF4] text-[#244F73]">
                                &#10003; Selected
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#DCEAF4]/70 text-[#244F73]">
                                Available
                            </span>
                        @endif
                    </div>
                    <h3 class="text-base font-bold text-[#1A2840] mt-4">Standard Typing</h3>
                    <p class="text-xs text-[#4E5F6E] mt-1.5 leading-relaxed">
                        Use your keyboard to enter and edit each section at your own pace.
                    </p>
                </button>

                <!-- Voice Dictation Card -->
                <button type="button"
                        wire:click="selectInputMode('voice')"
                        role="radio"
                        aria-checked="{{ $inputMode === 'voice' ? 'true' : 'false' }}"
                        class="text-left rounded-xl p-5 transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#244F73] {{ $inputMode === 'voice' ? 'border-2 border-[#244F73] bg-[#EBF3FA]/60 shadow-sm' : 'border border-gray-200 bg-white hover:border-[#244F73]/50' }}">
                    <div class="flex items-start justify-between gap-3">
                        <div class="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-[#244F73] shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                        </div>
                        @if($inputMode === 'voice')
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-[#244F73]">
                                &#10003; Selected
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#DCEAF4]/70 text-[#244F73]">
                                Available
                            </span>
                        @endif
                    </div>
                    <h3 class="text-base font-bold text-[#1A2840] mt-4">Voice Dictation</h3>
                    <p class="text-xs text-[#4E5F6E] mt-1.5 leading-relaxed">
                        Speak your answers in Tagalog or English and watch them become editable text.
                    </p>
                </button>

                <!-- 3. Resume Scan & Auto-Fill Card -->
                <div class="rounded-xl border border-gray-200 bg-white hover:border-[#244F73]/50 p-5 shadow-xs transition-all h-full flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between gap-3">
                            <div class="w-10 h-10 rounded-lg bg-[#244F73] text-white flex items-center justify-center shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#DCEAF4] text-[#244F73]">
                                ⚡ Fastest
                            </span>
                        </div>
                        <h3 class="text-base font-bold text-[#1A2840] mt-4">Resume Scan &amp; Auto-Fill</h3>
                        <p class="text-xs text-[#4E5F6E] mt-1.5 leading-relaxed">
                            Upload your resume (PDF, DOCX, TXT) to auto-build your profile, review the output, and save.
                        </p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-100 space-y-2">
                        <label for="resume-scan-input"
                               class="flex items-center justify-center gap-2 w-full py-2 px-3 rounded-lg bg-[#244F73] text-white text-xs font-bold hover:bg-[#1A2840] cursor-pointer transition shadow-2xs focus-within:ring-2 focus-within:ring-[#244F73]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            <span>Upload Resume File</span>
                            <input id="resume-scan-input"
                                   type="file"
                                   wire:model="resumeScanUpload"
                                   accept=".pdf,.docx,.txt"
                                   class="hidden">
                        </label>
                        <button type="button"
                                wire:click="loadSampleResume"
                                class="w-full text-center py-1 text-[11px] font-semibold text-[#244F73] hover:underline cursor-pointer">
                            ⚡ Or try sample resume
                        </button>
                    </div>
                </div>
            </div>

            {{-- Livewire Uploading & Scanning Indicator --}}
            <div wire:loading wire:target="resumeScanUpload" class="pt-2">
                <div class="flex items-center gap-3 p-4 rounded-xl bg-[#DCEAF4] text-[#244F73] text-xs font-semibold animate-pulse border border-[#244F73]/20 shadow-xs">
                    <svg class="w-5 h-5 animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <div class="min-w-0">
                        <div class="font-bold text-sm">Scanning &amp; Parsing Resume...</div>
                        <div class="text-[11px] opacity-80">Extracting contact info, skills, education, and work experience into your profile...</div>
                    </div>
                </div>
            </div>

            @error('resumeScanUpload')
                <div class="text-xs text-red-600 font-semibold flex items-center gap-1.5 pt-1">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span>{{ $message }}</span>
                </div>
            @enderror

            @if($inputMode === 'standard')
                <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 text-xs font-bold text-[#244F73]">
                            <span>Standard typing mode selected</span>
                        </div>
                        <p class="text-xs text-[#4E5F6E]">Enter your profile details section by section with full manual control.</p>
                    </div>
                    <button type="button"
                            wire:click="goToStep(2)"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-[#244F73] text-white text-xs font-bold hover:bg-[#1A2840] transition shadow-xs focus:ring-2 focus:ring-[#244F73] shrink-0">
                        <span>Continue to Professional Details</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            @endif

            <!-- Speech-to-Text Status & Listening Widget -->
            <div class="pt-4 border-t border-gray-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2 text-xs font-bold text-[#176B47]">
                        <svg class="w-4 h-4 text-[#176B47]" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>Speech input active &amp; ready</span>
                    </div>
                    <p class="text-xs text-[#4E5F6E]">
                        Try saying &ldquo;My name is {{ $firstName }} {{ $lastName }}.&rdquo; Pause whenever you need a moment.
                    </p>
                    <div class="flex flex-wrap items-center gap-3 pt-1">
                        <button type="button"
                                wire:click="goToStep(2)"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-[#244F73] text-white text-xs font-bold hover:bg-[#1A2840] transition shadow-xs focus:ring-2 focus:ring-[#244F73] shrink-0">
                            <span>Continue to Professional Details</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        <button type="button"
                                wire:click="selectInputMode('standard')"
                                class="text-xs font-bold text-[#244F73] hover:underline focus:outline-none focus:ring-2 focus:ring-[#244F73] rounded">
                            Prefer your keyboard? Switch to Standard Typing
                        </button>
                    </div>
                </div>

                <!-- Listening Pill Box with WCAG 44px Touch Targets -->
                <div class="rounded-xl border border-[#244F73]/40 bg-white px-4 py-3 sm:px-5 sm:py-3.5 shadow-xs flex flex-wrap sm:flex-nowrap items-center justify-between gap-4 w-full sm:w-auto min-w-0 max-w-full">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-full bg-[#244F73] text-white flex items-center justify-center ring-4 ring-[#DCEAF4] shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-[#244F73]" aria-live="polite">
                                {{ $isListening ? 'Listening...' : 'Dictation Paused' }}
                            </div>
                            <div class="flex items-center gap-0.5 my-1" aria-hidden="true">
                                <span class="w-1 h-2.5 bg-[#244F73] rounded-full {{ $isListening ? 'animate-pulse' : 'opacity-40' }}"></span>
                                <span class="w-1 h-4 bg-[#244F73] rounded-full {{ $isListening ? 'animate-pulse' : 'opacity-40' }}"></span>
                                <span class="w-1 h-2 bg-[#244F73] rounded-full {{ $isListening ? 'animate-pulse' : 'opacity-40' }}"></span>
                                <span class="w-1 h-5 bg-[#244F73] rounded-full {{ $isListening ? 'animate-pulse' : 'opacity-40' }}"></span>
                                <span class="w-1 h-3 bg-[#244F73] rounded-full {{ $isListening ? 'animate-pulse' : 'opacity-40' }}"></span>
                                <span class="w-1 h-4 bg-[#244F73] rounded-full {{ $isListening ? 'animate-pulse' : 'opacity-40' }}"></span>
                                <span class="w-1 h-2 bg-[#244F73] rounded-full {{ $isListening ? 'animate-pulse' : 'opacity-40' }}"></span>
                                <span class="w-1 h-3.5 bg-[#244F73] rounded-full {{ $isListening ? 'animate-pulse' : 'opacity-40' }}"></span>
                            </div>
                            <div class="text-[11px] text-gray-500">Speak naturally &middot; Tagalog / English</div>
                        </div>
                    </div>

                    <button type="button"
                            wire:click="toggleListening"
                            :aria-pressed="{{ $isListening ? 'true' : 'false' }}"
                            aria-label="{{ $isListening ? 'Pause voice dictation' : 'Resume voice dictation' }}"
                            class="min-w-[44px] min-h-[44px] w-11 h-11 rounded-full border border-gray-300 flex items-center justify-center text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:ring-2 focus:ring-[#244F73] transition-colors cursor-pointer">
                        @if($isListening)
                            <svg class="w-4 h-4 text-[#244F73]" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        @else
                            <svg class="w-4 h-4 text-[#244F73]" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </button>
                </div>
            </div>
        </section>

        <!-- Bottom Info Cards Row -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            <div class="lg:col-span-8 bg-white rounded-xl border border-gray-200 p-6 shadow-xs">
                <h3 class="text-base font-bold text-[#1A2840]">A few simple steps</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                    <div>
                        <div class="text-xs font-bold text-[#244F73]">01</div>
                        <div class="text-xs font-bold text-[#1A2840] mt-1">Tell your story</div>
                        <div class="text-[11px] text-gray-500 mt-0.5">Experience &amp; education</div>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-[#244F73]">02</div>
                        <div class="text-xs font-bold text-[#1A2840] mt-1">Share your needs</div>
                        <div class="text-[11px] text-gray-500 mt-0.5">Workplace accommodations</div>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-[#244F73]">03</div>
                        <div class="text-xs font-bold text-[#1A2840] mt-1">Make it yours</div>
                        <div class="text-[11px] text-gray-500 mt-0.5">Attachments &amp; review</div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-4 rounded-xl bg-[#EBF3FA] border border-[#B8D0E2] p-5 space-y-2">
                <div class="flex items-center gap-2 text-xs font-bold text-[#1A2840]">
                    <svg class="w-4 h-4 text-[#244F73] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span>You have the final say</span>
                </div>
                <p class="text-xs text-[#4E5F6E] leading-relaxed">
                    Your voice only creates text. Audio is never shared with employers. Review and edit every section before saving your profile.
                </p>
            </div>
        </div>
    @endif

    {{-- ===================================================================== --}}
    {{-- STEP 2: PROFESSIONAL DETAILS                                          --}}
    {{-- ===================================================================== --}}
    @if($currentStep === 2)
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-start w-full min-w-0">
            <!-- Left Form Column -->
            <section class="xl:col-span-8 bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 lg:p-10 shadow-xs space-y-10 min-w-0 w-full" aria-labelledby="step2-heading">
                <div class="space-y-5">
                    <div class="space-y-2">
                        <h2 id="step2-heading" class="text-xl font-bold text-[#1A2840] tracking-tight">Professional details</h2>
                        <p class="text-sm text-gray-500 leading-relaxed max-w-2xl">
                            Speak through each category in order. Your spoken answers will map directly into the matching text boxes before continuing to the Accommodations Checklist.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-4 pt-2">
                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-[#DCEAF4] text-[#244F73]">
                            <span class="w-2 h-2 rounded-full bg-[#244F73]" aria-hidden="true"></span>
                            <span>{{ $inputMode === 'voice' ? 'Voice Dictation on' : 'Standard Typing on' }}</span>
                        </span>

                        <div class="flex flex-wrap items-center gap-4">
                            <label for="step2-resume-scan-input"
                                   class="inline-flex items-center gap-2 text-xs font-semibold text-[#244F73] hover:bg-[#EBF3FA]/60 cursor-pointer focus-within:outline-none focus-within:ring-2 focus-within:ring-[#244F73] focus-within:ring-offset-2 rounded-lg px-3.5 py-2 bg-white border border-[#244F73]/30 shadow-2xs transition-colors">
                                <svg class="w-4 h-4 text-[#244F73] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <span>Scan &amp; Auto-Fill from Resume</span>
                                <input id="step2-resume-scan-input"
                                       type="file"
                                       wire:model="resumeScanUpload"
                                       accept=".pdf,.docx,.txt"
                                       class="sr-only">
                            </label>

                            <button type="button"
                                    wire:click="selectInputMode('{{ $inputMode === 'voice' ? 'standard' : 'voice' }}')"
                                    class="inline-flex items-center gap-2 text-xs font-semibold text-[#244F73] hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-2 rounded-lg px-2.5 py-2 cursor-pointer transition-colors">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <rect x="3" y="5" width="18" height="14" rx="2" stroke-width="2"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 9h2m3 0h2m3 0h1M7 13h10"/>
                                </svg>
                                <span>{{ $inputMode === 'voice' ? 'Switch to typing' : 'Switch to voice dictation' }}</span>
                            </button>
                        </div>
                    </div>

                    {{-- Livewire Uploading & Scanning Indicator (Step 2 Trigger) --}}
                    <div wire:loading wire:target="resumeScanUpload" class="pt-2">
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-[#DCEAF4] text-[#244F73] text-xs font-semibold animate-pulse border border-[#244F73]/20">
                            <svg class="w-5 h-5 animate-spin shrink-0" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <span>Scanning and parsing resume... Auto-building your profile fields...</span>
                        </div>
                    </div>

                    {{-- Resume Scan Output Review Banner --}}
                    @if($resumeScanSuccess && $scanSummary)
                        <div class="rounded-2xl border-2 border-emerald-300 bg-gradient-to-br from-emerald-50 via-white to-emerald-50/50 p-6 shadow-xs space-y-4" role="status" aria-live="polite">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-sm font-bold text-emerald-950">Resume Scanned &amp; Profile Auto-Built</h3>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                Review Mode
                                            </span>
                                        </div>
                                        <p class="text-xs text-emerald-800 mt-1 leading-relaxed">
                                            Extracted details from <span class="font-semibold text-emerald-950">{{ $scanSummary['filename'] }}</span>. Review each section below, make any edits, and proceed to save.
                                        </p>
                                    </div>
                                </div>
                                <button type="button"
                                        wire:click="dismissScanSummary"
                                        class="text-emerald-700 hover:text-emerald-950 text-xs font-semibold px-2.5 py-1.5 rounded-lg hover:bg-emerald-100 transition cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-2"
                                        aria-label="Dismiss scan notification">
                                    Dismiss &#10005;
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-1 text-xs">
                                @if($scanSummary['headline'])
                                    <div class="bg-white rounded-lg p-3 border border-emerald-200/80 shadow-2xs">
                                        <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Headline</span>
                                        <span class="font-semibold text-gray-900 truncate block mt-1" title="{{ $scanSummary['headline'] }}">{{ $scanSummary['headline'] }}</span>
                                    </div>
                                @endif
                                @if(!empty($scanSummary['contact']))
                                    <div class="bg-white rounded-lg p-3 border border-emerald-200/80 shadow-2xs">
                                        <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Contact &amp; Location</span>
                                        <span class="font-medium text-gray-900 truncate block mt-1">{{ implode(' · ', $scanSummary['contact']) }}</span>
                                    </div>
                                @endif
                                @if($scanSummary['skills_count'] > 0)
                                    <div class="bg-white rounded-lg p-3 border border-emerald-200/80 shadow-2xs">
                                        <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Skills Auto-Tagged</span>
                                        <span class="font-semibold text-gray-900 block mt-1">{{ $scanSummary['skills_count'] }} skills populated</span>
                                    </div>
                                @endif
                                @if($scanSummary['education'])
                                    <div class="bg-white rounded-lg p-3 border border-emerald-200/80 shadow-2xs">
                                        <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Education</span>
                                        <span class="font-semibold text-gray-900 truncate block mt-1" title="{{ $scanSummary['education'] }}">{{ $scanSummary['education'] }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- 1. Personal Identification -->
                <div class="space-y-6 pt-8 border-t border-gray-200/80">
                    @if ($errors->any())
                        <div class="p-5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs flex items-start gap-3.5" role="alert" aria-live="assertive">
                            <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <div class="font-bold text-sm">Please complete the required details before continuing</div>
                                <p class="mt-1.5 text-red-700 leading-relaxed">Some required fields (such as your Name, Phone Number, or City) need your attention.</p>
                            </div>
                        </div>
                    @endif

                    <h3 class="text-base font-bold text-[#1A2840]">Personal Identification</h3>

                    <!-- 2x2 Photo / Avatar Upload Field for Digital Bio-Data -->
                    <div class="p-6 rounded-2xl bg-white border border-gray-200 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                        <div class="flex items-center gap-5">
                            <div class="relative w-20 h-20 sm:w-24 sm:h-24 rounded-xl bg-primary-tint/40 text-primary border-2 border-primary-border flex items-center justify-center font-bold text-lg overflow-hidden shrink-0 shadow-2xs">
                                <div wire:loading wire:target="avatarUpload" class="absolute inset-0 bg-black/50 flex items-center justify-center z-10">
                                    <svg class="size-6 animate-spin text-white" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                </div>
                                @if($avatarPath)
                                    <img src="{{ Storage::url($avatarPath) }}" alt="Saved 2x2 Photo" class="w-full h-full object-cover">
                                @elseif($avatarUpload)
                                    <img src="{{ $avatarUpload->temporaryUrl() }}" alt="2x2 Photo Preview" class="w-full h-full object-cover">
                                @else
                                    <div class="flex flex-col items-center justify-center text-center p-1">
                                        <span class="font-heading font-bold text-xl text-primary">{{ strtoupper(substr($firstName, 0, 1).substr($lastName, 0, 1)) ?: 'MS' }}</span>
                                        <span class="text-[9px] uppercase font-bold text-primary tracking-wider mt-0.5">2x2 Photo</span>
                                    </div>
                                @endif
                            </div>
                            <div class="space-y-1.5">
                                <span class="block text-sm font-bold text-primary-dark">
                                    2x2 Formal ID Photo <span class="font-normal text-xs text-gray-500">(Recommended for Profile)</span>
                                </span>
                                <p class="text-xs sm:text-[13px] text-gray-500 leading-relaxed max-w-md">
                                    Featured on your official Digital Profile. A clear 2x2 photo with a white or plain background is recommended (JPG/PNG, up to 5 MB).
                                </p>
                                @error('avatarUpload')
                                    <p class="text-xs text-red-600 mt-1 flex items-center gap-1.5" role="alert">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                        <span>{{ $message }}</span>
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 shrink-0">
                            <label for="avatar-upload-input" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-primary-dark hover:bg-gray-50 cursor-pointer shadow-2xs transition-colors focus-within:outline-none focus-within:ring-2 focus-within:ring-[#244F73] focus-within:ring-offset-2">
                                <svg class="size-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                <span>{{ ($avatarUpload || $avatarPath) ? __('Change 2x2 Photo') : __('Upload 2x2 Photo') }}</span>
                                <input id="avatar-upload-input"
                                       type="file"
                                       wire:model="avatarUpload"
                                       accept="image/png,image/jpeg,image/webp"
                                       class="hidden"
                                       aria-label="Upload 2x2 ID photo or avatar">
                            </label>

                            @if($avatarUpload || $avatarPath)
                                <button type="button"
                                        wire:click="removeAvatar"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-lg border border-red-200 text-xs font-semibold text-red-700 hover:bg-red-50 transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2"
                                        aria-label="Remove uploaded 2x2 photo">
                                    <svg class="size-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Remove</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Balanced 2x2 Name Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 min-w-0">
                        <div class="min-w-0">
                            <label for="first-name" class="block text-xs sm:text-[13px] font-semibold text-[#1A2840] mb-2">
                                First Name <span class="text-red-600" aria-hidden="true">*</span>
                            </label>
                            <input id="first-name"
                                   type="text"
                                   wire:model.live="firstName"
                                   aria-required="true"
                                   aria-invalid="{{ $errors->has('firstName') ? 'true' : 'false' }}"
                                   @if($errors->has('firstName')) aria-describedby="first-name-error" @endif
                                   class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('firstName') ? 'border-red-600 focus:ring-red-600 focus:border-red-600' : 'border-gray-300 hover:border-gray-400 focus:ring-[#244F73] focus:border-[#244F73]' }} text-sm text-gray-900 bg-white transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1">
                            @error('firstName')
                                <p id="first-name-error" class="text-xs text-red-600 mt-1.5 flex items-center gap-1.5" role="alert">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                        <div class="min-w-0">
                            <label for="middle-name" class="block text-xs sm:text-[13px] font-semibold text-[#1A2840] mb-2">Middle Name</label>
                            <input id="middle-name"
                                   type="text"
                                   wire:model.live="middleName"
                                   placeholder="Optional"
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 hover:border-gray-400 bg-white text-sm text-gray-900 placeholder-gray-400 transition-colors focus:outline-none focus:ring-2 focus:ring-[#244F73] focus:ring-offset-1 focus:border-[#244F73]">
                        </div>
                        <div class="min-w-0">
                            <label for="last-name" class="block text-xs sm:text-[13px] font-semibold text-[#1A2840] mb-2">
                                Last Name <span class="text-red-600" aria-hidden="true">*</span>
                            </label>
                            <input id="last-name"
                                   type="text"
                                   wire:model.live="lastName"
                                   aria-required="true"
                                   aria-invalid="{{ $errors->has('lastName') ? 'true' : 'false' }}"
                                   @if($errors->has('lastName')) aria-describedby="last-name-error" @endif
                                   class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('lastName') ? 'border-red-600 focus:ring-red-600 focus:border-red-600' : 'border-gray-300 hover:border-gray-400 focus:ring-[#244F73] focus:border-[#244F73]' }} text-sm text-gray-900 bg-white transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1">
                            @error('lastName')
                                <p id="last-name-error" class="text-xs text-red-600 mt-1.5 flex items-center gap-1.5" role="alert">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                        <div class="min-w-0">
                            <label for="name-suffix" class="block text-xs sm:text-[13px] font-semibold text-[#1A2840] mb-2">Suffix</label>
                            <input id="name-suffix"
                                   type="text"
                                   wire:model.live="suffix"
                                   placeholder="Optional"
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 hover:border-gray-400 bg-white text-sm text-gray-900 placeholder-gray-400 transition-colors focus:outline-none focus:ring-2 focus:ring-[#244F73] focus:ring-offset-1 focus:border-[#244F73]">
                        </div>
                    </div>

                    <!-- Email, Phone, PWD ID Number (Auto-filled & Read-only) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5 min-w-0 pt-1">
                        <div class="min-w-0">
                            <label for="email-address" class="block text-xs sm:text-[13px] font-semibold text-[#1A2840] mb-2">
                                Email Address <span class="text-red-600" aria-hidden="true">*</span>
                            </label>
                            <input id="email-address"
                                   type="email"
                                   wire:model.live="email"
                                   aria-required="true"
                                   aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                                   @if($errors->has('email')) aria-describedby="email-error" @endif
                                   class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('email') ? 'border-red-600 focus:ring-red-600 focus:border-red-600' : 'border-gray-300 hover:border-gray-400 focus:ring-[#244F73] focus:border-[#244F73]' }} text-sm text-gray-900 bg-white transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1">
                            @error('email')
                                <p id="email-error" class="text-xs text-red-600 mt-1.5 flex items-center gap-1.5" role="alert">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                        <div class="min-w-0">
                            <label for="phone-number" class="block text-xs sm:text-[13px] font-semibold text-[#1A2840] mb-2">
                                Phone Number <span class="text-red-600" aria-hidden="true">*</span>
                            </label>
                            <input id="phone-number"
                                   type="text"
                                   wire:model.live="phone"
                                   placeholder="e.g., +63 917 000 0000"
                                   aria-required="true"
                                   aria-invalid="{{ $errors->has('phone') ? 'true' : 'false' }}"
                                   @if($errors->has('phone')) aria-describedby="phone-error" @endif
                                   class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('phone') ? 'border-red-600 focus:ring-red-600 focus:border-red-600' : 'border-gray-300 hover:border-gray-400 focus:ring-[#244F73] focus:border-[#244F73]' }} text-sm text-gray-900 placeholder-gray-400 bg-white transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1">
                            @error('phone')
                                <p id="phone-error" class="text-xs text-red-600 mt-1.5 flex items-center gap-1.5" role="alert">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                        <div class="sm:col-span-2 xl:col-span-1 min-w-0">
                            <label for="pwd-id-number" class="block text-xs sm:text-[13px] font-semibold text-[#1A2840] mb-2">PWD ID Number</label>
                            <input id="pwd-id-number"
                                   type="text"
                                   value="{{ $pwdIdNumber }}"
                                   placeholder="e.g., PWD-0931-7000-1234"
                                   readonly
                                   aria-readonly="true"
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 bg-gray-100 text-sm text-gray-700 placeholder-gray-400 font-medium cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-[#244F73] focus:ring-offset-1">
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 flex items-center gap-2 leading-relaxed">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>PWD ID is automatically linked from your verified account registration record and is read-only.</span>
                    </p>

                    <!-- Multiple Disabilities Selection -->
                    <fieldset class="pt-2 space-y-2.5">
                        <legend class="text-xs sm:text-[13px] font-semibold text-[#1A2840]">
                            Disability Category / Categories <span class="font-normal text-gray-500">(Select all that apply)</span>
                        </legend>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                            @foreach(['Visual', 'Hearing', 'Motor/Physical', 'Speech', 'Cerebral Palsy', 'Psychosocial', 'Intellectual', 'Chronic / Orthopedic'] as $disOpt)
                                <label class="inline-flex items-center gap-2.5 px-3 py-2 rounded-lg border text-xs font-medium cursor-pointer transition-colors {{ in_array($disOpt, $disabilityTypes, true) ? 'border-[#244F73] bg-[#EBF3FA] text-[#1A2840] font-bold' : 'border-gray-300 bg-white text-gray-700 hover:border-gray-400' }}">
                                    <input type="checkbox"
                                           value="{{ $disOpt }}"
                                           wire:model.live="disabilityTypes"
                                           class="w-4 h-4 rounded border-gray-300 text-[#244F73] focus:ring-[#244F73] shrink-0">
                                    <span class="leading-tight">{{ $disOpt }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <!-- Complete Address & Searchable PSGC Location Comboboxes -->
                    <div class="space-y-4 pt-2">
                        <!-- Row 1: Complete Street / House / Lot / Purok Address -->
                        <div class="min-w-0">
                            <label for="street-address-input" class="block text-xs sm:text-[13px] font-semibold text-[#1A2840] mb-2">
                                House / Unit No., Street, Subdivision / Purok <span class="font-normal text-gray-500">(Complete Address)</span>
                            </label>
                            <div class="relative">
                                <input id="street-address-input"
                                       type="text"
                                       wire:model.live="address"
                                       placeholder="e.g., Blk 4 Lot 12, Veterans Ave., Purok 3"
                                       aria-label="Complete street or house address"
                                       class="h-11 w-full px-3.5 pr-9 rounded-lg border border-gray-300 hover:border-gray-400 bg-white text-sm text-gray-900 placeholder-gray-400 transition-colors focus:outline-none focus:ring-2 focus:ring-[#244F73] focus:ring-offset-1 focus:border-[#244F73]">
                            </div>
                            @error('address')
                                <p class="text-xs text-red-600 mt-1.5" role="alert">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Row 2: Country, Province, City / Municipality -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 items-start min-w-0">
                            <!-- Country Searchable Combobox -->
                            <div class="relative min-w-0" x-data="{ openCountry: false }">
                                <label for="country-combobox" class="block text-xs sm:text-[13px] font-semibold text-[#1A2840] mb-2">Country</label>
                                <div class="relative">
                                    <input id="country-combobox"
                                           type="text"
                                           wire:model.live="country"
                                           @focus="openCountry = true"
                                           @click.away="openCountry = false"
                                           @keydown.escape="openCountry = false"
                                           role="combobox"
                                           :aria-expanded="openCountry.toString()"
                                           aria-autocomplete="list"
                                           aria-label="Country"
                                           placeholder="Select country"
                                           class="h-11 w-full pl-3.5 pr-8 rounded-lg border border-gray-300 hover:border-gray-400 bg-white text-sm text-gray-900 placeholder-gray-400 transition-colors focus:outline-none focus:ring-2 focus:ring-[#244F73] focus:ring-offset-1 focus:border-[#244F73]">
                                    <button type="button"
                                            @click="openCountry = !openCountry"
                                            tabindex="-1"
                                            aria-label="Toggle country list"
                                            class="absolute inset-y-0 right-0 px-2.5 flex items-center text-gray-400 hover:text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                </div>
                                <ul x-show="openCountry"
                                    x-cloak
                                    role="listbox"
                                    aria-label="Countries"
                                    class="absolute left-0 right-0 w-full mt-1.5 max-h-48 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-xl z-50 py-1 text-xs">
                                    @foreach($countries as $countryName)
                                        <li>
                                            <button type="button"
                                                    wire:click="$set('country', '{{ addslashes($countryName) }}')"
                                                    @click="openCountry = false"
                                                    class="w-full text-left px-3.5 py-2 truncate whitespace-nowrap hover:bg-[#EBF3FA] text-[#1A2840] font-medium focus-visible:outline-none focus-visible:bg-[#EBF3FA] focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-[#244F73]">
                                                {{ $countryName }}
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <!-- Province Searchable Combobox -->
                            <div class="relative min-w-0" x-data="{ openProvince: false }">
                                <label for="province-combobox" class="block text-xs sm:text-[13px] font-semibold text-[#1A2840] mb-2">Province</label>
                                <div class="relative">
                                    <input id="province-combobox"
                                           type="text"
                                           wire:model.live="province"
                                           @focus="openProvince = true"
                                           @click.away="openProvince = false"
                                           @keydown.escape="openProvince = false"
                                           role="combobox"
                                           :aria-expanded="openProvince.toString()"
                                           aria-autocomplete="list"
                                           aria-label="Province"
                                           placeholder="Select or enter province..."
                                           class="h-11 w-full pl-3.5 pr-8 rounded-lg border border-gray-300 hover:border-gray-400 bg-white text-sm text-gray-900 placeholder-gray-400 transition-colors focus:outline-none focus:ring-2 focus:ring-[#244F73] focus:ring-offset-1 focus:border-[#244F73]">
                                    <button type="button"
                                            @click="openProvince = !openProvince"
                                            tabindex="-1"
                                            aria-label="Toggle province list"
                                            class="absolute inset-y-0 right-0 px-2.5 flex items-center text-gray-400 hover:text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                </div>
                                @error('province')
                                    <p class="text-xs text-red-600 mt-1.5" role="alert">{{ $message }}</p>
                                @enderror
                                <ul x-show="openProvince"
                                    x-cloak
                                    role="listbox"
                                    aria-label="Provinces"
                                    class="absolute left-0 right-0 w-full mt-1.5 max-h-56 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-xl z-50 py-1 text-xs">
                                    @foreach($provinces as $provinceOption)
                                        <li>
                                            <button type="button"
                                                    wire:click="selectProvinceOption('{{ addslashes($provinceOption) }}')"
                                                    @click="openProvince = false"
                                                    class="w-full text-left px-3.5 py-2 truncate whitespace-nowrap hover:bg-[#EBF3FA] focus-visible:outline-none focus-visible:bg-[#EBF3FA] focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-[#244F73] {{ $province === $provinceOption ? 'bg-[#EBF3FA] font-bold text-[#244F73]' : 'text-[#1A2840]' }}">
                                                {{ $provinceOption }}
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <!-- City Searchable Combobox (Single-line clean items: "City Name (ZIP 7000)") -->
                            <div class="relative min-w-0" x-data="{ openCity: false }">
                                <label for="city-combobox" class="block text-xs sm:text-[13px] font-semibold text-[#1A2840] mb-2">
                                    City / Municipality <span class="text-red-600" aria-hidden="true">*</span>
                                </label>
                                <div class="relative">
                                    <input id="city-combobox"
                                           type="text"
                                           wire:model.live="city"
                                           @focus="openCity = true"
                                           @click.away="openCity = false"
                                           @keydown.escape="openCity = false"
                                           role="combobox"
                                           :aria-expanded="openCity.toString()"
                                           aria-autocomplete="list"
                                           aria-label="City"
                                           placeholder="Search city..."
                                           aria-required="true"
                                           aria-invalid="{{ $errors->has('city') ? 'true' : 'false' }}"
                                           class="h-11 w-full pl-3.5 pr-8 rounded-lg border {{ $errors->has('city') ? 'border-red-600 focus:ring-red-600 focus:border-red-600' : 'border-gray-300 hover:border-gray-400 focus:ring-[#244F73] focus:border-[#244F73]' }} bg-white text-sm text-gray-900 placeholder-gray-400 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1">
                                    <button type="button"
                                            @click="openCity = !openCity"
                                            tabindex="-1"
                                            aria-label="Toggle city list"
                                            class="absolute inset-y-0 right-0 px-2.5 flex items-center text-gray-400 hover:text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                </div>
                                @error('city')
                                    <p class="text-xs text-red-600 mt-1.5 flex items-center gap-1.5" role="alert">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                        <span>{{ $message }}</span>
                                    </p>
                                @enderror
                                <ul x-show="openCity"
                                    x-cloak
                                    role="listbox"
                                    aria-label="Cities"
                                    class="absolute left-0 right-0 w-full mt-1.5 max-h-56 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-xl z-50 py-1 text-xs">
                                    @foreach($cities as $cityOption)
                                        <li>
                                            <button type="button"
                                                    wire:click="selectCityOption('{{ addslashes($cityOption->name) }}')"
                                                    @click="openCity = false"
                                                    class="w-full text-left px-3.5 py-2 truncate whitespace-nowrap hover:bg-[#EBF3FA] focus-visible:outline-none focus-visible:bg-[#EBF3FA] focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-[#244F73] {{ $city === $cityOption->name ? 'bg-[#EBF3FA] font-bold text-[#244F73]' : 'text-[#1A2840]' }}">
                                                {{ $cityOption->name }} (ZIP {{ $cityOption->zip_code }})
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <!-- Row 3: Barangay + Zip Code -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 items-start min-w-0">
                            <!-- Barangay Searchable Combobox -->
                            <div class="relative sm:col-span-2 min-w-0" x-data="{ openBarangay: false }">
                                <label for="barangay-combobox" class="block text-xs sm:text-[13px] font-semibold text-[#1A2840] mb-2">Barangay</label>
                                <div class="relative">
                                    <input id="barangay-combobox"
                                           type="text"
                                           wire:model.live="barangay"
                                           @focus="openBarangay = true"
                                           @click.away="openBarangay = false"
                                           @keydown.escape="openBarangay = false"
                                           role="combobox"
                                           :aria-expanded="openBarangay.toString()"
                                           aria-autocomplete="list"
                                           aria-label="Barangay"
                                           placeholder="Search or enter barangay..."
                                           class="h-11 w-full pl-3.5 pr-8 rounded-lg border border-gray-300 hover:border-gray-400 bg-white text-sm text-gray-900 placeholder-gray-400 transition-colors focus:outline-none focus:ring-2 focus:ring-[#244F73] focus:ring-offset-1 focus:border-[#244F73]">
                                    <button type="button"
                                            @click="openBarangay = !openBarangay"
                                            tabindex="-1"
                                            aria-label="Toggle barangay list"
                                            class="absolute inset-y-0 right-0 px-2.5 flex items-center text-gray-400 hover:text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                </div>
                                <ul x-show="openBarangay"
                                    x-cloak
                                    role="listbox"
                                    aria-label="Barangays"
                                    class="absolute left-0 right-0 w-full mt-1.5 max-h-56 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-xl z-50 py-1 text-xs">
                                    @foreach($barangays as $brgyOption)
                                        <li>
                                            <button type="button"
                                                    wire:click="selectBarangayOption('{{ addslashes($brgyOption->name) }}')"
                                                    @click="openBarangay = false"
                                                    class="w-full text-left px-3.5 py-2 truncate whitespace-nowrap hover:bg-[#EBF3FA] focus-visible:outline-none focus-visible:bg-[#EBF3FA] focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-[#244F73] {{ $barangay === $brgyOption->name ? 'bg-[#EBF3FA] font-bold text-[#244F73]' : 'text-[#1A2840]' }}">
                                                {{ $brgyOption->name }}
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <!-- Zip Code (Auto-populated from selected city, editable if custom) -->
                            <div class="min-w-0">
                                <label for="zip-code-input" class="block text-xs sm:text-[13px] font-semibold text-[#1A2840] mb-2">Zip Code</label>
                                <input id="zip-code-input"
                                       type="text"
                                       wire:model.live="zipCode"
                                       placeholder="e.g., 7000"
                                       aria-label="Zip Code"
                                       class="h-11 w-full px-3.5 rounded-lg border border-gray-300 hover:border-gray-400 bg-white text-sm text-gray-900 font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-[#244F73] focus:ring-offset-1 focus:border-[#244F73]">
                            </div>
                        </div>
                    </div>

                    <!-- Spoken Languages Checkboxes + "Others" Conditional Input -->
                    <fieldset class="pt-4 space-y-3">
                        <legend class="text-xs sm:text-[13px] font-semibold text-[#1A2840]">Spoken Languages</legend>
                        <div class="flex flex-wrap items-center gap-3">
                            @foreach($languageOptions as $langOpt)
                                <label class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-lg border text-xs sm:text-[13px] cursor-pointer transition-colors focus-within:ring-2 focus-within:ring-[#244F73] focus-within:ring-offset-2 {{ in_array($langOpt->name, $spokenLanguages, true) ? 'bg-[#EBF3FA] border-[#244F73] text-[#1A2840] font-semibold' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50 hover:border-gray-400' }}">
                                    <input type="checkbox"
                                           value="{{ $langOpt->name }}"
                                           wire:model.live="spokenLanguages"
                                           class="rounded border-gray-400 text-[#244F73] focus:ring-2 focus:ring-[#244F73] focus:ring-offset-1 w-4 h-4">
                                    <span>{{ $langOpt->name }}</span>
                                </label>
                            @endforeach

                            <!-- "Others" Checkbox -->
                            <label class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-lg border text-xs sm:text-[13px] cursor-pointer transition-colors focus-within:ring-2 focus-within:ring-[#244F73] focus-within:ring-offset-2 {{ $hasOtherLanguage ? 'bg-[#EBF3FA] border-[#244F73] text-[#1A2840] font-semibold' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50 hover:border-gray-400' }}">
                                <input type="checkbox"
                                       wire:model.live="hasOtherLanguage"
                                       class="rounded border-gray-400 text-[#244F73] focus:ring-2 focus:ring-[#244F73] focus:ring-offset-1 w-4 h-4">
                                <span>Others</span>
                            </label>
                        </div>

                        @if($hasOtherLanguage)
                            <div class="pt-2 max-w-sm">
                                <label for="other-language-input" class="sr-only">Specify other spoken language</label>
                                <input id="other-language-input"
                                       type="text"
                                       wire:model.live="otherLanguage"
                                       placeholder="Please specify other language(s)..."
                                       class="w-full px-3.5 py-2.5 rounded-lg border border-[#244F73] bg-white text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#244F73] focus:ring-offset-1">
                            </div>
                        @endif
                    </fieldset>
                </div>

                <!-- 2. Professional Headline & Summary -->
                <div class="space-y-4 pt-8 border-t border-gray-200/80">
                    <div>
                        <label for="professional-headline" class="block text-base font-bold text-[#1A2840]">Professional Headline</label>
                        <p id="headline-helper" class="text-xs sm:text-[13px] text-gray-500 mt-1.5 mb-4 leading-relaxed max-w-2xl">
                            Your main job role or target title (optional &mdash; auto-filled from your latest job if left blank).
                        </p>
                        <input id="professional-headline"
                               type="text"
                               wire:model.live="headline"
                               aria-describedby="headline-helper"
                               placeholder="e.g., Administrative Assistant, Data Entry Clerk, or Customer Support"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 hover:border-gray-400 bg-white text-sm text-gray-900 placeholder-gray-400 transition-colors focus:outline-none focus:ring-2 focus:ring-[#244F73] focus:ring-offset-1 focus:border-[#244F73]">
                    </div>
                </div>

                <!-- 3. Work Experience (Start Year & End Year + Present Toggle) -->
                <div class="space-y-6 pt-8 border-t border-gray-200/80">
                    <h3 class="text-base font-bold text-[#1A2840]">Work Experience</h3>

                    @foreach($experiences as $index => $exp)
                        <div wire:key="exp-item-{{ $index }}" class="p-6 rounded-2xl border border-gray-200 bg-gray-50/40 space-y-5">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-xs font-bold uppercase tracking-wider text-[#244F73]">Experience #{{ $index + 1 }}</span>
                                @if(count($experiences) > 1)
                                    <button type="button"
                                            wire:click="removeExperience({{ $index }})"
                                            class="text-xs text-red-600 hover:underline font-semibold rounded px-2 py-1 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2">
                                        Remove
                                    </button>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label for="job-title-{{ $index }}" class="block text-xs sm:text-[13px] font-semibold text-[#1A2840] mb-2">Job Title</label>
                                    <input id="job-title-{{ $index }}"
                                           type="text"
                                           wire:model.live="experiences.{{ $index }}.job_title"
                                           placeholder="e.g., Administrative Assistant or Data Entry Clerk"
                                           class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 hover:border-gray-400 bg-white text-sm text-gray-900 placeholder-gray-400 transition-colors focus:outline-none focus:ring-2 focus:ring-[#244F73] focus:ring-offset-1 focus:border-[#244F73]">
                                </div>
                                <div>
                                    <label for="company-name-{{ $index }}" class="block text-xs sm:text-[13px] font-semibold text-[#1A2840] mb-2">Company Name</label>
                                    <input id="company-name-{{ $index }}"
                                           type="text"
                                           wire:model.live="experiences.{{ $index }}.company_name"
                                           placeholder="e.g., Zamboanga City LGU or BrightPath Solutions"
                                           class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 hover:border-gray-400 bg-white text-sm text-gray-900 placeholder-gray-400 transition-colors focus:outline-none focus:ring-2 focus:ring-[#244F73] focus:ring-offset-1 focus:border-[#244F73]">
                                </div>
                            </div>

                            <!-- Start Year, End Year & Present Toggle -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 items-end">
                                <div>
                                    <label for="start-year-{{ $index }}" class="block text-xs sm:text-[13px] font-semibold text-[#1A2840] mb-2">Start Year</label>
                                    <input id="start-year-{{ $index }}"
                                           type="number"
                                           min="1970"
                                           max="2030"
                                           wire:model.live="experiences.{{ $index }}.start_year"
                                           placeholder="2023"
                                           class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 hover:border-gray-400 bg-white text-sm text-gray-900 placeholder-gray-400 transition-colors focus:outline-none focus:ring-2 focus:ring-[#244F73] focus:ring-offset-1 focus:border-[#244F73]">
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <label for="end-year-{{ $index }}" class="block text-xs sm:text-[13px] font-semibold text-[#1A2840]">End Year</label>
                                        <label class="inline-flex items-center gap-2 text-xs font-semibold text-[#244F73] cursor-pointer rounded px-1.5 py-0.5 focus-within:ring-2 focus-within:ring-[#244F73] focus-within:ring-offset-2">
                                            <input type="checkbox"
                                                   wire:click="toggleExperienceCurrent({{ $index }})"
                                                   @checked(!empty($exp['is_current']))
                                                   class="rounded border-gray-400 text-[#244F73] focus:ring-2 focus:ring-[#244F73] focus:ring-offset-1 w-4 h-4">
                                            <span>Present</span>
                                        </label>
                                    </div>
                                    @if(!empty($exp['is_current']))
                                        <input id="end-year-{{ $index }}"
                                               type="text"
                                               value="Present"
                                               disabled
                                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 bg-gray-100 text-sm text-gray-600 font-semibold cursor-not-allowed">
                                    @else
                                        <input id="end-year-{{ $index }}"
                                               type="number"
                                               min="1970"
                                               max="2030"
                                               wire:model.live="experiences.{{ $index }}.end_year"
                                               placeholder="2025"
                                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 hover:border-gray-400 bg-white text-sm text-gray-900 placeholder-gray-400 transition-colors focus:outline-none focus:ring-2 focus:ring-[#244F73] focus:ring-offset-1 focus:border-[#244F73]">
                                    @endif
                                </div>
                            </div>

                            <div>
                                <label for="responsibilities-{{ $index }}" class="block text-xs sm:text-[13px] font-semibold text-[#1A2840] mb-2">Responsibilities</label>
                                <textarea id="responsibilities-{{ $index }}"
                                          rows="3"
                                          wire:model.live="experiences.{{ $index }}.description"
                                          placeholder="Briefly describe your main duties and accomplishments..."
                                          class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 hover:border-gray-400 bg-white text-sm text-gray-900 placeholder-gray-400 leading-relaxed transition-colors focus:outline-none focus:ring-2 focus:ring-[#244F73] focus:ring-offset-1 focus:border-[#244F73]"></textarea>
                            </div>
                        </div>
                    @endforeach

                    <button type="button"
                            wire:click="addExperience"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-xs font-bold text-[#244F73] hover:bg-gray-50 hover:border-gray-400 cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-2">
                        <span>+ Add another experience</span>
                    </button>
                </div>

                <!-- 4. Education (Single Dropdown: Highest Educational Attainment + Strictly Optional Certificate) -->
                <div class="space-y-6 pt-8 border-t border-gray-200/80">
                    <div>
                        <h3 class="text-base font-bold text-[#1A2840]">Education</h3>
                        <p class="text-xs sm:text-[13px] text-gray-500 mt-1.5 leading-relaxed max-w-2xl">
                            Select your highest educational level. Uploading diplomas or certificates for Tertiary, Vocational, or any level is strictly optional.
                        </p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-end">
                        <div class="md:col-span-5 flex flex-col justify-end min-w-0">
                            <label for="highest-education-select" class="block text-xs sm:text-[13px] font-semibold text-[#1A2840] mb-2">
                                Highest Educational Attainment
                            </label>
                            <select id="highest-education-select"
                                    wire:model.live="highestEducation"
                                    class="h-11 w-full px-3.5 rounded-lg border border-gray-300 hover:border-gray-400 bg-white text-sm text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-[#244F73] focus:ring-offset-1 focus:border-[#244F73]">
                                @foreach($educationLevels as $eduOption)
                                    <option value="{{ $eduOption->name }}">{{ $eduOption->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-4 flex flex-col justify-end min-w-0">
                            <label for="institution-name" class="block text-xs sm:text-[13px] font-semibold text-[#1A2840] mb-2">
                                Institution Name <span class="font-normal text-gray-500">(Optional)</span>
                            </label>
                            <div class="relative">
                                <input id="institution-name"
                                       type="text"
                                       wire:model.live="institutionName"
                                       placeholder="e.g., Western Mindanao State University"
                                       class="h-11 w-full px-3.5 pr-9 rounded-lg border border-gray-300 hover:border-gray-400 bg-white text-sm text-gray-900 placeholder-gray-400 transition-colors focus:outline-none focus:ring-2 focus:ring-[#244F73] focus:ring-offset-1 focus:border-[#244F73]">
                            </div>
                        </div>
                        <div class="md:col-span-3 flex flex-col justify-end min-w-0">
                            <label for="year-graduated" class="block text-xs sm:text-[13px] font-semibold text-[#1A2840] mb-2">
                                Year Graduated <span class="font-normal text-gray-500">(Optional)</span>
                            </label>
                            <input id="year-graduated"
                                   type="text"
                                   inputmode="numeric"
                                   maxlength="4"
                                   wire:model.live="yearGraduated"
                                   placeholder="e.g., 2025"
                                   class="h-11 w-full px-3.5 rounded-lg border border-gray-300 hover:border-gray-400 bg-white text-sm text-gray-900 placeholder-gray-400 transition-colors focus:outline-none focus:ring-2 focus:ring-[#244F73] focus:ring-offset-1 focus:border-[#244F73]">
                        </div>
                    </div>

                    <!-- Optional Education Certificate / Diploma Upload -->
                    <div class="flex flex-wrap items-center justify-between gap-4 pt-2">
                        <div class="text-xs text-gray-500 leading-relaxed">
                            @if($educationCertificateFilename)
                                <span class="font-semibold text-[#176B47]">&#10003; Certificate attached: {{ $educationCertificateFilename }}</span>
                            @else
                                <span>Certificate or diploma upload is optional and never required to complete your profile.</span>
                            @endif
                        </div>
                        <label class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-[#244F73] hover:bg-gray-50 hover:border-gray-400 cursor-pointer transition-colors focus-within:outline-none focus-within:ring-2 focus-within:ring-[#244F73] focus-within:ring-offset-2">
                            <span>{{ $educationCertificateFilename ? 'Replace Certificate (Optional)' : 'Upload Certificate / Diploma (Optional)' }}</span>
                            <input type="file"
                                   wire:model="educationCertificateUpload"
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   class="sr-only"
                                   aria-label="Upload optional education certificate or diploma">
                        </label>
                    </div>
                </div>

                <!-- 5. Skills & Strengths (Pre-populated Clickable Toggle Tags + Custom Skill Input) -->
                <div class="space-y-6 pt-8 border-t border-gray-200/80">
                    <div>
                        <h3 class="text-base font-bold text-[#1A2840]">Skills &amp; Strengths</h3>
                        <p class="text-xs sm:text-[13px] text-gray-500 mt-1.5 leading-relaxed max-w-2xl">
                            Click any tag below to toggle your skills on or off, or add your own custom skill.
                        </p>
                    </div>

                    <div class="p-6 rounded-2xl border border-gray-200 bg-gray-50/40 space-y-5">
                        <div class="flex flex-wrap gap-2.5" role="group" aria-label="Common skills toggle grid">
                            @foreach($skillOptions as $skillOpt)
                                @php
                                    $isSkillActive = in_array($skillOpt->name, $selectedSkills, true);
                                @endphp
                                <button type="button"
                                        wire:key="skill-tag-{{ $skillOpt->id }}"
                                        wire:click="toggleSkill('{{ addslashes($skillOpt->name) }}')"
                                        aria-pressed="{{ $isSkillActive ? 'true' : 'false' }}"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full text-xs font-semibold transition-all cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-2 {{ $isSkillActive ? 'bg-[#244F73] text-white shadow-2xs' : 'bg-[#DCEAF4]/70 text-[#1A2840] hover:bg-[#DCEAF4]' }}">
                                    @if($isSkillActive)
                                        <span aria-hidden="true">&#10003;</span>
                                    @endif
                                    <span>{{ $skillOpt->name }}</span>
                                </button>
                            @endforeach
                        </div>

                        <!-- Add Custom Skill Input & Button -->
                        <div class="pt-4 border-t border-gray-200/80 flex flex-col sm:flex-row sm:items-center gap-3">
                            <div class="flex-1">
                                <label for="custom-skill-input" class="sr-only">Add custom skill</label>
                                <input id="custom-skill-input"
                                       type="text"
                                       wire:model="customSkillInput"
                                       wire:keydown.enter.prevent="addCustomSkill"
                                       placeholder="Add custom skill (e.g., Graphic Design, Encoding, Tailoring)..."
                                       class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 hover:border-gray-400 bg-white text-sm text-gray-900 placeholder-gray-400 transition-colors focus:outline-none focus:ring-2 focus:ring-[#244F73] focus:ring-offset-1 focus:border-[#244F73]">
                            </div>
                            <button type="button"
                                    wire:click="addCustomSkill"
                                    class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-lg bg-[#244F73] text-white text-xs font-bold hover:bg-[#1A2840] transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#1A2840] focus-visible:ring-offset-2 shrink-0">
                                <span>+ Add</span>
                            </button>
                        </div>

                        <p class="text-xs text-gray-500">
                            {{ count($selectedSkills) }} {{ Str::plural('skill', count($selectedSkills)) }} selected. Click any tag above to toggle it on or off.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Right Preview & Voice Column -->
            <aside class="xl:col-span-4 space-y-6 min-w-0 w-full">
                <!-- Voice Listening Widget with WCAG 44px Controls -->
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-xs flex items-center justify-between gap-4 min-w-0">
                    <div class="flex items-center gap-3.5 min-w-0 flex-1">
                        <div class="w-11 h-11 rounded-full bg-[#244F73] text-white flex items-center justify-center ring-4 ring-[#DCEAF4] shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-xs font-bold text-[#244F73] truncate" aria-live="polite">
                                {{ $isListening ? 'Listening...' : 'Dictation Paused' }}
                            </div>
                            <div class="flex items-center gap-0.5 my-1.5" aria-hidden="true">
                                <span class="w-1 h-2.5 bg-[#244F73] rounded-full {{ $isListening ? 'animate-pulse' : 'opacity-40' }}"></span>
                                <span class="w-1 h-4 bg-[#244F73] rounded-full {{ $isListening ? 'animate-pulse' : 'opacity-40' }}"></span>
                                <span class="w-1 h-2 bg-[#244F73] rounded-full {{ $isListening ? 'animate-pulse' : 'opacity-40' }}"></span>
                                <span class="w-1 h-5 bg-[#244F73] rounded-full {{ $isListening ? 'animate-pulse' : 'opacity-40' }}"></span>
                                <span class="w-1 h-3 bg-[#244F73] rounded-full {{ $isListening ? 'animate-pulse' : 'opacity-40' }}"></span>
                                <span class="w-1 h-4 bg-[#244F73] rounded-full {{ $isListening ? 'animate-pulse' : 'opacity-40' }}"></span>
                            </div>
                            <div class="text-xs text-gray-500 truncate">Speak naturally &middot; 00:18</div>
                        </div>
                    </div>
                    <button type="button"
                            wire:click="toggleListening"
                            :aria-pressed="{{ $isListening ? 'true' : 'false' }}"
                            aria-label="{{ $isListening ? 'Pause voice dictation' : 'Resume voice dictation' }}"
                            class="min-w-[44px] min-h-[44px] w-11 h-11 rounded-full border border-gray-300 flex items-center justify-center text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-2 transition-colors cursor-pointer shrink-0">
                        @if($isListening)
                            <svg class="w-4 h-4 text-[#244F73]" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        @else
                            <svg class="w-4 h-4 text-[#244F73]" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </button>
                </div>

                <!-- Live Profile Preview Card -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs space-y-5">
                    <div>
                        <h3 class="text-sm font-bold text-[#1A2840]">Profile preview</h3>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                            Your details are coming together. Nothing is published yet.
                        </p>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-gray-100">
                        <div class="text-sm font-bold text-[#1A2840]">
                            {{ trim(implode(' ', array_filter([$firstName, $middleName, $lastName, $suffix]))) ?: 'Candidate Name' }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $effectiveHeadline }}@if($city) &middot; {{ $city }}@endif
                        </div>
                        @if($pwdIdNumber)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#E8F5E9] text-[#176B47]">
                                PWD ID Verified ({{ $pwdIdNumber }})
                            </span>
                        @endif
                    </div>

                    @if(!empty($selectedSkills))
                        <div class="space-y-2.5 pt-4 border-t border-gray-100">
                            <div class="text-xs font-bold text-[#1A2840]">Skills &amp; strengths</div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($selectedSkills as $sk)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-[#DCEAF4] text-[#244F73]">
                                        {{ $sk }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($bio)
                        <p class="text-xs text-gray-500 leading-relaxed pt-2 border-t border-gray-100">
                            {{ $bio }}
                        </p>
                    @endif
                </div>

                <!-- Sequential Guidance Tip -->
                <div class="rounded-2xl bg-white border border-gray-200 p-6 shadow-xs space-y-2.5">
                    <div class="flex items-center gap-2 text-xs font-bold text-[#1A2840]">
                        <svg class="w-4 h-4 text-[#244F73] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="10" stroke-width="2"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16v-4m0-4h.01"/>
                        </svg>
                        <span>Speak through each category sequentially</span>
                    </div>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        Start with Personal Identification, then Professional Headline, Work Experience, Education, and finally Skills. Pause when you finish each section so your spoken answers map directly into the correct text boxes.
                    </p>
                </div>
            </aside>
        </div>
    @endif

    {{-- ===================================================================== --}}
    {{-- STEP 3: ACCOMMODATION CHECKLIST                                       --}}
    {{-- ===================================================================== --}}
    @if($currentStep === 3)
        <section class="bg-white rounded-xl border border-gray-200 p-6 shadow-xs space-y-6" aria-labelledby="step3-heading">
            <div>
                <h2 id="step3-heading" class="text-lg font-bold text-[#1A2840]">Your workplace, made accessible</h2>
                <p class="text-xs text-[#4E5F6E] mt-1">
                    Select all that apply. These preferences help us find roles with the right workplace support.
                </p>
            </div>

            <!-- 3 Category Columns: Physical, Visual, Auditory -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 min-w-0">
                <!-- Physical -->
                <div class="rounded-xl border border-gray-200 p-4 space-y-3 bg-gray-50/30">
                    <div>
                        <h3 class="text-sm font-bold text-[#1A2840]">Physical</h3>
                        <p class="text-[11px] text-gray-500">Spaces that make movement easier.</p>
                    </div>
                    <div class="space-y-2.5">
                        @foreach($physicalAccommodations as $acc)
                            @php
                                $isChecked = in_array((int) $acc->id, $selectedAccommodations, true);
                            @endphp
                            <button type="button"
                                    wire:key="acc-phys-{{ $acc->id }}"
                                    wire:click="toggleAccommodation({{ $acc->id }})"
                                    role="checkbox"
                                    aria-checked="{{ $isChecked ? 'true' : 'false' }}"
                                    class="w-full text-left p-3.5 rounded-xl transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#244F73] {{ $isChecked ? 'border-2 border-[#244F73] bg-[#EBF3FA]/70' : 'border border-gray-200 bg-white hover:border-[#244F73]/50' }}">
                                <div class="flex items-start gap-2.5">
                                    <span class="w-4 h-4 rounded mt-0.5 flex items-center justify-center text-[10px] font-bold shrink-0 {{ $isChecked ? 'bg-[#244F73] text-white' : 'border border-gray-300 bg-white' }}">
                                        @if($isChecked) &#10003; @endif
                                    </span>
                                    <div>
                                        <div class="text-xs font-bold text-[#1A2840]">{{ $acc->name }}</div>
                                        <div class="text-[11px] text-[#4E5F6E] mt-0.5">{{ $acc->description ?? 'Verified workplace accommodation support.' }}</div>
                                    </div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Visual -->
                <div class="rounded-xl border border-gray-200 p-4 space-y-3 bg-gray-50/30">
                    <div>
                        <h3 class="text-sm font-bold text-[#1A2840]">Visual</h3>
                        <p class="text-[11px] text-gray-500">Tools for clear, accessible information.</p>
                    </div>
                    <div class="space-y-2.5">
                        @foreach($visualAccommodations as $acc)
                            @php
                                $isChecked = in_array((int) $acc->id, $selectedAccommodations, true);
                            @endphp
                            <button type="button"
                                    wire:key="acc-vis-{{ $acc->id }}"
                                    wire:click="toggleAccommodation({{ $acc->id }})"
                                    role="checkbox"
                                    aria-checked="{{ $isChecked ? 'true' : 'false' }}"
                                    class="w-full text-left p-3.5 rounded-xl transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#244F73] {{ $isChecked ? 'border-2 border-[#244F73] bg-[#EBF3FA]/70' : 'border border-gray-200 bg-white hover:border-[#244F73]/50' }}">
                                <div class="flex items-start gap-2.5">
                                    <span class="w-4 h-4 rounded mt-0.5 flex items-center justify-center text-[10px] font-bold shrink-0 {{ $isChecked ? 'bg-[#244F73] text-white' : 'border border-gray-300 bg-white' }}">
                                        @if($isChecked) &#10003; @endif
                                    </span>
                                    <div>
                                        <div class="text-xs font-bold text-[#1A2840]">{{ $acc->name }}</div>
                                        <div class="text-[11px] text-[#4E5F6E] mt-0.5">{{ $acc->description ?? 'Verified digital and visual support.' }}</div>
                                    </div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Auditory -->
                <div class="rounded-xl border border-gray-200 p-4 space-y-3 bg-gray-50/30">
                    <div>
                        <h3 class="text-sm font-bold text-[#1A2840]">Auditory</h3>
                        <p class="text-[11px] text-gray-500">Communication that includes everyone.</p>
                    </div>
                    <div class="space-y-2.5">
                        @foreach($auditoryAccommodations as $acc)
                            @php
                                $isChecked = in_array((int) $acc->id, $selectedAccommodations, true);
                            @endphp
                            <button type="button"
                                    wire:key="acc-aud-{{ $acc->id }}"
                                    wire:click="toggleAccommodation({{ $acc->id }})"
                                    role="checkbox"
                                    aria-checked="{{ $isChecked ? 'true' : 'false' }}"
                                    class="w-full text-left p-3.5 rounded-xl transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#244F73] {{ $isChecked ? 'border-2 border-[#244F73] bg-[#EBF3FA]/70' : 'border border-gray-200 bg-white hover:border-[#244F73]/50' }}">
                                <div class="flex items-start gap-2.5">
                                    <span class="w-4 h-4 rounded mt-0.5 flex items-center justify-center text-[10px] font-bold shrink-0 {{ $isChecked ? 'bg-[#244F73] text-white' : 'border border-gray-300 bg-white' }}">
                                        @if($isChecked) &#10003; @endif
                                    </span>
                                    <div>
                                        <div class="text-xs font-bold text-[#1A2840]">{{ $acc->name }}</div>
                                        <div class="text-[11px] text-[#4E5F6E] mt-0.5">{{ $acc->description ?? 'Inclusive communication support.' }}</div>
                                    </div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 pt-1">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-[#DCEAF4] text-[#244F73]">
                    {{ count($selectedAccommodations) }} {{ Str::plural('accommodation', count($selectedAccommodations)) }} selected
                </span>
                <span class="text-xs text-gray-500">You can change these preferences anytime from your profile.</span>
            </div>
        </section>

        <!-- Additional Notes + Privacy Reassurance -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
            <div class="lg:col-span-8 space-y-2">
                <label for="accommodation-notes" class="block text-xs font-bold text-[#1A2840]">
                    Anything else we should know? <span class="font-normal text-gray-500">(optional)</span>
                </label>
                <textarea id="accommodation-notes"
                          rows="3"
                          wire:model.live="accommodationNotes"
                          placeholder="Optional: Share any specific interview or workplace setup requests..."
                          class="w-full p-3.5 rounded-xl border border-gray-200 bg-white text-xs text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#244F73]"></textarea>
            </div>

            <div class="lg:col-span-4 rounded-xl bg-[#EBF3FA] border border-[#B8D0E2] p-5 space-y-2">
                <div class="flex items-center gap-2 text-xs font-bold text-[#1A2840]">
                    <svg class="w-4 h-4 text-[#244F73] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span>Your needs, your choice</span>
                </div>
                <p class="text-xs text-[#4E5F6E] leading-relaxed">
                    Only your selected support needs are used for matching. You do not need to share medical records or describe a disability.
                </p>
            </div>
        </div>
    @endif

    {{-- ===================================================================== --}}
    {{-- STEP 4: RESUME, OPTIONAL FSL VIDEO & PWD ID UPLOAD                    --}}
    {{-- ===================================================================== --}}
    @if($currentStep === 4)
        <section class="bg-white rounded-xl border border-gray-200 p-6 shadow-xs space-y-6" aria-labelledby="step4-heading">
            <div>
                <h2 id="step4-heading" class="text-lg font-bold text-[#1A2840]">Add your supporting files</h2>
                <p class="text-xs text-[#4E5F6E] mt-1">
                    Your professional resume is auto-generated from your profile, and your verified PWD ID from registration is already attached. You can optionally add a Filipino Sign Language (FSL) video introduction.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Card 1: Professional Resume (Auto-Generated PDF Injected) -->
                <div class="rounded-xl border-2 border-dashed border-[#244F73]/50 bg-[#F4F7FA]/70 p-5 space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-[#244F73]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#E8F5E9] text-[#176B47]">
                            &#10003; Attached
                        </span>
                    </div>

                    <div>
                        <h3 class="text-base font-bold text-[#1A2840]">Professional resume</h3>
                        <p class="text-xs text-[#4E5F6E] mt-1">
                            Automatically generated from your profile details, or choose a PDF/Word document from your device.
                        </p>
                        <div class="text-[11px] font-bold text-[#244F73] mt-1">PDF, DOC, DOCX &middot; Up to 10 MB</div>
                    </div>

                    <!-- Injected Auto-Generated Resume File Card -->
                    <div class="bg-white rounded-xl border border-gray-200 p-3.5 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-[#EBF3FA] text-[#244F73] flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-[#1A2840] truncate">{{ $resumeFilename }}</div>
                                <div class="text-[11px] text-gray-500">PDF &middot; {{ $resumeFileSize }} &middot; Auto-generated</div>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-[#176B47] shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>

                    <!-- Card Actions: Preview resume + Download Resume + Replace file -->
                    <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button"
                                    wire:click="previewResume"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-gray-300 bg-white text-xs font-bold text-[#244F73] hover:bg-gray-50 focus:ring-2 focus:ring-[#244F73]">
                                <span>Preview resume</span>
                            </button>

                            <button type="button"
                                    wire:click="downloadResume"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-[#244F73] text-white text-xs font-bold hover:bg-[#1A2840] focus:ring-2 focus:ring-[#244F73]">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                <span>Download Resume</span>
                            </button>
                        </div>

                        <label class="text-xs font-bold text-[#244F73] hover:underline cursor-pointer">
                            <span>Replace file</span>
                            <input type="file" wire:model="customResumeUpload" accept=".pdf,.doc,.docx" class="hidden">
                        </label>
                    </div>
                </div>

                <!-- Card 2: FSL Video Introduction (Strictly Conditional on Real Upload) -->
                <div class="rounded-xl border-2 border-dashed {{ $hasFslAttached ? 'border-[#244F73]/50 bg-[#F4F7FA]/70' : 'border-gray-300 bg-gray-50/50' }} p-5 space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-[#244F73]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        @if($hasFslAttached)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#E8F5E9] text-[#176B47]">
                                &#10003; Attached
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-gray-200/80 text-gray-700">
                                Optional &middot; Not attached
                            </span>
                        @endif
                    </div>

                    <div>
                        <h3 class="text-base font-bold text-[#1A2840]">FSL video introduction <span class="text-xs font-normal text-gray-500">(Optional)</span></h3>
                        <p class="text-xs text-[#4E5F6E] mt-1">
                            Upload a Filipino Sign Language (FSL) video introduction if you wish to share one with employers.
                        </p>
                        <div class="text-[11px] font-bold text-[#244F73] mt-1">MP4 &middot; Up to 100 MB &middot; Recommended: 1&ndash;2 minutes</div>
                    </div>

                    @if($hasFslAttached)
                        <div class="bg-white rounded-xl border border-gray-200 p-3.5 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-[#EBF3FA] text-[#244F73] flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-[#1A2840] truncate">{{ $fslVideoFilename }}</div>
                                    <div class="text-[11px] text-gray-500">MP4 &middot; {{ $fslVideoSize }}</div>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-[#176B47] shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>

                        <div class="flex items-center justify-between gap-2 pt-1">
                            <button type="button"
                                    wire:click="previewVideo"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-gray-300 bg-white text-xs font-bold text-[#244F73] hover:bg-gray-50 focus:ring-2 focus:ring-[#244F73]">
                                <span>&#9654; Preview video</span>
                            </button>

                            <div class="flex items-center gap-3">
                                <label class="text-xs font-bold text-[#244F73] hover:underline cursor-pointer">
                                    <span>Replace file</span>
                                    <input type="file" wire:model="fslVideoUpload" accept="video/mp4" class="hidden">
                                </label>
                                <button type="button"
                                        wire:click="removeFslVideo"
                                        class="text-xs font-semibold text-red-600 hover:underline">
                                    Remove
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="bg-white rounded-xl border border-gray-200 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="text-xs text-gray-500">
                                No FSL video uploaded yet. This step is completely optional.
                            </div>
                            <label class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg border border-[#244F73] bg-white text-xs font-bold text-[#244F73] hover:bg-[#EBF3FA] cursor-pointer shrink-0 focus-within:ring-2 focus-within:ring-[#244F73]">
                                <span>Upload FSL Video</span>
                                <input type="file" wire:model="fslVideoUpload" accept="video/mp4" class="hidden" aria-label="Upload optional FSL video introduction">
                            </label>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Dedicated Secure Card for PWD ID Card / Verification Document -->
            <div class="rounded-xl border {{ $hasPwdIdAttached ? 'border-[#A5D6A7] bg-[#F4FAF5]' : 'border-dashed border-gray-300 bg-gray-50/50' }} p-5 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-lg {{ $hasPwdIdAttached ? 'bg-[#E8F5E9] text-[#176B47] border border-[#A5D6A7]' : 'bg-white text-[#244F73] border border-gray-200' }} flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-base font-bold text-[#1A2840]">PWD ID Card / Verification Document</h3>
                                @if($hasPwdIdAttached)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#E8F5E9] text-[#176B47]">
                                        &#10003; Attached from Registration
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#DCEAF4] text-[#244F73]">
                                        Verification Document
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-[#4E5F6E] mt-1">
                                @if($hasPwdIdAttached)
                                    Your official PWD ID (ID: <strong class="text-primary-dark font-mono font-bold">{{ $pwdIdNumber ?: auth()->user()->pwd_id_number }}</strong>) was verified during registration and is automatically linked to your employment credentials. No re-upload needed.
                                @else
                                    Upload a clear photo or scan of your official Persons with Disability (PWD) ID card (PDF, JPG, or PNG, up to 10 MB) for credential verification.
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <label class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-[#1A2840] hover:bg-gray-50 cursor-pointer focus-within:ring-2 focus-within:ring-[#244F73]">
                            <span>{{ $hasPwdIdAttached ? 'Update scan (optional)' : 'Upload PWD ID Document' }}</span>
                            <input type="file"
                                   wire:model="pwdIdUpload"
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   class="hidden"
                                   aria-label="Upload official PWD ID document">
                        </label>
                        @if($pwdIdUpload)
                            <button type="button"
                                    wire:click="removePwdIdDocument"
                                    class="text-xs font-semibold text-red-600 hover:underline px-2">
                                Cancel
                            </button>
                        @endif
                    </div>
                </div>

                @if($hasPwdIdAttached)
                    <div class="bg-white rounded-xl border border-gray-200 p-3 flex items-center justify-between gap-3 shadow-2xs">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-[#176B47] shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-xs font-bold text-[#1A2840] truncate">
                                {{ $pwdIdFilename ?: 'PWD ID Registration Record (' . ($pwdIdNumber ?: auth()->user()->pwd_id_number) . ')' }}
                            </span>
                            @if($pwdIdFileSize)
                                <span class="text-[11px] text-gray-500">&middot; {{ $pwdIdFileSize }}</span>
                            @endif
                        </div>
                        <span class="text-[11px] font-semibold text-[#176B47]">RA 10173 Encrypted &amp; Protected</span>
                    </div>
                @endif
            </div>

            <!-- Dedicated Secure Card for Clinical Medical Certificate (Fit-to-Work) -->
            <div class="rounded-xl border {{ $hasMedCertAttached ? 'border-[#A5D6A7] bg-[#F4FAF5]' : 'border-dashed border-gray-300 bg-gray-50/50' }} p-5 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-lg {{ $hasMedCertAttached ? 'bg-[#E8F5E9] text-[#176B47] border border-[#A5D6A7]' : 'bg-white text-[#244F73] border border-gray-200' }} flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-base font-bold text-[#1A2840]">Clinical Medical Certificate (Fit-to-Work)</h3>
                                @if($hasMedCertAttached)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#E8F5E9] text-[#176B47]">
                                        &#10003; Attached
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#DCEAF4] text-[#244F73]">
                                        Optional Credential
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-[#4E5F6E] mt-1">
                                @if($hasMedCertAttached)
                                    Your Clinical Medical Certificate {{ $medCertNumber ? '(Ref: '.$medCertNumber.')' : '' }} is attached to your profile and visible to employers reviewing your application.
                                @else
                                    Upload a Fit-to-Work or clinical medical diagnosis certificate (PDF, JPG, or PNG, up to 10 MB) to share occupational fitness clearance alongside your PWD ID.
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <div class="relative">
                            <input type="text"
                                   wire:model.live="medCertNumber"
                                   placeholder="Med Cert / PRC Ref. (Optional)"
                                   aria-label="Medical Certificate or PRC reference number"
                                   class="h-9 w-48 sm:w-52 rounded-lg border border-gray-300 bg-white px-3 pr-8 text-xs text-[#1A2840] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#244F73]">
                        </div>
                        <label class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-[#1A2840] hover:bg-gray-50 cursor-pointer focus-within:ring-2 focus-within:ring-[#244F73]">
                            <span>{{ $hasMedCertAttached ? 'Update scan (optional)' : 'Upload Medical Certificate' }}</span>
                            <input type="file"
                                   wire:model="medCertUpload"
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   class="hidden"
                                   aria-label="Upload clinical medical certificate">
                        </label>
                        @if($medCertUpload || $medCertPath)
                            <button type="button"
                                    wire:click="removeMedCertDocument"
                                    class="text-xs font-semibold text-red-600 hover:underline px-2">
                                Remove
                            </button>
                        @endif
                    </div>
                </div>
                @error('medCertUpload')
                    <p class="text-xs text-red-600" role="alert">{{ $message }}</p>
                @enderror

                @if($hasMedCertAttached)
                    <div class="bg-white rounded-xl border border-gray-200 p-3 flex items-center justify-between gap-3 shadow-2xs">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-[#176B47] shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-xs font-bold text-[#1A2840] truncate">
                                {{ $medCertFilename ?: 'Medical Certificate Record' }}
                            </span>
                            @if($medCertFileSize)
                                <span class="text-[11px] text-gray-500">&middot; {{ $medCertFileSize }}</span>
                            @endif
                        </div>
                        <span class="text-[11px] font-semibold text-[#176B47]">RA 10173 Encrypted &amp; Protected</span>
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-2 text-xs font-semibold text-[#176B47]">
                <svg class="w-4 h-4 text-[#176B47]" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ $attachedFilesCount }} {{ Str::plural('file', $attachedFilesCount) }} attached &middot; File checks complete</span>
            </div>
        </section>

        <!-- Bottom Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="rounded-xl bg-[#EBF3FA] border border-[#B8D0E2] p-5 space-y-1.5">
                <div class="text-xs font-bold text-[#1A2840]">Your introduction, your language</div>
                <p class="text-xs text-[#4E5F6E] leading-relaxed">
                    Use FSL to share your strengths, interests, and the kind of work you enjoy. Keep your hands and face clearly visible in good lighting.
                </p>
            </div>
            <div class="rounded-xl bg-[#EBF3FA] border border-[#B8D0E2] p-5 space-y-1.5">
                <div class="text-xs font-bold text-[#1A2840]">You decide what employers see</div>
                <p class="text-xs text-[#4E5F6E] leading-relaxed">
                    These files become part of your saved candidate profile. Preview your attachments now; you can replace or remove them anytime.
                </p>
            </div>
        </div>
    @endif

    {{-- ===================================================================== --}}
    {{-- STEP 5: REVIEW & SAVE (CLEANED PROFILE SUMMARY VIEW)                  --}}
    {{-- ===================================================================== --}}
    @if($currentStep === 5)
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start w-full min-w-0">
            <!-- Left Summary Card -->
            <section class="xl:col-span-8 bg-white rounded-xl border border-gray-200 p-6 shadow-xs space-y-5 min-w-0 w-full" aria-labelledby="step5-heading">
                <h2 id="step5-heading" class="text-base font-bold text-[#1A2840]">Your employment profile</h2>

                <!-- Candidate Identity Banner -->
                <div class="flex flex-wrap items-center justify-between gap-4 pb-5 border-b border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-[#DCEAF4] text-[#244F73] flex items-center justify-center font-bold text-sm overflow-hidden shrink-0">
                            @if($avatarPath)
                                <img src="{{ Storage::url($avatarPath) }}" alt="Candidate avatar" class="w-full h-full object-cover">
                            @elseif($avatarUpload)
                                <img src="{{ $avatarUpload->temporaryUrl() }}" alt="Candidate avatar" class="w-full h-full object-cover">
                            @else
                                <span>{{ strtoupper(substr($firstName, 0, 1).substr($lastName, 0, 1)) ?: 'MS' }}</span>
                            @endif
                        </div>
                        <div>
                            <div class="text-lg font-bold text-[#1A2840]">
                                {{ trim(implode(' ', array_filter([$firstName, $middleName, $lastName, $suffix]))) ?: 'Candidate Name' }}
                            </div>
                            <div class="text-xs text-[#4E5F6E]">
                                {{ $effectiveHeadline }}@if($city || $country) &middot; {{ implode(', ', array_filter([$city, $country])) }}@endif
                            </div>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-[#E8F5E9] text-[#176B47]">
                        PWD ID Verified
                    </span>
                </div>

                <!-- Contact Info -->
                <div class="space-y-1.5 pb-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold text-[#1A2840]">Contact Info</h3>
                        <button type="button" wire:click="goToStep(2)" class="text-xs font-bold text-[#244F73] hover:underline">Edit</button>
                    </div>
                    @if($email)
                        <div class="text-xs text-[#4E5F6E]">{{ $email }}</div>
                    @endif
                    @if($phone)
                        <div class="text-xs text-[#4E5F6E]">{{ $phone }}</div>
                    @endif
                    @if($address || $barangay || $city || $province || $country || $zipCode)
                        <div class="text-xs text-[#4E5F6E]">{{ implode(', ', array_filter([$address, $barangay, $city, $province, $country, $zipCode ? 'ZIP '.$zipCode : null])) }}</div>
                    @endif
                    @php
                        $allLanguages = array_values(array_filter(array_merge($spokenLanguages, $hasOtherLanguage && $otherLanguage ? [$otherLanguage] : [])));
                    @endphp
                    @if(!empty($allLanguages))
                        <div class="text-xs text-[#4E5F6E]">
                            Languages: {{ implode(', ', $allLanguages) }}
                        </div>
                    @endif
                </div>

                <!-- Professional Summary -->
                @if($bio)
                    <div class="space-y-1 pb-4 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-bold text-[#1A2840]">Professional summary</h3>
                            <button type="button" wire:click="goToStep(2)" class="text-xs font-bold text-[#244F73] hover:underline">Edit</button>
                        </div>
                        <p class="text-xs text-[#4E5F6E] leading-relaxed">{{ $bio }}</p>
                    </div>
                @endif

                <!-- Work Experience (Cleanly Filtered) -->
                <div class="space-y-3 pb-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold text-[#1A2840]">Work Experience</h3>
                        <button type="button" wire:click="goToStep(2)" class="text-xs font-bold text-[#244F73] hover:underline">Edit</button>
                    </div>
                    @forelse($validExperiences as $exp)
                        <div class="space-y-1">
                            <div class="text-xs font-bold text-[#1A2840]">
                                {{ implode(' · ', array_filter([$exp['job_title'], $exp['company_name']])) }}
                            </div>
                            @if($exp['start_year'] || $exp['end_year'])
                                <div class="text-[11px] text-gray-500">
                                    {{ $exp['start_year'] ?: 'Start' }} &ndash; {{ $exp['end_year'] ?: 'Present' }}
                                </div>
                            @endif
                            @if($exp['description'])
                                <p class="text-xs text-[#4E5F6E] leading-relaxed">{{ $exp['description'] }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-gray-500">No work experience entries added yet.</p>
                    @endforelse
                </div>

                <!-- Education -->
                <div class="space-y-1 pb-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold text-[#1A2840]">Education</h3>
                        <button type="button" wire:click="goToStep(2)" class="text-xs font-bold text-[#244F73] hover:underline">Edit</button>
                    </div>
                    <div class="text-xs font-bold text-[#1A2840]">{{ $highestEducation }}</div>
                    @if($institutionName || $yearGraduated)
                        <div class="text-xs text-[#4E5F6E]">
                            {{ implode(' · ', array_filter([$institutionName, $yearGraduated ? 'Graduated '.$yearGraduated : null])) }}
                        </div>
                    @endif
                </div>

                <!-- Skills & Strengths -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold text-[#1A2840]">Skills &amp; strengths</h3>
                        <button type="button" wire:click="goToStep(2)" class="text-xs font-bold text-[#244F73] hover:underline">Edit</button>
                    </div>
                    @if(!empty($selectedSkills))
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($selectedSkills as $sk)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-[#DCEAF4] text-[#244F73]">
                                    {{ $sk }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-gray-500">No skills selected yet.</p>
                    @endif
                </div>
            </section>

            <!-- Right Column: Accommodations + Attachments -->
            <aside class="xl:col-span-4 space-y-5 min-w-0 w-full">
                <!-- Accommodation Needs Summary -->
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-xs space-y-3">
                    <h3 class="text-sm font-bold text-[#1A2840]">Accommodation needs</h3>
                    @if($selectedAccommodationObjects->isNotEmpty())
                        <ul class="space-y-2 text-xs text-[#1A2840]">
                            @foreach($selectedAccommodationObjects as $accObj)
                                <li class="flex items-center gap-2 font-medium">
                                    <svg class="w-4 h-4 text-[#244F73] shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $accObj->name }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-xs text-gray-500">No workplace accommodations selected.</p>
                    @endif
                    @if($accommodationNotes)
                        <p class="text-xs text-[#4E5F6E] pt-1 leading-relaxed">{{ $accommodationNotes }}</p>
                    @endif
                    <div class="pt-1">
                        <button type="button" wire:click="goToStep(3)" class="text-xs font-bold text-[#244F73] hover:underline">
                            Edit accommodations
                        </button>
                    </div>
                </div>

                <!-- Attachments Summary (Conditioned on Actual Upload State) -->
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-xs space-y-3">
                    <h3 class="text-sm font-bold text-[#1A2840]">Attachments</h3>

                    <div class="rounded-xl border border-gray-200 p-3 flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <div class="text-xs font-bold text-[#1A2840] truncate">{{ $resumeFilename }}</div>
                            <div class="text-[11px] text-gray-500">PDF &middot; {{ $resumeFileSize }} &middot; Auto-generated</div>
                        </div>
                        <span class="text-[#176B47] font-bold text-xs">&#10003;</span>
                    </div>

                    @if($hasFslAttached)
                        <div class="rounded-xl border border-gray-200 p-3 flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-[#1A2840] truncate">{{ $fslVideoFilename }}</div>
                                <div class="text-[11px] text-gray-500">MP4 &middot; {{ $fslVideoSize }}</div>
                            </div>
                            <span class="text-[#176B47] font-bold text-xs">&#10003;</span>
                        </div>
                    @else
                        <div class="rounded-xl border border-dashed border-gray-200 p-3 flex items-center justify-between gap-2 bg-gray-50/50">
                            <div class="min-w-0">
                                <div class="text-xs font-medium text-gray-600">FSL Video Introduction</div>
                                <div class="text-[11px] text-gray-400">Optional &middot; Not attached</div>
                            </div>
                        </div>
                    @endif

                    @if($hasPwdIdAttached)
                        <div class="rounded-xl border border-gray-200 p-3 flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-[#1A2840] truncate">{{ $pwdIdFilename }}</div>
                                <div class="text-[11px] text-gray-500">PWD ID Document &middot; {{ $pwdIdFileSize }}</div>
                            </div>
                            <span class="text-[#176B47] font-bold text-xs">&#10003;</span>
                        </div>
                    @endif

                    <div class="pt-1">
                        <button type="button" wire:click="goToStep(4)" class="text-xs font-bold text-[#244F73] hover:underline">
                            Preview or replace files
                        </button>
                    </div>
                </div>

                <!-- Ready for better matches -->
                <div class="rounded-xl bg-[#EBF3FA] border border-[#B8D0E2] p-5 space-y-1.5">
                    <div class="text-xs font-bold text-[#1A2840]">Ready for better matches</div>
                    <p class="text-xs text-[#4E5F6E] leading-relaxed">
                        Saving uses your skills and accommodation needs to recommend relevant jobs. It does not submit an application.
                    </p>
                </div>
            </aside>
        </div>

        <!-- Green Completion Banner -->
        <div class="rounded-xl bg-[#F0FDF4] border border-[#BBF7D0] px-5 py-3.5 flex items-center gap-2.5 text-xs font-semibold text-[#176B47]">
            <svg class="w-4 h-4 text-[#176B47] shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>All sections complete &middot; Professional details, {{ count($selectedAccommodations) }} accommodation needs, and {{ $attachedFilesCount }} {{ Str::plural('attachment', $attachedFilesCount) }} are ready to save.</span>
        </div>
    @endif

    <!-- Bottom Step Navigation Bar -->
    <div class="mt-10 rounded-2xl bg-white border border-gray-200 px-6 py-5 sm:px-8 shadow-xs flex flex-wrap items-center justify-between gap-5 w-full min-w-0">
        <div class="flex flex-wrap items-center gap-4 min-w-0 flex-1">
            @if($currentStep === 1)
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-2 px-5 py-3 min-h-[44px] rounded-xl border border-gray-300 bg-white text-xs sm:text-sm font-bold text-[#244F73] hover:bg-gray-50 hover:border-gray-400 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-2 shrink-0">
                    <span>&larr; Cancel</span>
                </a>
            @else
                <button type="button"
                        wire:click="prevStep"
                        class="inline-flex items-center gap-2 px-5 py-3 min-h-[44px] rounded-xl border border-gray-300 bg-white text-xs sm:text-sm font-bold text-[#244F73] hover:bg-gray-50 hover:border-gray-400 transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-2 shrink-0">
                    <span>&larr; Back</span>
                </button>
            @endif

            <div class="min-w-0 space-y-0.5">
                <div class="text-xs sm:text-[13px] font-bold text-[#1A2840] leading-snug">
                    {{ $currentStep === 5 ? 'Ready when you are. You can edit your profile anytime.' : 'Your profile stays private until you finish.' }}
                </div>
                <div class="text-xs text-gray-500 leading-snug">Save a draft to return to this step later.</div>
            </div>
        </div>

        @if ($errors->any())
            <div class="w-full p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs flex items-center gap-2.5" role="alert" aria-live="assertive">
                <svg class="w-4 h-4 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span class="font-semibold">Please complete all required fields above before proceeding to the next step.</span>
            </div>
        @endif

        <div class="flex flex-wrap items-center gap-3.5 max-w-full">
            <button type="button"
                    wire:click="saveDraftAndExit"
                    class="px-5 py-3 min-h-[44px] rounded-xl border border-gray-300 bg-white text-xs sm:text-sm font-bold text-[#244F73] hover:bg-gray-50 hover:border-gray-400 transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-2">
                Save draft &amp; exit
            </button>

            @if($currentStep < 5)
                <button type="button"
                        wire:click="nextStep"
                        class="inline-flex items-center gap-2 px-6 py-3 min-h-[44px] rounded-xl bg-[#244F73] text-white text-xs sm:text-sm font-bold hover:bg-[#1A2840] shadow-xs transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#1A2840] focus-visible:ring-offset-2">
                    <span>
                        @if($currentStep === 1)
                            &rarr; Continue to details
                        @elseif($currentStep === 2)
                            &rarr; Continue to accommodations
                        @elseif($currentStep === 3)
                            &rarr; Continue to attachments
                        @else
                            &rarr; Review my profile
                        @endif
                    </span>
                </button>
            @else
                <button type="button"
                        wire:click="saveProfileAndRedirect"
                        class="inline-flex items-center gap-2 px-6 py-3 min-h-[44px] rounded-xl bg-[#244F73] text-white text-xs sm:text-sm font-bold hover:bg-[#1A2840] shadow-xs transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#1A2840] focus-visible:ring-offset-2">
                    <span>&rarr; Save Profile &amp; Find Matches</span>
                </button>
            @endif
        </div>
    </div>

    <div class="mt-6 text-center text-xs text-gray-500">
        All steps support keyboard navigation. Voice input is always optional.
    </div>

    <!-- Print-Friendly Digital Profile Resume Preview Modal -->
    @if($showResumePreviewModal)
        @php
            $resumeUser = auth()->user();
            $resumeProfile = $resumeUser?->candidateProfile;
            $resumeFullName = trim(implode(' ', array_filter([$firstName, $middleName, $lastName, $suffix]))) ?: ($resumeUser?->name ?? 'Candidate Name');
            $resumePrintName = trim(implode(' ', array_filter([$firstName, $middleName, $lastName]))) ?: ($resumeUser?->name ?? 'Candidate Name');
            $resumeInitials = strtoupper(substr($firstName, 0, 1).substr($lastName, 0, 1)) ?: ($resumeUser?->initials() ?? 'MS');
            $resumeMaskedId = 'PWD-'.str_pad((string) ($resumeUser?->id ?? 0), 5, '0', STR_PAD_LEFT);
            $resumeUpdatedDate = $resumeProfile?->updated_at ? $resumeProfile->updated_at->format('M d, Y') : now()->format('M d, Y');
            $resumeLocation = implode(', ', array_filter([$address, $barangay, $city, $province, $country]));
            $resumeLanguages = array_values(array_filter(array_merge(
                $spokenLanguages,
                ($hasOtherLanguage && trim($otherLanguage) !== '') ? [trim($otherLanguage)] : []
            )));
            if (empty($resumeLanguages)) {
                $resumeLanguages = ['Filipino', 'English'];
            }
        @endphp
        <style>
            @media print {
                body * {
                    visibility: hidden !important;
                }
                #resume-preview-printable,
                #resume-preview-printable * {
                    visibility: visible !important;
                }
                #resume-preview-printable {
                    position: fixed !important;
                    inset: 0 !important;
                    width: 100% !important;
                    max-width: 100% !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    background: #ffffff !important;
                    box-shadow: none !important;
                    border: none !important;
                    overflow: visible !important;
                }
                #resume-preview-printable .resume-no-break {
                    break-inside: avoid !important;
                    page-break-inside: avoid !important;
                }
            }
        </style>
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="resume-preview-title">
            <div class="bg-white rounded-2xl max-w-4xl w-full shadow-2xl border border-gray-200 overflow-hidden flex flex-col max-h-[90vh]">
                <!-- Modal Top Toolbar -->
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between gap-4 shrink-0">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#176B47] shrink-0" aria-hidden="true"></span>
                        <h3 id="resume-preview-title" class="text-xs font-bold uppercase tracking-wider text-[#1A2840] truncate">
                            Auto-Generated Resume Preview &middot; {{ $resumeFilename }}
                        </h3>
                    </div>
                    <button type="button"
                            wire:click="closeResumePreviewModal"
                            class="p-1.5 rounded-lg text-gray-500 hover:text-gray-800 hover:bg-gray-200/70 transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-2 shrink-0"
                            aria-label="Close resume preview">
                        &#10005;
                    </button>
                </div>

                <!-- Resume Document Canvas (Clean Print-Friendly Digital Profile Layout) -->
                <div class="overflow-y-auto flex-1 bg-white">
                    <article id="resume-preview-printable" class="bg-white p-6 sm:p-10 space-y-8 w-full min-w-0" aria-label="{{ __('Generated Candidate Digital Profile Resume') }}">
                        {{-- Clean White Official Header --}}
                        <header class="resume-no-break break-inside-avoid flex flex-col sm:flex-row items-start justify-between gap-6 pb-6 border-b-2 border-primary-dark/20 w-full min-w-0">
                            <div class="flex flex-col sm:flex-row items-start gap-6 min-w-0 flex-1">
                                {{-- 2x2 Photo Frame --}}
                                <div class="relative w-28 h-28 sm:w-32 sm:h-32 rounded-xl border-2 border-primary-border bg-primary-tint/30 flex items-center justify-center overflow-hidden shrink-0 shadow-xs">
                                    @if($avatarPath)
                                        <img src="{{ Storage::url($avatarPath) }}"
                                             alt="{{ $resumeFullName }} 2x2 Photo"
                                             class="w-full h-full object-cover"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="flex-col items-center justify-center text-center p-2" style="display: none;">
                                            <span class="font-heading text-2xl font-bold text-primary">{{ $resumeInitials }}</span>
                                            <span class="text-[10px] uppercase font-semibold text-body-muted mt-1 tracking-wider">2x2 Photo</span>
                                        </div>
                                    @elseif($avatarUpload)
                                        <img src="{{ $avatarUpload->temporaryUrl() }}"
                                             alt="{{ $resumeFullName }} 2x2 Photo"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="flex flex-col items-center justify-center text-center p-2">
                                            <span class="font-heading text-2xl font-bold text-primary">{{ $resumeInitials }}</span>
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
                                        @if($resumeProfile?->disability_type)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary-tint text-primary-dark border border-primary-border">
                                                Category: {{ $resumeProfile->disability_type }}
                                            </span>
                                        @endif
                                    </div>

                                    <h4 class="font-heading text-2xl sm:text-3xl font-extrabold text-primary-dark tracking-tight break-words">
                                        {{ $resumeFullName }}
                                    </h4>

                                    <p class="text-sm font-semibold text-primary break-words">
                                        {{ $effectiveHeadline ?: __('Professional seeking inclusive and barrier-free opportunities') }}
                                    </p>

                                    <div class="flex flex-wrap items-center gap-y-1 gap-x-4 pt-1 text-xs text-body-muted min-w-0">
                                        @if($resumeLocation)
                                            <span class="flex items-center gap-1">
                                                <svg class="size-3.5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <span class="break-words">{{ $resumeLocation }}</span>
                                            </span>
                                        @endif

                                        @if($email || $resumeUser?->email)
                                            <span class="flex items-center gap-1 min-w-0 break-all">
                                                <svg class="size-3.5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                <span>{{ $email ?: $resumeUser?->email }}</span>
                                            </span>
                                        @endif

                                        @if($phone)
                                            <span class="flex items-center gap-1 shrink-0">
                                                <svg class="size-3.5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                <span>{{ $phone }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Document Stamp / Meta (Masked ID only) --}}
                            <div class="sm:text-right space-y-1 text-xs shrink-0 pt-2 sm:pt-0">
                                <span class="inline-block uppercase tracking-wider text-[10px] font-bold text-primary bg-primary-tint px-2.5 py-1 rounded">
                                    Digital Profile
                                </span>
                                <p class="font-mono text-[11px] text-body-muted">
                                    ID: {{ $resumeMaskedId }}
                                </p>
                                <p class="text-[11px] text-body-muted">
                                    Updated: {{ $resumeUpdatedDate }}
                                </p>
                            </div>
                        </header>

                        {{-- 1. Personal Summary & Career Objective --}}
                        @if(trim($bio) !== '')
                            <section class="resume-no-break break-inside-avoid space-y-2">
                                <h5 class="font-heading text-xs font-bold uppercase tracking-wider text-primary-dark pb-1.5 border-b border-gray-100 flex items-center gap-2">
                                    <svg class="size-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span>{{ __('Personal Summary & Career Objective') }}</span>
                                </h5>
                                <p class="text-sm text-primary-dark leading-relaxed whitespace-pre-line">
                                    {{ $bio }}
                                </p>
                            </section>
                        @endif

                        {{-- 2. Required Workplace Accommodations (RA 7277 / RA 10524) --}}
                        <section class="resume-no-break break-inside-avoid rounded-xl border border-primary-border bg-primary-tint/30 p-5 space-y-3">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <h5 class="font-heading text-xs font-bold uppercase tracking-wider text-primary-dark flex items-center gap-2">
                                    <svg class="size-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="4" r="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0l-3 6m3-6l3 6M9 9h6"/></svg>
                                    <span>{{ __('Required Workplace Accommodations (RA 7277 / RA 10524)') }}</span>
                                </h5>
                                <span class="text-[11px] font-semibold text-primary">Incentive Eligible</span>
                            </div>

                            <p class="text-xs text-body-muted">
                                {{ __('Accommodations required by candidate to perform essential job duties efficiently without physical or communication barriers:') }}
                            </p>

                            <div class="flex flex-wrap gap-2 pt-1">
                                @forelse($selectedAccommodationObjects as $accObj)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white text-primary-dark border border-primary-border shadow-2xs">
                                        <svg class="size-3.5 text-secondary shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        <span>{{ $accObj->name }}</span>
                                    </span>
                                @empty
                                    @if(trim($accommodationNotes) !== '')
                                        <span class="text-xs text-primary-dark font-medium">{{ $accommodationNotes }}</span>
                                    @else
                                        <span class="text-xs text-body-muted italic">{{ __('Standard workplace accommodations indicated upon application.') }}</span>
                                    @endif
                                @endforelse
                            </div>

                            @if(trim($accommodationNotes) !== '' && $selectedAccommodationObjects->isNotEmpty())
                                <p class="text-xs text-primary-dark pt-1">
                                    <strong>{{ __('Candidate notes:') }}</strong> {{ $accommodationNotes }}
                                </p>
                            @endif
                        </section>

                        {{-- 3. Work Experience & Employment History --}}
                        <section class="resume-no-break break-inside-avoid space-y-4">
                            <h5 class="font-heading text-xs font-bold uppercase tracking-wider text-primary-dark pb-1.5 border-b border-gray-100 flex items-center gap-2">
                                <svg class="size-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>{{ __('Work Experience & Employment History') }}</span>
                            </h5>

                            <div class="space-y-4">
                                @forelse($validExperiences as $exp)
                                    <div class="resume-no-break break-inside-avoid space-y-1 text-sm border-l-2 border-primary/40 pl-3.5 py-0.5">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <h6 class="font-bold text-primary-dark text-base">{{ $exp['job_title'] ?: 'Professional Role' }}</h6>
                                            @if($exp['start_year'] || $exp['end_year'] || !empty($exp['is_current']))
                                                <span class="text-xs text-body-muted font-medium tabular-nums">
                                                    {{ $exp['start_year'] ?: 'Start' }} &mdash; {{ !empty($exp['is_current']) ? 'Present' : ($exp['end_year'] ?: 'End') }}
                                                </span>
                                            @endif
                                        </div>
                                        @if($exp['company_name'])
                                            <p class="text-xs font-semibold text-primary">
                                                {{ $exp['company_name'] }}
                                            </p>
                                        @endif
                                        @if($exp['description'])
                                            <p class="text-xs text-body-muted leading-relaxed whitespace-pre-line pt-1">
                                                {{ $exp['description'] }}
                                            </p>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-xs text-body-muted italic">{{ __('No formal work experience added yet. Entry-level & vocational training candidate.') }}</p>
                                @endforelse
                            </div>
                        </section>

                        {{-- 4. Educational Attainment --}}
                        <section class="resume-no-break break-inside-avoid space-y-4">
                            <h5 class="font-heading text-xs font-bold uppercase tracking-wider text-primary-dark pb-1.5 border-b border-gray-100 flex items-center gap-2">
                                <svg class="size-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                                <span>{{ __('Educational Attainment') }}</span>
                            </h5>

                            <div class="space-y-3">
                                @if($highestEducation || $institutionName)
                                    <div class="resume-no-break break-inside-avoid space-y-0.5 text-sm border-l-2 border-secondary/40 pl-3.5 py-0.5">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <h6 class="font-bold text-primary-dark">{{ $highestEducation ?: __('Educational Background') }}</h6>
                                            @if($yearGraduated)
                                                <span class="text-xs text-body-muted font-medium tabular-nums">
                                                    Graduated {{ $yearGraduated }}
                                                </span>
                                            @endif
                                        </div>
                                        @if($institutionName)
                                            <p class="text-xs font-semibold text-secondary">
                                                {{ $institutionName }}
                                            </p>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-xs text-body-muted italic">{{ __('Educational background details available upon request.') }}</p>
                                @endif
                            </div>
                        </section>

                        {{-- 5. Two-Column Grid: Skills & Core Competencies + Communication & Languages --}}
                        <div class="resume-no-break break-inside-avoid grid grid-cols-1 sm:grid-cols-2 gap-6">
                            {{-- Skills & Core Competencies --}}
                            <section class="space-y-2.5">
                                <h5 class="font-heading text-xs font-bold uppercase tracking-wider text-primary-dark pb-1.5 border-b border-gray-100">
                                    {{ __('Skills & Core Competencies') }}
                                </h5>
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse($selectedSkills as $sk)
                                        <span class="px-2.5 py-1 rounded-md bg-gray-100 text-primary-dark text-xs font-medium">
                                            {{ $sk }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-body-muted italic">{{ __('Skills documented during onboarding.') }}</span>
                                    @endforelse
                                </div>
                            </section>

                            {{-- Communication & Languages --}}
                            <section class="space-y-2.5">
                                <h5 class="font-heading text-xs font-bold uppercase tracking-wider text-primary-dark pb-1.5 border-b border-gray-100">
                                    {{ __('Communication & Languages') }}
                                </h5>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($resumeLanguages as $lang)
                                        <span class="px-2.5 py-1 rounded-md bg-primary-tint/60 text-primary-dark text-xs font-medium">
                                            {{ $lang }}
                                        </span>
                                    @endforeach
                                </div>
                            </section>
                        </div>

                        {{-- 6. Legal Certification & Bottom Signature / Accreditation Footer --}}
                        <footer class="resume-no-break break-inside-avoid pt-6 border-t-2 border-primary-dark/20 space-y-6">
                            <p class="text-xs text-body-muted italic leading-relaxed text-center sm:text-left">
                                &ldquo;I hereby certify that all information stated herein is true, correct, and complete to the best of my knowledge and belief. Prepared in compliance with Republic Act No. 7277, RA 10524, and the Data Privacy Act of 2012 (RA 10173).&rdquo;
                            </p>

                            <div class="flex flex-col sm:flex-row items-center sm:items-end justify-between gap-6 pt-4 text-xs text-primary-dark w-full min-w-0">
                                <div class="space-y-1 text-center sm:text-left min-w-0 flex-1">
                                    <p class="text-[11px] text-body-muted">Accredited by PESO &amp; Republic Act No. 10524 Partner</p>
                                    <p class="font-bold text-xs">PWD-E Multimodal Employment Platform</p>
                                    <p class="text-[10px] text-body-muted">Western Mindanao State University &middot; College of Computing Studies</p>
                                </div>

                                <div class="text-center sm:text-right space-y-1 shrink-0 w-full sm:w-auto flex flex-col items-center sm:items-end">
                                    <div class="w-56 border-b border-primary-dark pb-1">
                                        <span class="font-bold uppercase tracking-wider text-xs">{{ $resumePrintName }}</span>
                                    </div>
                                    <p class="text-[10px] text-body-muted uppercase tracking-wider">Candidate Signature Over Printed Name</p>
                                    <p class="text-[10px] text-body-muted">Date: {{ now()->format('F d, Y') }}</p>
                                </div>
                            </div>
                        </footer>
                    </article>
                </div>

                <!-- Modal Footer Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3 shrink-0">
                    <button type="button"
                            wire:click="downloadResume"
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-[#244F73] text-white text-xs font-bold hover:bg-[#1A2840] transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#1A2840] focus-visible:ring-offset-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Download Resume</span>
                    </button>
                    <button type="button"
                            wire:click="closeResumePreviewModal"
                            class="px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-2">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ===================================================================== --}}
    {{-- RESUME SCAN OUTPUT REVIEW MODAL                                        --}}
    {{-- ===================================================================== --}}
    @if($showScanReviewModal && $scanSummary)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs overflow-y-auto"
             role="dialog"
             aria-modal="true"
             aria-labelledby="scan-review-modal-title">
            <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden my-8 animate-in fade-in zoom-in-95 duration-150">
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-gradient-to-r from-[#1A2840] to-[#244F73] text-white flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <h3 id="scan-review-modal-title" class="text-base font-bold text-white">Review Scanned Resume Output</h3>
                            <p class="text-xs text-white/80">Extracted from <span class="font-semibold text-white">{{ $scanSummary['filename'] }}</span></p>
                        </div>
                    </div>
                    <button type="button"
                            wire:click="closeScanReviewModal"
                            class="text-white/80 hover:text-white p-1 rounded-lg hover:bg-white/10 transition cursor-pointer"
                            aria-label="Close modal">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto text-xs text-[#1A2840]">
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start gap-3">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="leading-relaxed">
                            <strong>Your profile has been auto-built!</strong> Please review the extracted information below. You can save it immediately or click &ldquo;Review &amp; Edit in Builder&rdquo; to adjust any section before saving.
                        </p>
                    </div>

                    <!-- Personal & Title Card -->
                    <div class="p-4 rounded-xl border border-gray-200 bg-gray-50/60 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Candidate Identification</span>
                            <span class="text-[11px] text-[#244F73] font-semibold">✓ Extracted</span>
                        </div>
                        <div class="text-sm font-bold text-[#1A2840]">{{ $firstName }} {{ $middleName }} {{ $lastName }}</div>
                        @if($headline)
                            <div class="text-xs font-semibold text-[#244F73]">{{ $headline }}</div>
                        @endif
                        <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-600 pt-1">
                            @if($phone) <span>📞 {{ $phone }}</span> @endif
                            @if($email) <span>✉️ {{ $email }}</span> @endif
                            @if($city) <span>📍 {{ $barangay ? "$barangay, " : '' }}{{ $city }}</span> @endif
                        </div>
                    </div>

                    <!-- Skills Card -->
                    @if(!empty($selectedSkills))
                        <div class="p-4 rounded-xl border border-gray-200 bg-gray-50/60 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Detected Skills ({{ count($selectedSkills) }})</span>
                                <span class="text-[11px] text-[#244F73] font-semibold">✓ Auto-Tagged</span>
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($selectedSkills as $skill)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-[#DCEAF4] text-[#244F73] border border-[#244F73]/20">
                                        {{ $skill }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Work Experience Card -->
                    @if(!empty($experiences))
                        <div class="p-4 rounded-xl border border-gray-200 bg-gray-50/60 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Work Experience ({{ count($experiences) }})</span>
                                <span class="text-[11px] text-[#244F73] font-semibold">✓ Formatted</span>
                            </div>
                            <div class="space-y-2">
                                @foreach($experiences as $exp)
                                    <div class="p-2.5 rounded-lg bg-white border border-gray-200 space-y-1">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-[#1A2840]">{{ $exp['job_title'] ?? 'Role' }}</span>
                                            <span class="text-[11px] font-semibold text-[#244F73]">{{ $exp['start_year'] ?? '' }} – {{ !empty($exp['is_current']) ? 'Present' : ($exp['end_year'] ?? '') }}</span>
                                        </div>
                                        <div class="text-xs text-gray-600 font-medium">{{ $exp['company_name'] ?? 'Company' }}</div>
                                        @if(!empty($exp['description']))
                                            <p class="text-[11px] text-gray-500 leading-relaxed">{{ $exp['description'] }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Education Card -->
                    @if($institutionName || $highestEducation)
                        <div class="p-4 rounded-xl border border-gray-200 bg-gray-50/60 space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Education</span>
                                <span class="text-[11px] text-[#244F73] font-semibold">✓ Matched</span>
                            </div>
                            <div class="font-bold text-[#1A2840]">{{ $institutionName ?: 'Educational Institution' }}</div>
                            <div class="text-xs text-gray-600">{{ $highestEducation }} {{ $yearGraduated ? "· Class of $yearGraduated" : '' }}</div>
                        </div>
                    @endif

                    <!-- Accommodations Card -->
                    @if(!empty($selectedAccommodations))
                        <div class="p-4 rounded-xl border border-gray-200 bg-gray-50/60 space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Recommended Accommodations</span>
                                <span class="text-[11px] text-emerald-700 font-semibold">✓ Matched to Needs</span>
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($selectedAccommodationObjects as $acc)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        ✓ {{ $acc->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Modal Footer Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-wrap items-center justify-between gap-3">
                    <button type="button"
                            wire:click="closeScanReviewModal"
                            class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-xs font-bold text-gray-700 hover:bg-gray-100 transition focus:ring-2 focus:ring-[#244F73] cursor-pointer">
                        Review &amp; Edit in Builder
                    </button>

                    <button type="button"
                            wire:click="confirmAndSaveFromScan"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#176B47] text-white text-xs font-bold hover:bg-[#125337] transition shadow-xs focus:ring-2 focus:ring-[#176B47] cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Save &amp; Activate Profile</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

