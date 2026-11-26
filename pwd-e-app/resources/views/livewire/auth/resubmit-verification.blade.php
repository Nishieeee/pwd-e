<div wire:poll.8s="checkStatus" class="space-y-4">
    @if ($resubmitted && $status === 'pending')
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-xs text-emerald-900 font-medium flex items-start gap-2.5" role="status">
            <svg class="size-4 text-emerald-700 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <div>
                <p class="font-bold">{{ __('Documents Submitted for Re-evaluation') }}</p>
                <p class="mt-0.5 text-emerald-800">{{ __('Your updated verification file(s) have been queued. Our PESO / LGU administrators will review them shortly.') }}</p>
            </div>
        </div>
    @endif

    @if ($status === 'rejected')
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-900" role="alert">
            <div class="flex items-center gap-2 font-bold text-red-800">
                <svg class="size-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <span>{{ __('Status: Verification Rejected — Action Required') }}</span>
            </div>
            @if ($reason)
                <div class="mt-2.5 text-xs text-red-800 bg-white/90 p-3 rounded-lg border border-red-200">
                    <span class="font-bold">{{ __('Administrator Feedback:') }}</span> {{ $reason }}
                </div>
            @endif
            <p class="mt-2 text-xs text-red-700">{{ __('Please review the administrator feedback above and upload a valid, clear copy of your PWD ID card and/or Clinical Medical Certificate below.') }}</p>
        </div>
    @elseif ($status === 'needs_more_docs')
        <div class="rounded-xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-950 shadow-2xs" role="alert">
            <div class="flex items-center gap-2 font-bold text-amber-900">
                <svg class="size-4 shrink-0 text-amber-700" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <span>{{ __('Action Required: Additional Document or Clearer Copy Requested') }}</span>
            </div>
            <div class="mt-2.5 text-xs text-amber-900 bg-white/90 p-3 rounded-lg border border-amber-200">
                <span class="font-bold">{{ __('Administrator Request:') }}</span>
                <span>{{ $reason ?: __('Please upload a clearer copy of your PWD ID card or Clinical Medical Certificate to complete verification.') }}</span>
            </div>
            <p class="mt-2 text-xs text-amber-800">{{ __('Use the form below to re-upload a clearer scan/photo or attach an additional credential.') }}</p>
        </div>
    @else
        <div class="rounded-xl border border-primary-border bg-primary-tint px-4 py-3.5 text-sm text-primary-dark space-y-2.5" role="status" aria-live="polite">
            <div class="flex items-center gap-2 font-bold">
                <svg class="size-4 animate-spin text-primary shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                <span>{{ __('Status: Pending Review (Under RA 10524 Verification)') }}</span>
            </div>
            <p class="text-xs text-body-muted">
                {{ __('Your registration documents are queued with the PESO / LGU administrator. If the administrator requests additional documents or a clearer copy, instructions and an upload form will appear right here automatically.') }}
            </p>
            @if ($user?->isCandidate())
                <div class="flex flex-wrap items-center justify-between gap-2 pt-1 border-t border-primary-border/60 text-xs">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="text-body-muted font-medium">{{ __('On file:') }}</span>
                        @if ($user->pwd_id_path)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white border border-primary-border text-[11px] font-semibold text-primary-dark">🪪 {{ __('PWD ID Attached') }}</span>
                        @endif
                        @if ($user->medical_certificate_path)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white border border-primary-border text-[11px] font-semibold text-primary-dark">🩺 {{ __('Medical Cert Attached') }}</span>
                        @endif
                        @if (! $user->pwd_id_path && ! $user->medical_certificate_path)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 border border-amber-200 text-[11px] font-semibold text-amber-800">{{ __('No document scan uploaded yet') }}</span>
                        @endif
                    </div>
                    <button type="button" wire:click="toggleProactiveUpload" class="text-xs font-bold text-primary hover:underline cursor-pointer">
                        {{ $showProactiveUpload ? __('Hide upload form') : __('Upload / replace document') }}
                    </button>
                </div>
            @endif
        </div>
    @endif

    @if (in_array($status, ['needs_more_docs', 'rejected'], true) || $showProactiveUpload)
        <div class="rounded-xl border border-primary-border bg-white p-5 shadow-xs space-y-4">
            <div class="border-b border-primary-border pb-3">
                <h3 class="font-bold text-sm text-primary-dark flex items-center gap-2">
                    <svg class="size-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span>{{ in_array($status, ['needs_more_docs', 'rejected'], true) ? __('Submit Requested Verification Document(s)') : __('Upload or Replace Verification Document(s)') }}</span>
                </h3>
                <p class="text-xs text-body-muted mt-1">
                    {{ __('Select the credential type you are submitting and attach a clear JPG, PNG, or PDF (max 5MB).') }}
                </p>
            </div>

            <form wire:submit="submit" class="space-y-4 text-xs">
                @if ($user?->isCandidate())
                    <div>
                        <label class="block font-bold text-primary-dark mb-1.5">{{ __('Credential to Submit') }}</label>
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                            <label class="flex items-start gap-2 rounded-lg border p-2.5 cursor-pointer transition-colors {{ $documentType === 'pwd_id' ? 'border-primary bg-primary/5 ring-1 ring-primary' : 'border-primary-border hover:bg-slate-50' }}">
                                <input type="radio" wire:model.live="documentType" value="pwd_id" class="mt-0.5 text-primary focus:ring-primary">
                                <div>
                                    <div class="font-bold text-primary-dark">{{ __('PWD ID Card') }}</div>
                                    <div class="text-[10px] text-body-muted">{{ __('Official LGU PWD ID') }}</div>
                                </div>
                            </label>
                            <label class="flex items-start gap-2 rounded-lg border p-2.5 cursor-pointer transition-colors {{ $documentType === 'medical_certificate' ? 'border-primary bg-primary/5 ring-1 ring-primary' : 'border-primary-border hover:bg-slate-50' }}">
                                <input type="radio" wire:model.live="documentType" value="medical_certificate" class="mt-0.5 text-primary focus:ring-primary">
                                <div>
                                    <div class="font-bold text-primary-dark">{{ __('Medical Cert') }}</div>
                                    <div class="text-[10px] text-body-muted">{{ __('Fit-to-Work / Clinical') }}</div>
                                </div>
                            </label>
                            <label class="flex items-start gap-2 rounded-lg border p-2.5 cursor-pointer transition-colors {{ $documentType === 'both' ? 'border-primary bg-primary/5 ring-1 ring-primary' : 'border-primary-border hover:bg-slate-50' }}">
                                <input type="radio" wire:model.live="documentType" value="both" class="mt-0.5 text-primary focus:ring-primary">
                                <div>
                                    <div class="font-bold text-primary-dark">{{ __('Both Credentials') }}</div>
                                    <div class="text-[10px] text-body-muted">{{ __('PWD ID + Medical Cert') }}</div>
                                </div>
                            </label>
                        </div>
                        @error('documentType') <span class="text-red-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-3 {{ $documentType === 'both' ? 'sm:grid-cols-2' : '' }}">
                        <div>
                            <label class="block font-bold text-primary-dark mb-1">
                                {{ $documentType === 'medical_certificate' ? __('Medical Certificate / PRC Ref. No.') : __('PWD ID Card Number') }}
                            </label>
                            <div class="relative">
                                <input type="text" wire:model="idNumber" class="w-full rounded-lg border border-primary-border px-3 py-2 pr-8 text-xs focus:ring-2 focus:ring-primary focus:outline-none" placeholder="{{ $documentType === 'medical_certificate' ? 'e.g. MED-2026-001 / PRC-123456' : 'e.g. 13-7404-000-0012345' }}" />
                            </div>
                            @error('idNumber') <span class="text-red-600 text-[11px] mt-0.5 block">{{ $message }}</span> @enderror
                        </div>

                        @if ($documentType === 'both')
                            <div>
                                <label class="block font-bold text-primary-dark mb-1">
                                    {{ __('Med Cert / PRC Ref. No. (Optional)') }}
                                </label>
                                <div class="relative">
                                    <input type="text" wire:model="medicalCertificateNumber" class="w-full rounded-lg border border-primary-border px-3 py-2 pr-8 text-xs focus:ring-2 focus:ring-primary focus:outline-none" placeholder="e.g. MED-2026-001 / PRC-123456" />
                                </div>
                                @error('medicalCertificateNumber') <span class="text-red-600 text-[11px] mt-0.5 block">{{ $message }}</span> @enderror
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 gap-3 {{ $documentType === 'both' ? 'sm:grid-cols-2' : '' }}">
                        @if ($documentType === 'pwd_id' || $documentType === 'both')
                            <div>
                                <label class="block font-bold text-primary-dark mb-1">
                                    {{ __('Upload Clear Copy of PWD ID (JPG, PNG, PDF)') }}
                                </label>
                                <input type="file" wire:model="documentFile" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-xs text-body-muted file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-tint file:text-primary-dark hover:file:bg-primary/20 cursor-pointer" />
                                @error('documentFile') <span class="text-red-600 text-[11px] mt-0.5 block">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        @if ($documentType === 'medical_certificate')
                            <div>
                                <label class="block font-bold text-primary-dark mb-1">
                                    {{ __('Upload Clear Copy of Medical Certificate (JPG, PNG, PDF)') }}
                                </label>
                                <input type="file" wire:model="documentFile" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-xs text-body-muted file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-tint file:text-primary-dark hover:file:bg-primary/20 cursor-pointer" />
                                @error('documentFile') <span class="text-red-600 text-[11px] mt-0.5 block">{{ $message }}</span> @enderror
                            </div>
                        @elseif ($documentType === 'both')
                            <div>
                                <label class="block font-bold text-primary-dark mb-1">
                                    {{ __('Upload Clear Copy of Medical Certificate (JPG, PNG, PDF)') }}
                                </label>
                                <input type="file" wire:model="secondaryDocumentFile" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-xs text-body-muted file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-tint file:text-primary-dark hover:file:bg-primary/20 cursor-pointer" />
                                @error('secondaryDocumentFile') <span class="text-red-600 text-[11px] mt-0.5 block">{{ $message }}</span> @enderror
                            </div>
                        @endif
                    </div>
                @else
                    <div>
                        <label class="block font-bold text-primary-dark mb-1">{{ __('Business Permit / Registration Number') }}</label>
                        <div class="relative">
                            <input type="text" wire:model="idNumber" class="w-full rounded-lg border border-primary-border px-3 py-2 pr-8 text-xs focus:ring-2 focus:ring-primary focus:outline-none" placeholder="e.g. BP-2026-9876" />
                        </div>
                        @error('idNumber') <span class="text-red-600 text-[11px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-bold text-primary-dark mb-1">{{ __('Upload Business Permit / DTI / SEC Registration') }}</label>
                        <input type="file" wire:model="documentFile" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-xs text-body-muted file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-tint file:text-primary-dark hover:file:bg-primary/20 cursor-pointer" />
                        @error('documentFile') <span class="text-red-600 text-[11px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>
                @endif

                <div class="pt-1">
                    <button type="submit" wire:loading.attr="disabled" class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-xs font-bold text-white hover:bg-primary-dark transition-colors shadow-xs cursor-pointer">
                        <span wire:loading.remove wire:target="submit,documentFile,secondaryDocumentFile">{{ __('Submit for Verification Review') }}</span>
                        <span wire:loading wire:target="submit,documentFile,secondaryDocumentFile">{{ __('Uploading document(s)...') }}</span>
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
