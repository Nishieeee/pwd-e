<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header Banner --}}
    <div class="relative overflow-hidden rounded-2xl border border-primary-border bg-white p-6 sm:p-8 shadow-sm" style="background: linear-gradient(135deg, #DCEAF4 0%, #FFFFFF 100%);">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1 text-xs font-bold text-secondary bg-secondary-tint px-2.5 py-0.5 rounded-full">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        {{ __('RA 10524 Inclusive Opportunity') }}
                    </span>
                    <span class="text-xs text-body-muted">{{ $job->employment_type ? str_replace('_', ' ', $job->employment_type) : 'Full-time' }}</span>
                </div>
                <h1 class="font-heading text-2xl sm:text-3xl font-bold text-primary-dark">{{ __('Apply for') }} {{ $job->title }}</h1>
                <p class="text-sm text-body-muted mt-1">{{ $job->employerProfile?->company_name ?? 'Verified Employer' }} · {{ $job->location }}</p>
            </div>
            @if($job->accommodations->isNotEmpty())
            <div class="sm:text-right shrink-0">
                <span class="text-xs text-body-muted block mb-1">{{ __('Verified Accommodations') }}</span>
                <div class="flex flex-wrap sm:justify-end gap-1.5 max-w-xs">
                    @foreach($job->accommodations->take(3) as $acc)
                        <span class="text-xs font-semibold bg-white border border-primary-border text-primary-dark px-2.5 py-1 rounded-full">✓ {{ $acc->name }}</span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- State A: Missing PWD ID Gate --}}
    @if ($missingPwdId)
        <div class="rounded-2xl border border-amber-300 bg-amber-50 p-6 sm:p-8 shadow-sm space-y-4" role="alert">
            <div class="flex items-start gap-4">
                <div class="size-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div class="space-y-2 flex-1">
                    <h2 class="font-heading text-lg font-bold text-amber-900">{{ __('PWD Identification Required') }}</h2>
                    <p class="text-sm text-amber-800 leading-relaxed">
                        {{ __('Under Republic Act No. 10524, a valid Person with Disability (PWD) ID number is required to submit job applications. This ensures legal affirmative action benefits, equal opportunity accreditation, and employer statutory tax deductions.') }}
                    </p>
                    <p class="text-xs text-amber-700">
                        {{ __('Please update your profile with your PWD ID details to proceed with your application.') }}
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('candidate.profile.builder', ['step' => 4]) }}" class="inline-flex items-center gap-2 rounded-lg bg-primary-dark px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary transition-colors focus:ring-2 focus:ring-primary">
                            <span>{{ __('Add PWD ID to Profile') }}</span>
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    {{-- State B: Already Applied or Submitted Confirmation --}}
    @elseif ($submitted || $alreadyApplied)
        <div class="rounded-2xl border border-secondary bg-white p-6 sm:p-10 shadow-sm text-center space-y-6">
            <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-secondary text-white shadow-md">
                <svg class="size-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>

            <div class="space-y-2 max-w-lg mx-auto">
                <h2 class="font-heading text-2xl font-bold text-primary-dark">
                    {{ $alreadyApplied && !$submitted ? __('Application on File') : __('Application Submitted Successfully!') }}
                </h2>
                <p class="text-sm text-body-muted leading-relaxed">
                    {{ __('Your verified profile, accommodations statement, and credentials have been securely transmitted to') }}
                    <strong class="text-primary-dark">{{ $job->employerProfile?->company_name ?? 'the employer' }}</strong>.
                </p>
            </div>

            {{-- Reference Number Card --}}
            <div class="inline-block rounded-xl border border-primary-border bg-surface p-5 text-center min-w-[280px]" x-data="{ copied: false }">
                <span class="text-xs text-body-muted block uppercase tracking-wider font-semibold">{{ __('Application Reference Number') }}</span>
                <div class="font-mono text-2xl font-bold text-primary-dark tracking-wider my-1 tabular-nums">
                    {{ $formattedReference ?? ('PWD-APP-' . date('Y') . '-' . str_pad((string) $referenceNumber, 5, '0', STR_PAD_LEFT)) }}
                </div>
                <div class="flex items-center justify-center gap-3 mt-3">
                    <button type="button"
                            @click="navigator.clipboard.writeText('{{ $formattedReference }}'); copied = true; setTimeout(() => copied = false, 2500)"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary hover:text-primary-dark transition-colors px-3 py-1.5 rounded-md border border-primary-border bg-white shadow-xs">
                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span x-text="copied ? 'Copied to Clipboard!' : 'Copy Reference'"></span>
                    </button>
                    <button type="button"
                            onclick="window.print()"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-body-muted hover:text-primary-dark transition-colors px-3 py-1.5 rounded-md border border-gray-200 bg-white shadow-xs">
                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Print Receipt</span>
                    </button>
                </div>
            </div>

            {{-- Next Steps Timeline --}}
            <div class="max-w-md mx-auto text-left border-t border-gray-100 pt-6 space-y-3">
                <span class="text-xs font-bold uppercase tracking-wider text-primary-dark block">{{ __('What Happens Next') }}</span>
                <ol class="space-y-2 text-xs text-body-muted">
                    <li class="flex items-start gap-2.5">
                        <span class="size-5 rounded-full bg-secondary text-white flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">✓</span>
                        <span><strong>Application Logged:</strong> Fit score recorded under RA 10524 guidelines.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="size-5 rounded-full bg-primary-tint text-primary-dark flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">2</span>
                        <span><strong>Employer Review:</strong> Typically reviewed within 3–5 business days.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="size-5 rounded-full bg-primary-tint text-primary-dark flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">3</span>
                        <span><strong>Interview Scheduling:</strong> You will be notified in-portal and via email when shortlisted.</span>
                    </li>
                </ol>
            </div>

            <div class="pt-4 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('candidate.applications') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary-dark px-6 py-3 text-sm font-semibold text-white hover:bg-primary shadow-sm transition-colors">
                    {{ __('Track in Applications') }}
                    <span aria-hidden="true">&rarr;</span>
                </a>
                <a href="{{ route('candidate.jobs') }}" class="inline-flex items-center gap-2 rounded-lg border border-primary-border bg-white px-6 py-3 text-sm font-semibold text-primary-dark hover:bg-primary-tint transition-colors">
                    {{ __('Browse More Jobs') }}
                </a>
            </div>
        </div>

    {{-- State C: Application Form --}}
    @else
        <form wire:submit="submit" class="space-y-6">
            {{-- Section 1: Verified Profile Credentials (Low-Typing Design) --}}
            <div class="rounded-2xl border border-primary-border bg-white p-6 sm:p-8 shadow-sm space-y-5">
                <div>
                    <h2 class="font-heading text-lg font-bold text-primary-dark">{{ __('1. Verified Profile & Documents') }}</h2>
                    <p class="text-xs text-body-muted mt-1">{{ __('These documents will be securely attached to your application. You can use your saved profile records or supply custom files.') }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Digital Bio-Data / Profile Resume Card --}}
                    <div class="rounded-xl border border-primary-border p-4 bg-surface space-y-3 flex flex-col justify-between">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-primary-dark">{{ __('Digital Bio-Data') }}</span>
                                <span class="text-xs font-semibold text-secondary bg-secondary-tint px-2.5 py-0.5 rounded-full">{{ __('Active') }}</span>
                            </div>
                            <div class="flex items-start gap-2 text-xs text-body-muted">
                                <svg class="size-4 text-secondary shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>{{ __('Auto-generated from your profile credentials and accommodations.') }}</span>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-gray-200">
                            <a href="{{ route('candidate.profile.show') }}" target="_blank"
                               class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary hover:underline">
                                <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                <span>Preview Bio-Data</span>
                            </a>
                        </div>
                    </div>

                    {{-- PWD ID Verification Card --}}
                    <div class="rounded-xl border border-primary-border p-4 bg-surface space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-primary-dark">{{ __('Government PWD ID') }}</span>
                            <span class="text-xs font-semibold text-secondary bg-secondary-tint px-2.5 py-0.5 rounded-full">{{ __('Verified') }}</span>
                        </div>

                        <div class="flex items-start gap-2 text-xs text-body-muted">
                            <svg class="size-4 text-secondary shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>ID: <strong class="text-primary-dark font-mono">{{ $user->pwd_id_number }}</strong></span>
                        </div>

                        <div>
                            <label for="pwdIdFile" class="block text-xs font-medium text-body-muted mb-1">{{ __('Update card scan (optional)') }}</label>
                            <input id="pwdIdFile" type="file" wire:model="pwdIdFile" accept="image/*,.pdf" class="w-full text-xs text-body-muted file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary-tint file:text-primary-dark hover:file:bg-primary/20" />
                            @error('pwdIdFile') <span class="text-xs text-red-600 block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- FSL Video Card --}}
                    <div class="rounded-xl border border-primary-border p-4 bg-surface space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-primary-dark">{{ __('FSL Video Introduction') }}</span>
                            <span class="text-xs font-semibold text-primary bg-primary-tint px-2.5 py-0.5 rounded-full">{{ __('Optional') }}</span>
                        </div>

                        @if($candidateProfile?->fsl_video_path)
                            <div class="flex items-start gap-2 text-xs text-body-muted">
                                <svg class="size-4 text-secondary shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>{{ __('Profile FSL video attached') }}</span>
                            </div>
                            <label class="flex items-center gap-2 text-xs text-primary-dark cursor-pointer">
                                <input type="checkbox" wire:model.live="useProfileFsl" class="rounded text-primary focus:ring-primary" />
                                <span>{{ __('Include profile FSL video') }}</span>
                            </label>
                        @endif

                        <div>
                            <label for="fslVideoFile" class="block text-xs font-medium text-body-muted mb-1">{{ __('Upload new FSL clip (max 50MB)') }}</label>
                            <input id="fslVideoFile" type="file" wire:model="fslVideoFile" accept="video/*" class="w-full text-xs text-body-muted file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary-tint file:text-primary-dark hover:file:bg-primary/20" />
                            @error('fslVideoFile') <span class="text-xs text-red-600 block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 2: Cover Letter with Assistive Voice & Predictive Input --}}
            <div class="rounded-2xl border border-primary-border bg-white p-6 sm:p-8 shadow-sm space-y-4"
                 x-data="{
                     listening: false,
                     recognition: null,
                     hasSpeech: 'webkitSpeechRecognition' in window || 'SpeechRecognition' in window,
                     initSpeech() {
                         if (!this.hasSpeech) return;
                         const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
                         this.recognition = new SpeechRec();
                         this.recognition.continuous = true;
                         this.recognition.interimResults = false;
                         this.recognition.lang = 'en-PH';
                         this.recognition.onresult = (e) => {
                             let transcript = '';
                             for (let i = e.resultIndex; i < e.results.length; ++i) {
                                 transcript += e.results[i][0].transcript;
                             }
                             $wire.appendPhrase(transcript);
                         };
                         this.recognition.onerror = () => { this.listening = false; };
                         this.recognition.onend = () => { this.listening = false; };
                     },
                     toggleSpeech() {
                         if (!this.hasSpeech) return;
                         if (!this.recognition) this.initSpeech();
                         if (this.listening) {
                             this.recognition.stop();
                             this.listening = false;
                         } else {
                             this.recognition.start();
                             this.listening = true;
                         }
                     }
                 }">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h2 class="font-heading text-lg font-bold text-primary-dark">{{ __('2. Cover Letter & Accommodation Note') }}</h2>
                        <p class="text-xs text-body-muted mt-0.5">{{ __('Share any specific workplace accommodation requests or introductory notes with the employer.') }}</p>
                    </div>

                    {{-- Voice Dictation Toggle (Web Speech API) --}}
                    <template x-if="hasSpeech">
                        <button type="button"
                                @click="toggleSpeech()"
                                :class="listening ? 'bg-red-600 text-white animate-pulse' : 'bg-primary-tint text-primary-dark hover:bg-primary/20'"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors focus:ring-2 focus:ring-primary shrink-0"
                                :aria-label="listening ? 'Stop voice dictation' : 'Start voice dictation'">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                            <span x-text="listening ? 'Listening... (Click to Stop)' : 'Voice Dictation'"></span>
                        </button>
                    </template>
                </div>

                {{-- Low-Typing Starter Chips --}}
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <span class="text-xs font-semibold text-body-muted">{{ __('Quick insert:') }}</span>
                    <button type="button" wire:click="appendPhrase('I am excited to apply for this role and contribute to your inclusive workplace.')" class="text-xs bg-surface hover:bg-primary-tint border border-primary-border text-primary-dark px-3.5 py-2 rounded-full min-h-[44px] inline-flex items-center transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                        + {{ __('Excited to apply') }}
                    </button>
                    <button type="button" wire:click="appendPhrase('My experience aligns well with the stated requirements and technical qualifications.')" class="text-xs bg-surface hover:bg-primary-tint border border-primary-border text-primary-dark px-3.5 py-2 rounded-full min-h-[44px] inline-flex items-center transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                        + {{ __('Qualifications match') }}
                    </button>
                    <button type="button" wire:click="appendPhrase('I utilize the accommodations verified in this listing and appreciate your barrier-free workplace.')" class="text-xs bg-surface hover:bg-primary-tint border border-primary-border text-primary-dark px-3.5 py-2 rounded-full min-h-[44px] inline-flex items-center transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                        + {{ __('Accommodations note') }}
                    </button>
                </div>

                {{-- Free-text Textarea with Predictive Text engine --}}
                <div>
                    <label for="coverLetter" class="sr-only">{{ __('Cover Letter') }}</label>
                    <textarea id="coverLetter"
                              wire:model="coverLetter"
                              rows="6"
                              class="w-full rounded-xl border border-primary-border p-3.5 text-sm text-primary-dark bg-white focus:outline-none focus:ring-2 focus:ring-primary leading-relaxed"
                              placeholder="{{ __('Write your note here... (Tip: type to see predictive word suggestions or use voice dictation)') }}"></textarea>
                    @error('coverLetter') <span class="text-xs text-red-600 block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Action Buttons Bar --}}
            <div class="flex items-center justify-between gap-4 pt-2">
                <a href="{{ route('candidate.jobs') }}" class="inline-flex items-center gap-2 rounded-lg border border-primary-border bg-white px-5 py-2.5 text-sm font-semibold text-primary-dark hover:bg-primary-tint transition-colors">
                    <span aria-hidden="true">&larr;</span>
                    {{ __('Back to Job Matches') }}
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary-dark px-7 py-3 text-sm font-bold text-white hover:bg-primary shadow-sm transition-colors focus:ring-2 focus:ring-primary min-w-[180px]"
                        wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Submit Application') }} &rarr;</span>
                    <span wire:loading class="inline-flex items-center gap-2">
                        <svg class="animate-spin size-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        {{ __('Submitting...') }}
                    </span>
                </button>
            </div>
        </form>
    @endif
</div>
