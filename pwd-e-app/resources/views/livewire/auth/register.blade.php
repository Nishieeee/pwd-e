<x-layouts::auth.full :title="__('Register')">
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
            </div>
        </div>
    </header>

    <x-accessibility-panel />

    <main class="mx-auto max-w-[1200px] px-4 py-6 sm:px-6">
        <a href="{{ route('home') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-primary hover:underline">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Back to Home
        </a>

        <div class="flex flex-col-reverse overflow-hidden rounded-2xl bg-white shadow-[0_8px_24px_rgba(26,40,64,0.06)] md:grid md:grid-cols-2">
            <section class="bg-gradient-to-b from-primary-tint to-[#D6E7F3] p-5 md:p-10">
                <span class="inline-flex items-center gap-1 rounded-full border border-primary-border bg-white px-3.5 py-2 text-xs font-semibold text-primary-dark">
                    Accessibility-first employment
                </span>
                <h1 class="mt-4 font-heading text-3xl font-extrabold leading-tight text-primary-dark md:text-[40px]">
                    Create your inclusive account.
                </h1>
                <p class="mt-3 max-w-[46ch] text-base text-body-muted">
                    Join PWD-E as a job seeker or hiring partner. Your privacy is protected under RA 10173.
                </p>
            </section>

            <section class="p-5 md:p-8 lg:p-16" x-data="{ role: '{{ old('role', 'candidate') }}' }">
                <p class="text-xs font-semibold uppercase tracking-[0.08em] text-primary">New Account</p>
                <h2 class="mt-2 font-heading text-3xl font-bold text-primary-dark md:text-[34px]">Register</h2>

                @if ($errors->any())
                    <div class="mt-4 rounded-lg border border-[#B3261E] bg-[#FDECEA] px-3 py-2 text-sm text-[#B3261E]" role="alert" aria-live="assertive">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register.store') }}" enctype="multipart/form-data" class="mt-6 flex flex-col gap-5" novalidate>
                    @csrf

                    <div>
                        <p class="mb-1.5 text-sm font-semibold text-primary-dark">I am registering as</p>
                        <div class="flex gap-3" role="radiogroup" aria-label="Registration role">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="role" value="candidate" x-model="role" class="sr-only" {{ old('role', 'candidate') === 'candidate' ? 'checked' : '' }}>
                                <span class="flex h-12 items-center justify-center rounded-lg border border-[#CBD5E1] text-sm font-semibold text-primary-dark" :class="role === 'candidate' ? 'bg-primary text-white border-primary' : 'bg-white'">Candidate (PWD)</span>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="role" value="employer" x-model="role" class="sr-only" {{ old('role') === 'employer' ? 'checked' : '' }}>
                                <span class="flex h-12 items-center justify-center rounded-lg border border-[#CBD5E1] text-sm font-semibold text-primary-dark" :class="role === 'employer' ? 'bg-primary text-white border-primary' : 'bg-white'">Employer</span>
                            </label>
                        </div>
                        @error('role')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="name" class="mb-1.5 block text-sm font-semibold text-primary-dark">Full name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Full name"
                               class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                        @error('name')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-semibold text-primary-dark">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" placeholder="you@example.com"
                               class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                        @error('email')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-semibold text-primary-dark">Password</label>
                        <input id="password" name="password" type="password" required autocomplete="new-password" placeholder="Password"
                               class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                        @error('password')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-1.5 block text-sm font-semibold text-primary-dark">Confirm password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" placeholder="Confirm password"
                               class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                    </div>

                    {{-- Candidate fields --}}
                    <div x-show="role === 'candidate'" x-cloak class="flex flex-col gap-5">
                        <div>
                            <label for="disability_type" class="mb-1.5 block text-sm font-semibold text-primary-dark">Disability type</label>
                            <select id="disability_type" name="disability_type" class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 text-base text-primary-dark focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                <option value="">Select…</option>
                                @foreach (['Visual','Hearing','Motor/Physical','Speech','Cerebral Palsy','Psychosocial','Intellectual','Multiple'] as $dt)
                                    <option value="{{ $dt }}" {{ old('disability_type') === $dt ? 'selected' : '' }}>{{ $dt }}</option>
                                @endforeach
                            </select>
                            @error('disability_type')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="pwd_id_number" class="mb-1.5 block text-sm font-semibold text-primary-dark">PWD ID number</label>
                            <input id="pwd_id_number" name="pwd_id_number" type="text" value="{{ old('pwd_id_number') }}" placeholder="PWD-123456"
                                   class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                            @error('pwd_id_number')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="pwd_id_file" class="mb-1.5 block text-sm font-semibold text-primary-dark">PWD ID document (optional)</label>
                            <input id="pwd_id_file" name="pwd_id_file" type="file" accept=".jpg,.jpeg,.png,.pdf"
                                   class="block w-full text-sm text-primary-dark file:mr-3 file:rounded-md file:border-0 file:bg-primary file:px-3 file:py-2 file:text-white">
                            @error('pwd_id_file')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                        </div>
                        <label class="flex cursor-pointer items-start gap-2 text-sm text-primary-dark">
                            <input type="checkbox" name="consent" value="1" {{ old('consent') ? 'checked' : '' }} class="mt-1 h-4 w-4">
                            <span>I consent to the collection of my disability information for matching purposes (RA 10173).</span>
                        </label>
                        @error('consent')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                    </div>

                    {{-- Employer fields --}}
                    <div x-show="role === 'employer'" x-cloak class="flex flex-col gap-5">
                        <div>
                            <label for="company_name" class="mb-1.5 block text-sm font-semibold text-primary-dark">Company name</label>
                            <input id="company_name" name="company_name" type="text" value="{{ old('company_name') }}" placeholder="Company Inc."
                                   class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                            @error('company_name')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="business_permit_number" class="mb-1.5 block text-sm font-semibold text-primary-dark">Business permit number</label>
                            <input id="business_permit_number" name="business_permit_number" type="text" value="{{ old('business_permit_number') }}" placeholder="BP-123456"
                                   class="h-12 w-full rounded-lg border border-[#CBD5E1] bg-white px-3.5 text-base text-primary-dark placeholder:text-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                            @error('business_permit_number')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="business_permit_file" class="mb-1.5 block text-sm font-semibold text-primary-dark">Business permit document (optional)</label>
                            <input id="business_permit_file" name="business_permit_file" type="file" accept=".jpg,.jpeg,.png,.pdf"
                                   class="block w-full text-sm text-primary-dark file:mr-3 file:rounded-md file:border-0 file:bg-primary file:px-3 file:py-2 file:text-white">
                            @error('business_permit_file')<p class="mt-1.5 text-sm text-[#B3261E]">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <button type="submit" class="flex h-[52px] w-full items-center justify-center gap-2 rounded-lg bg-[#2A5D8F] text-base font-semibold text-white transition hover:bg-[#224B73] focus:outline-none focus:ring-2 focus:ring-primary/40">
                        Create account
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>

                    <p class="text-center text-base text-body-muted">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-semibold text-primary hover:underline">Sign in</a>
                    </p>
                </form>
            </section>
        </div>
    </main>
</x-layouts::auth.full>