<div class="space-y-6">
    <!-- Header Banner -->
    <div class="relative overflow-hidden rounded-2xl border border-primary-border bg-white p-6 sm:p-8 shadow-sm" style="background: linear-gradient(135deg, #DCEAF4 0%, #FFFFFF 100%); box-shadow: 0 4px 20px rgba(26,40,64,0.06);">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-2 max-w-2xl">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-primary-border bg-white px-3 py-1 text-xs font-bold text-secondary shadow-xs">
                    <svg class="size-3.5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    {{ __('Inclusive Language Enforcement') }}
                </span>

                <h1 class="font-heading text-2xl sm:text-3xl font-bold text-primary-dark">
                    {{ __('Exclusionary Language Lexicon') }}
                </h1>

                <p class="text-sm sm:text-base text-body-muted leading-relaxed">
                    {{ __('Terms flagged by the real-time inclusive job scanner to prevent discriminatory phrasing and promote accessible terminology.') }}
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-primary-border bg-white px-4 py-2.5 text-sm font-semibold text-primary-dark shadow-xs transition hover:bg-primary-tint"
                >
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('Back to Dashboard') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Add / Edit Form -->
    <form wire:submit="save" class="rounded-2xl border border-primary-border bg-white p-6 shadow-sm space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <h2 class="font-heading text-lg font-bold text-primary-dark">
                {{ $editingId ? __('Edit Lexicon Term') : __('Register New Term') }}
            </h2>
            @if ($editingId)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                    {{ __('Editing Mode') }}
                </span>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
            <div>
                <label for="lexicon-phrase" class="block text-xs font-bold uppercase tracking-wider text-body-muted mb-1.5">
                    {{ __('Flagged Phrase / Term') }} <span class="text-rose-600">*</span>
                </label>
                <input
                    id="lexicon-phrase"
                    type="text"
                    wire:model="phrase"
                    required
                    placeholder="{{ __('e.g. wheelchair bound') }}"
                    class="w-full rounded-xl border border-primary-border bg-surface px-3.5 py-2.5 text-sm text-primary-dark placeholder-gray-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary"
                />
                @error('phrase') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="lexicon-category" class="block text-xs font-bold uppercase tracking-wider text-body-muted mb-1.5">
                    {{ __('Category') }}
                </label>
                <input
                    id="lexicon-category"
                    type="text"
                    wire:model="category"
                    placeholder="{{ __('e.g. mobility, sensory, cognitive') }}"
                    class="w-full rounded-xl border border-primary-border bg-surface px-3.5 py-2.5 text-sm text-primary-dark placeholder-gray-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary"
                />
                @error('category') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="lexicon-suggestion" class="block text-xs font-bold uppercase tracking-wider text-body-muted mb-1.5">
                    {{ __('Recommended Alternative') }}
                </label>
                <input
                    id="lexicon-suggestion"
                    type="text"
                    wire:model="suggestion"
                    placeholder="{{ __('e.g. person who uses a wheelchair') }}"
                    class="w-full rounded-xl border border-primary-border bg-surface px-3.5 py-2.5 text-sm text-primary-dark placeholder-gray-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary"
                />
                @error('suggestion') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="flex items-center gap-3">
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" wire:model="isActive" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-secondary"></div>
                <span class="ml-3 text-sm font-semibold text-primary-dark">{{ __('Active Rule (scanned on job creation)') }}</span>
            </label>
        </div>

        <div class="flex flex-wrap items-center gap-3 pt-2">
            <button
                type="submit"
                class="inline-flex items-center gap-2 rounded-lg bg-primary-dark px-5 py-2.5 min-h-[44px] text-sm font-semibold text-white shadow-xs transition hover:bg-primary focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
            >
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ $editingId ? __('Update Term') : __('Add Term') }}
            </button>
            @if ($editingId)
                <button
                    type="button"
                    wire:click="resetForm"
                    class="inline-flex items-center rounded-lg border border-primary-border bg-white px-5 py-2.5 min-h-[44px] text-sm font-semibold text-primary-dark shadow-xs transition hover:bg-primary-tint"
                >
                    {{ __('Cancel') }}
                </button>
            @endif
        </div>
    </form>

    <!-- Terms List Table -->
    <div class="overflow-hidden rounded-2xl border border-primary-border bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <caption class="sr-only">{{ __('Exclusionary language terms list') }}</caption>
                <thead>
                    <tr class="border-b border-primary-border bg-surface/60 text-left text-xs uppercase tracking-wider text-body-muted">
                        <th scope="col" class="px-6 py-3.5 font-bold">{{ __('Flagged Phrase') }}</th>
                        <th scope="col" class="px-6 py-3.5 font-bold">{{ __('Category') }}</th>
                        <th scope="col" class="px-6 py-3.5 font-bold">{{ __('Inclusive Suggestion') }}</th>
                        <th scope="col" class="px-6 py-3.5 font-bold">{{ __('Status') }}</th>
                        <th scope="col" class="px-6 py-3.5 text-right font-bold">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-border/60">
                    @forelse ($terms as $term)
                        <tr class="transition hover:bg-surface/50">
                            <td class="px-6 py-4 font-semibold text-primary-dark font-mono text-sm">{{ $term->phrase }}</td>
                            <td class="px-6 py-4 text-body-muted">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700">
                                    {{ $term->category ?? 'General' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-body-muted">{{ $term->suggestion ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $term->is_active ? 'bg-secondary-tint text-secondary border border-emerald-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $term->is_active ? 'bg-secondary' : 'bg-gray-400' }}"></span>
                                    {{ $term->is_active ? __('Active') : __('Inactive') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex flex-wrap items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="edit({{ $term->id }})"
                                        class="inline-flex items-center gap-1 rounded-lg border border-primary-border bg-white px-3 py-1.5 text-xs font-semibold text-primary-dark shadow-2xs transition hover:bg-primary-tint"
                                    >
                                        <svg class="size-3 text-body-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        {{ __('Edit') }}
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="toggle({{ $term->id }})"
                                        class="inline-flex items-center rounded-lg border border-primary-border bg-white px-3 py-1.5 text-xs font-semibold text-primary-dark shadow-2xs transition hover:bg-primary-tint"
                                    >
                                        {{ $term->is_active ? __('Deactivate') : __('Activate') }}
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="delete({{ $term->id }})"
                                        wire:confirm="{{ __('Are you sure you want to delete this term?') }}"
                                        class="inline-flex items-center rounded-lg bg-rose-50 border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-100"
                                    >
                                        {{ __('Delete') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-body-muted">
                                {{ __('No terms found in the lexicon.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-primary-border">
            {{ $terms->links() }}
        </div>
    </div>
</div>
