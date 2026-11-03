<x-layouts::auth.full :title="__('Log in')">
    {{-- Navbar --}}
    <header class="bg-primary-dark">
        <div class="mx-auto flex max-w-[1200px] items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-white font-heading font-bold text-lg">
                <span class="grid h-9 w-9 place-items-center rounded-full bg-primary text-white">P</span>
                PWD-E
            </a>
            <nav class="hidden items-center gap-6 text-sm font-medium text-white md:flex">
                <a href="{{ route('home') }}" class="hover:underline">Home</a>
                <a href="{{ route('jobs') }}" class="hover:underline">Jobs</a>
                <a href="{{ route('employers') }}" class="hover:underline">Employers</a>
                <a href="{{ route('about') }}" class="hover:underline">About</a>
            </nav>
            <div class="flex items-center gap-2">
                <a href="{{ route('login') }}" class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-primary-dark">Sign In</a>
                <a href="{{ route('register') }}" class="hidden rounded-lg bg-white px-4 py-2 text-sm font-semibold text-primary-dark sm:inline-block">Register</a>
                <button type="button" class="grid h-11 w-11 place-items-center rounded-lg text-white md:hidden" aria-label="Open menu">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
    </header>

    <x-accessibility-panel />

    <main class="mx-auto max-w-[1200px] px-4 py-6 sm:px-6">
        <a href="{{ route('home') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-primary hover:underline">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Back to Home
        </a>

        <div class="flex flex-col-reverse overflow-hidden rounded-2xl bg-white shadow-[0_8px_24px_rgba(26,40,64,0.06)] md:grid md:grid-cols-2">
            {{-- Left promo panel --}}
            <section class="bg-gradient-to-b from-primary-tint to-[#D6E7F3] p-5 md:p-10">
                <span class="inline-flex items-center gap-1 rounded-full border border-primary-border bg-white px-3.5 py-2 text-xs font-semibold text-primary-dark">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="4" r="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0l-3 6m3-6l3 6M9 9h6"/></svg> Accessibility-first employment
                </span>
                <h1 class="mt-4 font-heading text-3xl font-extrabold leading-tight text-primary-dark md:text-[40px]">
                    Your next opportunity belongs here.
                </h1>
                <p class="mt-3 max-w-[46ch] text-base text-body-muted">
                    Connecting Persons with Disabilities to Accessible &amp; Inclusive Workplaces.
                </p>

                <div class="mt-6 hidden md:block">
                    <img src="{{ asset('assets/Inclusive workplace illustration.png') }}"
                         alt="" aria-hidden="true" loading="eager" decoding="async"
                         width="370" height="165" class="mx-auto w-[70%] max-w-[340px]">
                </div>

                <div class="mt-6 space-y-5">
                    <div class="flex gap-3">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-primary-border bg-white text-primary-dark" aria-hidden="true">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </span>
                        <div>
                            <p class="font-semibold text-primary-dark">Opportunities without barriers</p>
                            <p class="text-sm text-body-muted">Discover roles with verified workplace accommodations.</p>
                        </div>
                    </div>
                    <hr class="border-primary-border">
                    <div class="flex gap-3">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-primary-border bg-white text-primary-dark" aria-hidden="true">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </span>
                        <div>
                            <p class="font-semibold text-primary-dark">Inclusive employers you can trust</p>
                            <p class="text-sm text-body-muted">Connect with PESO &amp; DOLE-accredited organizations.</p>
                        </div>
                    </div>
                </div>

                <p class="mt-6 flex items-center gap-2 text-sm font-semibold text-primary-dark">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg> Your privacy is protected under RA 10173.
                </p>
            </section>

            {{-- Right form column --}}
            <section class="p-5 md:p-8 lg:p-16">
                <p class="text-xs font-semibold uppercase tracking-[0.08em] text-primary">Your PWD-E Account</p>
                <h2 class="mt-2 font-heading text-3xl font-bold text-primary-dark md:text-[34px]">Welcome back</h2>
                <p class="mt-2 text-base text-body-muted">Sign in to continue your journey toward barrier-free employment.</p>

                @if (session('status'))
                    <div class="mt-4 rounded-lg border border-primary-border bg-primary-tint px-3 py-2 text-sm text-primary-dark" role="status" aria-live="polite">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mt-4 rounded-lg border border-[#B3261E] bg-[#FDECEA] px-3 py-2 text-sm text-[#B3261E]" role="alert" aria-live="assertive">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="mt-6 flex flex-col gap-5" novalidate>
                    @csrf

                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-semibold text-primary-dark">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                               autocomplete="email" inputmode="email" placeholder="you@example.com"
                               aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                               aria-describedby="{{ $errors->has('email') ? 'email-error' : '' }}"
                               class="h-12 w-full rounded-lg border {{ $errors->has('email') ? 'border-[#B3261E]' : 'border-[#CBD5E1]' }} bg-white px-3.5 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                        <p id="email-live-error" class="mt-1.5 hidden items-center gap-1 text-sm text-[#B3261E]">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                            <span id="email-live-msg">Please enter a valid email address.</span>
                        </p>
                        @error('email')
                            <p id="email-error" class="mt-1.5 flex items-center gap-1 text-sm text-[#B3261E]">
                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-semibold text-primary-dark">Password</label>
                        <div class="relative">
                            <input id="password" name="password" type="password" required
                                   autocomplete="current-password" placeholder="Enter your password"
                                   aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                                   aria-describedby="{{ $errors->has('password') ? 'password-error' : '' }}"
                                   class="h-12 w-full rounded-lg border {{ $errors->has('password') ? 'border-[#B3261E]' : 'border-[#CBD5E1]' }} bg-white px-3.5 pr-12 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                            <button type="button" id="toggle-password" aria-label="Show password" aria-pressed="false"
                                    class="absolute right-1 top-1 grid h-10 w-10 place-items-center rounded-md text-body-muted hover:text-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/30">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                        @error('password')
                            <p id="password-error" class="mt-1.5 flex items-center gap-1 text-sm text-[#B3261E]">
                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex cursor-pointer items-center gap-2 text-sm text-primary-dark">
                            <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}
                                   class="peer sr-only">
                            <span class="grid h-5 w-5 place-items-center rounded border border-primary-border bg-white peer-checked:bg-primary-dark peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-primary/40">
                                <svg class="hidden h-3.5 w-3.5 peer-checked:block" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            Remember me
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm font-semibold text-primary hover:underline">Forgot password?</a>
                        @endif
                    </div>

                    <button type="submit" id="submit-btn"
                            class="flex h-[52px] w-full items-center justify-center gap-2 rounded-lg bg-[#2A5D8F] text-base font-semibold text-white transition hover:bg-[#224B73] focus:outline-none focus:ring-2 focus:ring-primary/40 disabled:opacity-60">
                        <span id="submit-label">Sign In</span>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        <svg id="submit-spinner" class="hidden h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                    </button>

                    <p class="text-center text-base text-body-muted">
                        New to PWD-E?
                        <a href="{{ route('register') }}" class="font-semibold text-primary hover:underline">Create an account</a>
                    </p>

                    <div class="flex gap-3 rounded-xl bg-primary-tint p-4">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-white text-primary-dark" aria-hidden="true">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-primary-dark">Hiring for an inclusive workplace?</p>
                            <p class="text-sm text-body-muted">Employers can sign in with their registered work email to manage jobs and connect with PWD talent.</p>
                        </div>
                    </div>
                </form>
            </section>
        </div>

        <p class="mt-6 text-center text-sm text-body-muted">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg> Need help with your account?
            <a href="mailto:support@pwd-e.local" class="font-semibold text-primary hover:underline">Contact support</a>
        </p>
    </main>

    <footer class="bg-[#20272F] px-4 py-8 text-white">
        <div class="mx-auto max-w-[1200px]">
            <div class="flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
                <div>
                    <p class="font-heading text-xl font-bold">PWD-E</p>
                    <p class="mt-1 text-sm text-white/70">Connecting talent to a barrier-free future.</p>
                </div>
                <nav class="flex flex-wrap gap-x-6 gap-y-2 text-sm">
                    <a href="#" class="hover:underline">Contact Us</a>
                    <a href="#" class="hover:underline">FAQ / Help Center</a>
                    <a href="#" class="hover:underline">Accessibility Statement</a>
                </nav>
            </div>
            <hr class="my-6 border-white/10">
            <div class="flex flex-col gap-2 text-xs text-white/60 sm:flex-row sm:items-center sm:justify-between">
                <span>© 2026 PWD-E. All rights reserved.</span>
                <div class="flex gap-4">
                    <a href="#" class="hover:underline">Privacy Policy (RA 10173)</a>
                    <a href="#" class="hover:underline">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        (function () {
            const emailInput = document.getElementById('email');
            const emailLiveError = document.getElementById('email-live-error');
            if (emailInput) {
                const validateEmail = function () {
                    const v = emailInput.value.trim();
                    const ok = v === '' || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
                    emailInput.setAttribute('aria-invalid', ok ? 'false' : 'true');
                    emailInput.classList.toggle('border-[#B3261E]', !ok && v !== '');
                    emailInput.classList.toggle('border-[#CBD5E1]', ok || v === '');
                    if (emailLiveError) {
                        if (!ok && v !== '') {
                            emailLiveError.classList.remove('hidden');
                            emailLiveError.classList.add('flex');
                        } else {
                            emailLiveError.classList.add('hidden');
                            emailLiveError.classList.remove('flex');
                        }
                    }
                };
                emailInput.addEventListener('input', validateEmail);
                emailInput.addEventListener('blur', validateEmail);
                emailInput.addEventListener('input', validateEmail);
                emailInput.addEventListener('blur', validateEmail);
            }
            const btn = document.getElementById('toggle-password');
            const input = document.getElementById('password');
            if (btn && input) {
                btn.addEventListener('click', function () {
                    const show = input.type === 'password';
                    input.type = show ? 'text' : 'password';
                    btn.setAttribute('aria-pressed', String(show));
                    btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                });
            }
            const form = document.querySelector('form[action*="login.store"]');
            const submitBtn = document.getElementById('submit-btn');
            const label = document.getElementById('submit-label');
            const spinner = document.getElementById('submit-spinner');
            if (form && submitBtn) {
                form.addEventListener('submit', function () {
                    submitBtn.disabled = true;
                    submitBtn.setAttribute('aria-busy', 'true');
                    if (label) label.textContent = 'Signing in…';
                    if (spinner) spinner.classList.remove('hidden');
                });
            }
        })();
    </script>
</x-layouts::auth.full>