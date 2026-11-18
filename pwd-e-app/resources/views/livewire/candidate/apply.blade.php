<div class="space-y-6">
    <div class="relative overflow-hidden rounded-2xl border border-primary-border bg-white p-6 sm:p-8 shadow-sm" style="background: linear-gradient(135deg, #DCEAF4 0%, #FFFFFF 100%);">
            <h1 class="font-heading text-2xl sm:text-3xl font-bold text-primary-dark">{{ __('Apply for') }} {{ $job->title }}</h1>
            <p class="text-sm text-body-muted mt-1">{{ $job->employerProfile?->company_name }} · {{ $job->location }}</p>
        </div>

        @if ($missingPwdId)
            <div class="rounded-2xl border border-red-200 bg-red-50 p-6 shadow-sm">
                <h2 class="font-heading text-lg font-bold text-red-700">{{ __('PWD ID required') }}</h2>
                <p class="text-sm text-body-muted mt-2">{{ __('You need a PWD ID on file before you can apply. Please complete your profile first.') }}</p>
                <a href="{{ route('candidate.profile.builder') }}" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-primary-dark px-5 py-3 text-sm font-semibold text-white hover:bg-primary">
                    {{ __('Complete profile') }}
                </a>
            </div>
        @elseif ($submitted)
            <div class="rounded-2xl border border-secondary bg-secondary-tint p-6 shadow-sm text-center">
                <div class="mx-auto mb-4 flex size-14 items-center justify-center rounded-full bg-secondary text-white">
                    <svg class="size-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h2 class="font-heading text-xl font-bold text-secondary">{{ __('Application submitted!') }}</h2>
                <p class="text-sm text-body-muted mt-2">{{ __('Your application has been received. A confirmation email has been sent.') }}</p>
                <p class="mt-4 text-sm font-semibold text-primary-dark">{{ __('Reference number') }}: <span class="tabular-nums">{{ $referenceNumber }}</span></p>
                <a href="{{ route('candidate.applications') }}" class="mt-6 inline-flex items-center gap-2 rounded-lg bg-primary-dark px-5 py-3 text-sm font-semibold text-white hover:bg-primary">
                    {{ __('View my applications') }}
                </a>
            </div>
        @else
            <form wire:submit="submit" class="rounded-2xl border border-primary-border bg-white p-6 shadow-sm space-y-5">
                <div>
                    <label for="coverLetter" class="block text-sm font-medium text-primary-dark mb-1">{{ __('Cover letter (optional)') }}</label>
                    <textarea id="coverLetter" wire:model="coverLetter" rows="6" class="w-full rounded-lg border border-primary-border p-3 text-sm" placeholder="{{ __('Tell the employer why you are a great fit...') }}"></textarea>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label for="pwdIdFile" class="block text-sm font-medium text-primary-dark mb-1">{{ __('PWD ID (optional)') }}</label>
                        <input id="pwdIdFile" type="file" wire:model="pwdIdFile" accept="image/*,.pdf" class="w-full text-sm" />
                    </div>
                    <div>
                        <label for="resumeFile" class="block text-sm font-medium text-primary-dark mb-1">{{ __('Résumé PDF (optional)') }}</label>
                        <input id="resumeFile" type="file" wire:model="resumeFile" accept=".pdf" class="w-full text-sm" />
                    </div>
                    <div>
                        <label for="fslVideoFile" class="block text-sm font-medium text-primary-dark mb-1">{{ __('FSL video (optional)') }}</label>
                        <input id="fslVideoFile" type="file" wire:model="fslVideoFile" accept="video/*" class="w-full text-sm" />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('candidate.jobs') }}" class="rounded-lg border border-primary-border bg-white px-5 py-2.5 text-sm font-semibold text-primary-dark hover:bg-primary-tint">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary-dark px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ __('Submit application') }}</span>
                        <span wire:loading>{{ __('Submitting...') }}</span>
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>