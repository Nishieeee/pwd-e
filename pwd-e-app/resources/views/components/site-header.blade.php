<header x-data="{ scrolled: false }" @scroll.window="scrolled = window.scrollY > 480" :class="scrolled ? 'bg-primary-dark text-white shadow' : 'bg-transparent text-primary'" class="fixed top-0 left-0 right-0 z-50 transition-colors">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between" aria-label="Primary">
        <div class="flex items-center gap-8">
            <a href="/" class="flex items-center gap-2 font-heading font-bold text-lg"><img src="{{ asset('assets/Logo.png') }}" alt="PWD-E" class="h-24 w-auto">PWD-E</a>
            <ul class="hidden md:flex gap-6 text-sm">
                <li><a href="/" class="hover:underline">Home</a></li>
                <li><a href="/jobs" class="hover:underline">Jobs</a></li>
                <li><a href="/employers" class="hover:underline">Employers</a></li>
                <li><a href="/about" class="hover:underline">About</a></li>
            </ul>
        </div>
        <div class="flex items-center gap-3 text-sm">
            <a href="/login" class="hover:underline">Sign In</a>
            <a href="/register" class="bg-primary text-white font-semibold px-4 py-2 rounded-lg hover:bg-primary-dark">Register</a>
        </div>
    </nav>
</header>