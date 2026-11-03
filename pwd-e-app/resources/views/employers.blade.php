<!DOCTYPE html>
<html lang="en" x-data="{ readable: false, a11yOpen: false }" :data-readable-font="readable ? 'true' : null">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PWD-E — Employers</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-white text-dark-neutral antialiased">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-50 focus:bg-primary-dark focus:text-white focus:px-3 focus:py-2 focus:rounded">Skip to main content</a>
    <x-site-header />
    <x-accessibility-panel />
    <main id="main">
        <!-- Hero -->
        <section class="relative" style="background: linear-gradient(180deg, #DCEAF4 0%, #FFFFFF 100%);">
            <div class="max-w-[1200px] mx-auto px-6 sm:px-10 lg:px-12 pt-24 pb-12 md:grid md:grid-cols-2 md:gap-8 md:items-center">
                <div>
                    <span class="inline-flex items-center bg-secondary-tint text-secondary text-sm font-medium px-4 py-1 rounded-full">For Employers</span>
                    <h1 class="font-heading text-primary-dark text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mt-4">Build a Stronger, Inclusive Workforce.</h1>
                    <p class="text-body-muted mt-4 max-w-prose">Connect with a dedicated pool of skilled professionals with disabilities. PWD-E equips your company with the tools to make inclusive hiring seamless, compliant, and financially rewarding.</p>
                    <div class="mt-6 flex flex-col sm:flex-row gap-3">
                        <a href="/employers/register" class="block text-center bg-secondary text-white font-semibold px-6 py-3 rounded-lg min-h-[48px] flex items-center justify-center hover:bg-secondary/90">Register as an Employer</a>
                        <a href="/employers/login" class="block text-center border border-primary-border text-secondary font-semibold px-6 py-3 rounded-lg min-h-[48px] flex items-center justify-center hover:bg-primary-tint">Employer Sign In</a>
                    </div>
                </div>
                <div class="mt-8 md:mt-0 flex justify-center">
                    <img src="{{ asset('assets/employers-hero.svg') }}" alt="Illustration of an inclusive office team, including a colleague using a wheelchair, working together at a desk" class="max-w-sm w-full" loading="eager" fetchpriority="high" onerror="this.style.display='none'">
                </div>
            </div>
        </section>
        <!-- Why Partner -->
        <section class="max-w-[1200px] mx-auto px-6 sm:px-10 lg:px-12 py-16">
            <div class="text-center">
                <span class="inline-block bg-primary-tint text-primary-dark text-sm font-medium px-4 py-2 rounded-full">The Value Proposition</span>
                <h2 class="font-heading text-primary-dark text-3xl font-bold mt-4">Why Partner with PWD-E?</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-6 mt-10">
                @foreach ([
                    ['icon'=>'users','title'=>'Untethered Skilled Talent','body'=>'Access a diverse talent pool ready to contribute across various industries, matched to your roles based on both professional skills and accessibility fit.'],
                    ['icon'=>'percent','title'=>'Unlock Statutory Tax Incentives','body'=>'Under Republic Act No. 10524, private employers who hire PWDs are entitled to an additional tax deduction of 25% of their salaries and wages. We make claiming this benefit effortless.'],
                    ['icon'=>'award','title'=>'Enhance Corporate Social Responsibility','body'=>'Join the network of forward-thinking corporations fostering inclusive work environments and exceeding legal employment mandates.'],
                ] as $c)
                <article class="bg-white rounded-2xl border border-primary-border shadow-sm p-6">
                    <div class="w-10 h-10 rounded-lg bg-primary-tint flex items-center justify-center" aria-hidden="true">
                        @if($c['icon']==='users')
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        @elseif($c['icon']==='percent')
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2a10 10 0 100 20 10 10 0 000-20zm-3 7a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm6 11a1.5 1.5 0 110-3 1.5 1.5 0 010 3zM9 15l6-6"/></svg>
                        @else
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>
                    <h3 class="font-heading text-primary-dark text-lg font-semibold mt-4">{{ $c['title'] }}</h3>
                    <p class="text-body-muted text-sm mt-2">{{ $c['body'] }}</p>
                </article>
                @endforeach
            </div>
        </section>
        <!-- Features -->
        <section class="bg-surface py-16">
            <div class="max-w-[1200px] mx-auto px-6 sm:px-10 lg:px-12">
                <div class="text-center">
                    <span class="inline-block bg-primary-tint text-primary-dark text-sm font-medium px-4 py-2 rounded-full">Built for Inclusive Hiring</span>
                    <h2 class="font-heading text-primary-dark text-3xl font-bold mt-4">Employer-Exclusive Platform Features</h2>
                </div>
                <div class="mt-10 space-y-12">
                    <!-- Row 1 -->
                    <div class="md:grid md:grid-cols-2 md:gap-10 items-center">
                        <div>
                            <p class="text-secondary text-xs font-bold tracking-widest uppercase">01 / EMPLOYER TOOLS</p>
                            <h3 class="font-heading text-primary-dark text-xl font-bold mt-2">Workspace Accessibility Matrix</h3>
                            <p class="text-body-muted mt-3">No more guessing games. Complete a straightforward onboarding checklist to declare your physical and digital accommodations (e.g., wheelchair ramps, screen readers, remote options). Our algorithm uses this matrix to match you with candidates who can thrive in your specific environment.</p>
                        </div>
                        <div class="mt-6 md:mt-0 bg-white rounded-2xl border border-primary-border shadow-sm p-5" role="img" aria-label="Preview mock: Workspace Accessibility Matrix checklist">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-8 h-8 rounded-lg bg-primary-tint flex items-center justify-center" aria-hidden="true"><svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg></div>
                                <span class="font-semibold text-primary-dark text-sm">Workspace Accessibility Matrix</span>
                            </div>
                            <p class="text-xs font-bold text-body-muted uppercase tracking-wider">Workplace Accommodations</p>
                            <div class="mt-2 space-y-2">
                                @foreach (['Wheelchair ramps','Screen readers','Remote options'] as $r)
                                <div class="flex items-center justify-between bg-surface rounded-lg px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded bg-primary-tint flex items-center justify-center" aria-hidden="true"><svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m-8-8h16"/></svg></div>
                                        <span class="text-sm text-primary-dark">{{ $r }}</span>
                                    </div>
                                    <span class="w-5 h-5 rounded bg-secondary flex items-center justify-center" aria-hidden="true"><svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- Row 2 -->
                    <div class="md:grid md:grid-cols-2 md:gap-10 items-center">
                        <div class="order-1 md:order-2">
                            <p class="text-secondary text-xs font-bold tracking-widest uppercase">02 / EMPLOYER TOOLS</p>
                            <h3 class="font-heading text-primary-dark text-xl font-bold mt-2">Exclusionary Language Detector</h3>
                            <p class="text-body-muted mt-3">Write job descriptions that welcome everyone. As you type, our tool flags language that could exclude people with disabilities, like &quot;must lift 50 lbs,&quot; and suggests more inclusive wording.</p>
                        </div>
                        <div class="order-2 md:order-1 mt-6 md:mt-0">
                            <div class="bg-white rounded-2xl border border-primary-border shadow-sm p-5" role="img" aria-label="Preview mock: Exclusionary Language Detector">
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="w-8 h-8 rounded-lg bg-primary-tint flex items-center justify-center" aria-hidden="true"><svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                                    <span class="font-semibold text-primary-dark text-sm">Exclusionary Language Detector</span>
                                </div>
                                <span class="inline-block text-xs bg-primary-tint text-primary-dark px-3 py-1 rounded-full">Example job draft</span>
                                <div class="mt-3 rounded-lg p-3" style="background-color:#FCF6E5; border:1px solid #F0E3BE;">
                                    <p class="text-xs font-bold uppercase" style="color:#8A6A1F;">Potential Physical Barrier</p>
                                    <p class="text-sm text-dark-neutral mt-1">&quot;must lift 50 lbs&quot;</p>
                                </div>
                                <div class="mt-3 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span class="text-xs text-secondary">Review physical requirements and consider inclusive alternatives.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Row 3 -->
                    <div class="md:grid md:grid-cols-2 md:gap-10 items-center">
                        <div>
                            <p class="text-secondary text-xs font-bold tracking-widest uppercase">03 / EMPLOYER TOOLS</p>
                            <h3 class="font-heading text-primary-dark text-xl font-bold mt-2">Automated Compliance &amp; Incentive Tracker</h3>
                            <p class="text-body-muted mt-3">Managing your inclusive workforce is built right into your dashboard. The system automatically calculates your company's PWD workforce percentage and generates the formatted compliance reports required for BIR and LGU tax deduction claims.</p>
                        </div>
                        <div class="mt-6 md:mt-0 bg-white rounded-2xl border border-primary-border shadow-sm p-5" role="img" aria-label="Preview mock: Compliance & Incentive Tracker">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-8 h-8 rounded-lg bg-primary-tint flex items-center justify-center" aria-hidden="true"><svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                                <span class="font-semibold text-primary-dark text-sm">Compliance &amp; Incentive Tracker</span>
                            </div>
                            <div class="bg-primary-tint rounded-lg p-3 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-white flex items-center justify-center" aria-hidden="true"><svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18M9 17V9m4 8V5m4 12v-4"/></svg></div>
                                <div>
                                    <p class="text-sm font-semibold text-primary-dark">PWD workforce percentage</p>
                                    <p class="text-xs text-body-muted">Automatically calculated in your dashboard</p>
                                </div>
                            </div>
                            <p class="text-xs font-bold text-body-muted uppercase tracking-wider mt-3">Formatted Compliance Reports</p>
                            <div class="mt-2 space-y-2">
                                @foreach (['BIR tax deduction claims','LGU tax deduction claims'] as $r)
                                <div class="flex items-center justify-between bg-surface rounded-lg px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded bg-primary-tint flex items-center justify-center" aria-hidden="true"><svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                                        <span class="text-sm text-primary-dark">{{ $r }}</span>
                                    </div>
                                    <svg class="w-4 h-4 text-body-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Steps -->
        <section class="max-w-[1200px] mx-auto px-6 sm:px-10 lg:px-12 py-16">
            <div class="text-center">
                <span class="inline-block bg-primary-tint text-primary-dark text-sm font-medium px-4 py-2 rounded-full">Your Hiring Journey</span>
                <h2 class="font-heading text-primary-dark text-3xl font-bold mt-4">How the Hiring Process Works</h2>
            </div>
            <div class="grid md:grid-cols-2 gap-6 mt-10">
                @foreach ([
                    ['n'=>'01','title'=>'Register & Verify','body'=>'Create your employer account. To maintain a safe and legitimate environment for our candidates, all employer business permits are verified by system administrators before activation.'],
                    ['n'=>'02','title'=>'Declare Accommodations','body'=>'Fill out your Workplace Accessibility Matrix to highlight your inclusive facilities and digital tools.'],
                    ['n'=>'03','title'=>'Draft & Publish','body'=>'Create your job posting. Our language scanner will help you refine the wording before it goes live to candidates.'],
                    ['n'=>'04','title'=>'Match & Hire','body'=>'Receive applications from candidates whose accessibility requirements align with your declared accommodations, ensuring a high-quality, mutually beneficial fit from day one.'],
                ] as $s)
                <article class="bg-white rounded-2xl border border-primary-border shadow-sm p-6">
                    <div class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-full bg-primary-dark text-white flex items-center justify-center font-bold text-sm">{{ $s['n'] }}</span>
                        <h3 class="font-heading text-primary-dark text-lg font-semibold">{{ $s['title'] }}</h3>
                    </div>
                    <p class="text-body-muted text-sm mt-3">{{ $s['body'] }}</p>
                </article>
                @endforeach
            </div>
        </section>
        <!-- Dark CTA -->
        <section class="max-w-[1200px] mx-auto px-6 sm:px-10 lg:px-12 py-12">
            <div class="rounded-2xl bg-primary-dark text-white text-center p-10 md:p-16">
                <h2 class="font-heading text-white text-2xl sm:text-3xl font-bold">Ready to make accessibility your competitive advantage?</h2>
                <a href="/employers/register" class="inline-flex items-center justify-center mt-6 bg-white text-primary-dark font-semibold px-6 py-3 rounded-lg min-h-[48px] hover:bg-primary-tint">Create Employer Account Today</a>
            </div>
        </section>
    </main>
    <x-site-footer />
</body>
</html>
