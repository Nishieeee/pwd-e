<div>
    <flux:modal.trigger name="a11y-settings">
        <button type="button" class="inline-flex items-center justify-center rounded-lg p-2 text-dark-neutral hover:bg-primary-tint" aria-label="{{ __('Accessibility settings') }}" title="{{ __('Accessibility settings') }}">
            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
        </button>
    </flux:modal.trigger>

    <flux:modal name="a11y-settings" class="md:w-96">
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Accessibility settings') }}</flux:heading>
            <p class="text-sm text-body-muted">{{ __('Preferences are saved to your account and apply on every page.') }}</p>

            <div class="space-y-3">
                <flux:toggle wire:model.live="highContrast" :label="__('High contrast')" />
                <flux:toggle wire:model.live="largeText" :label="__('Large text (125%)')" />
                <flux:toggle wire:model.live="dyslexiaFont" :label="__('Dyslexia-friendly font')" />
                <flux:toggle wire:model.live="reducedMotion" :label="__('Reduced motion')" />
                <flux:toggle wire:model.live="predictiveText" :label="__('Predictive text')" />
            </div>
        </div>
    </flux:modal>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('a11y-settings-changed', ({ settings } = {}) => {
                try {
                    if (settings) {
                        localStorage.setItem('pwde_scale', settings.large_text ? '125' : '100');
                        localStorage.setItem('pwde_contrast_level', settings.high_contrast ? '125' : '100');
                        localStorage.setItem('pwde_contrast', settings.high_contrast ? 'high' : 'normal');
                        localStorage.setItem('pwde_dyslexia', settings.dyslexia_font ? 'true' : 'false');
                    }
                } catch (e) {}
                window.location.reload();
            });
        });
    </script>
</div>