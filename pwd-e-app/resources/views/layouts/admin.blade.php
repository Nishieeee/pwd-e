<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{
          a11yContrastLevel: parseInt(localStorage.getItem('pwde_contrast_level') || (localStorage.getItem('pwde_contrast') === 'high' ? '125' : '100')),
          a11yScale: parseInt(localStorage.getItem('pwde_scale') || '100'),
          a11yDyslexia: localStorage.getItem('pwde_dyslexia') === 'true',
          a11yScreenReader: localStorage.getItem('pwde_screenreader') === 'true',
          mobileMenuOpen: false,
          setContrast(val) {
              this.a11yContrastLevel = Math.min(200, Math.max(100, parseInt(val)));
              localStorage.setItem('pwde_contrast_level', this.a11yContrastLevel);
              localStorage.setItem('pwde_contrast', this.a11yContrastLevel > 100 ? 'high' : 'normal');
              this.applyContrast();
          },
          increaseContrast() {
              if (this.a11yContrastLevel < 200) this.setContrast(this.a11yContrastLevel + 25);
          },
          decreaseContrast() {
              if (this.a11yContrastLevel > 100) this.setContrast(this.a11yContrastLevel - 25);
          },
          cycleContrast() {
              const levels = [100, 125, 150, 175];
              const idx = levels.indexOf(this.a11yContrastLevel);
              const next = idx === -1 || idx === levels.length - 1 ? levels[0] : levels[idx + 1];
              this.setContrast(next);
          },
          applyContrast() {
              const isHigh = this.a11yContrastLevel > 100;
              document.documentElement.classList.toggle('high-contrast-mode', isHigh);
              document.documentElement.classList.toggle('a11y-high-contrast', isHigh);
              document.documentElement.classList.toggle('contrast-more', isHigh);
              if (isHigh) {
                  document.documentElement.style.setProperty('--pwde-contrast-filter', 'contrast(' + this.a11yContrastLevel + '%)');
                  document.documentElement.style.filter = 'contrast(' + this.a11yContrastLevel + '%)';
              } else {
                  document.documentElement.style.removeProperty('--pwde-contrast-filter');
                  document.documentElement.style.filter = '';
              }
          },
          increaseScale() {
              if (this.a11yScale < 150) {
                  this.a11yScale += 10;
                  localStorage.setItem('pwde_scale', this.a11yScale);
                  document.documentElement.style.fontSize = this.a11yScale + '%';
              }
          },
          decreaseScale() {
              if (this.a11yScale > 90) {
                  this.a11yScale -= 10;
                  localStorage.setItem('pwde_scale', this.a11yScale);
                  document.documentElement.style.fontSize = this.a11yScale + '%';
              }
          },
          resetScale() {
              this.a11yScale = 100;
              localStorage.setItem('pwde_scale', '100');
              document.documentElement.style.fontSize = '100%';
          },
          toggleDyslexia() {
              this.a11yDyslexia = !this.a11yDyslexia;
              localStorage.setItem('pwde_dyslexia', this.a11yDyslexia ? 'true' : 'false');
          },
          toggleScreenReader() {
              this.a11yScreenReader = !this.a11yScreenReader;
              localStorage.setItem('pwde_screenreader', this.a11yScreenReader ? 'true' : 'false');
              const msg = this.a11yScreenReader ? 'Screen reader assistive enhancements enabled' : 'Screen reader enhancements disabled';
              const live = document.getElementById('a11y-announcer');
              if (live) live.textContent = msg;
          }
      }"
      x-init="
          if (a11yScale !== 100) document.documentElement.style.fontSize = a11yScale + '%';
          if (a11yContrastLevel > 100) {
              document.documentElement.style.setProperty('--pwde-contrast-filter', 'contrast(' + a11yContrastLevel + '%)');
              document.documentElement.style.filter = 'contrast(' + a11yContrastLevel + '%)';
              document.documentElement.classList.add('high-contrast-mode', 'a11y-high-contrast', 'contrast-more');
          }
      "
      :class="{ 'contrast-more': a11yContrastLevel > 100, 'high-contrast-mode': a11yContrastLevel > 100 }"
      :data-readable-font="a11yDyslexia ? 'true' : 'false'">
<head>
    @include('partials.head')
    <style>
        .high-contrast-mode { filter: var(--pwde-contrast-filter, contrast(125%)); }
        .high-contrast-mode body { background-color: #ffffff !important; color: #000000 !important; }
        [data-readable-font="true"] body, [data-readable-font="true"] * { font-family: 'Atkinson Hyperlegible Next', 'Lexend', ui-sans-serif, system-ui, sans-serif !important; letter-spacing: 0.02em !important; }
        aside::-webkit-scrollbar { width: 6px; }
        aside::-webkit-scrollbar-track { background: transparent; }
        aside::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 9999px; }
        aside::-webkit-scrollbar-thumb:hover { background: #334155; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="min-h-screen lg:h-screen lg:overflow-hidden bg-[#F4F7FA] text-[#25313B] antialiased flex flex-col lg:flex-row">
    <!-- Accessible Skip Link -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-white focus:text-[#1A2840] focus:ring-2 focus:ring-[#244F73] focus:shadow-lg focus:rounded-md">
        Skip to main content
    </a>

    <!-- Live Announcer for Screen Readers -->
    <div id="a11y-announcer" class="sr-only" role="status" aria-live="polite" aria-atomic="true"></div>

    @php
        $adminUser = auth()->user();
        $pendingVerificationsCount = \App\Models\VerificationRequest::where('status', 'pending')->count();
    @endphp

    <!-- Mobile Header Bar -->
    <header class="lg:hidden flex items-center justify-between bg-[#0e1726] text-white px-4 py-3 border-b border-[#1e293b]" role="banner">
        <div class="flex items-center gap-3">
            <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" class="p-1 rounded-md text-white hover:bg-[#1e293b] focus:outline-none focus:ring-2 focus:ring-white" aria-label="Toggle navigation menu" :aria-expanded="mobileMenuOpen.toString()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full overflow-hidden shrink-0 ring-1 ring-white/15 bg-white/5">
                    <img src="{{ asset('assets/Logo.png') }}" alt="" class="w-full h-full object-cover scale-[1.9]" aria-hidden="true">
                </div>
                <div class="leading-tight">
                    <span class="font-bold text-sm text-white tracking-tight block">PWD-E</span>
                    <span class="block text-[10px] text-gray-300 font-normal">Admin portal</span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <span class="inline-flex items-center gap-1 text-emerald-400 font-medium">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Platform Admin
            </span>
        </div>
    </header>

    <!-- Sidebar Navigation -->
    <aside :class="{ 'block': mobileMenuOpen, 'hidden lg:flex': !mobileMenuOpen }"
           class="w-full lg:w-64 bg-[#0e1726] text-white flex-col justify-between shrink-0 lg:sticky lg:top-0 lg:h-screen overflow-y-auto px-4 py-4 z-40 transition-all duration-200"
           role="navigation" aria-label="Admin Portal Navigation">
        <div>
            <!-- Compact Branding -->
            <div class="flex items-center gap-2.5 pb-3.5 border-b border-[#1e293b]">
                <div class="w-8 h-8 rounded-full overflow-hidden shrink-0 ring-1 ring-white/15 bg-white/5 shadow-xs">
                    <img src="{{ asset('assets/Logo.png') }}" alt="" class="w-full h-full object-cover scale-[1.9]" aria-hidden="true">
                </div>
                <div class="min-w-0">
                    <span class="font-bold text-[15px] text-white tracking-tight leading-tight block">PWD-E</span>
                    <span class="block text-[11px] text-gray-400 font-normal leading-tight mt-0.5">Admin portal</span>
                </div>
            </div>

            <!-- Workspace Menu -->
            <div class="pt-5">
                <div class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-3 px-3">ADMINISTRATION</div>
                <nav class="space-y-1.5" aria-label="Admin portal links">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 {{ request()->routeIs('admin.dashboard') ? 'bg-[#244F73] text-white shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                       @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        <span class="flex-1">Dashboard</span>
                    </a>

                    <!-- Verification Queue -->
                    <a href="{{ route('admin.verifications.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 {{ request()->routeIs('admin.verifications.*') ? 'bg-[#244F73] text-white shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                       @if(request()->routeIs('admin.verifications.*')) aria-current="page" @endif>
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.verifications.*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span class="flex-1">Verification Queue</span>
                        @if ($pendingVerificationsCount > 0)
                            <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                {{ $pendingVerificationsCount }}
                            </span>
                        @endif
                    </a>

                    <!-- User Management -->
                    <a href="{{ route('admin.users') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 {{ request()->routeIs('admin.users') ? 'bg-[#244F73] text-white shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                       @if(request()->routeIs('admin.users')) aria-current="page" @endif>
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.users') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span class="flex-1">User Management</span>
                    </a>

                    <!-- Inclusive Lexicon -->
                    <a href="{{ route('admin.lexicon') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 {{ request()->routeIs('admin.lexicon') ? 'bg-[#244F73] text-white shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                       @if(request()->routeIs('admin.lexicon')) aria-current="page" @endif>
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.lexicon') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span class="flex-1">Inclusive Lexicon</span>
                    </a>

                    <!-- Audit Log -->
                    <a href="{{ route('admin.audit-log') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 {{ request()->routeIs('admin.audit-log') ? 'bg-[#244F73] text-white shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                       @if(request()->routeIs('admin.audit-log')) aria-current="page" @endif>
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.audit-log') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <span class="flex-1">Audit Log</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Admin Profile Card -->
        <div class="mt-6 pt-4 border-t border-[#1e293b]">
            <div class="flex items-center gap-2.5 px-2.5 py-2.5 rounded-lg bg-white/[0.03] border border-[#1e293b]">
                <div class="w-9 h-9 rounded-full bg-[#244F73] text-white flex items-center justify-center font-semibold text-xs border border-white/20 shrink-0">
                    {{ $adminUser?->initials() ?? 'AD' }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="font-semibold text-[13px] text-white truncate leading-snug" title="{{ $adminUser?->name ?? 'Administrator' }}">
                        {{ $adminUser?->name ?? 'Administrator' }}
                    </div>
                    <div class="flex items-center gap-1 mt-0.5 text-[11px] text-emerald-400">
                        <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="truncate">Platform Administrator</span>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 lg:h-screen lg:overflow-y-auto">
        <!-- Top Accessibility & Navigation Header -->
        <header class="bg-[#F8FAFC] border-b border-gray-200 px-4 sm:px-6 lg:px-8 py-2 sm:py-2.5 flex flex-wrap items-center justify-between gap-y-2 gap-x-3.5 sticky top-0 z-30 shadow-xs min-w-0" role="region" aria-label="Accessibility settings and profile controls">
            <!-- Left: Accessibility Tools -->
            <div class="flex items-center flex-wrap gap-2 text-xs text-gray-700 min-w-0">
                <div class="flex items-center gap-1.5 sm:gap-2 font-semibold text-[#1A2840] mr-1 sm:mr-2 shrink-0">
                    <svg class="w-5 h-5 text-[#244F73] shrink-0" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"/>
                        <circle cx="12" cy="7.25" r="1.35" fill="currentColor" stroke="none"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.25 10.5l4.75 1 4.75-1M12 11.5v3.25m0 0l-2.6 3.75m2.6-3.75l2.6 3.75"/>
                    </svg>
                    <span>Accessibility</span>
                </div>

                <!-- Contrast Level Controls -->
                <div class="inline-flex items-center rounded-lg border border-gray-300 bg-white p-0.5 shadow-2xs shrink-0" role="group" aria-label="Contrast level controls">
                    <button type="button" @click="decreaseContrast()" class="px-2 sm:px-2.5 py-1.5 rounded-md text-gray-700 hover:bg-gray-100 font-bold text-xs transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-1 cursor-pointer" aria-label="Decrease contrast" title="Decrease contrast">C-</button>
                    <button type="button" @click="cycleContrast()" class="px-2 sm:px-2.5 py-1.5 rounded-md text-gray-700 font-semibold text-xs hover:bg-gray-100 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-1 inline-flex items-center gap-1 cursor-pointer" :class="a11yContrastLevel > 100 ? 'font-bold text-[#1A2840]' : ''" aria-label="Contrast level: click to cycle presets">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke-width="2"/><path fill="currentColor" d="M12 3a9 9 0 000 18V3z"/></svg>
                        <span x-text="a11yContrastLevel > 100 ? a11yContrastLevel + '%' : 'Contrast'">Contrast</span>
                    </button>
                    <button type="button" @click="increaseContrast()" class="px-2 sm:px-2.5 py-1.5 rounded-md text-gray-700 hover:bg-gray-100 font-bold text-xs transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-1 cursor-pointer" aria-label="Increase contrast" title="Increase contrast">C+</button>
                </div>

                <!-- Font Resizing Controls -->
                <div class="inline-flex items-center rounded-lg border border-gray-300 bg-white p-0.5 shadow-2xs shrink-0" role="group" aria-label="Text scaling controls">
                    <button type="button" @click="decreaseScale()" class="px-2 sm:px-2.5 py-1.5 rounded-md text-gray-700 hover:bg-gray-100 font-bold text-xs transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-1 cursor-pointer" aria-label="Decrease text size">A-</button>
                    <button type="button" @click="resetScale()" class="px-2 sm:px-2.5 py-1.5 rounded-md text-gray-700 font-semibold text-xs hover:bg-gray-100 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-1 cursor-pointer" aria-label="Reset text size to 100%">
                        <span x-text="a11yScale + '%'">100%</span>
                    </button>
                    <button type="button" @click="increaseScale()" class="px-2 sm:px-2.5 py-1.5 rounded-md text-gray-700 hover:bg-gray-100 font-bold text-xs transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-1 cursor-pointer" aria-label="Increase text size">A+</button>
                </div>

                <!-- Dyslexia Font Toggle -->
                <button type="button"
                        @click="toggleDyslexia()"
                        :class="a11yDyslexia ? 'bg-[#1A2840] text-white border-[#1A2840]' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'"
                        class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-lg font-medium border shadow-2xs transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-2 cursor-pointer shrink-0"
                        :aria-pressed="a11yDyslexia.toString()"
                        aria-label="Toggle dyslexia-friendly font">
                    <span class="font-serif font-bold text-xs" aria-hidden="true">T</span>
                    <span>Dyslexia-friendly font</span>
                </button>

                <!-- Screen Reader Helper Toggle -->
                <button type="button"
                        @click="toggleScreenReader()"
                        :class="a11yScreenReader ? 'bg-[#1A2840] text-white border-[#1A2840]' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'"
                        class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-lg font-medium border shadow-2xs transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-2 cursor-pointer shrink-0"
                        :aria-pressed="a11yScreenReader.toString()"
                        aria-label="Toggle screen reader live assistance">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                    </svg>
                    <span>Screen reader</span>
                </button>
            </div>

            <!-- Right: Settings, Notifications, Accessibility Menu, Logout -->
            <div class="flex items-center gap-3 sm:gap-4 text-xs font-medium text-gray-700 shrink-0 ml-auto">
                <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-1.5 text-gray-700 hover:text-[#1A2840] transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-2 rounded-md px-2 py-1.5">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Settings</span>
                </a>

                <livewire:notification-bell />

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 text-gray-700 hover:text-red-700 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-2 rounded-md px-2 py-1.5 cursor-pointer">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Log out</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Dynamic Main Content -->
        <main id="main-content" class="flex-1 p-6 lg:p-8" tabindex="-1">
            {{ $slot }}
        </main>
    </div>

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
    <script>
        // Safeguard desktop 100vh layout against unintended window scroll shifts
        window.addEventListener('scroll', function () {
            if (window.innerWidth >= 1024 && window.scrollY !== 0) {
                window.scrollTo(0, 0);
            }
        }, { passive: true });
    </script>
</body>
</html>
