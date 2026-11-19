<footer class="bg-[#142033] text-white border-t border-white/10" aria-labelledby="footer-heading">
    <h2 id="footer-heading" class="sr-only">Footer</h2>
    <div class="max-w-[1200px] mx-auto px-6 sm:px-10 lg:px-12 py-12 sm:py-16">
        <div class="grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-12 lg:gap-8">
            {{-- Brand column --}}
            <div class="lg:col-span-4 space-y-4">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3 font-heading font-bold text-2xl text-white tracking-tight">
                    <img src="{{ asset('assets/Logo.png') }}" alt="" class="h-10 w-auto" aria-hidden="true">
                    <span>PWD-E</span>
                </a>
                <p class="text-sm text-white/70 leading-relaxed max-w-sm">
                    Connecting Persons with Disabilities to accessible, barrier-free, and inclusive workplaces across the Philippines.
                </p>
                <div class="rounded-xl border border-white/10 bg-white/5 p-3.5 backdrop-blur-xs flex items-center gap-3 max-w-sm">
                    <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-primary/40 text-white font-bold text-xs tracking-wider border border-white/10" aria-hidden="true">WMSU</span>
                    <div class="text-xs">
                        <p class="font-semibold text-white/90">Western Mindanao State University</p>
                        <p class="text-white/60">College of Computing Studies</p>
                    </div>
                </div>
            </div>

            {{-- Portals --}}
            <div class="lg:col-span-2">
                <h3 class="font-heading text-xs font-bold uppercase tracking-wider text-white">Portals</h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('home') }}" class="text-white/70 hover:text-white transition-colors duration-150">Home</a></li>
                    <li><a href="{{ route('jobs') }}" class="text-white/70 hover:text-white transition-colors duration-150">Candidate Portal</a></li>
                    <li><a href="{{ route('employers') }}" class="text-white/70 hover:text-white transition-colors duration-150">Employer Portal</a></li>
                    <li><a href="{{ route('login') }}" class="text-white/70 hover:text-white transition-colors duration-150">Account Sign In</a></li>
                    <li><a href="{{ route('register') }}" class="text-white/70 hover:text-white transition-colors duration-150">New Registration</a></li>
                </ul>
            </div>

            {{-- Compliance & Legal --}}
            <div class="lg:col-span-3">
                <h3 class="font-heading text-xs font-bold uppercase tracking-wider text-white">Statutory &amp; Legal</h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li>
                        <a href="{{ route('about') }}#accessibility" class="text-white/70 hover:text-white transition-colors duration-150">
                            Accessibility Statement
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('about') }}#privacy" class="text-white/70 hover:text-white transition-colors duration-150">
                            Privacy Policy (RA 10173)
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('about') }}#accessibility" class="text-white/70 hover:text-white transition-colors duration-150">
                            RA 7277 (Magna Carta for PWDs)
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('employers') }}" class="text-white/70 hover:text-white transition-colors duration-150">
                            RA 10524 (Tax Incentives)
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('about') }}#accessibility" class="text-white/70 hover:text-white transition-colors duration-150">
                            RA 11106 (Filipino Sign Language)
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Help & Contact --}}
            <div class="lg:col-span-3">
                <h3 class="font-heading text-xs font-bold uppercase tracking-wider text-white">Help &amp; Support</h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('about') }}" class="text-white/70 hover:text-white transition-colors duration-150">About PWD-E</a></li>
                    <li>
                        <a href="mailto:support@pwd-e.local" class="text-white/70 hover:text-white transition-colors duration-150 inline-flex items-center gap-1.5">
                            <svg class="size-4 shrink-0 text-white/50" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>support@pwd-e.local</span>
                        </a>
                    </li>
                    <li class="pt-1">
                        <span class="block text-xs text-white/60 leading-relaxed">
                            Public Employment Service Office (PESO) Collaboration · Zamboanga City, Philippines
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Compliance Badges Bar --}}
        <div class="mt-10 pt-8 border-t border-white/10 flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-white/80">
                    <svg class="size-3.5 text-[#4CAF50]" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    WCAG 2.1 AA Compliant
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-white/80">
                    <svg class="size-3.5 text-[#2196F3]" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    RA 10524 Verified Quota
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-white/80">
                    <svg class="size-3.5 text-[#FFC107]" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                    RA 10173 DPA Protected
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-white/80">
                    <svg class="size-3.5 text-[#00BCD4]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"/></svg>
                    RA 11106 FSL Integrated
                </span>
            </div>

            {{-- Back to top button --}}
            <a href="#main" @click.prevent="window.scrollTo({top: 0, behavior: 'smooth'})" class="inline-flex items-center gap-2 text-xs font-medium text-white/70 hover:text-white transition-colors duration-150 py-1.5 px-3 rounded-lg border border-white/10 hover:border-white/20 bg-white/5 hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-primary/40">
                <span>Back to Top</span>
                <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
            </a>
        </div>

        {{-- Bottom Sub-Footer --}}
        <div class="mt-6 pt-6 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-white/60">
            <p>© 2026 PWD-E. Empowering inclusive employment across the Philippines.</p>
            <div class="flex items-center gap-4">
                <a href="{{ route('about') }}#privacy" class="hover:text-white transition-colors">Privacy Policy</a>
                <span class="text-white/20" aria-hidden="true">·</span>
                <a href="{{ route('about') }}#accessibility" class="hover:text-white transition-colors">Accessibility</a>
                <span class="text-white/20" aria-hidden="true">·</span>
                <a href="{{ route('about') }}" class="hover:text-white transition-colors">About</a>
            </div>
        </div>
    </div>
</footer>
