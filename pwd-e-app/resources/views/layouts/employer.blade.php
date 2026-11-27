<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{
          a11yContrastLevel: parseInt(localStorage.getItem('pwde_contrast_level') || (localStorage.getItem('pwde_contrast') === 'high' ? '125' : '100')),
          a11yScale: (function () { const v = parseInt(localStorage.getItem('pwde_scale') || (window.__pwdeInitialScale || '100'), 10); return (!isNaN(v) && v >= 90 && v <= 150) ? v : 100; })(),
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
          applyScale() {
              this.a11yScale = Math.min(150, Math.max(90, parseInt(this.a11yScale, 10) || 100));
              document.documentElement.style.fontSize = this.a11yScale + '%';
              document.documentElement.classList.toggle('a11y-large-text', this.a11yScale >= 125);
          },
          increaseScale() { if (this.a11yScale < 150) { this.a11yScale += 10; localStorage.setItem('pwde_scale', this.a11yScale); this.applyScale(); } },
          decreaseScale() { if (this.a11yScale > 90) { this.a11yScale -= 10; localStorage.setItem('pwde_scale', this.a11yScale); this.applyScale(); } },
          resetScale() { this.a11yScale = 100; localStorage.setItem('pwde_scale', '100'); this.applyScale(); },
          toggleDyslexia() { this.a11yDyslexia = !this.a11yDyslexia; localStorage.setItem('pwde_dyslexia', this.a11yDyslexia ? 'true' : 'false'); },
          toggleScreenReader() { this.a11yScreenReader = !this.a11yScreenReader; localStorage.setItem('pwde_screenreader', this.a11yScreenReader ? 'true' : 'false'); const msg = this.a11yScreenReader ? 'Screen reader assistive enhancements enabled' : 'Screen reader enhancements disabled'; const live = document.getElementById('a11y-announcer'); if (live) live.textContent = msg; }
      }"
      x-init="
          applyScale();
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
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-white focus:text-[#1A2840] focus:ring-2 focus:ring-[#244F73] focus:shadow-lg focus:rounded-md">Skip to main content</a>
    <div id="a11y-announcer" class="sr-only" role="status" aria-live="polite" aria-atomic="true"></div>

    @php
        $employerUser = auth()->user();
        $employerProfile = $employerUser?->employerProfile;
        $employerApplicantsCount = \App\Models\Application::whereHas('jobPosting', function ($q) use ($employerProfile) {
            $q->where('employer_profile_id', $employerProfile?->id ?? 0);
        })->count();
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
                    <span class="block text-[10px] text-gray-300 font-normal">Employer portal</span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <span class="text-emerald-400 font-medium">Verified Employer</span>
        </div>
    </header>

    <!-- Sidebar Navigation -->
    <aside :class="{ 'block': mobileMenuOpen, 'hidden lg:flex': !mobileMenuOpen }"
           class="w-full lg:w-64 bg-[#0e1726] text-white flex-col justify-between shrink-0 lg:sticky lg:top-0 lg:h-screen overflow-y-auto px-4 py-4 z-40 transition-all duration-200"
           role="navigation" aria-label="Employer Portal Workspace Navigation">
        <div>
            <!-- Compact Branding -->
            <div class="flex items-center gap-2.5 pb-3.5 border-b border-[#1e293b]">
                <div class="w-8 h-8 rounded-full overflow-hidden shrink-0 ring-1 ring-white/15 bg-white/5 shadow-xs">
                    <img src="{{ asset('assets/Logo.png') }}" alt="" class="w-full h-full object-cover scale-[1.9]" aria-hidden="true">
                </div>
                <div class="min-w-0">
                    <span class="font-bold text-[15px] text-white tracking-tight leading-tight block">PWD-E</span>
                    <span class="block text-[11px] text-gray-400 font-normal leading-tight mt-0.5">Employer portal</span>
                </div>
            </div>

            <!-- Workspace Menu -->
            <div class="pt-5">
                <div class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-3 px-3">YOUR WORKSPACE</div>
                <nav class="space-y-1.5" aria-label="Employer workspace links">
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 {{ request()->routeIs('dashboard') ? 'bg-[#244F73] text-white shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                       @if(request()->routeIs('dashboard')) aria-current="page" @endif>
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('employer.jobs.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 {{ request()->routeIs('employer.jobs.*') ? 'bg-[#244F73] text-white shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                       @if(request()->routeIs('employer.jobs.*')) aria-current="page" @endif>
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('employer.jobs.*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>Job Postings</span>
                    </a>

                    <a href="{{ route('employer.talents') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 {{ request()->routeIs('employer.talents') ? 'bg-[#244F73] text-white shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                       @if(request()->routeIs('employer.talents')) aria-current="page" @endif>
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('employer.talents') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                        </svg>
                        <span>Talent Discovery</span>
                    </a>

                    <a href="{{ route('employer.applicants') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 {{ request()->routeIs('employer.applicants') || request()->routeIs('employer.jobs.applicants') ? 'bg-[#244F73] text-white shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                       @if(request()->routeIs('employer.applicants') || request()->routeIs('employer.jobs.applicants')) aria-current="page" @endif>
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('employer.applicants') || request()->routeIs('employer.jobs.applicants') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="flex-1">Applicants</span>
                        @if($employerApplicantsCount > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                {{ $employerApplicantsCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('employer.workforce') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 {{ request()->routeIs('employer.workforce') ? 'bg-[#244F73] text-white shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                       @if(request()->routeIs('employer.workforce')) aria-current="page" @endif>
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('employer.workforce') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Workforce &amp; Tax</span>
                    </a>

                    @if($employerProfile)
                        <a href="{{ route('employer.profile') }}"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 {{ request()->routeIs('employer.profile') ? 'bg-[#244F73] text-white shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                           @if(request()->routeIs('employer.profile')) aria-current="page" @endif>
                            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('employer.profile') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span>Company Profile</span>
                        </a>
                    @endif
                </nav>
            </div>
        </div>

        <!-- Bottom Support & Company Profile Area -->
        <div class="mt-6 pt-4 border-t border-[#1e293b] space-y-3">
            <!-- Compact Help & Support Widget -->
            <div class="px-3.5 py-3 rounded-lg bg-[#132238] border border-[#1e293b]">
                <div class="flex items-center gap-1.5 text-sky-400 mb-1">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                    <span class="text-xs font-semibold text-white">Here to support you</span>
                </div>
                <p class="text-[11px] text-gray-300 leading-snug mb-2">
                    Find answers, FSL video support, or report a barrier.
                </p>
                <a href="{{ route('about') }}" class="inline-flex items-center gap-1 text-[11px] font-semibold text-sky-300 hover:text-white transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 rounded">
                    <span>Visit Help Center</span>
                    <span aria-hidden="true">&rarr;</span>
                </a>
            </div>

            <!-- Employer Profile Card -->
            <div class="flex items-start gap-2.5 px-2.5 py-2.5 rounded-lg bg-white/[0.03] border border-[#1e293b]">
                <div class="w-9 h-9 rounded-full bg-[#244F73] text-white flex items-center justify-center font-semibold text-xs border border-white/20 shrink-0 mt-0.5">
                    {{ $employerUser?->initials() ?? 'AE' }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="font-semibold text-[13px] text-white truncate leading-snug" title="{{ $employerProfile?->company_name ?? $employerUser?->name ?? 'Acme Accessibility' }}">
                        {{ $employerProfile?->company_name ?? $employerUser?->name ?? 'Acme Accessibility' }}
                    </div>
                    <div class="flex items-center gap-1 mt-0.5 text-[11px] text-emerald-400">
                        <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="truncate">Verified Employer</span>
                    </div>
                    @if($employerProfile)
                        <div class="mt-1.5 flex items-center gap-1.5 text-[11px]">
                            <a href="{{ route('employer.profile') }}?tab=edit" class="text-sky-400 hover:text-sky-300 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 rounded">
                                {{ __('Edit Profile') }}
                            </a>
                            <span class="text-gray-400/60" aria-hidden="true">&middot;</span>
                            <a href="{{ route('candidate.employers.show', $employerProfile) }}" target="_blank" rel="noopener noreferrer" class="text-gray-300 hover:text-white hover:underline inline-flex items-center gap-0.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 rounded">
                                <span>{{ __('Public View') }}</span>
                                <svg class="size-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 lg:h-screen lg:overflow-y-auto">
        <!-- Top Accessibility & Navigation Header -->
        @include('partials.accessibility-header-bar')

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