<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AccessibilitySettings extends Component
{
    public bool $highContrast = false;

    public bool $largeText = false;

    public bool $dyslexiaFont = false;

    public bool $reducedMotion = false;

    public bool $predictiveText = true;

    public function mount(): void
    {
        $settings = Auth::user()?->display_settings ?? [];
        $this->highContrast = (bool) ($settings['high_contrast'] ?? false);
        $this->largeText = (bool) ($settings['large_text'] ?? false);
        $this->dyslexiaFont = (bool) ($settings['dyslexia_font'] ?? false);
        $this->reducedMotion = (bool) ($settings['reduced_motion'] ?? false);
        $this->predictiveText = (bool) ($settings['predictive_text'] ?? true);
    }

    public function updated($property): void
    {
        $this->persist();
    }

    protected function persist(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $payload = [
            'high_contrast' => $this->highContrast,
            'large_text' => $this->largeText,
            'font_scale' => $this->largeText ? 125 : 100,
            'dyslexia_font' => $this->dyslexiaFont,
            'reduced_motion' => $this->reducedMotion,
            'predictive_text' => $this->predictiveText,
        ];

        $user->update([
            'display_settings' => $payload,
        ]);

        $this->dispatch('a11y-settings-changed', settings: $payload);
    }

    public function render(): View
    {
        return view('livewire.accessibility-settings');
    }
}