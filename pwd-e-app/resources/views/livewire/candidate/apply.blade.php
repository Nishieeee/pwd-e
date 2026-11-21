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
            {{-- Section 1: Choose Application Formats (Multi-Send Selection) --}}
            <div class="rounded-2xl border border-primary-border bg-white p-6 sm:p-8 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-gray-100 pb-4">
                    <div>
                        <h2 class="font-heading text-lg font-bold text-primary-dark">{{ __('1. Choose Application Formats') }}</h2>
                        <p class="text-xs text-body-muted mt-0.5">{{ __('Select which formats to send to the employer. You can select one, two, or all three.') }}</p>
                    </div>
                    <span class="text-xs font-semibold text-body-muted bg-surface px-3 py-1.5 rounded-full border border-primary-border">
                        {{ __('Multi-send supported') }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    {{-- Option 1: Digital Profile Card --}}
                    <div class="rounded-2xl transition-all duration-200 flex flex-col justify-between p-5 {{ $includeBiodata ? 'border-2 border-primary bg-primary-tint/20 ring-1 ring-primary/20 shadow-sm' : 'border border-gray-200 bg-gray-50/50 hover:border-gray-300' }}">
                        <div class="space-y-3.5">
                            <div class="flex items-center justify-between">
                                <span class="size-8 rounded-lg {{ $includeBiodata ? 'bg-primary text-white' : 'bg-gray-200 text-gray-600' }} flex items-center justify-center font-bold text-xs">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </span>
                                <button type="button"
                                        wire:click="toggleBiodata"
                                        class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full transition-colors {{ $includeBiodata ? 'bg-secondary text-white shadow-xs' : 'bg-white border border-gray-300 text-body-muted hover:text-primary-dark' }}"
                                        role="checkbox"
                                        aria-checked="{{ $includeBiodata ? 'true' : 'false' }}">
                                    <span>{{ $includeBiodata ? '✓ Selected' : '+ Add Option' }}</span>
                                </button>
                            </div>

                            <div>
                                <h3 class="font-bold text-sm text-primary-dark">{{ __('Digital Profile') }}</h3>
                                <p class="text-xs text-body-muted mt-1 leading-relaxed">
                                    {{ __('Comprehensive profile compiled from your credentials, skills, work history, and RA 7277 accommodation specifications.') }}
                                </p>
                            </div>

                            <div class="rounded-lg bg-white/80 p-3 border border-primary-border/60 text-xs text-body-muted space-y-1.5">
                                <div class="flex items-center gap-1.5 text-primary-dark font-semibold">
                                    <svg class="size-3.5 text-secondary shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    <span>{{ __('Zero upload required') }}</span>
                                </div>
                                <p class="text-[11px] text-body-muted">{{ __('Instantly ready from your saved profile credentials.') }}</p>
                            </div>
                        </div>

                        <div class="pt-4 mt-4 border-t border-gray-200/80 flex items-center justify-between">
                            <a href="{{ route('candidate.profile.show') }}" target="_blank"
                               class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:text-primary-dark underline">
                                <span>{{ __('Preview Profile') }}</span>
                                <svg class="size-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                            <span class="text-[11px] font-semibold text-secondary uppercase">{{ __('Recommended') }}</span>
                        </div>
                    </div>

                    {{-- Option 2: Tailored Résumé (PDF) Card --}}
                    <div class="rounded-2xl transition-all duration-200 flex flex-col justify-between p-5 {{ $includeResume ? 'border-2 border-primary bg-primary-tint/20 ring-1 ring-primary/20 shadow-sm' : 'border border-gray-200 bg-gray-50/50 hover:border-gray-300' }}">
                        <div class="space-y-3.5">
                            <div class="flex items-center justify-between">
                                <span class="size-8 rounded-lg {{ $includeResume ? 'bg-primary text-white' : 'bg-gray-200 text-gray-600' }} flex items-center justify-center font-bold text-xs">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                </span>
                                <button type="button"
                                        wire:click="toggleResume"
                                        class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full transition-colors {{ $includeResume ? 'bg-secondary text-white shadow-xs' : 'bg-white border border-gray-300 text-body-muted hover:text-primary-dark' }}"
                                        role="checkbox"
                                        aria-checked="{{ $includeResume ? 'true' : 'false' }}">
                                    <span>{{ $includeResume ? '✓ Selected' : '+ Add Option' }}</span>
                                </button>
                            </div>

                            <div>
                                <h3 class="font-bold text-sm text-primary-dark">{{ __('Tailored Résumé (PDF)') }}</h3>
                                <p class="text-xs text-body-muted mt-1 leading-relaxed">
                                    {{ __('Attach your custom formatted PDF curriculum vitae or tailored resume.') }}
                                </p>
                            </div>

                            @if($candidateProfile?->resume_pdf_path)
                                <div class="rounded-lg bg-white/80 p-3 border border-primary-border/60 text-xs text-body-muted space-y-1">
                                    <div class="flex items-center gap-1.5 text-secondary font-semibold">
                                        <svg class="size-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        <span>{{ __('Profile PDF attached') }}</span>
                                    </div>
                                    <p class="text-[11px] text-body-muted">{{ __('Will use your current saved résumé unless you upload a new one below.') }}</p>
                                </div>
                            @endif

                            @if($includeResume)
                                <div class="space-y-1">
                                    <label for="resumeFile" class="block text-[11px] font-semibold text-primary-dark">
                                        {{ $candidateProfile?->resume_pdf_path ? __('Replace with custom PDF (optional):') : __('Upload Résumé (PDF required):') }}
                                    </label>
                                    <input id="resumeFile" type="file" wire:model="resumeFile" accept=".pdf" class="w-full text-xs text-body-muted file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary-tint file:text-primary-dark hover:file:bg-primary/20" />
                                    @error('resumeFile') <span class="text-xs text-red-600 block mt-1">{{ $message }}</span> @enderror
                                </div>
                            @endif
                        </div>

                        <div class="pt-4 mt-4 border-t border-gray-200/80 flex items-center justify-between text-xs text-body-muted">
                            <span>{{ __('Format: PDF only') }}</span>
                            <span>{{ __('Max 5 MB') }}</span>
                        </div>
                    </div>

                    {{-- Option 3: FSL Video Introduction Card --}}
                    <div class="rounded-2xl transition-all duration-200 flex flex-col justify-between p-5 {{ $includeFslVideo ? 'border-2 border-secondary bg-secondary-tint/20 ring-1 ring-secondary/20 shadow-sm' : 'border border-gray-200 bg-gray-50/50 hover:border-gray-300' }}">
                        <div class="space-y-3.5">
                            <div class="flex items-center justify-between">
                                <span class="size-8 rounded-lg {{ $includeFslVideo ? 'bg-secondary text-white' : 'bg-gray-200 text-gray-600' }} flex items-center justify-center font-bold text-xs">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </span>
                                <button type="button"
                                        wire:click="toggleFslVideo"
                                        class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full transition-colors {{ $includeFslVideo ? 'bg-secondary text-white shadow-xs' : 'bg-white border border-gray-300 text-body-muted hover:text-primary-dark' }}"
                                        role="checkbox"
                                        aria-checked="{{ $includeFslVideo ? 'true' : 'false' }}">
                                    <span>{{ $includeFslVideo ? '✓ Selected' : '+ Add Option' }}</span>
                                </button>
                            </div>

                            <div>
                                <div class="flex items-center gap-1.5">
                                    <h3 class="font-bold text-sm text-primary-dark">{{ __('FSL Video Pitch') }}</h3>
                                    <span class="text-[10px] bg-secondary-tint text-secondary font-bold px-1.5 py-0.5 rounded">RA 11106</span>
                                </div>
                                <p class="text-xs text-body-muted mt-1 leading-relaxed">
                                    {{ __('Filipino Sign Language video self-introduction, giving Deaf candidates a direct voice in the hiring process.') }}
                                </p>
                            </div>

                            @if($candidateProfile?->fsl_video_path)
                                <div class="rounded-lg bg-white/80 p-3 border border-primary-border/60 text-xs text-body-muted space-y-1">
                                    <div class="flex items-center gap-1.5 text-secondary font-semibold">
                                        <svg class="size-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        <span>{{ __('Profile FSL video attached') }}</span>
                                    </div>
                                    <p class="text-[11px] text-body-muted">{{ __('Will use your saved profile pitch unless you upload a new video below.') }}</p>
                                </div>
                            @endif

                            @if($includeFslVideo)
                                <div class="space-y-1">
                                    <label for="fslVideoFile" class="block text-[11px] font-semibold text-primary-dark">
                                        {{ $candidateProfile?->fsl_video_path ? __('Replace with new FSL clip (optional):') : __('Upload FSL clip (required):') }}
                                    </label>
                                    <input id="fslVideoFile" type="file" wire:model="fslVideoFile" accept="video/mp4,video/webm,video/quicktime" class="w-full text-xs text-body-muted file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary-tint file:text-primary-dark hover:file:bg-primary/20" />
                                    @error('fslVideoFile') <span class="text-xs text-red-600 block mt-1">{{ $message }}</span> @enderror
                                </div>
                            @endif
                        </div>

                        <div class="pt-4 mt-4 border-t border-gray-200/80 flex items-center justify-between text-xs text-body-muted">
                            <span>{{ __('MP4, WebM, MOV') }}</span>
                            <span>{{ __('Max 50 MB') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Live Selection Feedback Bar --}}
                <div class="rounded-xl border border-primary-border bg-surface p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-0.5">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-primary-dark uppercase tracking-wider">{{ __('Attached to this Application:') }}</span>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ ($includeBiodata ? 1 : 0) + ($includeResume ? 1 : 0) + ($includeFslVideo ? 1 : 0) > 0 ? 'bg-secondary text-white' : 'bg-red-100 text-red-700' }}">
                                {{ ($includeBiodata ? 1 : 0) + ($includeResume ? 1 : 0) + ($includeFslVideo ? 1 : 0) }} of 3 Selected
                            </span>
                        </div>
                        <p class="text-xs text-body-muted">
                            {{ __('The employer will receive and review all selected formats upon submission.') }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @if($includeBiodata)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-white text-primary-dark border border-primary-border shadow-xs">
                                <span class="text-secondary font-black">✓</span> {{ __('Digital Profile') }}
                            </span>
                        @endif
                        @if($includeResume)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-white text-primary-dark border border-primary-border shadow-xs">
                                <span class="text-secondary font-black">✓</span> {{ __('Résumé (PDF)') }}
                            </span>
                        @endif
                        @if($includeFslVideo)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-white text-primary-dark border border-primary-border shadow-xs">
                                <span class="text-secondary font-black">✓</span> {{ __('FSL Video') }}
                            </span>
                        @endif
                    </div>
                </div>

                @error('materials')
                    <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-xs font-semibold text-red-700 flex items-center gap-2">
                        <svg class="size-4 shrink-0 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span>{{ $message }}</span>
                    </div>
                @enderror

                {{-- Compact Verified PWD ID Strip --}}
                <div class="rounded-xl border border-gray-200 bg-gray-50/60 p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="size-5 rounded-full bg-secondary text-white flex items-center justify-center font-bold text-xs shrink-0">✓</span>
                        <span class="text-body-muted">
                            {{ __('Government PWD ID:') }}
                            <strong class="text-primary-dark font-mono font-bold">{{ $user->pwd_id_number }}</strong>
                            <span class="text-secondary font-semibold ml-1">({{ __('Verified under RA 7277') }})</span>
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <label for="pwdIdFile" class="text-xs text-body-muted hover:text-primary-dark cursor-pointer font-medium">
                            {{ __('Update card scan (optional):') }}
                        </label>
                        <input id="pwdIdFile" type="file" wire:model="pwdIdFile" accept="image/*,.pdf" class="text-xs text-body-muted file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[11px] file:font-semibold file:bg-white file:border file:border-gray-200 file:text-primary-dark" />
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
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h2 class="font-heading text-lg font-bold text-primary-dark">{{ __('2. Cover Letter & Accommodation Note') }}</h2>
                        <p class="text-xs text-body-muted mt-0.5">{{ __('Choose to write your introductory note in the portal or upload a formatted PDF cover letter.') }}</p>
                    </div>

                    {{-- Mode Segmented Switch --}}
                    <div class="inline-flex rounded-xl border border-primary-border bg-surface p-1 shrink-0" role="tablist" aria-label="{{ __('Cover letter submission mode') }}">
                        <button type="button"
                                wire:click="setCoverLetterType('text')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $coverLetterType === 'text' ? 'bg-primary-dark text-white shadow-2xs' : 'text-primary-dark hover:bg-white/80' }}"
                                role="tab"
                                aria-selected="{{ $coverLetterType === 'text' ? 'true' : 'false' }}">
                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>{{ __('Write Note in Portal') }}</span>
                        </button>
                        <button type="button"
                                wire:click="setCoverLetterType('upload')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $coverLetterType === 'upload' ? 'bg-primary-dark text-white shadow-2xs' : 'text-primary-dark hover:bg-white/80' }}"
                                role="tab"
                                aria-selected="{{ $coverLetterType === 'upload' ? 'true' : 'false' }}">
                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <span>{{ __('Upload PDF Document') }}</span>
                        </button>
                    </div>
                </div>

                @if($coverLetterType === 'text')
                    {{-- Free-Text Portal Note with Predictive Text & Voice Dictation --}}
                    <div class="space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            {{-- Low-Typing Starter Chips --}}
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-xs font-semibold text-body-muted">{{ __('Quick insert:') }}</span>
                                <button type="button" wire:click="appendPhrase('I am excited to apply for this role and contribute to your inclusive workplace.')" class="text-xs bg-surface hover:bg-primary-tint border border-primary-border text-primary-dark px-3.5 py-1.5 rounded-full inline-flex items-center transition-colors">
                                    + {{ __('Excited to apply') }}
                                </button>
                                <button type="button" wire:click="appendPhrase('My experience aligns well with the stated requirements and technical qualifications.')" class="text-xs bg-surface hover:bg-primary-tint border border-primary-border text-primary-dark px-3.5 py-1.5 rounded-full inline-flex items-center transition-colors">
                                    + {{ __('Qualifications match') }}
                                </button>
                                <button type="button" wire:click="appendPhrase('I utilize the accommodations verified in this listing and appreciate your barrier-free workplace.')" class="text-xs bg-surface hover:bg-primary-tint border border-primary-border text-primary-dark px-3.5 py-1.5 rounded-full inline-flex items-center transition-colors">
                                    + {{ __('Accommodations note') }}
                                </button>
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
                @else
                    {{-- Upload PDF Cover Letter --}}
                    <div class="rounded-xl border border-dashed border-primary-border bg-surface/60 p-6 text-center space-y-4">
                        @if ($coverLetterFile)
                            <div class="flex items-center justify-between p-3.5 rounded-xl bg-white border border-emerald-200 shadow-2xs max-w-lg mx-auto">
                                <div class="flex items-center gap-3">
                                    <div class="size-9 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs">
                                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div class="text-left">
                                        <div class="text-xs font-bold text-primary-dark truncate max-w-xs">{{ $coverLetterFile->getClientOriginalName() }}</div>
                                        <div class="text-[11px] text-body-muted">{{ number_format($coverLetterFile->getSize() / 1024, 1) }} KB · {{ __('PDF Cover Letter attached') }}</div>
                                    </div>
                                </div>
                                <button type="button" wire:click="removeCoverLetterFile" class="text-xs font-semibold text-red-600 hover:text-red-800 px-2.5 py-1 rounded-md hover:bg-red-50 transition-colors">
                                    {{ __('Remove') }}
                                </button>
                            </div>
                        @else
                            <div class="max-w-md mx-auto space-y-2">
                                <div class="size-12 rounded-full bg-primary-tint text-primary flex items-center justify-center mx-auto mb-2">
                                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                </div>
                                <div class="text-xs font-bold text-primary-dark">
                                    <label for="coverLetterFileInput" class="text-primary hover:underline cursor-pointer">
                                        <span>{{ __('Click to select PDF document') }}</span>
                                        <input id="coverLetterFileInput" type="file" wire:model="coverLetterFile" accept="application/pdf" class="sr-only" />
                                    </label>
                                    <span class="text-body-muted font-normal"> {{ __('or drag and drop') }}</span>
                                </div>
                                <p class="text-[11px] text-body-muted">{{ __('PDF files only, up to 5MB. Screen-reader accessible tagged PDFs are recommended.') }}</p>
                                <div wire:loading wire:target="coverLetterFile" class="text-xs text-primary font-semibold">
                                    {{ __('Uploading PDF cover letter...') }}
                                </div>
                            </div>
                        @endif
                        @error('coverLetterFile') <span class="text-xs text-red-600 block mt-1">{{ $message }}</span> @enderror
                    </div>
                @endif
            </div>

            {{-- Action Buttons Bar --}}
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 pt-2">
                <a href="{{ route('candidate.jobs') }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-primary-border bg-white px-5 py-2.5 text-sm font-semibold text-primary-dark hover:bg-primary-tint transition-colors">
                    <span aria-hidden="true">&larr;</span>
                    {{ __('Back to Job Matches') }}
                </a>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <div class="text-xs text-body-muted text-right hidden sm:block">
                        <span>{{ __('Attaching:') }}</span>
                        <strong class="text-primary-dark font-semibold">
                            {{ implode(', ', array_filter([$includeBiodata ? 'Digital Profile' : null, $includeResume ? 'Résumé' : null, $includeFslVideo ? 'FSL Pitch' : null])) ?: 'None selected' }}
                        </strong>
                    </div>

                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary-dark px-7 py-3 text-sm font-bold text-white hover:bg-primary shadow-sm transition-colors focus:ring-2 focus:ring-primary min-w-[190px] disabled:opacity-50 disabled:cursor-not-allowed"
                            wire:loading.attr="disabled"
                            @disabled(! $includeBiodata && ! $includeResume && ! $includeFslVideo)>
                        <span wire:loading.remove>{{ __('Submit Application') }} &rarr;</span>
                        <span wire:loading class="inline-flex items-center gap-2">
                            <svg class="animate-spin size-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            {{ __('Submitting...') }}
                        </span>
                    </button>
                </div>
            </div>
        </form>
    @endif
</div>
