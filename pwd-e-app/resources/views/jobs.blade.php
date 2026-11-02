<!DOCTYPE html>
<html lang="en" x-data="{ readable: false, a11yOpen: false }" :data-readable-font="readable ? 'true' : null">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PWD-E — Inclusive Job Directory</title>
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
        <section class="relative" style="background: linear-gradient(180deg, #DCEAF4 0%, #FFFFFF 100%);">
            <div class="max-w-[1200px] mx-auto px-6 sm:px-10 lg:px-12 pt-24 pb-12 text-center">
                <h1 class="font-heading text-primary-dark text-3xl sm:text-4xl font-bold">Inclusive Job Directory</h1>
                <p class="text-body-muted mt-3 max-w-2xl mx-auto">Every job opening below has been verified under our <strong>Workspace Accessibility Matrix</strong> and is accredited under Republic Act 10524.</p>
                <div class="mt-8 bg-white rounded-2xl shadow-sm border border-primary-border p-4 max-w-5xl mx-auto">
                    <label for="search" class="block text-sm font-bold text-primary-dark mb-2">Search for Inclusive Opportunities (Skill, Location, and Accommodation)</label>
                    <div class="flex flex-col md:flex-row gap-2">
                        <input id="search" type="search" inputmode="search" enterkeyhint="search" class="flex-1 rounded-lg border border-primary-border bg-surface px-4 py-3 text-base min-h-[48px] focus:outline-none focus:ring-2 focus:ring-primary" placeholder="e.g. Accessibility in Manila with Ramp Access or Screen Reader">
                        <button type="button" class="bg-primary-dark text-white font-semibold px-6 py-3 rounded-lg min-h-[48px] hover:bg-primary md:w-auto w-full">Find Inclusive Jobs</button>
                    </div>
                    <div class="mt-4 pt-4 border-t border-primary-border flex flex-wrap items-center gap-2">
                        <span class="text-sm text-body-muted">Popular Accommodations:</span>
                        @foreach (['Ramp Access','Screen Reader Ready','FSL Support','Remote / Hybrid','Braille Elevators'] as $pill)
                            <button type="button" class="text-sm border border-primary-border rounded-full px-3 py-1.5 text-primary-dark bg-white hover:bg-primary-tint">{{ $pill }}</button>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
        <section class="max-w-[1200px] mx-auto px-6 sm:px-10 lg:px-12 py-12">
            <ul class="space-y-4 md:space-y-6 list-none p-0">
                @php
                $jobs = [
                    ['title'=>'Digital Accessibility & Frontend Specialist','employer'=>'InnovateTech Philippines Corp.','location'=>'Taguig City (BGC) / Hybrid WFH','type'=>'Full-Time','salary'=>'₱65,000 – ₱85,000 / month','desc'=>'Lead accessibility auditing and frontend implementation across our government and enterprise portals. Ensure full compliance with WCAG 2.1 AA benchmarks.','accs'=>['ramp','elevator','restroom','screenreader','magnifier','remote','flexible hours']],
                    ['title'=>'Digital Records & Data Archiving Specialist','employer'=>'Zamboanga Maritime Logistics Inc.','location'=>'Zamboanga City / On-site Ground Floor','type'=>'Full-Time','salary'=>'₱26,000 – ₱36,000 / month','desc'=>'Digitize shipping manifests, index customs records, and manage document verification. Facility features ground-floor access with ramp and motorized desks.','accs'=>['ramp','restroom','ground','desk','flexible hours']],
                    ['title'=>'Customer Support Specialist (Chat & FSL Video Relay)','employer'=>'Converge Global Solutions BPO','location'=>'Cebu IT Park, Cebu City / Hybrid','type'=>'Full-Time','salary'=>'₱32,000 – ₱44,000 / month','desc'=>'Handle client inquiries via omnichannel chat and provide Filipino Sign Language (FSL) video assistance for Deaf customers. Bilingual FSL staff on site.','accs'=>['fsl','visual alarm','quiet room','captions','remote']],
                    ['title'=>'Tax Compliance & Statutory Reporting Analyst','employer'=>'Aegis Financial Partners PH','location'=>'Makati City / Hybrid WFH','type'=>'Full-Time','salary'=>'₱48,000 – ₱65,000 / month','desc'=>'Manage corporate tax filing, BIR certifications, and calculate statutory tax incentive deductions under Republic Act 10524 for corporate clients.','accs'=>['screenreader','magnifier','ramp','elevator','remote','flexible hours']],
                    ['title'=>'Inclusive Content Creator & Creative Copywriter','employer'=>'Mindanao Creative Media Lab','location'=>'Fully Remote / Zamboanga Region','type'=>'Remote / Contract','salary'=>'₱30,000 – ₱42,000 / month','desc'=>'Produce accessible marketing content, social media posts with alt text, and campaign narratives promoting inclusive livelihood opportunities in Mindanao.','accs'=>['remote','speech','flexible hours','captions']],
                ];
                @endphp
                @foreach ($jobs as $job)
                <li>
                    <article class="bg-white rounded-2xl border border-primary-border shadow-sm p-5 md:p-6" style="box-shadow: 0 4px 16px rgba(26,40,64,0.08);">
                        <div class="md:flex md:gap-6">
                            <div class="flex-1 min-w-0">
                                <h2 class="font-heading text-primary-dark text-xl font-bold">{{ $job['title'] }}</h2>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <span class="text-xs font-medium bg-secondary-tint text-secondary px-3 py-1 rounded-full">Verified Inclusive</span>
                                    <span class="text-xs font-medium bg-primary-tint text-primary-dark px-3 py-1 rounded-full">RA 10524 Accredited</span>
                                </div>
                                <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-body-muted">
                                    <span class="font-medium text-primary-dark">{{ $job['employer'] }}</span>
                                    <span class="inline-flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>{{ $job['location'] }}</span>
                                    <span class="inline-flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/></svg>{{ $job['type'] }}</span>
                                    <span class="inline-flex items-center gap-1" style="overflow-wrap: anywhere;"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>{{ $job['salary'] }}</span>
                                </div>
                                <p class="mt-3 text-body-muted text-sm md:text-base">{{ $job['desc'] }}</p>
                                <div class="mt-3">
                                    <span class="text-sm font-bold text-primary-dark">Verified Accommodations:</span>
                                    <div class="mt-1 flex flex-wrap gap-1.5">
                                        @foreach ($job['accs'] as $a)
                                            <span class="text-xs bg-primary-tint text-primary-dark px-2.5 py-1 rounded-full">✓ {{ $a }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 md:mt-0 md:w-48 shrink-0">
                                <div class="bg-surface rounded-xl border border-primary-border p-4 flex flex-col gap-2">
                                    <span class="text-center text-sm font-medium text-primary-dark bg-primary-tint rounded-full px-3 py-1">Inclusive Employer</span>
                                    <a href="#" class="block text-center bg-primary-dark text-white font-semibold py-2.5 rounded-lg min-h-[40px] flex items-center justify-center hover:bg-primary">Apply via Portal</a>
                                </div>
                            </div>
                        </div>
                    </article>
                </li>
                @endforeach
            </ul>
        </section>
    </main>
    <x-site-footer />
</body>
</html>
