<!DOCTYPE html>
<html lang="en" x-data="{ readable: false, a11yOpen: false }" :data-readable-font="readable ? 'true' : null">
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

    {{-- A11y floating toggle --}}
    <button type="button" @click="a11yOpen = !a11yOpen" class="fixed bottom-4 right-4 z-50 w-14 h-14 rounded-full bg-primary text-white flex items-center justify-center shadow-lg" aria-label="Accessibility options" :aria-expanded="a11yOpen">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="4" r="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0l-3 6m3-6l3 6M9 9h6"/></svg>
    </button>
    <div x-show="a11yOpen" x-cloak class="fixed bottom-20 right-4 z-50 bg-primary-dark text-white rounded-xl shadow-lg p-3 flex flex-col gap-2 text-xs w-56" role="dialog" aria-label="Accessibility controls">
        <button type="button" class="px-2 py-2 rounded hover:bg-white/10 text-left" aria-pressed="false">High Contrast</button>
        <label class="flex items-center gap-2 px-2 py-2">
            <span>Font</span>
            <input type="range" min="100" max="200" value="100" class="w-full" aria-label="Font resize">
        </label>
        <button type="button" class="px-2 py-2 rounded hover:bg-white/10 text-left" aria-pressed="false">Screen Reader</button>
        <button type="button" @click="readable = !readable" class="px-2 py-2 rounded hover:bg-white/10 text-left" :aria-pressed="readable">Readable Font</button>
    </div>

    <main id="main">
        {{-- S1 Hero --}}
        <section class="relative bg-primary-tint" style="background-image: url('{{ asset('assets/Background.jpg') }}'); background-size: cover; background-position: center;">
            <div class="absolute inset-0" style="background-color: rgba(255,255,255,0.8);" aria-hidden="true"></div>
            <div class="relative max-w-[1200px] mx-auto px-6 sm:px-10 lg:px-12 pt-32 pb-16 text-center">
                <span class="inline-flex items-center gap-2 bg-white text-primary-dark text-sm font-medium px-4 py-2 rounded-full border border-primary-border">
                    <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    WCAG 2.1 Level AA &amp; Republic Act No. 10524 / 11036 Compliant
                </span>
                <h1 class="font-heading text-primary-dark text-4xl sm:text-5xl font-bold mt-6 max-w-3xl mx-auto">Connecting Persons with Disabilities to Accessible &amp; Inclusive Workplaces.</h1>
                <p class="text-dark-neutral mt-4 max-w-2xl mx-auto font-medium">Finding a job shouldn't mean fighting accessibility barriers. PWD-E matches your unique talents with verified, barrier-free employers across the Philippines.</p>
                <div class="mt-8 bg-white rounded-2xl shadow-sm border border-primary-border p-4 max-w-5xl mx-auto">
                    <label for="search" class="block text-sm font-medium text-primary-dark mb-2">Search for Inclusive Opportunities (Skill, Location, and Accommodation)</label>
                    <div class="flex flex-col md:flex-row gap-2">
                        <input id="search" type="search" class="flex-1 rounded-lg border border-primary-border bg-surface px-4 py-3 text-base min-h-[48px] focus:outline-none focus:ring-2 focus:ring-primary" placeholder="e.g. Remote data entry, FSL support">
                        <button type="button" class="bg-primary-dark text-white font-semibold px-6 py-3 rounded-lg min-h-[48px] hover:bg-primary md:w-auto w-full">Find Inclusive Jobs</button>
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
        <section class="max-w-[1200px] mx-auto px-6 sm:px-10 lg:px-12 py-16">
            <div class="text-center">
                <span class="inline-block bg-primary-tint text-primary-dark text-sm font-medium px-4 py-2 rounded-full">Our Purpose &amp; Vision</span>
                <h2 class="font-heading text-primary-dark text-3xl font-bold mt-4">Why Choose PWD-E</h2>
                <p class="text-body-muted mt-3 max-w-2xl mx-auto">Unlike conventional job boards that match purely on traditional credentials, PWD-E eliminates structural barriers by guaranteeing physical and digital workplace accommodations.</p>
            </div>
            <div class="grid md:grid-cols-2 gap-6 mt-10">
                <article class="border-t-4 border-primary rounded-2xl bg-white shadow-sm p-6 flex flex-col h-full">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-lg bg-primary-tint flex items-center justify-center" aria-hidden="true"><img src="{{ asset('icons/user-round.png') }}" alt="" class="w-6 h-6"></div>
                        <span class="text-xs font-medium bg-primary-tint text-primary px-3 py-1 rounded-full">Tailored for Candidates</span>
                    </div>
                    <h3 class="font-heading text-primary-dark text-xl font-semibold mt-4">For Jobseekers</h3>
                    <blockquote class="bg-surface rounded-lg p-3 mt-3 text-sm text-body-muted border-l-4 border-primary">"Build a profile using voice dictation or Filipino Sign Language (FSL) video, and browse jobs where physical and digital accommodations are guaranteed—not an afterthought."</blockquote>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li class="flex items-start gap-2"><span class="text-secondary shrink-0">✓</span><p class="text-justify"><strong>Multimodal Profile Creation:</strong> Speak hands-free or upload an FSL video resume with automated sync captions.</p></li>
                        <li class="flex items-start gap-2"><span class="text-secondary shrink-0">✓</span><p class="text-justify"><strong>No Surprise Barriers:</strong> Know upfront if a workplace has ramps, accessible restrooms, or screen-reader systems.</p></li>
                        <li class="flex items-start gap-2"><span class="text-secondary shrink-0">✓</span><p class="text-justify"><strong>Verified PESO/DOLE Protection:</strong> Connect with accredited inclusive employers under RA 10524.</p></li>
                    </ul>
                    <div class="mt-auto pt-6"><a href="/jobs" class="block text-center bg-primary-dark text-white font-semibold py-3 rounded-lg hover:bg-primary">Explore Candidate Portal →</a></div>
                </article>
                <article class="border-t-4 border-secondary rounded-2xl bg-white shadow-sm p-6 flex flex-col h-full">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-lg bg-secondary-tint flex items-center justify-center" aria-hidden="true"><img src="{{ asset('icons/building.png') }}" alt="" class="w-6 h-6"></div>
                        <span class="text-xs font-medium bg-secondary-tint text-secondary px-3 py-1 rounded-full">Tailored for Organizations</span>
                    </div>
                    <h3 class="font-heading text-primary-dark text-xl font-semibold mt-4">For Employers</h3>
                    <blockquote class="bg-surface rounded-lg p-3 mt-3 text-sm text-body-muted border-l-4 border-secondary">"Access an untethered pool of skilled PWD talent, audit your workplace accessibility, and effortlessly claim statutory tax incentives under Republic Act No. 10524."</blockquote>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li class="flex items-start gap-2"><span class="text-secondary shrink-0">✓</span><p class="text-justify"><strong>Untethered Skilled Talent:</strong> Hire qualified professionals across IT, accounting, logistics, and creative sectors.</p></li>
                        <li class="flex items-start gap-2"><span class="text-secondary shrink-0">✓</span><p class="text-justify"><strong>Workplace Accessibility Matrix:</strong> Audit facility ramps, elevators, and software to earn DOLE accreditation.</p></li>
                        <li class="flex items-start gap-2"><span class="text-secondary shrink-0">✓</span><p class="text-justify"><strong>25% Tax Deduction Claim:</strong> Automatically generate BIR Form 1702-RT schedules for significant tax savings.</p></li>
                    </ul>
                    <div class="mt-auto pt-6"><a href="/employers" class="block text-center bg-secondary text-white font-semibold py-3 rounded-lg hover:bg-secondary/90">Join as Inclusive Employer →</a></div>
                </article>
            </div>
        </section>

        {{-- S3 How it works --}}
        <section class="bg-surface py-16">
            <div class="max-w-[1200px] mx-auto px-6 sm:px-10 lg:px-12">
                <div class="text-center">
                    <span class="inline-block bg-primary-tint text-primary-dark text-sm font-medium px-4 py-2 rounded-full">Seamless 3-Step Journey</span>
                    <h2 class="font-heading text-primary-dark text-3xl font-bold mt-4">How PWD-E Works</h2>
                    <p class="text-body-muted mt-3 max-w-2xl mx-auto">A frictionless, accessible pathway connecting talent, accommodation requirements, and certified employers.</p>
                </div>
                <div class="grid md:grid-cols-3 gap-6 mt-10">
                    @foreach ([
                        ['step'=>'1','title'=>'Build Your Multimodal Profile','body'=>'Register hands-free via our Voice Profile Builder with live speech dictation, or upload a Filipino Sign Language (FSL) video resume with synchronized bilingual captions conforming to RA 11106.','cta'=>'Try Voice Setup →'],
                        ['step'=>'2','title'=>'Get Accessibility-Matched','body'=>'Our intelligent algorithm calculates a composite fit score weighted by 60% Workplace Accommodation Fit (ramps, screen readers, flexible hours) and 40% Professional Skill Match, eliminating inaccessible mismatches.','cta'=>'View Matched Jobs →'],
                        ['step'=>'3','title'=>'Apply & Thrive','body'=>'Connect directly with PESO & DOLE-accredited employers committed to barrier-free career growth. Enjoy pre-arranged accessible interviews and full workplace accommodation support from day one.','cta'=>'Browse Opportunities →'],
                    ] as $s)
                        <article class="relative bg-white rounded-2xl shadow-sm border border-primary-border p-6 flex flex-col h-full">
                            <span class="absolute -top-3 left-4 bg-primary-dark text-white text-xs font-bold px-3 py-1 rounded-full">STEP {{ $s['step'] }}</span>
                            <div class="w-10 h-10 rounded-lg bg-primary-tint flex items-center justify-center mt-2" aria-hidden="true"><img src="{{ asset('icons/compass.png') }}" alt="" class="w-6 h-6"></div>
                            <h3 class="font-heading text-primary-dark text-lg font-semibold mt-4">{{ $s['title'] }}</h3>
                            <p class="text-body-muted text-sm mt-2">{{ $s['body'] }}</p>
                            <div class="mt-auto pt-6"><a href="#" class="block text-center border border-primary-border text-primary-dark font-semibold py-2 rounded-lg hover:bg-primary-tint">{{ $s['cta'] }}</a></div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- S4 Features --}}
        <section class="max-w-[1200px] mx-auto px-6 sm:px-10 lg:px-12 py-16">
            <div class="text-center">
                <span class="inline-block bg-primary-tint text-primary-dark text-sm font-medium px-4 py-2 rounded-full">Platform Innovations</span>
                <h2 class="font-heading text-primary-dark text-3xl font-bold mt-4">Empowering Features Showcase</h2>
                <p class="text-body-muted mt-3 max-w-2xl mx-auto">Specialized technological tools designed to solve longstanding hiring frictions and protect candidate rights.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6 mt-10">
                <article class="bg-white rounded-2xl shadow-sm border border-primary-border p-6 flex flex-col h-full">
                    <div class="w-10 h-10 rounded-lg bg-primary-tint flex items-center justify-center" aria-hidden="true"><img src="{{ asset('icons/scan-text.png') }}" alt="" class="w-6 h-6"></div>
                    <span class="inline-block mt-3 text-xs font-medium bg-primary-tint text-primary px-3 py-1 rounded-full">Real-Time NLP Scan</span>
                    <h3 class="font-heading text-primary-dark text-lg font-semibold mt-3">Exclusionary Language Detector</h3>
                    <p class="text-body-muted text-sm mt-2">Helps employers identify and rewrite ableist job requirements (e.g., changing "must stand for 8 hours" to flexible ergonomic alternatives or removing unnecessary driving license prerequisites) with 1-click inclusive suggestions.</p>
                    <div class="bg-surface rounded-lg p-3 mt-3 text-sm">
                        <p class="text-red-600"><s>Must have normal hearing and speech</s></p>
                        <p class="text-secondary">→ Effective communication skills via spoken, written, or FSL tools</p>
                    </div>
                    <div class="mt-auto pt-6"><a href="#" class="block text-center bg-primary-dark text-white font-semibold py-3 rounded-lg hover:bg-primary">Test Inclusive Job Creator</a></div>
                </article>
                <article class="bg-white rounded-2xl shadow-sm border border-primary-border p-6 flex flex-col h-full">
                    <div class="w-10 h-10 rounded-lg bg-secondary-tint flex items-center justify-center" aria-hidden="true"><img src="{{ asset('icons/file-chart-column.png') }}" alt="" class="w-6 h-6"></div>
                    <span class="inline-block mt-3 text-xs font-medium bg-secondary-tint text-secondary px-3 py-1 rounded-full">Philippines Tax Code</span>
                    <h3 class="font-heading text-primary-dark text-lg font-semibold mt-3">RA 10524 Tax Savings Calculator</h3>
                    <p class="text-body-muted text-sm mt-2">Demonstrates real financial justification for corporate diversity initiatives by automatically calculating 25% additional gross payroll deductions under Republic Act No. 10524 and formatting ready-to-file BIR deduction reports.</p>
                    <div class="bg-surface rounded-lg p-3 mt-3 text-sm text-secondary font-semibold">Measurable ROI: Companies employing verified PWDs deduct an extra 25% of annual wages directly from gross taxable income.</div>
                    <div class="mt-auto pt-6"><a href="#" class="block text-center bg-secondary text-white font-semibold py-3 rounded-lg hover:bg-secondary/90">Open Tax Calculator</a></div>
                </article>
                <article class="bg-white rounded-2xl shadow-sm border border-primary-border p-6 flex flex-col h-full">
                    <div class="w-10 h-10 rounded-lg bg-primary-tint flex items-center justify-center" aria-hidden="true"><img src="{{ asset('icons/lock-keyhole.png') }}" alt="" class="w-6 h-6"></div>
                    <span class="inline-block mt-3 text-xs font-medium bg-primary-tint text-primary-dark px-3 py-1 rounded-full">RA 10173 Compliant</span>
                    <h3 class="font-heading text-primary-dark text-lg font-semibold mt-3">Granular Privacy Controls</h3>
                    <p class="text-body-muted text-sm mt-2">Because disability data is Sensitive Personal Information under Philippine law, jobseekers maintain total authority over what is disclosed. Choose whether to share medical diagnoses or disclose strictly workplace accommodations upon interview fit.</p>
                    <div class="bg-surface rounded-lg p-3 mt-3 text-sm space-y-1">
                        <p class="font-medium text-primary-dark">Candidate Consent:</p>
                        <p class="text-body-muted">Your medical history is never publicly exposed. Employers only see accommodation specifications you actively approve.</p>
                    </div>
                    <div class="mt-auto pt-6"><a href="#" class="block text-center bg-primary-dark text-white font-semibold py-3 rounded-lg hover:bg-primary">View Privacy Settings</a></div>
                </article>
            </div>
        </section>

        {{-- S5 Impact --}}
        <section class="bg-surface py-16">
            <div class="max-w-[1200px] mx-auto px-6 sm:px-10 lg:px-12">
                <x-section-header eyebrow="Proven Nationwide Results" title="Real Impact &amp; Authentic Representation" intro="Verified employment metrics and real Filipino PWD professionals excelling in barrier-free physical and remote workspaces." eyebrow-tone="green" />
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
                    <p class="text-body-muted text-sm mt-3">Monitored in partnership with Western Mindanao State University and Regional Public Employment Service Offices.</p>
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
        <section class="bg-primary-tint py-12">
            <div class="max-w-[1200px] mx-auto px-6 sm:px-10 lg:px-12 text-center">
                <span class="inline-block bg-white text-primary-dark text-sm font-medium px-4 py-2 rounded-full">Begin Your Journey</span>
                <h2 class="font-heading text-primary-dark text-3xl font-bold mt-4">Ready to Experience Barrier-Free Employment?</h2>
                <p class="text-body-muted mt-3 max-w-2xl mx-auto">Choose your tailored gateway below and start building an inclusive, accessible future today.</p>
                <div class="grid md:grid-cols-2 gap-6 mt-10 max-w-3xl mx-auto">
                    <div class="bg-white rounded-2xl shadow-sm border border-primary-border p-6 text-left flex flex-col h-full">
                        <span class="text-xs font-medium bg-primary-tint text-primary px-3 py-1 rounded-full">For PWD Jobseekers</span>
                        <h3 class="font-heading text-primary-dark text-lg font-semibold mt-3">Looking for an Accessible Job?</h3>
                        <p class="text-body-muted text-sm mt-2">Create your multimodal profile in minutes using voice dictation or FSL video. Define your accommodation needs and discover verified barrier-free positions with leading companies.</p>
                        <div class="mt-auto pt-6"><a href="/jobs" class="block text-center bg-primary text-white font-semibold py-3 rounded-lg min-h-[52px] flex items-center justify-center">Explore Candidate Portal →</a></div>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-primary-border p-6 text-left flex flex-col h-full">
                        <span class="text-xs font-medium bg-secondary-tint text-secondary px-3 py-1 rounded-full">For Inclusive Organizations</span>
                        <h3 class="font-heading text-primary-dark text-lg font-semibold mt-3">Want to Build an Inclusive Workforce?</h3>
                        <p class="text-body-muted text-sm mt-2">Audit your workplace with our accessibility matrix, eliminate ableist hiring criteria with our NLP scanner, and claim 25% statutory gross payroll tax deductions under RA 10524.</p>
                        <div class="mt-auto pt-6"><a href="/employers" class="block text-center bg-secondary text-white font-semibold py-3 rounded-lg min-h-[52px] flex items-center justify-center">Join as an Inclusive Employer →</a></div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <x-site-footer />
</body>
</html>
