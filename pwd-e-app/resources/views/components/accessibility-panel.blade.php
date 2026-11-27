@props(['alpine' => true])

<style>
    .high-contrast-mode {
        filter: var(--pwde-contrast-filter, contrast(125%)) !important;
    }
    .high-contrast-mode body {
        background-color: #ffffff !important;
        color: #000000 !important;
    }
    .high-contrast-mode a {
        text-decoration: underline !important;
    }
    [data-readable-font="true"],
    [data-readable-font="true"] body,
    [data-readable-font="true"] * {
        font-family: 'Atkinson Hyperlegible Next', 'Lexend', ui-sans-serif, system-ui, sans-serif !important;
        letter-spacing: 0.02em !important;
    }
</style>

<!-- Live Screen Reader Announcer -->
<div id="a11y-announcer" class="sr-only" role="status" aria-live="polite" aria-atomic="true"></div>

<!-- Floating Accessibility Widget -->
<div x-data="{
        open: false,
        contrastLevel: parseInt(localStorage.getItem('pwde_contrast_level') || (localStorage.getItem('pwde_contrast') === 'high' ? '125' : '100')),
        scale: (function () { const v = parseInt(localStorage.getItem('pwde_scale') || (window.__pwdeInitialScale || '100'), 10); return (!isNaN(v) && v >= 90 && v <= 150) ? v : 100; })(),
        dyslexia: localStorage.getItem('pwde_dyslexia') === 'true',
        screenReader: localStorage.getItem('pwde_screenreader') === 'true',

        init() {
            this.applyAll();
        },

        setContrast(val) {
            this.contrastLevel = Math.min(200, Math.max(100, parseInt(val)));
            localStorage.setItem('pwde_contrast_level', this.contrastLevel);
            localStorage.setItem('pwde_contrast', this.contrastLevel > 100 ? 'high' : 'normal');
            this.applyContrast();
            this.announce('Contrast set to ' + this.contrastLevel + ' percent');
        },

        increaseContrast() {
            if (this.contrastLevel < 200) this.setContrast(this.contrastLevel + 25);
        },

        decreaseContrast() {
            if (this.contrastLevel > 100) this.setContrast(this.contrastLevel - 25);
        },

        resetContrast() {
            this.setContrast(100);
        },

        applyContrast() {
            const isHigh = this.contrastLevel > 100;
            document.documentElement.classList.toggle('high-contrast-mode', isHigh);
            document.documentElement.classList.toggle('a11y-high-contrast', isHigh);
            document.documentElement.classList.toggle('contrast-more', isHigh);
            if (isHigh) {
                document.documentElement.style.setProperty('--pwde-contrast-filter', 'contrast(' + this.contrastLevel + '%)');
                document.documentElement.style.filter = 'contrast(' + this.contrastLevel + '%)';
            } else {
                document.documentElement.style.removeProperty('--pwde-contrast-filter');
                document.documentElement.style.filter = '';
            }
        },

        setScale(val) {
            this.scale = Math.min(150, Math.max(90, parseInt(val)));
            localStorage.setItem('pwde_scale', this.scale);
            this.applyScale();
            this.announce('Text scale set to ' + this.scale + ' percent');
        },

        increaseScale() {
            if (this.scale < 150) this.setScale(this.scale + 10);
        },

        decreaseScale() {
            if (this.scale > 90) this.setScale(this.scale - 10);
        },

        resetScale() {
            this.setScale(100);
        },

        applyScale() {
            document.documentElement.style.fontSize = this.scale + '%';
            document.documentElement.classList.toggle('a11y-large-text', this.scale >= 125);
        },

        toggleDyslexia() {
            this.dyslexia = !this.dyslexia;
            localStorage.setItem('pwde_dyslexia', this.dyslexia ? 'true' : 'false');
            this.applyDyslexia();
            this.announce(this.dyslexia ? 'Dyslexia friendly font enabled' : 'Dyslexia friendly font disabled');
        },

        applyDyslexia() {
            if (this.dyslexia) {
                document.documentElement.setAttribute('data-readable-font', 'true');
                if (document.body) document.body.setAttribute('data-readable-font', 'true');
                document.documentElement.classList.add('a11y-dyslexia');
            } else {
                document.documentElement.removeAttribute('data-readable-font');
                if (document.body) document.body.removeAttribute('data-readable-font');
                document.documentElement.classList.remove('a11y-dyslexia');
            }
        },

        toggleScreenReader() {
            this.screenReader = !this.screenReader;
            localStorage.setItem('pwde_screenreader', this.screenReader ? 'true' : 'false');
            const msg = this.screenReader ? 'Screen reader voice assistance enabled' : 'Screen reader assistance disabled';
            this.announce(msg, true);
        },

        resetAll() {
            this.contrastLevel = 100;
            this.scale = 100;
            this.dyslexia = false;
            this.screenReader = false;
            localStorage.removeItem('pwde_contrast_level');
            localStorage.removeItem('pwde_contrast');
            localStorage.removeItem('pwde_scale');
            localStorage.removeItem('pwde_dyslexia');
            localStorage.removeItem('pwde_screenreader');
            this.applyAll();
            this.announce('Accessibility settings reset to default', true);
        },

        applyAll() {
            this.applyContrast();
            this.applyScale();
            this.applyDyslexia();
        },

        announce(text, speak = false) {
            const live = document.getElementById('a11y-announcer');
            if (live) live.textContent = text;
            if ((this.screenReader || speak) && 'speechSynthesis' in window) {
                try {
                    window.speechSynthesis.cancel();
                    const u = new SpeechSynthesisUtterance(text);
                    u.rate = 1.0;
                    window.speechSynthesis.speak(u);
                } catch(e) {}
            }
        }
    }"
    @keydown.escape.window="open = false"
    class="relative">

    <!-- Floating Trigger Button -->
    <button type="button"
            @click="open = !open"
            class="fixed bottom-6 right-6 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-[#244F73] hover:bg-[#1A2840] text-white shadow-2xl focus:outline-none focus:ring-4 focus:ring-[#244F73]/40 transition-all transform hover:scale-105 active:scale-95 cursor-pointer"
            aria-label="Toggle accessibility tools menu"
            title="Accessibility options"
            :aria-expanded="open.toString()">
        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <circle cx="12" cy="4" r="2" stroke-width="2" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0l-3 6m3-6l3 6M9 9h6" />
        </svg>
        <span class="sr-only">Accessibility Controls</span>
    </button>

    <!-- Floating Settings Panel -->
    <div x-show="open"
         x-cloak
         @click.outside="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="fixed bottom-24 right-6 z-50 w-84 max-w-[calc(100vw-2rem)] rounded-2xl bg-white border-2 border-[#244F73]/20 text-[#1A2840] shadow-2xl p-5 space-y-4"
         role="dialog"
         aria-modal="true"
         aria-label="Accessibility options panel">

        <!-- Panel Header -->
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-[#DCEAF4] text-[#244F73] flex items-center justify-center font-bold text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"/>
                        <circle cx="12" cy="7.25" r="1.35" fill="currentColor" stroke="none"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.25 10.5l4.75 1 4.75-1M12 11.5v3.25m0 0l-2.6 3.75m2.6-3.75l2.6 3.75"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-[#1A2840]">Accessibility Tools</h2>
                    <span class="text-[10px] text-gray-500">WCAG 2.1 AA Compliant</span>
                </div>
            </div>
            <button type="button"
                    @click="open = false"
                    class="w-7 h-7 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-[#244F73] cursor-pointer"
                    aria-label="Close accessibility options">
                ✕
            </button>
        </div>

        <!-- 1. Contrast Adjustment -->
        <div class="p-3 rounded-xl border border-gray-200 space-y-2.5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-md bg-gray-100 flex items-center justify-center text-gray-700">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" stroke-width="2"/>
                            <path fill="currentColor" d="M12 3a9 9 0 000 18V3z"/>
                        </svg>
                    </div>
                    <div class="text-xs font-bold text-[#1A2840]">Contrast Level</div>
                </div>
                <span class="text-xs font-bold text-[#244F73]" x-text="contrastLevel + '%'">100%</span>
            </div>

            <!-- Contrast Slider -->
            <input type="range"
                   min="100"
                   max="200"
                   step="5"
                   :value="contrastLevel"
                   @input="setContrast($event.target.value)"
                   class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-[#244F73]"
                   aria-label="Contrast level slider">

            <!-- Quick Action Preset Buttons -->
            <div class="grid grid-cols-4 gap-1.5 pt-1">
                <button type="button"
                        @click="setContrast(100)"
                        class="px-1.5 py-1 rounded-lg border border-gray-300 hover:bg-gray-50 font-bold text-xs text-gray-700 transition-colors focus:ring-2 focus:ring-[#244F73] cursor-pointer"
                        :class="contrastLevel === 100 ? 'bg-[#244F73] text-white border-[#244F73]' : ''"
                        aria-label="Set contrast to 100% (Normal)">
                    100%
                </button>
                <button type="button"
                        @click="setContrast(125)"
                        class="px-1.5 py-1 rounded-lg border border-gray-300 hover:bg-gray-50 font-bold text-xs text-gray-700 transition-colors focus:ring-2 focus:ring-[#244F73] cursor-pointer"
                        :class="contrastLevel === 125 ? 'bg-[#244F73] text-white border-[#244F73]' : ''"
                        aria-label="Set contrast to 125% (Medium)">
                    125%
                </button>
                <button type="button"
                        @click="setContrast(150)"
                        class="px-1.5 py-1 rounded-lg border border-gray-300 hover:bg-gray-50 font-bold text-xs text-gray-700 transition-colors focus:ring-2 focus:ring-[#244F73] cursor-pointer"
                        :class="contrastLevel === 150 ? 'bg-[#244F73] text-white border-[#244F73]' : ''"
                        aria-label="Set contrast to 150% (High)">
                    150%
                </button>
                <button type="button"
                        @click="setContrast(175)"
                        class="px-1.5 py-1 rounded-lg border border-gray-300 hover:bg-gray-50 font-bold text-xs text-gray-700 transition-colors focus:ring-2 focus:ring-[#244F73] cursor-pointer"
                        :class="contrastLevel >= 175 ? 'bg-[#244F73] text-white border-[#244F73]' : ''"
                        aria-label="Set contrast to 175% (Maximum)">
                    175%
                </button>
            </div>
        </div>

        <!-- 2. Text Resizing / Font Scaling -->
        <div class="p-3 rounded-xl border border-gray-200 space-y-2.5">
            <div class="flex items-center justify-between">
                <div class="text-xs font-bold text-[#1A2840]">Text Scaling</div>
                <span class="text-xs font-bold text-[#244F73]" x-text="scale + '%'">100%</span>
            </div>

            <!-- Slider -->
            <input type="range"
                   min="90"
                   max="150"
                   step="5"
                   :value="scale"
                   @input="setScale($event.target.value)"
                   class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-[#244F73]"
                   aria-label="Text scale slider">

            <!-- Quick Action Buttons -->
            <div class="grid grid-cols-3 gap-2 pt-1">
                <button type="button"
                        @click="decreaseScale()"
                        class="px-2 py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 font-bold text-xs text-gray-700 transition-colors focus:ring-2 focus:ring-[#244F73] cursor-pointer"
                        aria-label="Decrease font size">
                    A- (90%)
                </button>
                <button type="button"
                        @click="resetScale()"
                        class="px-2 py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 font-bold text-xs text-gray-700 transition-colors focus:ring-2 focus:ring-[#244F73] cursor-pointer"
                        aria-label="Reset font size to 100%">
                    100%
                </button>
                <button type="button"
                        @click="increaseScale()"
                        class="px-2 py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 font-bold text-xs text-gray-700 transition-colors focus:ring-2 focus:ring-[#244F73] cursor-pointer"
                        aria-label="Increase font size">
                    A+ (150%)
                </button>
            </div>
        </div>

        <!-- 3. Dyslexia / Readable Font -->
        <div class="flex items-center justify-between p-3 rounded-xl border border-gray-200 hover:border-[#244F73]/40 transition-colors">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center font-serif font-extrabold text-sm text-gray-800">
                    T
                </div>
                <div>
                    <div class="text-xs font-bold text-[#1A2840]">Dyslexia-Friendly Font</div>
                    <div class="text-[11px] text-gray-500">Atkinson Hyperlegible font</div>
                </div>
            </div>
            <button type="button"
                    @click="toggleDyslexia()"
                    :class="dyslexia ? 'bg-[#176B47] text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all focus:outline-none focus:ring-2 focus:ring-[#244F73] cursor-pointer"
                    :aria-pressed="dyslexia.toString()">
                <span x-text="dyslexia ? 'ON' : 'OFF'">OFF</span>
            </button>
        </div>

        <!-- 4. Screen Reader Assistance -->
        <div class="flex items-center justify-between p-3 rounded-xl border border-gray-200 hover:border-[#244F73]/40 transition-colors">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xs font-bold text-[#1A2840]">Screen Reader Audio</div>
                    <div class="text-[11px] text-gray-500">Voice feedback &amp; aria updates</div>
                </div>
            </div>
            <button type="button"
                    @click="toggleScreenReader()"
                    :class="screenReader ? 'bg-[#176B47] text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all focus:outline-none focus:ring-2 focus:ring-[#244F73] cursor-pointer"
                    :aria-pressed="screenReader.toString()">
                <span x-text="screenReader ? 'ON' : 'OFF'">OFF</span>
            </button>
        </div>

        <!-- Panel Footer / Reset All -->
        <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
            <span class="text-[11px] text-gray-500">Preferences auto-saved</span>
            <button type="button"
                    @click="resetAll()"
                    class="text-xs font-semibold text-red-600 hover:text-red-700 hover:underline focus:outline-none focus:ring-2 focus:ring-red-400 rounded px-1.5 py-0.5 cursor-pointer">
                Reset all
            </button>
        </div>
    </div>
</div>
