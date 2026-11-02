<!DOCTYPE html>
<html lang="en" x-data="{ readable: false, a11yOpen: false }" :data-readable-font="readable ? 'true' : null">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PWD-E — About</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-white text-dark-neutral antialiased">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-50 focus:bg-primary-dark focus:text-white focus:px-3 focus:py-2 focus:rounded">Skip to main content</a>
    <x-site-header />
    <button type="button" @click="a11yOpen = !a11yOpen" class="fixed bottom-4 right-4 z-50 w-14 h-14 rounded-full bg-primary text-white flex items-center justify-center shadow-lg" aria-label="Accessibility options" :aria-expanded="a11yOpen">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="4" r="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0l-3 6m3-6l3 6M9 9h6"/></svg>
    </button>
    <div x-show="a11yOpen" x-cloak class="fixed bottom-20 right-4 z-50 bg-primary-dark text-white rounded-xl shadow-lg p-3 flex flex-col gap-2 text-xs w-56" role="dialog" aria-label="Accessibility controls">
        <button type="button" class="px-2 py-2 rounded hover:bg-white/10 text-left" aria-pressed="false">High Contrast</button>
        <label class="flex items-center gap-2 px-2 py-2"><span>Font</span><input type="range" min="100" max="200" value="100" class="w-full" aria-label="Font resize"></label>
        <button type="button" class="px-2 py-2 rounded hover:bg-white/10 text-left" aria-pressed="false">Screen Reader</button>
        <button type="button" @click="readable = !readable" class="px-2 py-2 rounded hover:bg-white/10 text-left" :aria-pressed="readable">Readable Font</button>
    </div>
    <main id="main">
        <!-- Hero -->
        <section class="relative" style="background: linear-gradient(180deg, #DCEAF4 0%, #FFFFFF 100%);">
            <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10 pt-24 pb-12 md:grid md:grid-cols-5 md:gap-8 md:items-start">
                <div class="md:col-span-3">
                    <span class="inline-flex items-center bg-white border border-primary-border text-primary-dark text-sm font-medium px-4 py-1 rounded-full">About PWD-E</span>
                    <h1 class="font-heading text-primary-dark text-3xl sm:text-4xl md:text-5xl font-bold mt-4">Ability deserves opportunity. Access makes it possible.</h1>
                    <p class="text-body-muted mt-4 max-w-prose">PWD-E connects Persons with Disabilities to accessible and inclusive workplaces across the Philippines. We place accommodation fit alongside professional skills—so a job opportunity can be a practical possibility, not just a listing.</p>
                    <p class="text-body-muted mt-3 max-w-prose">A more accessible path from showing your strengths to finding a workplace where you can thrive.</p>
                </div>
                <div class="md:col-span-2 mt-8 md:mt-0">
                    <div class="bg-white rounded-2xl border border-primary-border shadow-sm p-6">
                        <h2 class="font-heading text-primary-dark text-lg font-bold">More than a skills match</h2>
                        <div class="mt-4 space-y-3">
                            @foreach ([
                                ['icon'=>'briefcase','title'=>'Professional skills','desc'=>'Experience, strengths and career goals.'],
                                ['icon'=>'access','title'=>'Accommodation fit','desc'=>'Physical, digital and communication access.'],
                                ['icon'=>'building','title'=>'An inclusive workplace','desc'=>'Clear expectations and accessible hiring.'],
                            ] as $r)
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-lg bg-primary-tint flex items-center justify-center shrink-0" aria-hidden="true">
                                    @if($r['icon']==='briefcase')
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    @elseif($r['icon']==='access')
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="4" r="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0l-3 6m3-6l3 6M9 9h6"/></svg>
                                    @else
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-primary-dark text-sm">{{ $r['title'] }}</p>
                                    <p class="text-body-muted text-xs">{{ $r['desc'] }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <p class="mt-4 text-sm text-secondary">The right opportunity considers both what you can do and what you need to do it.</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- Purpose & Vision -->
        <section class="bg-surface py-16">
            <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10">
                <div class="text-center">
                    <span class="inline-block bg-primary-tint text-primary-dark text-sm font-medium px-4 py-2 rounded-full">Our Purpose &amp; Vision</span>
                    <h2 class="font-heading text-primary-dark text-3xl font-bold mt-4">Employment inclusion starts with access.</h2>
                    <p class="text-body-muted mt-3 max-w-2xl mx-auto">Our purpose is to make accessibility part of the employment conversation from the very beginning.</p>
                </div>
                <div class="grid md:grid-cols-2 gap-6 mt-10">
                    @foreach ([
                        ['icon'=>'target','title'=>'Our mission','body'=>'Connect Persons with Disabilities and inclusive employers through a hiring experience that values talent, makes accommodation needs visible and supports informed choices.'],
                        ['icon'=>'eye','title'=>'Our vision','body'=>'A Philippine employment landscape where disability is not a barrier to participation, and accessible workplaces allow people to contribute, develop and belong.'],
                    ] as $c)
                    <article class="bg-white rounded-2xl border border-primary-border shadow-sm p-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-primary-tint flex items-center justify-center" aria-hidden="true">
                                @if($c['icon']==='target')
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
                                @else
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                @endif
                            </div>
                            <h3 class="font-heading text-primary-dark text-lg font-semibold">{{ $c['title'] }}</h3>
                        </div>
                        <p class="text-body-muted text-sm mt-3">{{ $c['body'] }}</p>
                    </article>
                    @endforeach
                </div>
            </div>
        </section>
        <!-- Why This Matters -->
        <section class="bg-surface py-16">
            <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10">
                <div class="text-center">
                    <span class="inline-block bg-primary-tint text-primary-dark text-sm font-medium px-4 py-2 rounded-full">Why This Matters</span>
                    <h2 class="font-heading text-primary-dark text-3xl font-bold mt-4">The barriers are in the process—not the person.</h2>
                    <p class="text-body-muted mt-3 max-w-2xl mx-auto">A conventional hiring journey can exclude people before their skills are even considered. PWD-E addresses these points of friction.</p>
                </div>
                <div class="grid md:grid-cols-3 gap-6 mt-10">
                    @foreach ([
                        ['icon'=>'chat','title'=>'One way to communicate','problem'=>'Text-only applications and rigid interview formats can hide a candidate\'s strengths.','respond'=>'Voice dictation, Filipino Sign Language (FSL) video profiles and accessible interviews provide more ways to participate.'],
                        ['icon'=>'building','title'=>'Unclear workplace access','problem'=>'A job description may explain the role without explaining whether the workplace is accessible.','respond'=>'Upfront accommodation information and employer accessibility review help candidates consider fit before applying.'],
                        ['icon'=>'shield','title'=>'Pressure to disclose','problem'=>'Sharing sensitive disability information can feel like a condition of being considered.','respond'=>'Candidate-controlled disclosure keeps the focus on relevant accommodation needs and professional abilities.'],
                    ] as $c)
                    <article class="bg-white rounded-2xl border border-primary-border shadow-sm p-6">
                        <div class="w-10 h-10 rounded-lg bg-primary-tint flex items-center justify-center" aria-hidden="true">
                            @if($c['icon']==='chat')
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            @elseif($c['icon']==='building')
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            @else
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            @endif
                        </div>
                        <h3 class="font-heading text-primary-dark text-lg font-semibold mt-3">{{ $c['title'] }}</h3>
                        <p class="text-body-muted text-sm mt-2">{{ $c['problem'] }}</p>
                        <p class="text-xs font-bold text-secondary uppercase tracking-wider mt-4">How PWD-E Responds</p>
                        <p class="text-body-muted text-sm mt-1">{{ $c['respond'] }}</p>
                    </article>
                    @endforeach
                </div>
            </div>
        </section>
        <!-- How PWD-E Works -->
        <section class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10 py-16">
            <div class="text-center">
                <span class="inline-block bg-primary-tint text-primary-dark text-sm font-medium px-4 py-2 rounded-full">How PWD-E Works</span>
                <h2 class="font-heading text-primary-dark text-3xl font-bold mt-4">How PWD-E Works</h2>
                <p class="text-body-muted mt-3 max-w-2xl mx-auto">A frictionless, accessible pathway connecting talent, accommodation requirements, and certified employers.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6 mt-10">
                @foreach ([
                    ['step'=>'1','icon'=>'mic','title'=>'Build Your Multimodal Profile','body'=>'Register hands-free via our Voice Profile Builder with live speech dictation, or upload a Filipino Sign Language (FSL) video resume with synchronized bilingual captions conforming to RA 11106.','cta'=>'Try Voice Setup →'],
                    ['step'=>'2','icon'=>'target','title'=>'Get Accessibility-Matched','body'=>'Our intelligent algorithm calculates a composite fit score weighted by 60% Workplace Accommodation Fit (ramps, screen readers, flexible hours) and 40% Professional Skill Match, ensuring accessible first consideration.','cta'=>'View Matched Jobs →'],
                    ['step'=>'3','icon'=>'briefcase','title'=>'Apply & Thrive','body'=>'Connect directly with PESO & DOLE-accredited employers committed to barrier-free career growth. Enjoy pre-arranged accessible interviews and full workplace accommodation support from day one.','cta'=>'Browse Opportunities →'],
                ] as $s)
                <article class="bg-white rounded-2xl border border-primary-border shadow-sm p-6 relative">
                    <span class="absolute -top-3 left-4 bg-primary-dark text-white text-xs font-bold px-3 py-1 rounded-full">STEP {{ $s['step'] }}</span>
                    <div class="w-10 h-10 rounded-lg bg-primary-tint flex items-center justify-center mt-2" aria-hidden="true">
                        @if($s['icon']==='mic')
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 1a3 3 0 00-3 3v8a3 3 0 006 0V4a3 3 0 00-3-3zM19 10v2a7 7 0 01-14 0v-2M12 19v4M8 23h8"/></svg>
                        @elseif($s['icon']==='target')
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
                        @else
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        @endif
                    </div>
                    <h3 class="font-heading text-primary-dark text-lg font-semibold mt-4">{{ $s['title'] }}</h3>
                    <p class="text-body-muted text-sm mt-2">{{ $s['body'] }}</p>
                    <div class="mt-auto pt-6"><a href="#" class="block text-center border border-primary-border text-primary-dark font-semibold py-2 rounded-lg hover:bg-primary-tint">{{ $s['cta'] }}</a></div>
                </article>
                @endforeach
            </div>
        </section>
        <!-- Accommodation First -->
        <section class="bg-surface py-16">
            <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10">
                <div class="md:grid md:grid-cols-2 md:gap-10 items-start">
                    <div>
                        <span class="inline-flex items-center bg-white border border-primary-border text-primary-dark text-sm font-medium px-4 py-1 rounded-full">Accommodation First</span>
                        <h2 class="font-heading text-primary-dark text-3xl font-bold mt-4">Ask what makes good work possible.</h2>
                        <p class="text-body-muted mt-3 max-w-prose">Skills matter. So do the conditions that allow someone to use them. PWD-E brings these conversations together from the start.</p>
                    </div>
                    <div class="mt-8 md:mt-0 space-y-6">
                        <div class="bg-white rounded-2xl border border-primary-border shadow-sm p-6">
                            <h3 class="font-heading text-primary-dark text-lg font-bold">Access can mean different things</h3>
                            <div class="mt-4 space-y-3">
                                @foreach ([
                                    ['icon'=>'monitor','text'=>'Screen-free routes and accessible layouts'],
                                    ['icon'=>'screen','text'=>'Screen-reader-compatible digital tools'],
                                    ['icon'=>'chat','text'=>'FSL support and communication preferences'],
                                    ['icon'=>'home','text'=>'Remote, hybrid or flexible work arrangements'],
                                ] as $r)
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-primary-tint flex items-center justify-center shrink-0" aria-hidden="true">
                                        @if($r['icon']==='monitor' || $r['icon']==='screen')
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 21h8m-4-4v4"/></svg>
                                        @elseif($r['icon']==='chat')
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                        @else
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                        @endif
                                    </div>
                                    <span class="text-sm text-primary-dark">{{ $r['text'] }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-10 grid md:grid-cols-3 gap-6">
                    @foreach ([
                        ['n'=>'01','title'=>'Start with the person','body'=>'Candidates express their experience, skills and accommodation preferences, with voice dictation and FSL video options for their profiles.'],
                        ['n'=>'02','title'=>'Make the workplace visible','body'=>'Employers share accommodation information and participate in accessibility reviews, so access is part of the opportunity—not an afterthought.'],
                        ['n'=>'03','title'=>'Carry access into hiring','body'=>'Accessible interviews and clear accommodation conversations help both sides assess fit while respecting candidate disclosure choices.'],
                    ] as $s)
                    <div class="bg-white rounded-2xl border border-primary-border shadow-sm p-6">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-full bg-primary-dark text-white flex items-center justify-center font-bold text-sm">{{ $s['n'] }}</span>
                            <h3 class="font-heading text-primary-dark text-lg font-semibold">{{ $s['title'] }}</h3>
                        </div>
                        <p class="text-body-muted text-sm mt-3">{{ $s['body'] }}</p>
                    </div>
                    @endforeach
                </div>
                <p class="mt-6 text-xs text-body-muted italic">Accommodation needs are individual. Candidates and employers should confirm the arrangements relevant to each role.</p>
            </div>
        </section>
        <!-- Rooted in the Philippines -->
        <section class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10 py-16">
            <div class="md:grid md:grid-cols-5 md:gap-8 items-start">
                <div class="md:col-span-3">
                    <span class="inline-flex items-center bg-secondary-tint text-secondary text-sm font-medium px-4 py-1 rounded-full">Rooted in the Philippines</span>
                    <h2 class="font-heading text-primary-dark text-3xl font-bold mt-4">A local context.<br>A shared responsibility.</h2>
                    <p class="text-body-muted mt-4 max-w-prose">PWD-E's focus is the Philippine employment experience: the languages people use, the accommodations workplaces provide and the rights that shape inclusive hiring. Filipino Sign Language belongs in that conversation alongside spoken and written communication.</p>
                </div>
                <div class="md:col-span-2 mt-8 md:mt-0">
                    <div class="bg-white rounded-2xl border border-primary-border shadow-sm p-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-primary-tint flex items-center justify-center" aria-hidden="true">
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                            <div>
                                <p class="font-heading text-primary-dark text-lg font-bold">Western Mindanao State University</p>
                                <p class="text-body-muted text-sm">College of Computing Studies</p>
                            </div>
                        </div>
                        <span class="inline-block mt-3 text-xs font-medium bg-secondary-tint text-secondary px-3 py-1 rounded-full">Accessibility-First Portal</span>
                        <p class="text-body-muted text-sm mt-3">This academic context frames the portal's focus on technology that responds to real barriers to participation and employment.</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- Access, Rights & Trust -->
        <section class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10 py-16">
            <div class="text-center">
                <span class="inline-block bg-primary-tint text-primary-dark text-sm font-medium px-4 py-2 rounded-full">Access, Rights &amp; Trust</span>
                <h2 class="font-heading text-primary-dark text-3xl font-bold mt-4">Inclusion must also protect your choices.</h2>
                <p class="text-body-muted mt-3 max-w-2xl mx-auto">Accessibility and privacy are parts of the same commitment: helping people participate without giving up their dignity or control.</p>
            </div>
            <div class="grid md:grid-cols-2 gap-6 mt-10">
                <article class="bg-white rounded-2xl border border-primary-border shadow-sm p-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-primary-tint flex items-center justify-center" aria-hidden="true">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="4" r="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0l-3 6m3-6l3 6M9 9h6"/></svg>
                        </div>
                        <h3 class="font-heading text-primary-dark text-lg font-semibold">Access across the journey</h3>
                    </div>
                    <p class="text-body-muted text-sm mt-3">PWD-E's accessibility commitments are guided by WCAG 2.1 AA. Shared controls for high contrast, font resizing and screen reader voice keep access needs visible across the portal.</p>
                    <p class="text-body-muted text-sm mt-2">RA 10524 informs the focus on employment opportunities for Persons with Disabilities. RA 11106 provides the Philippine context for recognizing Filipino Sign Language.</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <span class="text-xs font-medium bg-primary-tint text-primary-dark px-3 py-1 rounded-full">WCAG 2.1 AA</span>
                        <span class="text-xs font-medium bg-primary-tint text-primary-dark px-3 py-1 rounded-full">RA 10524</span>
                        <span class="text-xs font-medium bg-primary-tint text-primary-dark px-3 py-1 rounded-full">RA 11106</span>
                    </div>
                    <a href="/accessibility" class="inline-block mt-4 text-sm font-semibold text-primary hover:underline">Read the Accessibility Statement →</a>
                </article>
                <article class="bg-white rounded-2xl border border-primary-border shadow-sm p-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-primary-tint flex items-center justify-center" aria-hidden="true">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <h3 class="font-heading text-primary-dark text-lg font-semibold">Your information, your control</h3>
                    </div>
                    <p class="text-body-muted text-sm mt-3">Disability information is sensitive personal information. PWD-E's privacy commitments are grounded in RA 10173, the Data Privacy Act, and candidate-controlled disclosure.</p>
                    <div class="mt-3 bg-primary-tint rounded-lg p-3">
                        <p class="font-semibold text-primary-dark text-sm">Disclose accommodations, not a medical history</p>
                        <p class="text-body-muted text-xs mt-1">You choose what accommodation details to share and sensitive details in hiring conversations should focus on the support relevant to the job.</p>
                    </div>
                    <a href="/privacy" class="inline-block mt-4 text-sm font-semibold text-primary hover:underline">Read the Privacy Policy (RA 10173) →</a>
                </article>
            </div>
        </section>
        <!-- Dark CTA -->
        <section class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10 py-12">
            <div class="rounded-2xl bg-primary-dark text-white text-center p-10 md:p-16">
                <h2 class="font-heading text-white text-2xl sm:text-3xl font-bold">Be Part of a More Inclusive Workforce</h2>
                <p class="mt-3 text-white/80 max-w-xl mx-auto">Join PWD-E and help create a future where employment opportunities are accessible to everyone.</p>
                <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="/jobs" class="block text-center bg-white text-primary-dark font-semibold px-6 py-3 rounded-lg min-h-[48px] flex items-center justify-center hover:bg-primary-tint">Find Job Opportunities</a>
                    <a href="/employers" class="block text-center border border-white text-white font-semibold px-6 py-3 rounded-lg min-h-[48px] flex items-center justify-center hover:bg-white/10">Join as an Employer</a>
                </div>
            </div>
        </section>
    </main>
    <x-site-footer />
</body>
</html>
