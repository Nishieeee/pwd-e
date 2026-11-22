<div class="space-y-6">
    <!-- Hero Header matching Landing Page Design -->
    <div class="w-full relative overflow-hidden rounded-2xl border border-primary-border bg-white p-6 lg:p-7 shadow-sm" style="background: linear-gradient(135deg, #DCEAF4 0%, #FFFFFF 100%); box-shadow: 0 4px 20px rgba(26,40,64,0.06);">
        <div class="flex flex-col gap-4">
            <div>
                <a
                    href="{{ route('employer.jobs.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-primary hover:underline -ml-1"
                >
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('Back to Job Postings') }}
                </a>
            </div>

            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-primary-border bg-white px-3 py-1 text-xs font-bold text-secondary shadow-xs">
                    <svg class="size-3.5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    {{ __('Editing Opportunity') }}
                </span>
                <h1 class="font-heading text-2xl sm:text-3xl font-bold text-primary-dark">
                    {{ __('Edit Job Posting') }}
                </h1>
                <p class="text-sm sm:text-base text-body-muted max-w-2xl">
                    {{ __('Update your role requirements, workplace accommodations, and compensation details.') }}
                </p>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="saveChanges" class="space-y-6">
        <!-- 1. Role Overview Card -->
        <section class="rounded-2xl border border-primary-border border-t-4 border-t-primary bg-white p-6 sm:p-8 shadow-sm space-y-6" style="box-shadow: 0 4px 16px rgba(26,40,64,0.06);">
            <div class="border-b border-primary-border/60 pb-4">
                <h2 class="font-heading text-xl font-bold text-primary-dark">
                    {{ __('1. Role Overview') }}
                </h2>
                <p class="text-xs text-body-muted mt-1">
                    {{ __('Key title, work arrangement, location, and compensation parameters.') }}
                </p>
            </div>

            <!-- Job Title -->
            <div>
                <label for="job-title" class="block text-sm font-bold text-primary-dark mb-1.5">
                    {{ __('Job Title') }} <span class="text-rose-500">*</span>
                </label>
                <input
                    id="job-title"
                    type="text"
                    wire:model="title"
                    placeholder="{{ __('e.g. Accessibility Analyst, Customer Support Associate') }}"
                    required
                    class="w-full rounded-xl border border-primary-border bg-surface px-4 py-3 text-sm text-dark-neutral placeholder-body-muted focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition"
                />
                @error('title')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Employment Type & Work Arrangement -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="employment_type" class="block text-sm font-bold text-primary-dark mb-1.5">
                        {{ __('Employment Type') }} <span class="text-rose-500">*</span>
                    </label>
                    <select
                        id="employment_type"
                        wire:model="employment_type"
                        class="w-full rounded-xl border border-primary-border bg-surface px-4 py-3 text-sm text-dark-neutral focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition"
                    >
                        <option value="full_time">{{ __('Full-Time') }}</option>
                        <option value="part_time">{{ __('Part-Time') }}</option>
                        <option value="contract">{{ __('Contract / Project-Based') }}</option>
                    </select>
                    @error('employment_type')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="work_arrangement" class="block text-sm font-bold text-primary-dark mb-1.5">
                        {{ __('Work Arrangement') }} <span class="text-rose-500">*</span>
                    </label>
                    <select
                        id="work_arrangement"
                        wire:model.live="work_arrangement"
                        class="w-full rounded-xl border border-primary-border bg-surface px-4 py-3 text-sm text-dark-neutral focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition"
                    >
                        <option value="onsite">{{ __('On-Site (At Verified Facility)') }}</option>
                        <option value="remote">{{ __('Fully Remote (Work from Home)') }}</option>
                        <option value="hybrid">{{ __('Hybrid (On-Site & Work from Home)') }}</option>
                    </select>
                    @error('work_arrangement')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Location -->
            <div>
                <label for="job-location" class="block text-sm font-bold text-primary-dark mb-1.5">
                    {{ __('Work Location / City') }}
                    @if ($work_arrangement !== 'remote') <span class="text-rose-500">*</span> @endif
                </label>
                <input
                    id="job-location"
                    type="text"
                    wire:model="location"
                    placeholder="{{ $work_arrangement === 'remote' ? 'Optional for remote roles' : 'e.g. Makati City, Metro Manila' }}"
                    class="w-full rounded-xl border border-primary-border bg-surface px-4 py-3 text-sm text-dark-neutral placeholder-body-muted focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition"
                />
                @error('location')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Salary Range & Deadline -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                <div>
                    <label for="salary_min" class="block text-sm font-bold text-primary-dark mb-1.5">
                        {{ __('Minimum Salary (₱ / month)') }}
                    </label>
                    <input
                        id="salary_min"
                        type="number"
                        min="0"
                        step="500"
                        wire:model="salary_min"
                        placeholder="e.g. 30000"
                        class="w-full rounded-xl border border-primary-border bg-surface px-4 py-3 text-sm text-dark-neutral placeholder-body-muted focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition"
                    />
                    @error('salary_min')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="salary_max" class="block text-sm font-bold text-primary-dark mb-1.5">
                        {{ __('Maximum Salary (₱ / month)') }}
                    </label>
                    <input
                        id="salary_max"
                        type="number"
                        min="0"
                        step="500"
                        wire:model="salary_max"
                        placeholder="e.g. 45000"
                        class="w-full rounded-xl border border-primary-border bg-surface px-4 py-3 text-sm text-dark-neutral placeholder-body-muted focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition"
                    />
                    @error('salary_max')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="closes_at" class="block text-sm font-bold text-primary-dark mb-1.5">
                        {{ __('Application Closing Date') }}
                    </label>
                    <input
                        id="closes_at"
                        type="date"
                        wire:model="closes_at"
                        class="w-full rounded-xl border border-primary-border bg-surface px-4 py-3 text-sm text-dark-neutral focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition"
                    />
                    @error('closes_at')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        <!-- 2. Description & Required Skills Card -->
        <section class="rounded-2xl border border-primary-border border-t-4 border-t-secondary bg-white p-6 sm:p-8 shadow-sm space-y-6" style="box-shadow: 0 4px 16px rgba(26,40,64,0.06);">
            <div class="border-b border-primary-border/60 pb-4">
                <h2 class="font-heading text-xl font-bold text-primary-dark">
                    {{ __('2. Job Description & Required Skills') }}
                </h2>
                <p class="text-xs text-body-muted mt-1">
                    {{ __('Keep descriptions clear and inclusive.') }}
                </p>
            </div>

            <!-- Description Textarea -->
            <div>
                <label for="job-desc" class="block text-sm font-bold text-primary-dark mb-1.5">
                    {{ __('Job Description & Scope of Work') }} <span class="text-rose-500">*</span>
                </label>
                <textarea
                    id="job-desc"
                    wire:model="description"
                    rows="7"
                    placeholder="{{ __('Describe role responsibilities, deliverables, team structure, and workplace culture...') }}"
                    class="w-full rounded-xl border border-primary-border bg-surface p-4 text-sm text-dark-neutral placeholder-body-muted focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition"
                ></textarea>
                <div class="mt-2 flex items-center justify-between text-xs text-body-muted">
                    <span>{{ __('Minimum 20 characters.') }}</span>
                    <span>{{ strlen($description) }} {{ __('characters') }}</span>
                </div>
                @error('description')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Required Skills Chips Input -->
            <div class="space-y-3">
                <label class="block text-sm font-bold text-primary-dark">
                    {{ __('Required Skills & Competencies') }}
                </label>

                <!-- Active Chips Container -->
                <div class="flex flex-wrap items-center gap-2 min-h-[38px] p-2.5 rounded-xl border border-primary-border bg-surface">
                    @forelse ($required_skills as $index => $skill)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-primary-tint px-3 py-1 text-xs font-bold text-primary-dark border border-primary-border">
                            {{ $skill }}
                            <button
                                type="button"
                                wire:click="removeSkill({{ $index }})"
                                class="text-primary hover:text-rose-600 transition"
                                aria-label="{{ __('Remove') }} {{ $skill }}"
                            >
                                &times;
                            </button>
                        </span>
                    @empty
                        <span class="text-xs text-body-muted">{{ __('No skills added yet.') }}</span>
                    @endforelse
                </div>

                <!-- Input to add skill -->
                <div class="flex items-center gap-2 max-w-md">
                    <input
                        type="text"
                        wire:model="newSkill"
                        wire:keydown.enter.prevent="addSkill"
                        placeholder="{{ __('Type a skill and press Enter') }}"
                        class="flex-1 rounded-xl border border-primary-border bg-surface px-4 py-2.5 text-sm text-dark-neutral placeholder-body-muted focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition"
                    />
                    <button
                        type="button"
                        wire:click="addSkill"
                        class="rounded-xl border border-primary-border bg-white px-4 py-2.5 text-xs font-bold text-primary-dark hover:bg-primary-tint transition shadow-xs"
                    >
                        {{ __('Add Skill') }}
                    </button>
                </div>
            </div>
        </section>

        <!-- 3. Accommodations Taxonomy Checklist (POST-06) -->
        <section class="rounded-2xl border border-primary-border border-t-4 border-t-primary-dark bg-white p-6 sm:p-8 shadow-sm space-y-6" style="box-shadow: 0 4px 16px rgba(26,40,64,0.06);">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-primary-border/60 pb-4">
                <div>
                    <h2 class="font-heading text-xl font-bold text-primary-dark">
                        {{ __('3. Workplace Accessibility Accommodations') }}
                    </h2>
                    <p class="text-xs text-body-muted mt-1">
                        {{ __('Declared accommodations are matched with candidate accessibility preferences.') }}
                    </p>
                </div>
                <div class="rounded-full bg-secondary-tint px-3.5 py-1 text-xs font-bold text-secondary border border-secondary/20">
                    <span class="font-extrabold">{{ count($selectedAccommodations) }}</span> {{ __('accommodations checked') }}
                </div>
            </div>

            <!-- Categories Grid -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                @foreach ($accommodationsByCategory as $category => $items)
                    <div class="rounded-2xl border border-primary-border bg-surface/70 p-5 space-y-3.5">
                        <div class="flex items-center gap-2">
                            <span class="size-2 rounded-full bg-primary"></span>
                            <h3 class="font-heading text-sm font-bold text-primary-dark">
                                {{ $categoryLabels[$category] ?? ucfirst(str_replace('_', ' ', $category)) }}
                            </h3>
                        </div>

                        <div class="space-y-2.5">
                            @foreach ($items as $acc)
                                <label class="flex items-start gap-3 cursor-pointer text-xs font-medium text-dark-neutral hover:text-primary transition">
                                    <input
                                        type="checkbox"
                                        wire:model="selectedAccommodations"
                                        value="{{ $acc->id }}"
                                        class="mt-0.5 size-4 rounded border-primary-border text-primary focus:ring-primary"
                                    />
                                    <span>{{ $acc->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Form Submission Footer Actions -->
        <div class="flex flex-col-reverse gap-4 sm:flex-row sm:items-center sm:justify-between pt-2">
            <a
                href="{{ route('employer.jobs.index') }}"
                class="inline-flex items-center justify-center rounded-xl border border-primary-border bg-white px-6 py-3 text-sm font-semibold text-body-muted hover:bg-surface hover:text-primary-dark transition"
            >
                {{ __('Cancel') }}
            </a>

            <div class="flex items-center gap-3">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-dark px-7 py-3.5 text-sm font-bold text-white shadow-sm hover:bg-primary transition min-h-[48px]"
                >
                    <svg class="size-4.5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    {{ __('Save Changes') }}
                </button>
            </div>
        </div>
    </form>

    <!-- Inclusive Language Review Modal (POST-02, POST-03) -->
    @if ($showScanModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-primary-dark/60 p-4 backdrop-blur-sm">
            <div class="w-full max-w-xl rounded-2xl border border-primary-border bg-white p-6 sm:p-8 shadow-2xl space-y-6 animate-in fade-in zoom-in-95 duration-150">
                <div class="flex items-start gap-4">
                    <div class="size-12 shrink-0 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600">
                        <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <span class="inline-block rounded-full bg-amber-100 text-amber-800 text-[11px] font-bold px-2.5 py-0.5 border border-amber-300">
                            {{ __('Inclusive Language Check') }}
                        </span>
                        <h3 class="font-heading text-xl font-bold text-primary-dark mt-1">
                            {{ __('Recommended Inclusive Rewording') }}
                        </h3>
                        <p class="text-xs text-body-muted mt-1 leading-relaxed">
                            {{ __('Replacing these terms ensures your posting complies with RA 10524 and fosters a welcoming environment.') }}
                        </p>
                    </div>
                </div>

                <div class="space-y-3 max-h-72 overflow-y-auto pr-1">
                    @foreach ($scanFlags as $flag)
                        <div class="flex flex-col gap-3 rounded-2xl border border-primary-border bg-surface p-4 text-xs sm:flex-row sm:items-center sm:justify-between">
                            <div class="space-y-1.5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-lg bg-rose-100 px-2.5 py-1 font-bold text-rose-700 line-through">
                                        {{ $flag['phrase'] }}
                                    </span>
                                    <span class="text-body-muted">&rarr;</span>
                                    <span class="rounded-lg bg-secondary-tint px-2.5 py-1 font-bold text-secondary border border-secondary/30">
                                        {{ $flag['suggestion'] }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-body-muted">
                                    {{ __('Category:') }} <span class="capitalize font-semibold text-primary-dark">{{ $flag['category'] ?? 'Inclusive Tone' }}</span>
                                    &bull; {{ $flag['occurrences'] }} {{ Str::plural('mention', $flag['occurrences']) }}
                                </p>
                            </div>

                            <button
                                type="button"
                                wire:click="applyReplacement('{{ $flag['phrase'] }}', '{{ $flag['suggestion'] }}')"
                                class="shrink-0 rounded-xl bg-secondary px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-secondary/90 transition"
                            >
                                {{ __('Replace') }}
                            </button>
                        </div>
                    @endforeach
                </div>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between pt-3 border-t border-primary-border/60">
                    <button
                        type="button"
                        wire:click="dismissAndSave"
                        class="text-xs font-semibold text-body-muted hover:text-primary-dark underline"
                    >
                        {{ __('Dismiss & Save Anyway') }}
                    </button>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            wire:click="$set('showScanModal', false)"
                            class="rounded-xl border border-primary-border bg-white px-4 py-2.5 text-xs font-bold text-body-muted hover:bg-surface transition"
                        >
                            {{ __('Keep Editing') }}
                        </button>

                        <button
                            type="button"
                            wire:click="applyAllReplacements"
                            class="rounded-xl bg-primary-dark px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-primary transition"
                        >
                            {{ __('Apply All & Save') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
