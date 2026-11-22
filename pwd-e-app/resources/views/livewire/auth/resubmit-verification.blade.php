<div class="rounded-xl border border-primary-border bg-white p-5 shadow-xs space-y-4">
    <div class="border-b border-primary-border pb-3">
        <h3 class="font-bold text-sm text-primary-dark flex items-center gap-2">
            <svg class="size-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            <span>{{ __('Resubmit Verification Document') }}</span>
        </h3>
        <p class="text-xs text-body-muted mt-1">
            {{ __('Upload a clearer photo or updated document to address the administrator note above.') }}
        </p>
    </div>

    @if ($resubmitted)
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 p-3.5 text-xs text-emerald-800 font-medium">
            ✓ {{ __('New document successfully uploaded and queued for review. Our administrators will process it shortly.') }}
        </div>
    @else
        <form wire:submit="submit" class="space-y-4 text-xs">
            <div>
                <label class="block font-bold text-primary-dark mb-1">
                    {{ $user?->isCandidate() ? __('PWD ID Number') : __('Business Permit / Registration Number') }}
                </label>
                <input type="text" wire:model="idNumber" class="w-full rounded-lg border border-primary-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary focus:outline-none" placeholder="{{ $user?->isCandidate() ? 'e.g. 13-7404-000-0012345' : 'e.g. BP-2026-9876' }}" />
                @error('idNumber') <span class="text-red-600 text-[11px] mt-0.5 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block font-bold text-primary-dark mb-1">
                    {{ $user?->isCandidate() ? __('Upload Government PWD ID (Image or PDF)') : __('Upload Business Permit / DTI / SEC Registration') }}
                </label>
                <input type="file" wire:model="documentFile" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-xs text-body-muted file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-tint file:text-primary-dark hover:file:bg-primary/20 cursor-pointer" />
                <span class="text-[11px] text-body-muted mt-1 block">{{ __('Max size: 5MB. Formats: JPG, PNG, PDF.') }}</span>
                @error('documentFile') <span class="text-red-600 text-[11px] mt-0.5 block">{{ $message }}</span> @enderror
            </div>

            <div class="pt-2">
                <button type="submit" wire:loading.attr="disabled" class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2 text-xs font-bold text-white hover:bg-primary-dark transition-colors shadow-xs">
                    <span wire:loading.remove>{{ __('Submit for Re-evaluation') }}</span>
                    <span wire:loading>{{ __('Uploading document...') }}</span>
                </button>
            </div>
        </form>
    @endif
</div>
