@php
    $navItems = [
        [
            'label' => 'Home',
            'href' => route('home'),
            'active' => request()->routeIs('home') || request()->is('/'),
        ],
        [
            'label' => 'Jobs',
            'href' => route('jobs'),
            'active' => request()->routeIs('jobs') || request()->is('jobs*'),
        ],
        [
            'label' => 'Employers',
            'href' => route('employers'),
            'active' => request()->routeIs('employers') || request()->is('employers*'),
        ],
        [
            'label' => 'About',
            'href' => route('about'),
            'active' => request()->routeIs('about') || request()->is('about*'),
        ],
    ];
@endphp

<header x-data="{ scrolled: false, menuOpen: false }"
        @scroll.window="scrolled = window.scrollY > 480"
        :class="scrolled ? 'bg-primary-dark text-white shadow' : 'bg-transparent text-primary'"
        class="fixed top-0 left-0 right-0 z-50 transition-colors">
    <nav class="max-w-[1200px] mx-auto px-6 sm:px-10 lg:px-12 h-16 flex items-center justify-between" aria-label="Primary">
        <div class="flex items-center gap-8">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-heading font-bold text-lg focus:outline-none focus:ring-0">
                <img src="{{ asset('assets/Logo.png') }}" alt="PWD-E" class="h-14 w-14 shrink-0 object-contain">
                <span>PWD-E</span>
            </a>
            <ul class="hidden md:flex items-center gap-6 text-sm">
                @foreach($navItems as $item)
                    <li>
                        <a href="{{ $item['href'] }}"
                           @if($item['active']) aria-current="page" @endif
                           :class="scrolled
                               ? '{{ $item['active'] ? 'text-white border-white font-bold border-b-2' : 'text-white/80 hover:text-white hover:border-white/50 font-medium border-b-2 border-transparent' }}'
                               : '{{ $item['active'] ? 'text-primary-dark border-primary-dark font-bold border-b-2' : 'text-primary hover:text-primary-dark hover:border-primary/40 font-medium border-b-2 border-transparent' }}'"
                           class="inline-flex items-center py-1.5 transition-all focus:outline-none focus:ring-0">
                            {{ $item['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="flex items-center gap-3 text-sm">
            @unless(request()->routeIs('login'))
                <a href="{{ route('login') }}" class="hidden md:inline font-medium hover:underline focus:outline-none focus:ring-0">Sign In</a>
            @endunless
            @unless(request()->routeIs('register'))
                <a href="{{ route('register') }}" class="hidden md:inline bg-primary text-white font-semibold px-4 py-2 rounded-lg hover:bg-primary-dark transition-colors focus:outline-none focus:ring-0">Register</a>
            @endunless
            <button type="button"
                    @click="menuOpen = !menuOpen"
                    class="md:hidden w-11 h-11 flex items-center justify-center rounded-lg focus:outline-none focus:ring-0"
                    aria-label="Menu"
                    :aria-expanded="menuOpen.toString()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </nav>
    <div x-show="menuOpen" x-cloak class="md:hidden bg-primary-dark text-white px-4 py-4 space-y-1.5 shadow-lg">
        @foreach($navItems as $item)
            <a href="{{ $item['href'] }}"
               @if($item['active']) aria-current="page" @endif
               class="min-h-[48px] flex items-center px-3 rounded-lg transition-colors focus:outline-none focus:ring-0 {{ $item['active'] ? 'font-bold bg-white/15 text-white border-l-4 border-white' : 'font-medium text-white/85 hover:bg-white/10 hover:text-white' }}">
                {{ $item['label'] }}
            </a>
        @endforeach
        @unless(request()->routeIs('login'))
            <a href="{{ route('login') }}" class="block min-h-[48px] flex items-center px-3 font-medium text-white/90 hover:bg-white/10 rounded-lg focus:outline-none focus:ring-0">Sign In</a>
        @endunless
        @unless(request()->routeIs('register'))
            <a href="{{ route('register') }}" class="block min-h-[48px] flex items-center bg-primary font-semibold rounded-lg px-3 hover:bg-primary/90 focus:outline-none focus:ring-0">Register</a>
        @endunless
    </div>
</header>
