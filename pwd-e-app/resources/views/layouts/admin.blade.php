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
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-[#176B47] flex items-center justify-center font-bold text-white text-xs shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div class="font-bold text-base leading-tight">PWD-E <span class="block text-[10px] text-gray-300 font-normal">Admin portal</span></div>
            </div>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <span class="inline-flex items-center gap-1 text-emerald-400 font-medium">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Platform Admin
            </span>
        </div>
    </header>

    <!-- Sidebar Navigation -->
    <aside :class="{ 'block': mobileMenuOpen, 'hidden lg:flex': !mobileMenuOpen }"
           class="w-full lg:w-64 bg-[#0e1726] text-white flex-col justify-between shrink-0 lg:sticky lg:top-0 lg:h-screen overflow-y-auto p-5 z-40 transition-all duration-200"
           role="navigation" aria-label="Admin Portal Navigation">
        <div>
            <!-- Branding -->
            <div class="flex items-center gap-3 pb-6 border-b border-[#1e293b]">
                <div class="w-10 h-10 rounded-full bg-[#176B47] flex items-center justify-center text-white font-bold text-sm shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <span class="font-bold text-lg text-white tracking-tight">PWD-E</span>
                    <span class="block text-xs text-gray-300 font-normal leading-none mt-0.5">Admin portal</span>
                </div>
            </div>

            <!-- Admin Profile Snippet -->
            <div class="flex items-center gap-3 py-5 border-b border-[#1e293b]">
                <div class="w-10 h-10 rounded-full bg-[#244F73] text-white flex items-center justify-center font-semibold text-sm border border-white/20 shrink-0">
                    {{ $adminUser?->initials() ?? 'AD' }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="font-semibold text-sm text-white truncate">{{ $adminUser?->name ?? 'Administrator' }}</div>
                    <div class="flex items-center gap-1.5 mt-0.5 text-xs text-emerald-400">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="truncate">Platform Administrator</span>
                    </div>
                </div>
            </div>

            <!-- Workspace Menu -->
            <div class="pt-6">
                <div class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-3 px-3">ADMINISTRATION</div>
                <nav class="space-y-1.5" aria-label="Admin portal links">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-[#244F73] text-white shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                       @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        <span class="flex-1">Dashboard</span>
                    </a>

                    <!-- Verification Queue -->
                    <a href="{{ route('admin.verifications.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.verifications.*') ? 'bg-[#244F73] text-white shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
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
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.users') ? 'bg-[#244F73] text-white shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                       @if(request()->routeIs('admin.users')) aria-current="page" @endif>
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.users') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span class="flex-1">User Management</span>
                    </a>

                    <!-- Inclusive Lexicon -->
                    <a href="{{ route('admin.lexicon') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.lexicon') ? 'bg-[#244F73] text-white shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                       @if(request()->routeIs('admin.lexicon')) aria-current="page" @endif>
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.lexicon') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span class="flex-1">Inclusive Lexicon</span>
                    </a>

                    <!-- Audit Log -->
                    <a href="{{ route('admin.audit-log') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.audit-log') ? 'bg-[#244F73] text-white shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                       @if(request()->routeIs('admin.audit-log')) aria-current="page" @endif>
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.audit-log') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <span class="flex-1">Audit Log</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Compliance & Security Widget -->
        <div class="mt-8 pt-4">
            <div class="p-4 rounded-xl bg-[#132238] border border-[#1e293b]">
                <div class="flex items-center gap-2 text-sky-400 mb-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span class="text-xs font-semibold text-white">Platform Governance</span>
                </div>
                <p class="text-xs text-gray-300 leading-relaxed mb-3">
                    Statutory oversight active under RA 7277, RA 10524, RA 10173, and RA 11106.
                </p>
                <a href="{{ route('admin.audit-log') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-sky-300 hover:text-white transition-colors">
                    <span>Review audit trail</span>
                    <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 lg:h-screen lg:overflow-y-auto">
        <!-- Top Accessibility & Navigation Header -->
        <header class="bg-white border-b border-gray-200 px-6 py-2.5 flex flex-wrap items-center justify-between gap-4 sticky top-0 z-30 shadow-xs" role="region" aria-label="Accessibility settings and profile controls">
            <!-- Left: Accessibility Tools -->
            <div class="flex items-center flex-wrap gap-2 text-xs text-gray-600">
                <div class="flex items-center gap-1.5 font-semibold text-[#244F73] mr-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke-width="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v18M3 12h18"/>
                    </svg>
                    <span>Accessibility</span>
                </div>

                <!-- Contrast Level Controls -->
                <div class="inline-flex items-center rounded-md border border-gray-300 bg-gray-100 p-0.5" role="group" aria-label="Contrast level controls">
                    <button type="button" @click="decreaseContrast()" class="px-2 py-1 rounded text-gray-700 hover:bg-white font-bold text-xs focus:ring-2 focus:ring-[#244F73]" aria-label="Decrease contrast" title="Decrease contrast">C-</button>
                    <button type="button" @click="cycleContrast()" class="px-2 py-1 text-gray-700 font-semibold text-xs hover:bg-white inline-flex items-center gap-1" :class="a11yContrastLevel > 100 ? 'font-bold text-[#1A2840]' : ''" aria-label="Contrast level: click to cycle presets">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke-width="2"/><path fill="currentColor" d="M12 3a9 9 0 000 18V3z"/></svg>
                        <span x-text="a11yContrastLevel > 100 ? a11yContrastLevel + '%' : 'Contrast'">Contrast</span>
                    </button>
                    <button type="button" @click="increaseContrast()" class="px-2 py-1 rounded text-gray-700 hover:bg-white font-bold text-xs focus:ring-2 focus:ring-[#244F73]" aria-label="Increase contrast" title="Increase contrast">C+</button>
                </div>

                <!-- Font Resizing Controls -->
                <div class="inline-flex items-center rounded-md border border-gray-300 bg-gray-100 p-0.5" role="group" aria-label="Text scaling controls">
                    <button type="button" @click="decreaseScale()" class="px-2 py-1 rounded text-gray-700 hover:bg-white font-bold text-xs focus:ring-2 focus:ring-[#244F73]" aria-label="Decrease text size">A-</button>
                    <button type="button" @click="resetScale()" class="px-2 py-1 text-gray-700 font-semibold text-xs hover:bg-white" aria-label="Reset text size to 100%">
                        <span x-text="a11yScale + '%'">100%</span>
                    </button>
                    <button type="button" @click="increaseScale()" class="px-2 py-1 rounded text-gray-700 hover:bg-white font-bold text-xs focus:ring-2 focus:ring-[#244F73]" aria-label="Increase text size">A+</button>
                </div>

                <!-- Dyslexia Font Toggle -->
                <button type="button"
                        @click="toggleDyslexia()"
                        :class="a11yDyslexia ? 'bg-[#1A2840] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md font-medium border border-gray-300 transition-colors focus:ring-2 focus:ring-[#244F73]"
                        :aria-pressed="a11yDyslexia.toString()"
                        aria-label="Toggle dyslexia-friendly font">
                    <span class="font-serif font-bold text-xs" aria-hidden="true">T</span>
                    <span>Dyslexia-friendly font</span>
                </button>

                <!-- Screen Reader Helper Toggle -->
                <button type="button"
                        @click="toggleScreenReader()"
                        :class="a11yScreenReader ? 'bg-[#1A2840] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md font-medium border border-gray-300 transition-colors focus:ring-2 focus:ring-[#244F73]"
                        :aria-pressed="a11yScreenReader.toString()"
                        aria-label="Toggle screen reader live assistance">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                    </svg>
                    <span>Screen reader</span>
                </button>
            </div>

            <!-- Right: Settings, Notifications, Accessibility Menu, Logout -->
            <div class="flex items-center gap-4 text-xs font-medium text-gray-700">
                <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-1.5 text-gray-600 hover:text-gray-900 transition-colors focus:ring-2 focus:ring-[#244F73] rounded p-1">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Settings</span>
                </a>

                <livewire:notification-bell />

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 text-gray-600 hover:text-red-700 transition-colors focus:ring-2 focus:ring-[#244F73] rounded p-1 cursor-pointer">
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
</body>
</html>
