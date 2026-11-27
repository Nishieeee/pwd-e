{{-- Shared Top Accessibility & Profile Controls Bar (Employer, Candidate, Admin Portals) --}}
<header class="a11y-utility-bar bg-[#F8FAFC] border-b border-gray-200 px-[14px] sm:px-[18px] lg:px-[22px] py-[7px] flex flex-wrap lg:flex-nowrap items-center justify-between gap-y-[6px] gap-x-[10px] sticky top-0 z-30 shadow-xs min-w-0" role="region" aria-label="Accessibility settings and profile controls">
    <!-- Left: Accessibility Tools -->
    <div class="flex items-center flex-wrap lg:flex-nowrap gap-[6px] text-[12px] leading-[16px] text-gray-700 min-w-0">
        <div class="flex items-center gap-[5px] font-semibold text-[#1A2840] mr-[2px] shrink-0" title="Accessibility controls">
            <svg class="w-[17px] h-[17px] text-[#244F73] shrink-0" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/>
                <circle cx="12" cy="7.25" r="1.35" fill="currentColor" stroke="none"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.25 10.5l4.75 1 4.75-1M12 11.5v3.25m0 0l-2.6 3.75m2.6-3.75l2.6 3.75"/>
            </svg>
            <span class="hidden xl:inline whitespace-nowrap">Accessibility</span>
        </div>

        <!-- Contrast Level Controls -->
        <div class="inline-flex items-center rounded-[8px] border border-gray-300 bg-white p-[2px] shadow-2xs shrink-0" role="group" aria-label="Contrast level controls">
            <button type="button" @click="decreaseContrast()" class="px-[7px] py-[4px] rounded-[6px] text-gray-700 hover:bg-gray-100 font-bold text-[12px] leading-[16px] transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-1 cursor-pointer" aria-label="Decrease contrast" title="Decrease contrast">C-</button>
            <button type="button" @click="cycleContrast()" class="px-[7px] py-[4px] rounded-[6px] text-gray-700 font-semibold text-[12px] leading-[16px] hover:bg-gray-100 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-1 inline-flex items-center gap-[4px] whitespace-nowrap cursor-pointer" :class="a11yContrastLevel > 100 ? 'font-bold text-[#1A2840] bg-sky-50' : ''" aria-label="Contrast level: click to cycle presets">
                <svg class="w-[13px] h-[13px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke-width="2"/><path fill="currentColor" d="M12 3a9 9 0 000 18V3z"/></svg>
                <span x-text="a11yContrastLevel > 100 ? a11yContrastLevel + '%' : 'Contrast'">Contrast</span>
            </button>
            <button type="button" @click="increaseContrast()" class="px-[7px] py-[4px] rounded-[6px] text-gray-700 hover:bg-gray-100 font-bold text-[12px] leading-[16px] transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-1 cursor-pointer" aria-label="Increase contrast" title="Increase contrast">C+</button>
        </div>

        <!-- Font Resizing Controls -->
        <div class="inline-flex items-center rounded-[8px] border border-gray-300 bg-white p-[2px] shadow-2xs shrink-0" role="group" aria-label="Text scaling controls">
            <button type="button" @click="decreaseScale()" class="px-[7px] py-[4px] rounded-[6px] text-gray-700 hover:bg-gray-100 font-bold text-[12px] leading-[16px] transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-1 cursor-pointer" aria-label="Decrease text size" title="Decrease text size">A-</button>
            <button type="button" @click="resetScale()" class="px-[7px] py-[4px] rounded-[6px] text-gray-700 font-semibold text-[12px] leading-[16px] tabular-nums min-w-[42px] text-center hover:bg-gray-100 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-1 cursor-pointer" :class="a11yScale !== 100 ? 'font-bold text-[#1A2840] bg-sky-50' : ''" aria-label="Reset text size to 100%" title="Click to reset text size to 100%">
                <span x-text="a11yScale + '%'">100%</span>
            </button>
            <button type="button" @click="increaseScale()" class="px-[7px] py-[4px] rounded-[6px] text-gray-700 hover:bg-gray-100 font-bold text-[12px] leading-[16px] transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-1 cursor-pointer" aria-label="Increase text size" title="Increase text size">A+</button>
        </div>

        <!-- Dyslexia Font Toggle -->
        <button type="button"
                @click="toggleDyslexia()"
                :class="a11yDyslexia ? 'bg-[#1A2840] text-white border-[#1A2840]' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'"
                class="inline-flex items-center gap-[5px] px-[9px] py-[6px] rounded-[8px] text-[12px] leading-[16px] font-medium border shadow-2xs transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-1 cursor-pointer whitespace-nowrap shrink-0"
                :aria-pressed="a11yDyslexia.toString()"
                aria-label="Toggle dyslexia-friendly font">
            <span class="font-serif font-bold text-[12px] leading-none" aria-hidden="true">T</span>
            <span>Dyslexia-friendly font</span>
        </button>

        <!-- Screen Reader Helper Toggle -->
        <button type="button"
                @click="toggleScreenReader()"
                :class="a11yScreenReader ? 'bg-[#1A2840] text-white border-[#1A2840]' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'"
                class="inline-flex items-center gap-[5px] px-[9px] py-[6px] rounded-[8px] text-[12px] leading-[16px] font-medium border shadow-2xs transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-1 cursor-pointer whitespace-nowrap shrink-0"
                :aria-pressed="a11yScreenReader.toString()"
                aria-label="Toggle screen reader live assistance">
            <svg class="w-[14px] h-[14px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
            </svg>
            <span>Screen reader</span>
        </button>
    </div>

    <!-- Right: Settings, Notifications, Logout -->
    <div class="flex items-center gap-[6px] sm:gap-[8px] text-[12px] leading-[16px] font-medium text-gray-700 shrink-0 ml-auto whitespace-nowrap">
        <div class="hidden lg:block h-[18px] w-px bg-gray-200 mr-[2px]" aria-hidden="true"></div>

        <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-[5px] text-gray-700 hover:text-[#1A2840] hover:bg-gray-100 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-1 rounded-[6px] px-[7px] py-[5px]">
            <svg class="w-[15px] h-[15px] text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span>Settings</span>
        </a>

        <div class="shrink-0 [&_button]:!p-[6px] [&_button>svg]:!w-[17px] [&_button>svg]:!h-[17px]">
            <livewire:notification-bell />
        </div>

        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="inline-flex items-center gap-[5px] text-gray-700 hover:text-red-700 hover:bg-red-50 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-1 rounded-[6px] px-[7px] py-[5px] cursor-pointer">
                <svg class="w-[15px] h-[15px] text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Log out</span>
            </button>
        </form>
    </div>
</header>
