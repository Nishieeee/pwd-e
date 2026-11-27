<x-layouts::auth.full :title="__('Register')">
    @php
        $psgcProvinces = collect();
        $psgcCities = collect();
        $psgcCityMeta = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('location_cities')) {
            $psgcCities = \App\Models\LocationCity::orderBy('name')->get(['name', 'province_name', 'zip_code']);
            $psgcProvinces = $psgcCities->pluck('province_name')->filter()->unique()->sort()->values();
            foreach ($psgcCities as $cItem) {
                $psgcCityMeta[strtolower($cItem->name)] = [
                    'province' => $cItem->province_name ?? '',
                    'zip' => $cItem->zip_code ?? '',
                ];
            }
        }
        $oldDisabilities = old('disability_types', old('disability_type') ? [old('disability_type')] : []);
    @endphp
    <header class="bg-primary-dark">
        <div class="mx-auto flex h-16 max-w-[1200px] items-center justify-between px-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-white font-heading font-bold text-lg focus:outline-none focus:ring-0">
                <img src="{{ asset('assets/Logo.png') }}" alt="PWD-E" class="h-14 w-14 shrink-0 object-contain">
                <span>PWD-E</span>
            </a>
            <nav class="hidden items-center gap-6 text-sm font-medium text-white md:flex">
                <a href="{{ route('home') }}" class="hover:underline focus:outline-none focus:ring-0">Home</a>
                <a href="{{ route('jobs') }}" class="hover:underline focus:outline-none focus:ring-0">Jobs</a>
                <a href="{{ route('employers') }}" class="hover:underline focus:outline-none focus:ring-0">Employers</a>
                <a href="{{ route('about') }}" class="hover:underline focus:outline-none focus:ring-0">About</a>
            </nav>
            <div class="flex items-center gap-2">
                @unless(request()->routeIs('login'))
                    <a href="{{ route('login') }}" class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-primary-dark focus:outline-none focus:ring-0">Sign In</a>
                @endunless
                @unless(request()->routeIs('register'))
                    <a href="{{ route('register') }}" class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-primary-dark focus:outline-none focus:ring-0">Register</a>
                @endunless
            </div>
        </div>
    </header>

    <x-accessibility-panel />

    <main class="mx-auto max-w-[1200px] px-4 py-6 sm:px-6">
        <a href="{{ route('home') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-primary hover:underline">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Back to Home
        </a>

        <div class="flex flex-col-reverse items-stretch overflow-hidden rounded-2xl bg-white shadow-[0_8px_24px_rgba(26,40,64,0.06)] md:grid md:grid-cols-2"
             x-data="{
                 role: '{{ old('role', 'candidate') }}',
                 step: {{ old('role', 'candidate') === 'employer' ? ($errors->hasAny(['company_name', 'company_type', 'industry', 'first_name', 'middle_name', 'last_name', 'email', 'phone', 'password']) ? 0 : ($errors->hasAny(['street_address', 'country', 'province', 'city', 'barangay', 'zip_code', 'terms']) ? 1 : 0)) : ($errors->hasAny(['first_name', 'middle_name', 'last_name', 'email', 'phone', 'password']) ? 0 : ($errors->hasAny(['date_of_birth']) ? 1 : ($errors->hasAny(['disability_type', 'disability_types', 'pwd_document_type', 'pwd_id_number', 'medical_certificate_number', 'pwd_id_file', 'medical_certificate_file', 'consent', 'terms']) ? 2 : 0))) }},
                 candidateSteps: 3,
                 employerSteps: 2,
                 candidateDocType: '{{ old('pwd_document_type', 'pwd_id') }}',
                 cityMeta: @js($psgcCityMeta),
                 showCandidatePassword: false,
                 showEmployerPassword: false,
                 canContinue: false,
                 totalSteps() { return this.role === 'candidate' ? this.candidateSteps : this.employerSteps; },
                 val(id) { const el = document.getElementById(id); return el ? (el.value || '').trim() : ''; },
                 checked(id) { const el = document.getElementById(id); return !!(el && !el.matches(':disabled') && el.checked); },
                 hasCheckedDisability() {
                     const boxes = document.querySelectorAll('input.candidate-disability-checkbox');
                     for (const b of boxes) {
                         if (!b.matches(':disabled') && b.checked) return true;
                     }
                     return false;
                 },
                 syncEmployerCity() {
                     const cVal = this.val('employer_city').toLowerCase();
                     if (cVal && this.cityMeta[cVal]) {
                         const provEl = document.getElementById('employer_province');
                         const zipEl = document.getElementById('employer_zip_code');
                         if (provEl && this.cityMeta[cVal].province && !provEl.value) {
                             provEl.value = this.cityMeta[cVal].province;
                         }
                         if (zipEl && this.cityMeta[cVal].zip) {
                             zipEl.value = this.cityMeta[cVal].zip;
                         }
                     }
                     this.updateState();
                 },
                 isAdultDob(dobVal) {
                     if (!dobVal) return false;
                     const parts = dobVal.split('-').map(Number);
                     if (parts.length !== 3 || parts.some(isNaN)) return false;
                     const now = new Date();
                     let age = now.getFullYear() - parts[0];
                     const m = (now.getMonth() + 1) - parts[1];
                     if (m < 0 || (m === 0 && now.getDate() < parts[2])) age--;
                     return age >= 18;
                 },
                 stepValid() {
                     if (this.role === 'candidate') {
                         if (this.step === 0) {
                             const p = this.val('candidate_password');
                             const pc = this.val('candidate_password_confirmation');
                             return this.val('candidate_first_name') !== '' &&
                                    this.val('candidate_last_name') !== '' &&
                                    this.val('candidate_email') !== '' &&
                                    this.val('candidate_phone').length >= 7 &&
                                    p.length >= 8 && p === pc;
                         }
                         if (this.step === 1) {
                             return this.isAdultDob(this.val('date_of_birth'));
                         }
                         if (this.step === 2) {
                             return this.hasCheckedDisability() &&
                                    this.val('pwd_id_number') !== '' &&
                                    this.checked('candidate_consent') &&
                                    this.checked('candidate_terms');
                         }
                     } else {
                         if (this.step === 0) {
                             const p = this.val('employer_password');
                             const pc = this.val('employer_password_confirmation');
                             return this.val('employer_company_name') !== '' &&
                                    this.val('employer_company_type') !== '' &&
                                    this.val('employer_email') !== '' &&
                                    this.val('employer_phone').length >= 7 &&
                                    p.length >= 8 && p === pc;
                         }
                         if (this.step === 1) {
                             return this.val('employer_province') !== '' &&
                                    this.val('employer_city') !== '' &&
                                    this.checked('employer_terms');
                         }
                     }
                     return true;
                 },
                 updateState() { this.canContinue = this.stepValid(); },
                 next() {
                     if (!this.stepValid()) return;
                     if (this.step < this.totalSteps() - 1) this.step++;
                     this.$nextTick(() => this.updateState());
                 },
                 prev() { if (this.step > 0) this.step--; this.$nextTick(() => this.updateState()); },
             }"
             x-init="$nextTick(() => updateState())"
             @input="updateState()"
             @change="updateState()">
            <section class="flex h-full flex-col justify-between bg-gradient-to-b from-primary-tint to-[#D6E7F3] px-5 py-6 md:px-9 md:py-7">
                <div class="sticky top-0 flex h-full flex-col justify-between gap-4">
                    {{-- Candidate (PWD) Mode Left Panel --}}
                    <div x-show="role === 'candidate'" class="flex flex-1 flex-col justify-between gap-3">
                        <div>
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-primary-border bg-white px-3 py-1.5 text-xs font-semibold text-primary-dark">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="4" r="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0l-3 6m3-6l3 6M9 9h6"/></svg>
                                <span>Accessibility-first employment</span>
                            </span>

                            <h1 class="mt-2.5 font-heading text-2xl font-extrabold leading-tight text-primary-dark md:text-[32px] lg:text-[34px]">
                                Your next chapter starts with inclusion.
                            </h1>
                            <p class="mt-1.5 max-w-[46ch] text-sm leading-relaxed text-body-muted">
                                Join PWD-E and connect with accessible, inclusive workplaces across the Philippines.
                            </p>
                        </div>

                        <div class="my-3 flex justify-center">
                            <img src="{{ asset('assets/inclusive collaboration.png') }}"
                                 onerror="this.onerror=null;this.src='{{ asset('assets/Inclusive workplace collaboration.png') }}';"
                                 alt=""
                                 aria-hidden="true"
                                 loading="eager"
                                 decoding="async"
                                 width="420"
                                 height="210"
                                 class="mx-auto w-full max-w-sm md:max-w-md h-auto object-contain">
                        </div>

                        <div class="space-y-2.5">
                            {{-- Step 1: Icon User/Account --}}
                            <div class="flex items-start gap-3">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-primary-border bg-white text-primary-dark" aria-hidden="true">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold leading-snug text-primary-dark">Create Your Account</p>
                                    <p class="text-xs leading-snug text-body-muted">Unlock access to hundreds of inclusive job opportunities tailored to you.</p>
                                </div>
                            </div>

                            {{-- Step 2: Icon ID/Verification --}}
                            <div class="flex items-start gap-3">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-primary-border bg-white text-primary-dark" aria-hidden="true">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                                    </svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold leading-snug text-primary-dark">Verify Your PWD Profile</p>
                                    <p class="text-xs leading-snug text-body-muted">Complete your profile with accommodations and skills to match with the right employers.</p>
                                </div>
                            </div>

                            {{-- Step 3: Icon Chat/Message --}}
                            <div class="flex items-start gap-3">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-primary-border bg-white text-primary-dark" aria-hidden="true">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                    </svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold leading-snug text-primary-dark">Connect Directly</p>
                                    <p class="text-xs leading-snug text-body-muted">Message instantly with RA 10524-accredited employers who value your talent.</p>
                                </div>
                            </div>

                            {{-- Step 4: Icon Briefcase/Check --}}
                            <div class="flex items-start gap-3">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-primary-border bg-white text-primary-dark" aria-hidden="true">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold leading-snug text-primary-dark">Start Working!</p>
                                    <p class="text-xs leading-snug text-body-muted">Find a fulfilling career where you can thrive and be your authentic self.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Employer Mode Left Panel --}}
                    <div x-show="role === 'employer'" x-cloak class="flex flex-1 flex-col justify-between gap-3">
                        <div>
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-primary-border bg-white px-3 py-1.5 text-xs font-semibold text-primary-dark">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span>For Employers</span>
                            </span>

                            <h1 class="mt-2.5 font-heading text-2xl font-extrabold leading-tight text-primary-dark md:text-[32px] lg:text-[34px]">
                                Build a stronger, inclusive workforce.
                            </h1>
                            <p class="mt-1.5 max-w-[46ch] text-sm leading-relaxed text-body-muted">
                                Connect with a dedicated pool of skilled professionals with disabilities and make inclusive hiring seamless.
                            </p>
                        </div>

                        <div class="my-3 flex justify-center">
                            <img src="{{ asset('assets/inclusive collaboration.png') }}"
                                 onerror="this.onerror=null;this.src='{{ asset('assets/Inclusive workplace collaboration.png') }}';"
                                 alt=""
                                 aria-hidden="true"
                                 loading="eager"
                                 decoding="async"
                                 width="420"
                                 height="210"
                                 class="mx-auto w-full max-w-sm md:max-w-md h-auto object-contain">
                        </div>

                        <div class="space-y-2.5">
                            {{-- Step 1: Icon Building/Setup --}}
                            <div class="flex items-start gap-3">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-primary-border bg-white text-primary-dark" aria-hidden="true">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold leading-snug text-primary-dark">Company Account Setup</p>
                                    <p class="text-xs leading-snug text-body-muted">Unlock immediate access to a specialized talent pool of verified professionals with disabilities.</p>
                                </div>
                            </div>

                            {{-- Step 2: Instant Onboarding --}}
                            <div class="flex items-start gap-3">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-primary-border bg-white text-primary-dark" aria-hidden="true">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold leading-snug text-primary-dark">Instant Onboarding</p>
                                    <p class="text-xs leading-snug text-body-muted">Post vacancies immediately and match with qualified PWD talents through real-time accessibility filters.</p>
                                </div>
                            </div>

                            {{-- Step 3: Icon Scale/Compliance --}}
                            <div class="flex items-start gap-3">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-primary-border bg-white text-primary-dark" aria-hidden="true">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold leading-snug text-primary-dark">Meet RA 10524 Compliance</p>
                                    <p class="text-xs leading-snug text-body-muted">Seamlessly fulfill legislative requirements for inclusive workplaces and access tax incentives.</p>
                                </div>
                            </div>

                            {{-- Step 4: Icon Handshake/Hire --}}
                            <div class="flex items-start gap-3">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-primary-border bg-white text-primary-dark" aria-hidden="true">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold leading-snug text-primary-dark">Connect &amp; Hire</p>
                                    <p class="text-xs leading-snug text-body-muted">Review candidate accommodation needs and start building a diverse, high-performing team.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Note --}}
                    <div class="mt-2 flex items-center gap-2 border-t border-primary-border pt-3 text-xs text-body-muted">
                        <svg class="h-4 w-4 shrink-0 text-primary-dark" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span>Your privacy is protected under RA 10173.</span>
                    </div>
                </div>
            </section>

            <section class="flex h-full flex-col p-5 md:p-8 lg:px-12 lg:py-8">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.08em] text-primary">New Account</p>
                    <h2 class="mt-1.5 font-heading text-3xl font-bold text-primary-dark md:text-[34px]">Register</h2>

                    <div class="mt-3.5 flex items-center gap-2" aria-label="Registration progress">
                        <template x-for="i in totalSteps()" :key="i">
                            <span class="h-2 flex-1 rounded-full" :class="i-1 <= step ? 'bg-primary' : 'bg-[#CBD5E1]'"></span>
                        </template>
                    </div>
                </div>

                <form method="POST" action="{{ route('register.store') }}" enctype="multipart/form-data" class="mt-5 flex flex-1 flex-col justify-between gap-5" novalidate
                      @keydown.enter="if (step < totalSteps() - 1) { $event.preventDefault(); next(); }">
                    @csrf
                    <input type="hidden" name="role" :value="role">

                    {{-- Step 0: Role + first inputs --}}
                    <div x-show="step === 0" x-cloak class="flex flex-col gap-5">
                        <div>
                            <p class="mb-1.5 text-sm font-semibold text-primary-dark">I am registering as <span class="text-[#B3261E]">*</span></p>
                            <div class="flex gap-3" role="radiogroup" aria-label="Registration role">
                                <label class="flex-1 cursor-pointer focus:outline-none focus:ring-0">
                                    <input type="radio" name="role" value="candidate" x-model="role" @change="step = 0; $nextTick(() => updateState())" class="sr-only">
                                    <span class="flex h-12 items-center justify-center rounded-lg border border-[#CBD5E1] text-sm font-semibold text-primary-dark focus:outline-none focus:ring-0" :class="role === 'candidate' ? 'bg-primary text-white border-primary' : 'bg-white'">Candidate (PWD)</span>
                                </label>
                                <label class="flex-1 cursor-pointer focus:outline-none focus:ring-0">
                                    <input type="radio" name="role" value="employer" x-model="role" @change="step = 0; $nextTick(() => updateState())" class="sr-only">
                                    <span class="flex h-12 items-center justify-center rounded-lg border border-[#CBD5E1] text-sm font-semibold text-primary-dark focus:outline-none focus:ring-0" :class="role === 'employer' ? 'bg-primary text-white border-primary' : 'bg-white'">Employer</span>
                                </label>
                            </div>
                            @error('role')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                        </div>

                        {{-- Candidate first inputs --}}
                        <fieldset x-show="role === 'candidate'" class="flex flex-col gap-5 border-0 p-0 m-0" :disabled="role !== 'candidate'">
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <div>
                                    <label for="candidate_first_name" class="mb-1.5 block text-sm font-semibold text-primary-dark">First name <span class="text-[#B3261E]">*</span></label>
                                    <input id="candidate_first_name" name="first_name" type="text" value="{{ old('first_name') }}" :required="role === 'candidate'" autocomplete="given-name" placeholder="First name" :disabled="role !== 'candidate'"
                                           class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                    @error('first_name')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="candidate_middle_name" class="mb-1.5 block text-sm font-semibold text-primary-dark">Middle name</label>
                                    <input id="candidate_middle_name" name="middle_name" type="text" value="{{ old('middle_name') }}" autocomplete="additional-name" placeholder="Middle name"
                                           class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                </div>
                                <div>
                                    <label for="candidate_last_name" class="mb-1.5 block text-sm font-semibold text-primary-dark">Last name <span class="text-[#B3261E]">*</span></label>
                                    <input id="candidate_last_name" name="last_name" type="text" value="{{ old('last_name') }}" :required="role === 'candidate'" autocomplete="family-name" placeholder="Last name"
                                           class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                    @error('last_name')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <label for="candidate_email" class="mb-1.5 block text-sm font-semibold text-primary-dark">Email address <span class="text-[#B3261E]">*</span></label>
                                    <div class="relative">
                                        <input id="candidate_email" name="email" type="email" value="{{ old('email') }}" :required="role === 'candidate'" autocomplete="email" placeholder="you@example.com"
                                               class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 pr-9 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                    </div>
                                    @error('email')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="candidate_phone" class="mb-1.5 block text-sm font-semibold text-primary-dark">Phone number <span class="text-[#B3261E]">*</span></label>
                                    <input id="candidate_phone" name="phone" type="tel" value="{{ old('phone') }}" :required="role === 'candidate'" autocomplete="tel" placeholder="e.g. +63 917 123 4567"
                                           class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                    @error('phone')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                                </div>
                            </div>
                            <div>
                                <label for="candidate_password" class="mb-1.5 block text-sm font-semibold text-primary-dark">Password <span class="text-[#B3261E]">*</span></label>
                                <div class="relative">
                                    <input id="candidate_password" name="password" :type="showCandidatePassword ? 'text' : 'password'" autocomplete="new-password" placeholder="Password (min. 8 characters)"
                                           class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 pr-12 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                    <button type="button" @click="showCandidatePassword = !showCandidatePassword" :aria-label="showCandidatePassword ? 'Hide password' : 'Show password'"
                                            class="absolute right-1 top-1 grid h-10 w-10 place-items-center rounded-md text-body-muted hover:text-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/30">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                </div>
                                @error('password')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="candidate_password_confirmation" class="mb-1.5 block text-sm font-semibold text-primary-dark">Confirm password <span class="text-[#B3261E]">*</span></label>
                                <input id="candidate_password_confirmation" name="password_confirmation" :type="showCandidatePassword ? 'text' : 'password'" autocomplete="new-password" placeholder="Confirm password"
                                       class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                            </div>
                        </fieldset>

                        {{-- Employer Step 0 inputs --}}
                        <fieldset x-show="role === 'employer'" class="flex flex-col gap-4 border-0 p-0 m-0" :disabled="role !== 'employer'">
                            <div>
                                <label for="employer_company_name" class="mb-1.5 block text-sm font-semibold text-primary-dark">Company name <span class="text-[#B3261E]">*</span></label>
                                <div class="relative">
                                    <input id="employer_company_name" name="company_name" type="text" value="{{ old('company_name') }}" :required="role === 'employer'" placeholder="e.g. Acme Accessibility Corp."
                                           class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 pr-9 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                </div>
                                @error('company_name')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                            </div>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <label for="employer_company_type" class="mb-1.5 block text-sm font-semibold text-primary-dark">Company type <span class="text-[#B3261E]">*</span></label>
                                    <select id="employer_company_type" name="company_type" :required="role === 'employer'" @change="updateState()"
                                            class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 text-sm text-primary-dark focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                        <option value="">Select company type…</option>
                                        @foreach ([
                                            'Private Corporation',
                                            'SME / Sole Proprietorship',
                                            'Partnership',
                                            'BPO / Multinational',
                                            'Cooperative / Social Enterprise',
                                            'NGO / Non-Profit Organization',
                                            'Government Agency / LGU',
                                            'Educational Institution',
                                        ] as $ct)
                                            <option value="{{ $ct }}" {{ old('company_type') === $ct ? 'selected' : '' }}>{{ $ct }}</option>
                                        @endforeach
                                    </select>
                                    @error('company_type')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label for="employer_industry" class="mb-1.5 block text-sm font-semibold text-primary-dark">Industry sector</label>
                                    <select id="employer_industry" name="industry"
                                            class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 text-sm text-primary-dark focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                        <option value="">Select industry…</option>
                                        @foreach ([
                                            'Information Technology & Software',
                                            'BPO & Customer Experience',
                                            'Healthcare & Medical Services',
                                            'Banking, Financial Services & FinTech',
                                            'Education & Academic Institutions',
                                            'Manufacturing & Logistics',
                                            'Retail, E-Commerce & Hospitality',
                                            'Government & Non-Profit Services',
                                            'Creative, Media & Design',
                                            'Other',
                                        ] as $ind)
                                            <option value="{{ $ind }}" {{ old('industry') === $ind ? 'selected' : '' }}>{{ $ind }}</option>
                                        @endforeach
                                    </select>
                                    @error('industry')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <div>
                                    <label for="employer_first_name" class="mb-1.5 block text-sm font-semibold text-primary-dark">Rep. first name</label>
                                    <div class="relative">
                                        <input id="employer_first_name" name="first_name" type="text" value="{{ old('first_name') }}" autocomplete="given-name" placeholder="First name"
                                               class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 pr-8 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                    </div>
                                    @error('first_name')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="employer_middle_name" class="mb-1.5 block text-sm font-semibold text-primary-dark">Rep. middle name</label>
                                    <div class="relative">
                                        <input id="employer_middle_name" name="middle_name" type="text" value="{{ old('middle_name') }}" autocomplete="additional-name" placeholder="Middle name"
                                               class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 pr-8 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                    </div>
                                    @error('middle_name')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="employer_last_name" class="mb-1.5 block text-sm font-semibold text-primary-dark">Rep. last name</label>
                                    <div class="relative">
                                        <input id="employer_last_name" name="last_name" type="text" value="{{ old('last_name') }}" autocomplete="family-name" placeholder="Last name"
                                               class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 pr-8 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                    </div>
                                    @error('last_name')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <label for="employer_email" class="mb-1.5 block text-sm font-semibold text-primary-dark">Work email address <span class="text-[#B3261E]">*</span></label>
                                    <div class="relative">
                                        <input id="employer_email" name="email" type="email" value="{{ old('email') }}" :required="role === 'employer'" autocomplete="email" placeholder="hr@company.com"
                                               class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 pr-9 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                    </div>
                                    @error('email')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="employer_phone" class="mb-1.5 block text-sm font-semibold text-primary-dark">Contact phone <span class="text-[#B3261E]">*</span></label>
                                    <input id="employer_phone" name="phone" type="tel" value="{{ old('phone') }}" :required="role === 'employer'" autocomplete="tel" placeholder="e.g. +63 917 000 0000"
                                           class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                    @error('phone')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <label for="employer_password" class="mb-1.5 block text-sm font-semibold text-primary-dark">Password <span class="text-[#B3261E]">*</span></label>
                                    <div class="relative">
                                        <input id="employer_password" name="password" :type="showEmployerPassword ? 'text' : 'password'" autocomplete="new-password" placeholder="Min. 8 characters"
                                               class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 pr-12 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                        <button type="button" @click="showEmployerPassword = !showEmployerPassword" :aria-label="showEmployerPassword ? 'Hide password' : 'Show password'"
                                                class="absolute right-1 top-1 grid h-10 w-10 place-items-center rounded-md text-body-muted hover:text-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/30">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                    </div>
                                    @error('password')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="employer_password_confirmation" class="mb-1.5 block text-sm font-semibold text-primary-dark">Confirm password <span class="text-[#B3261E]">*</span></label>
                                    <input id="employer_password_confirmation" name="password_confirmation" :type="showEmployerPassword ? 'text' : 'password'" autocomplete="new-password" placeholder="Confirm password"
                                           class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                </div>
                            </div>
                        </fieldset>

                        <div class="flex gap-3">
                            <button type="button" @click="next()" :disabled="!canContinue" :class="canContinue ? 'bg-[#2A5D8F] hover:bg-[#224B73]' : 'bg-[#94A3B8] cursor-not-allowed'" class="flex h-[52px] w-full items-center justify-center gap-2 rounded-lg text-base font-semibold text-white disabled:opacity-60">
                                Continue
                            </button>
                        </div>
                    </div>

                    {{-- Employer Step 1: Chopped Headquarters Address & Terms --}}
                    <fieldset x-show="role === 'employer' && step === 1" x-cloak :disabled="role !== 'employer'" class="flex flex-col gap-4 border-0 p-0 m-0">
                        <div>
                            <h3 class="text-sm font-bold text-primary-dark">Headquarters Address</h3>
                            <p class="text-xs text-body-muted mt-0.5">Provide your main company address. Additional branch addresses can be added anytime in your Company Profile.</p>
                        </div>

                        <div>
                            <label for="employer_street_address" class="mb-1.5 block text-sm font-semibold text-primary-dark">Building / Floor / Street Address</label>
                            <div class="relative">
                                <input id="employer_street_address" name="street_address" type="text" value="{{ old('street_address') }}" placeholder="e.g. 12F Inclusion Tower, Ayala Ave."
                                       class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 pr-9 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                            </div>
                            @error('street_address')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                            <div>
                                <label for="employer_country" class="mb-1.5 block text-sm font-semibold text-primary-dark">Country</label>
                                <input id="employer_country" name="country" type="text" value="{{ old('country', 'Philippines') }}"
                                       class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 text-base text-primary-dark focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                @error('country')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="employer_province" class="mb-1.5 block text-sm font-semibold text-primary-dark">Province <span class="text-[#B3261E]">*</span></label>
                                <div class="relative">
                                    <input id="employer_province" name="province" type="text" list="psgc-provinces-list" value="{{ old('province') }}" :required="role === 'employer'" @input="updateState()" @change="updateState()" placeholder="e.g. Metro Manila"
                                           class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 pr-8 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                </div>
                                @error('province')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="employer_city" class="mb-1.5 block text-sm font-semibold text-primary-dark">City / Municipality <span class="text-[#B3261E]">*</span></label>
                                <div class="relative">
                                    <input id="employer_city" name="city" type="text" list="psgc-cities-list" value="{{ old('city') }}" :required="role === 'employer'" @input="syncEmployerCity()" @change="syncEmployerCity()" placeholder="e.g. Makati City"
                                           class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 pr-8 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                </div>
                                @error('city')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                            <div class="sm:col-span-2">
                                <label for="employer_barangay" class="mb-1.5 block text-sm font-semibold text-primary-dark">Barangay</label>
                                <div class="relative">
                                    <input id="employer_barangay" name="barangay" type="text" value="{{ old('barangay') }}" placeholder="e.g. Barangay San Lorenzo"
                                           class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 pr-8 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                </div>
                                @error('barangay')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="employer_zip_code" class="mb-1.5 block text-sm font-semibold text-primary-dark">Zip code</label>
                                <input id="employer_zip_code" name="zip_code" type="text" value="{{ old('zip_code') }}" placeholder="e.g. 1223"
                                       class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                @error('zip_code')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <datalist id="psgc-provinces-list">
                            @foreach($psgcProvinces as $provOpt)
                                <option value="{{ $provOpt }}"></option>
                            @endforeach
                        </datalist>
                        <datalist id="psgc-cities-list">
                            @foreach($psgcCities as $cityOpt)
                                <option value="{{ $cityOpt->name }}">{{ $cityOpt->province_name }} (ZIP {{ $cityOpt->zip_code }})</option>
                            @endforeach
                        </datalist>

                        <label class="flex cursor-pointer items-start gap-2 text-sm text-primary-dark pt-1">
                            <input id="employer_terms" type="checkbox" name="terms" value="1" {{ old('terms') ? 'checked' : '' }} class="mt-1 h-4 w-4" @change="updateState()">
                            <span>I accept the Terms and Conditions. <span class="text-[#B3261E]">*</span></span>
                        </label>
                        @error('terms')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror

                        <div class="flex gap-3">
                            <button type="button" @click="prev()" class="flex h-[52px] flex-1 items-center justify-center rounded-lg border border-[#CBD5E1] text-base font-semibold text-primary-dark hover:bg-primary-tint">Back</button>
                            <button type="submit" :disabled="!canContinue" :class="canContinue ? 'bg-[#2A5D8F] hover:bg-[#224B73]' : 'bg-[#94A3B8] cursor-not-allowed'" class="flex h-[52px] flex-1 items-center justify-center gap-2 rounded-lg text-base font-semibold text-white disabled:opacity-60">
                                Create account
                            </button>
                        </div>
                    </fieldset>

                    {{-- Candidate Step 1: DOB --}}
                    <fieldset x-show="role === 'candidate' && step === 1" x-cloak :disabled="role !== 'candidate'" class="flex flex-col gap-5 border-0 p-0 m-0">
                        <div>
                            <label for="date_of_birth" class="mb-1.5 block text-sm font-semibold text-primary-dark">Date of birth <span class="text-[#B3261E]">*</span></label>
                            <input id="date_of_birth" name="date_of_birth" type="date" value="{{ old('date_of_birth') }}" :required="role === 'candidate'" @input="updateState()" @change="updateState()"
                                   class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 text-base text-primary-dark focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                            @error('date_of_birth')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                        </div>
                        <div class="flex gap-3">
                            <button type="button" @click="prev()" class="flex h-[52px] flex-1 items-center justify-center rounded-lg border border-[#CBD5E1] text-base font-semibold text-primary-dark hover:bg-primary-tint">Back</button>
                            <button type="button" @click="next()" :disabled="!canContinue" :class="canContinue ? 'bg-[#2A5D8F] hover:bg-[#224B73]' : 'bg-[#94A3B8] cursor-not-allowed'" class="flex h-[52px] flex-1 items-center justify-center rounded-lg text-base font-semibold text-white disabled:opacity-60">Next</button>
                        </div>
                    </fieldset>

                    {{-- Candidate Step 2: PWD ID, Medical Cert, or Both --}}
                    <fieldset x-show="role === 'candidate' && step === 2" x-cloak :disabled="role !== 'candidate'" class="flex flex-col gap-5 border-0 p-0 m-0">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-primary-dark">
                                Disability type(s) <span class="text-xs font-normal text-body-muted">(Select all that apply)</span> <span class="text-[#B3261E]">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                                @foreach (['Visual','Hearing','Motor/Physical','Speech','Cerebral Palsy','Psychosocial','Intellectual','Chronic / Orthopedic'] as $dt)
                                    <label class="flex items-center gap-2 rounded-lg border border-[#CBD5E1] bg-white px-2.5 py-2 text-xs font-medium text-primary-dark hover:bg-slate-50 cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 has-[:checked]:font-bold transition-colors">
                                        <input type="checkbox"
                                               name="disability_types[]"
                                               value="{{ $dt }}"
                                               {{ is_array($oldDisabilities) && in_array($dt, $oldDisabilities, true) ? 'checked' : '' }}
                                               @change="updateState()"
                                               class="candidate-disability-checkbox h-4 w-4 rounded text-primary focus:ring-primary shrink-0">
                                        <span class="leading-tight">{{ $dt }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('disability_types')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                            @error('disability_type')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-primary-dark">Qualification Credential <span class="text-[#B3261E]">*</span></label>
                            <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-3">
                                <label class="flex items-start gap-2.5 rounded-xl border p-3 cursor-pointer transition-all"
                                       :class="candidateDocType === 'pwd_id' ? 'border-primary bg-primary/5 ring-1 ring-primary' : 'border-[#CBD5E1] hover:bg-slate-50'">
                                    <input type="radio" name="pwd_document_type" value="pwd_id" x-model="candidateDocType" @change="$nextTick(() => updateState())" class="mt-0.5 text-primary focus:ring-primary h-4 w-4 shrink-0">
                                    <div>
                                        <p class="text-xs font-bold text-primary-dark">Government PWD ID</p>
                                        <p class="text-[11px] text-body-muted">Official LGU PDAO / CSWDO card</p>
                                    </div>
                                </label>
                                <label class="flex items-start gap-2.5 rounded-xl border p-3 cursor-pointer transition-all"
                                       :class="candidateDocType === 'medical_certificate' ? 'border-primary bg-primary/5 ring-1 ring-primary' : 'border-[#CBD5E1] hover:bg-slate-50'">
                                    <input type="radio" name="pwd_document_type" value="medical_certificate" x-model="candidateDocType" @change="$nextTick(() => updateState())" class="mt-0.5 text-primary focus:ring-primary h-4 w-4 shrink-0">
                                    <div>
                                        <p class="text-xs font-bold text-primary-dark">Clinical Medical Cert</p>
                                        <p class="text-[11px] text-body-muted">Fit-to-Work / Medical diagnosis</p>
                                    </div>
                                </label>
                                <label class="flex items-start gap-2.5 rounded-xl border p-3 cursor-pointer transition-all"
                                       :class="candidateDocType === 'both' ? 'border-primary bg-primary/5 ring-1 ring-primary' : 'border-[#CBD5E1] hover:bg-slate-50'">
                                    <input type="radio" name="pwd_document_type" value="both" x-model="candidateDocType" @change="$nextTick(() => updateState())" class="mt-0.5 text-primary focus:ring-primary h-4 w-4 shrink-0">
                                    <div>
                                        <p class="text-xs font-bold text-primary-dark">Both PWD ID &amp; Med Cert</p>
                                        <p class="text-[11px] text-body-muted">Upload both ID card &amp; medical cert</p>
                                    </div>
                                </label>
                            </div>
                            @error('pwd_document_type')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                        </div>

                        <div class="space-y-2">
                            <div class="grid grid-cols-1 gap-3" :class="candidateDocType === 'both' ? 'sm:grid-cols-2' : ''">
                                <div>
                                    <label for="pwd_id_number" class="mb-1.5 block text-sm font-semibold text-primary-dark">
                                        <span x-text="candidateDocType === 'medical_certificate' ? 'Medical Certificate / PRC License Ref. No.' : 'PWD ID Card Number'">PWD ID or Medical Certificate Number</span> <span class="text-[#B3261E]">*</span>
                                    </label>
                                    <div class="relative">
                                        <input id="pwd_id_number" name="pwd_id_number" type="text" value="{{ old('pwd_id_number') }}" :required="role === 'candidate'" @input="updateState()"
                                               :placeholder="candidateDocType === 'medical_certificate' ? 'e.g. MED-2026-001 / PRC-123456' : 'e.g. 12-3456-7890'"
                                               class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 pr-9 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                    </div>
                                    @error('pwd_id_number')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                                </div>

                                <div x-show="candidateDocType === 'both'" x-cloak>
                                    <label for="medical_certificate_number" class="mb-1.5 block text-sm font-semibold text-primary-dark">
                                        Med Cert / PRC Ref. No. <span class="text-xs font-normal text-body-muted">(Optional)</span>
                                    </label>
                                    <div class="relative">
                                        <input id="medical_certificate_number" name="medical_certificate_number" type="text" value="{{ old('medical_certificate_number') }}" :disabled="role !== 'candidate' || candidateDocType !== 'both'"
                                               placeholder="e.g. MED-2026-001 / PRC-123456"
                                               class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 pr-9 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                    </div>
                                    @error('medical_certificate_number')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <p class="text-xs text-body-muted" x-show="candidateDocType === 'medical_certificate'">
                                <strong>PESO Qualification Note:</strong> A Clinical Medical Certificate qualifies you for immediate job application. You may upload an official PWD ID card later for employer R.A. 10524 tax credits.
                            </p>
                            <p class="text-xs text-body-muted" x-show="candidateDocType === 'pwd_id'">
                                <strong>Tax Compliance Note:</strong> Your official PWD ID validates qualification and enables participating employers to claim statutory R.A. 10524 gross wage tax deductions.
                            </p>
                            <p class="text-xs text-body-muted" x-show="candidateDocType === 'both'" x-cloak>
                                <strong>Complete Credential Note:</strong> Submitting both your official PWD ID and Clinical Medical Certificate provides immediate occupational fitness clearance and full R.A. 10524 employer tax credit eligibility.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-3" :class="candidateDocType === 'both' ? 'sm:grid-cols-2' : ''">
                            <div x-show="candidateDocType === 'pwd_id' || candidateDocType === 'both'">
                                <label for="pwd_id_file" class="mb-1.5 block text-sm font-semibold text-primary-dark">
                                    Upload PWD ID Card (Photo / PDF)
                                </label>
                                <input id="pwd_id_file" name="pwd_id_file" type="file" accept=".jpg,.jpeg,.png,.pdf" :disabled="role !== 'candidate' || candidateDocType === 'medical_certificate'"
                                       class="block w-full text-sm text-primary-dark file:mr-3 file:rounded-md file:border-0 file:bg-primary file:px-3 file:py-2 file:text-white">
                                @error('pwd_id_file')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                            </div>

                            <div x-show="candidateDocType === 'medical_certificate' || candidateDocType === 'both'" x-cloak>
                                <label for="medical_certificate_file" class="mb-1.5 block text-sm font-semibold text-primary-dark">
                                    Upload Medical Certificate (Photo / PDF)
                                </label>
                                <input id="medical_certificate_file" name="medical_certificate_file" type="file" accept=".jpg,.jpeg,.png,.pdf" :disabled="role !== 'candidate' || candidateDocType === 'pwd_id'"
                                       class="block w-full text-sm text-primary-dark file:mr-3 file:rounded-md file:border-0 file:bg-primary file:px-3 file:py-2 file:text-white">
                                @error('medical_certificate_file')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <label class="flex cursor-pointer items-start gap-2 text-sm text-primary-dark">
                            <input id="candidate_consent" type="checkbox" name="consent" value="1" {{ old('consent') ? 'checked' : '' }} class="mt-1 h-4 w-4" @change="updateState()">
                            <span>I consent to the collection of my disability information for matching purposes (RA 10173). <span class="text-[#B3261E]">*</span></span>
                        </label>
                        @error('consent')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                        <label class="flex cursor-pointer items-start gap-2 text-sm text-primary-dark">
                            <input id="candidate_terms" type="checkbox" name="terms" value="1" {{ old('terms') ? 'checked' : '' }} class="mt-1 h-4 w-4" @change="updateState()">
                            <span>I accept the Terms and Conditions. <span class="text-[#B3261E]">*</span></span>
                        </label>
                        @error('terms')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                        <div class="flex gap-3">
                            <button type="button" @click="prev()" class="flex h-[52px] flex-1 items-center justify-center rounded-lg border border-[#CBD5E1] text-base font-semibold text-primary-dark hover:bg-primary-tint">Back</button>
                            <button type="submit" :disabled="!canContinue" :class="canContinue ? 'bg-[#2A5D8F] hover:bg-[#224B73]' : 'bg-[#94A3B8] cursor-not-allowed'" class="flex h-[52px] flex-1 items-center justify-center gap-2 rounded-lg text-base font-semibold text-white disabled:opacity-60">
                                Create account
                            </button>
                        </div>
                    </fieldset>



                    <p class="text-center text-base text-body-muted">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-semibold text-primary hover:underline">Sign in</a>
                    </p>
                </form>
            </section>
        </div>
    </main>

    <x-site-footer />

    <script>
        (function () {
            const fields = document.querySelectorAll('input[name], select[name]');
            const validate = function (el) {
                if (el.disabled || el.closest('[disabled]')) {
                    const existing = el.parentElement.querySelector('.client-error');
                    if (existing) existing.remove();
                    return;
                }
                const name = el.name;
                const val = (el.value || '').trim();
                let msg = '';
                if (el.hasAttribute('required') && val === '') {
                    msg = 'This field is required.';
                } else if (name === 'email' && val && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                    msg = 'Please enter a valid email address.';
                } else if (name === 'password' && val && val.length < 8) {
                    msg = 'Password must be at least 8 characters.';
                } else if (name === 'password_confirmation' && val) {
                    const fieldset = el.closest('fieldset');
                    const pw = fieldset ? fieldset.querySelector('input[name="password"]') : document.querySelector('input[name="password"]');
                    if (pw && val !== pw.value) msg = 'Passwords do not match.';
                } else if (name === 'date_of_birth' && val) {
                    const parts = val.split('-').map(Number);
                    if (parts.length !== 3 || parts.some(isNaN)) msg = 'Please enter a valid date.';
                    else {
                        const now = new Date();
                        let age = now.getFullYear() - parts[0];
                        const m = (now.getMonth() + 1) - parts[1];
                        if (m < 0 || (m === 0 && now.getDate() < parts[2])) age--;
                        if (age < 18) msg = 'You must be at least 18 years old.';
                    }
                } else if (name === 'disability_type' && val === '') {
                    msg = 'Please select a disability type.';
                } else if ((name === 'pwd_id_number' || name === 'company_name' || (el.hasAttribute('required') && (name === 'first_name' || name === 'last_name'))) && val === '') {
                    msg = 'This field is required.';
                }
                const anchor = el.parentElement && el.parentElement.classList.contains('relative') ? el.parentElement : el;
                const container = anchor.parentElement || el.parentElement;
                const span = container.querySelector('.client-error');
                if (msg) {
                    if (!span) {
                        const p = document.createElement('p');
                        p.className = 'mt-1.5 text-sm text-[#FF0000] client-error';
                        p.textContent = msg;
                        anchor.insertAdjacentElement('afterend', p);
                    } else {
                        span.textContent = msg;
                    }
                    el.setAttribute('aria-invalid', 'true');
                } else if (span) {
                    span.remove();
                    el.setAttribute('aria-invalid', 'false');
                }
            };
            fields.forEach(function (el) {
                if (el.type === 'checkbox' || el.type === 'hidden') return;
                if (el.type === 'file') {
                    el.addEventListener('change', function () {
                        const f = el.files && el.files[0];
                        const span = el.parentElement.querySelector('.client-error');
                        if (f && f.size > 5 * 1024 * 1024) {
                            if (!span) {
                                const p = document.createElement('p');
                                p.className = 'mt-1.5 text-sm text-[#FF0000] client-error';
                                p.textContent = 'File must be 5 MB or smaller.';
                                el.insertAdjacentElement('afterend', p);
                            } else {
                                span.textContent = 'File must be 5 MB or smaller.';
                            }
                            el.value = '';
                        } else if (span) {
                            span.remove();
                        }
                    });
                    return;
                }
                let t;
                const schedule = function () { clearTimeout(t); t = setTimeout(function () { validate(el); }, 2000); };
                el.addEventListener('input', schedule);
                el.addEventListener('change', schedule);
                el.addEventListener('blur', function () { clearTimeout(t); validate(el); });
            });
        })();
    </script>
</x-layouts::auth.full>