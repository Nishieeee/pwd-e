<!DOCTYPE html>
<html lang="en" x-data="{ readable: false }" :data-readable-font="readable ? 'true' : null">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PWD-E — Connecting Persons with Disabilities to Accessible & Inclusive Workplaces</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-white text-dark-neutral antialiased">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-50 focus:bg-primary-dark focus:text-white focus:px-3 focus:py-2 focus:rounded">Skip to main content</a>

    {{-- Top nav --}}
    <x-site-header />

    {{-- A11y toolbar --}}
    <div class="fixed top-20 right-4 z-40 bg-primary-dark text-white rounded-xl shadow-lg p-2 flex items-center gap-3 text-xs" role="group" aria-label="Accessibility controls">
        <button type="button" class="px-2 py-1 rounded hover:bg-white/10" aria-pressed="false">High Contrast</button>
        <label class="flex items-center gap-1">
            <span>Font</span>
            <input type="range" min="100" max="200" value="100" class="w-20" aria-label="Font resize">
        </label>
        <button type="button" class="px-2 py-1 rounded hover:bg-white/10" aria-pressed="false">Screen Reader</button>
        <button type="button" @click="readable = !readable" class="px-2 py-1 rounded hover:bg-white/10" :aria-pressed="readable">Readable Font</button>
    </div>

    <main id="main">
        {{-- S1 Hero --}}
        <section class="relative bg-primary-tint" style="background-image: url('{{ asset('assets/Background.jpg') }}'); background-size: cover; background-position: center;">
            <div class="absolute inset-0 bg-white/70" aria-hidden="true"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-16 text-center">
                <span class="inline-flex items-center gap-2 bg-white text-primary-dark text-sm font-medium px-4 py-2 rounded-full border border-primary-border">
                    <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    WCAG 2.1 Level AA &amp; Republic Act No. 10524 / 11036 Compliant
                </span>
                <h1 class="font-heading text-primary-dark text-4xl sm:text-5xl font-bold mt-6 max-w-3xl mx-auto">Connecting Persons with Disabilities to Accessible &amp; Inclusive Workplaces.</h1>
                <p class="text-body-muted mt-4 max-w-2xl mx-auto">A barrier-free employment platform matching PWD candidates with verified inclusive employers.</p>
                <div class="mt-8 bg-white rounded-2xl shadow-sm border border-primary-border p-4 max-w-5xl mx-auto">
                    <label for="search" class="block text-sm font-medium text-primary-dark mb-2">Search for Inclusive Opportunities (Skill, Location, and Accommodation)</label>
                    <div class="flex gap-2">
                        <input id="search" type="search" class="flex-1 rounded-lg border border-primary-border bg-surface px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-primary" placeholder="e.g. Remote data entry, FSL support">
                        <button type="button" class="bg-primary-dark text-white font-semibold px-6 py-3 rounded-lg hover:bg-primary">Find Inclusive Jobs</button>
                    </div>
                    <div class="mt-6 flex flex-wrap justify-center gap-2">
                        <span class="text-sm text-body-muted">Popular Accommodations:</span>
                        @foreach (['Ramp Access','Screen Reader Ready','FSL Support','Remote/Hybrid','Braille Elevators'] as $pill)
                            <span class="text-sm border border-primary-border rounded-full px-3 py-1 text-primary-dark">{{ $pill }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- S2 Why Choose --}}
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center">
                <span class="inline-block bg-primary-tint text-primary-dark text-sm font-medium px-4 py-2 rounded-full">Our Purpose &amp; Vision</span>
                <h2 class="font-heading text-primary-dark text-3xl font-bold mt-4">Why Choose PWD-E?</h2>
                <p class="text-body-muted mt-3 max-w-2xl mx-auto">Built for both candidates and organizations, with accessibility at the core.</p>
            </div>
            <div class="grid md:grid-cols-2 gap-6 mt-10">
                <article class="border-t-4 border-primary rounded-2xl bg-white shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-lg bg-primary-tint flex items-center justify-center" aria-hidden="true"><img src="{{ asset('icons/user-round.png') }}" alt="" class="w-6 h-6"></div>
                        <span class="text-xs font-medium bg-primary-tint text-primary px-3 py-1 rounded-full">Tailored for Candidates</span>
                    </div>
                    <h3 class="font-heading text-primary-dark text-xl font-semibold mt-4">For Jobseekers</h3>
                    <blockquote class="bg-surface rounded-lg p-3 mt-3 text-sm text-body-muted">"Find work that fits you, not the other way around."</blockquote>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li class="flex gap-2"><span class="text-secondary">✓</span> FSL &amp; voice signup flow</li>
                        <li class="flex gap-2"><span class="text-secondary">✓</span> Undo barriers in applications</li>
                        <li class="flex gap-2"><span class="text-secondary">✓</span> Verified PESO/DOLE employers</li>
                    </ul>
                    <a href="/jobs" class="mt-6 block text-center bg-primary-dark text-white font-semibold py-3 rounded-lg hover:bg-primary">Explore Candidate Portal →</a>
                </article>
                <article class="border-t-4 border-secondary rounded-2xl bg-white shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-lg bg-secondary-tint flex items-center justify-center" aria-hidden="true"><img src="{{ asset('icons/building.png') }}" alt="" class="w-6 h-6"></div>
                        <span class="text-xs font-medium bg-secondary-tint text-secondary px-3 py-1 rounded-full">Tailored for Organizations</span>
                    </div>
                    <h3 class="font-heading text-primary-dark text-xl font-semibold mt-4">For Employers</h3>
                    <blockquote class="bg-surface rounded-lg p-3 mt-3 text-sm text-body-muted">"Hire inclusively and claim your statutory benefits."</blockquote>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li class="flex gap-2"><span class="text-secondary">✓</span> Skilled PWD talent pool</li>
                        <li class="flex gap-2"><span class="text-secondary">✓</span> Accessibility matrix per role</li>
                        <li class="flex gap-2"><span class="text-secondary">✓</span> 25% tax deduction support</li>
                    </ul>
                    <a href="/employers" class="mt-6 block text-center bg-secondary text-white font-semibold py-3 rounded-lg hover:bg-secondary/90">Join as Inclusive Employer →</a>
                </article>
            </div>
        </section>

        {{-- S3 How it works --}}
        <section class="bg-surface py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <span class="inline-block bg-primary-tint text-primary-dark text-sm font-medium px-4 py-2 rounded-full">Seamless 3-Step Journey</span>
                    <h2 class="font-heading text-primary-dark text-3xl font-bold mt-4">How PWD-E Works</h2>
                    <p class="text-body-muted mt-3 max-w-2xl mx-auto">Three steps from profile to placement.</p>
                </div>
                <div class="grid md:grid-cols-3 gap-6 mt-10">
                    @foreach ([
                        ['step'=>'1','title'=>'Build Your Multimodal Profile','body'=>'Create a profile with voice, FSL, or text input.','cta'=>'Try Voice Setup'],
                        ['step'=>'2','title'=>'Get Accessibility-Matched','body'=>'Our engine matches your needs to inclusive roles.','cta'=>'View Matched Jobs'],
                        ['step'=>'3','title'=>'Apply & Thrive','body'=>'Apply barrier-free and get ongoing support.','cta'=>'Browse Opportunities'],
                    ] as $s)
                        <article class="relative bg-white rounded-2xl shadow-sm border border-primary-border p-6">
                            <span class="absolute -top-3 left-4 bg-primary-dark text-white text-xs font-bold px-3 py-1 rounded-full">STEP {{ $s['step'] }}</span>
                            <div class="w-10 h-10 rounded-lg bg-primary-tint flex items-center justify-center mt-2" aria-hidden="true"><img src="{{ asset('icons/compass.png') }}" alt="" class="w-6 h-6"></div>
                            <h3 class="font-heading text-primary-dark text-lg font-semibold mt-4">{{ $s['title'] }}</h3>
                            <p class="text-body-muted text-sm mt-2">{{ $s['body'] }}</p>
                            <a href="#" class="mt-6 block text-center border border-primary-border text-primary-dark font-semibold py-2 rounded-lg hover:bg-primary-tint">{{ $s['cta'] }}</a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- S4 Features --}}
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center">
                <span class="inline-block bg-primary-tint text-primary-dark text-sm font-medium px-4 py-2 rounded-full">Platform Innovations</span>
                <h2 class="font-heading text-primary-dark text-3xl font-bold mt-4">Empowering Features Showcase</h2>
                <p class="text-body-muted mt-3 max-w-2xl mx-auto">Tools that remove barriers at every step.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6 mt-10">
                <article class="bg-white rounded-2xl shadow-sm border border-primary-border p-6">
                    <div class="w-10 h-10 rounded-lg bg-primary-tint flex items-center justify-center" aria-hidden="true"><img src="{{ asset('icons/scan-text.png') }}" alt="" class="w-6 h-6"></div>
                    <span class="inline-block mt-3 text-xs font-medium bg-primary-tint text-primary px-3 py-1 rounded-full">Real-Time NLP Scan</span>
                    <h3 class="font-heading text-primary-dark text-lg font-semibold mt-3">Exclusive Language Detector</h3>
                    <p class="text-body-muted text-sm mt-2">Flags exclusionary job language before posting.</p>
                    <div class="bg-surface rounded-lg p-3 mt-3 text-sm">
                        <p class="text-red-600">✕ Must have normal hearing and speech</p>
                        <p class="text-secondary">✓ Effective communication skills via written or FSL</p>
                    </div>
                    <a href="#" class="mt-6 block text-center bg-primary-dark text-white font-semibold py-3 rounded-lg hover:bg-primary">Test Inclusive Job Creator</a>
                </article>
                <article class="bg-white rounded-2xl shadow-sm border border-primary-border p-6">
                    <div class="w-10 h-10 rounded-lg bg-secondary-tint flex items-center justify-center" aria-hidden="true"><img src="{{ asset('icons/file-chart-column.png') }}" alt="" class="w-6 h-6"></div>
                    <span class="inline-block mt-3 text-xs font-medium bg-secondary-tint text-secondary px-3 py-1 rounded-full">Philippines Tax Code</span>
                    <h3 class="font-heading text-primary-dark text-lg font-semibold mt-3">RA 10524 Tax Savings Calculator</h3>
                    <p class="text-body-muted text-sm mt-2">Quantify the 25% additional deduction benefit.</p>
                    <div class="bg-surface rounded-lg p-3 mt-3 text-sm text-secondary font-semibold">Measurable ROI for inclusive hiring.</div>
                    <a href="#" class="mt-6 block text-center bg-secondary text-white font-semibold py-3 rounded-lg hover:bg-secondary/90">Open Tax Calculator</a>
                </article>
                <article class="bg-white rounded-2xl shadow-sm border border-primary-border p-6">
                    <div class="w-10 h-10 rounded-lg bg-primary-tint flex items-center justify-center" aria-hidden="true"><img src="{{ asset('icons/lock-keyhole.png') }}" alt="" class="w-6 h-6"></div>
                    <span class="inline-block mt-3 text-xs font-medium bg-primary-tint text-primary-dark px-3 py-1 rounded-full">RA 10773 Compliant</span>
                    <h3 class="font-heading text-primary-dark text-lg font-semibold mt-3">Granular Privacy Controls</h3>
                    <p class="text-body-muted text-sm mt-2">Candidates control who sees their disability data.</p>
                    <div class="bg-surface rounded-lg p-3 mt-3 text-sm space-y-1">
                        <p class="font-medium text-primary-dark">Candidate Control:</p>
                        <p class="text-body-muted">Per-field visibility · consent logs · revoke anytime</p>
                    </div>
                    <a href="#" class="mt-6 block text-center bg-primary-dark text-white font-semibold py-3 rounded-lg hover:bg-primary">View Privacy Settings</a>
                </article>
            </div>
        </section>

        {{-- S5 Impact --}}
        <section class="bg-surface py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <x-section-header eyebrow="Proven Nationwide Results" title="Real Impact &amp; Authentic Representation" intro="Verified outcomes across the country." eyebrow-tone="green" />
                <div class="mt-10 bg-white rounded-2xl shadow-sm border border-primary-border p-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-primary-tint flex items-center justify-center" aria-hidden="true"><img src="{{ asset('icons/clipboard-check.png') }}" alt="" class="w-6 h-6"></div>
                            <h3 class="font-heading text-primary-dark text-lg font-semibold">Verified PESO &amp; DOLE Disability Employment Registry</h3>
                        </div>
                        <div class="flex gap-2">
                            <span class="text-xs font-medium bg-secondary-tint text-secondary px-3 py-1 rounded-full">RA 10524 Certified</span>
                            <span class="text-xs font-medium bg-primary-tint text-primary-dark px-3 py-1 rounded-full">Region IX LEO Hub</span>
                        </div>
                    </div>
                    <p class="text-body-muted text-sm mt-3">Aggregated, privacy-preserving statistics from participating agencies.</p>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                        @foreach ([
                            ['v'=>'1,248','l'=>'Verified PWD Hires'],
                            ['v'=>'184','l'=>'Accredited Inclusive Employers'],
                            ['v'=>'96','l'=>'Active Tax Certifications'],
                            ['v'=>'₱14.25M','l'=>'Statutory Tax Savings Claimed'],
                        ] as $stat)
                            <x-stat-box :value="$stat['v']" :label="$stat['l']" />
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- S6 Final CTA --}}
        <section class="bg-primary-tint py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="inline-block bg-white text-primary-dark text-sm font-medium px-4 py-2 rounded-full">Begin Your Journey</span>
                <h2 class="font-heading text-primary-dark text-3xl font-bold mt-4">Ready to Experience Barrier-Free Employment?</h2>
                <p class="text-body-muted mt-3 max-w-2xl mx-auto">Join candidates and employers building an inclusive workforce.</p>
                <div class="grid md:grid-cols-2 gap-6 mt-10 max-w-3xl mx-auto">
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h3 class="font-heading text-primary-dark text-lg font-semibold">Looking for an Accessible Job?</h3>
                        <a href="/jobs" class="mt-4 block text-center bg-primary-dark text-white font-semibold py-3 rounded-lg hover:bg-primary">Explore Candidate Portal →</a>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h3 class="font-heading text-primary-dark text-lg font-semibold">Want to Build an Inclusive Workforce?</h3>
                        <a href="/employers" class="mt-4 block text-center bg-secondary text-white font-semibold py-3 rounded-lg hover:bg-secondary/90">Join as an Inclusive Employer →</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <x-site-footer />
</body>
</html>
