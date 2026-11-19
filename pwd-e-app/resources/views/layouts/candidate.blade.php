<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{
          a11yContrast: localStorage.getItem('pwde_contrast') === 'high',
          a11yScale: parseInt(localStorage.getItem('pwde_scale') || '100'),
          a11yDyslexia: localStorage.getItem('pwde_dyslexia') === 'true',
          a11yScreenReader: localStorage.getItem('pwde_screenreader') === 'true',
          mobileMenuOpen: false,
          toggleContrast() {
              this.a11yContrast = !this.a11yContrast;
              localStorage.setItem('pwde_contrast', this.a11yContrast ? 'high' : 'normal');
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
      "
      :class="{ 'contrast-more': a11yContrast, 'high-contrast-mode': a11yContrast }"
      :data-readable-font="a11yDyslexia ? 'true' : 'false'"
>
<head>
    @include('partials.head')
    <style>
        .high-contrast-mode {
            filter: contrast(125%);
        }
        .high-contrast-mode body {
            background-color: #ffffff !important;
            color: #000000 !important;
        }
        [data-readable-font="true"] body, [data-readable-font="true"] * {
            font-family: 'Atkinson Hyperlegible Next', 'Lexend', ui-sans-serif, system-ui, sans-serif !important;
            letter-spacing: 0.02em !important;
        }
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
        $candidateUser = auth()->user();
        $candidateProfile = $candidateUser?->candidateProfile;
        $activeApplicationsCount = $candidateProfile ? $candidateProfile->applications()->whereIn('status', ['applied', 'under_review', 'interview', 'submitted'])->count() : 0;
    @endphp

    <!-- Mobile Header Bar -->
    <header class="lg:hidden flex items-center justify-between bg-[#0e1726] text-white px-4 py-3 border-b border-[#1e293b]" role="banner">
        <div class="flex items-center gap-3">
            <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" class="p-1 rounded-md text-white hover:bg-[#1e293b] focus:outline-none focus:ring-2 focus:ring-white" aria-label="Toggle navigation menu" :aria-expanded="mobileMenuOpen.toString()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-[#176B47] flex items-center justify-center font-bold text-white text-xs">PWD</div>
                <div class="font-bold text-base leading-tight">PWD-E <span class="block text-[10px] text-gray-300 font-normal">Candidate portal</span></div>
            </div>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <span class="text-emerald-400 font-medium">PWD ID Verified</span>
        </div>
    </header>

    <!-- Sidebar Navigation -->
    <aside :class="{ 'block': mobileMenuOpen, 'hidden lg:flex': !mobileMenuOpen }"
           class="w-full lg:w-64 bg-[#0e1726] text-white flex-col justify-between shrink-0 lg:sticky lg:top-0 lg:h-screen overflow-y-auto p-5 z-40 transition-all duration-200"
           role="navigation"
           aria-label="Candidate Portal Workspace Navigation">
        <div>
            <!-- Branding -->
            <div class="flex items-center gap-3 pb-6 border-b border-[#1e293b]">
                <div class="w-10 h-10 rounded-full bg-[#176B47] flex items-center justify-center text-white font-bold text-sm shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="7" r="4" stroke-width="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 21v-2a4 4 0 014-4h4a4 4 0 014 4v2"/>
                    </svg>
                </div>
                <div>
                    <span class="font-bold text-lg text-white tracking-tight">PWD-E</span>
                    <span class="block text-xs text-gray-300 font-normal leading-none mt-0.5">Candidate portal</span>
                </div>
            </div>

            <!-- Candidate Profile Snippet -->
            <div class="flex items-center gap-3 py-5 border-b border-[#1e293b]">
                <div class="w-10 h-10 rounded-full bg-[#244F73] text-white flex items-center justify-center font-semibold text-sm border border-white/20 shrink-0">
                    {{ $candidateUser?->initials() ?? 'MS' }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="font-semibold text-sm text-white truncate">{{ $candidateUser?->name ?? 'Maria Clara Santos' }}</div>
                    <div class="flex items-center gap-1.5 mt-0.5 text-xs text-emerald-400">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="truncate">PWD ID Verified</span>
                    </div>
                </div>
            </div>

            <!-- Workspace Menu -->
            <div class="pt-6">
                <div class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-3 px-3">
                    YOUR WORKSPACE
                </div>
                <nav class="space-y-1.5" aria-label="Candidate workspace links">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-[#244F73] text-white shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                       @if(request()->routeIs('dashboard')) aria-current="page" @endif>
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <!-- View my Profile -->
                    <a href="{{ route('candidate.profile.show') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('candidate.profile.show') ? 'bg-[#244F73] text-white shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                       @if(request()->routeIs('candidate.profile.show')) aria-current="page" @endif>
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('candidate.profile.show') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                        </svg>
                        <span>View my Profile</span>
                    </a>

                    <!-- Profile Builder -->
                    <a href="{{ route('candidate.profile.builder') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('candidate.profile.builder') ? 'bg-[#244F73] text-white shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                       @if(request()->routeIs('candidate.profile.builder')) aria-current="page" @endif>
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('candidate.profile.builder') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>Profile Builder</span>
                    </a>

                    <!-- Job Matches & Search -->
                    <a href="{{ route('candidate.jobs') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('candidate.jobs') ? 'bg-[#244F73] text-white shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                       @if(request()->routeIs('candidate.jobs')) aria-current="page" @endif>
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('candidate.jobs') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>Job Matches &amp; Search</span>
                    </a>

                    <!-- Applications -->
                    <a href="{{ route('candidate.applications') }}"
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('candidate.applications') ? 'bg-[#244F73] text-white shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                       @if(request()->routeIs('candidate.applications')) aria-current="page" @endif>
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('candidate.applications') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Applications</span>
                        </div>
                        @if($activeApplicationsCount > 0)
                            <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold rounded-full bg-white/20 text-white" aria-label="{{ $activeApplicationsCount }} active applications">
                                {{ $activeApplicationsCount }}
                            </span>
                        @endif
                    </a>
                </nav>
            </div>
        </div>

        <!-- Help & Support Widget -->
        <div class="mt-8 pt-4">
            <div class="p-4 rounded-xl bg-[#132238] border border-[#1e293b]">
                <div class="flex items-center gap-2 text-sky-400 mb-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                    <span class="text-xs font-semibold text-white">Here to support you</span>
                </div>
                <p class="text-xs text-gray-300 leading-relaxed mb-3">
                    Find answers, FSL video support, or report a barrier.
                </p>
                <a href="{{ route('about') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-sky-300 hover:text-white transition-colors">
                    <span>Visit Help Center</span>
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

                <!-- High Contrast Toggle -->
                <button type="button"
                        @click="toggleContrast()"
                        :class="a11yContrast ? 'bg-[#1A2840] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md font-medium border border-gray-300 transition-colors focus:ring-2 focus:ring-[#244F73]"
                        :aria-pressed="a11yContrast.toString()"
                        aria-label="Toggle high contrast mode">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke-width="2"/>
                        <path fill="currentColor" d="M12 3a9 9 0 000 18V3z"/>
                    </svg>
                    <span>Contrast</span>
                </button>

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

            <!-- Right: Settings, Notifications, Logout -->
            <div class="flex items-center gap-4 text-xs font-medium text-gray-700">
                <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-1.5 text-gray-600 hover:text-gray-900 transition-colors focus:ring-2 focus:ring-[#244F73] rounded p-1">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Settings</span>
                </a>

                <button type="button" class="inline-flex items-center gap-1.5 text-gray-600 hover:text-gray-900 transition-colors focus:ring-2 focus:ring-[#244F73] rounded p-1" aria-label="View notifications">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span>Notifications</span>
                </button>

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

