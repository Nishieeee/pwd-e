@props(['alpine' => true])

{{-- Floating accessibility toggle + bottom sheet (mobile) / panel (desktop) --}}
<button type="button"
        @click="a11yOpen = !a11yOpen"
        class="fixed bottom-4 right-4 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-primary text-white shadow-lg focus:outline-none focus:ring-2 focus:ring-primary/40"
        aria-label="Accessibility options"
        :aria-expanded="a11yOpen.toString()">
    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <circle cx="12" cy="4" r="2" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0l-3 6m3-6l3 6M9 9h6" />
    </svg>
</button>

<div x-show="a11yOpen" x-cloak
     class="fixed bottom-20 right-4 z-50 w-56 rounded-xl bg-primary-dark p-3 text-xs text-white shadow-lg"
     role="dialog" aria-label="Accessibility controls">
    <button type="button" class="w-full rounded px-2 py-2 text-left hover:bg-white/10" aria-pressed="false">High Contrast</button>
    <label class="flex items-center gap-2 px-2 py-2">
        <span>Font size</span>
        <input type="range" min="100" max="200" value="100" class="w-full" aria-label="Font resize">
    </label>
    <button type="button" class="w-full rounded px-2 py-2 text-left hover:bg-white/10" aria-pressed="false">Screen Reader</button>
    <button type="button" @click="readable = !readable" class="w-full rounded px-2 py-2 text-left hover:bg-white/10" :aria-pressed="readable">Readable Font</button>
</div>