<div class="space-y-6">
    <div>
        <h1 class="font-heading text-2xl font-bold text-primary-dark">{{ __('Exclusionary Language Lexicon') }}</h1>
        <p class="text-sm text-body-muted">{{ __('Terms flagged by the inclusive language scanner on job postings.') }}</p>
    </div>

        <!-- Add / Edit form -->
        <form wire:submit="save" class="rounded-2xl border border-primary-border bg-white p-6 shadow-sm space-y-4">
            <h2 class="font-heading text-lg font-bold text-primary-dark">
                {{ $editingId ? __('Edit Term') : __('Add Term') }}
            </h2>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <flux:input wire:model="phrase" :label="__('Phrase')" required />
                </div>
                <div>
                    <flux:input wire:model="category" :label="__('Category')" />
                </div>
                <div>
                    <flux:input wire:model="suggestion" :label="__('Inclusive suggestion')" />
                </div>
            </div>

            <div class="flex items-center gap-4">
                <flux:toggle wire:model="isActive" :label="__('Active')" />
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary-dark px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary">
                    {{ $editingId ? __('Update Term') : __('Add Term') }}
                </button>
                @if ($editingId)
                    <button type="button" wire:click="resetForm" class="rounded-lg border border-primary-border bg-white px-5 py-2.5 text-sm font-semibold text-primary-dark hover:bg-primary-tint">
                        {{ __('Cancel') }}
                    </button>
                @endif
            </div>
        </form>

        <!-- Terms list -->
        <div class="rounded-2xl border border-primary-border bg-white shadow-sm">
            <table class="w-full text-sm">
                <caption class="sr-only">{{ __('Exclusionary terms') }}</caption>
                <thead>
                    <tr class="border-b border-primary-border text-left text-xs uppercase tracking-wide text-body-muted">
                        <th scope="col" class="px-4 py-3">{{ __('Phrase') }}</th>
                        <th scope="col" class="px-4 py-3">{{ __('Category') }}</th>
                        <th scope="col" class="px-4 py-3">{{ __('Suggestion') }}</th>
                        <th scope="col" class="px-4 py-3">{{ __('Status') }}</th>
                        <th scope="col" class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-border/60">
                    @forelse ($terms as $term)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-primary-dark">{{ $term->phrase }}</td>
                            <td class="px-4 py-3 text-body-muted">{{ $term->category ?? '—' }}</td>
                            <td class="px-4 py-3 text-body-muted">{{ $term->suggestion ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $term->is_active ? 'bg-secondary-tint text-secondary' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $term->is_active ? __('Active') : __('Inactive') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex flex-wrap items-center gap-2">
                                    <button type="button" wire:click="edit({{ $term->id }})" class="rounded-lg border border-primary-border bg-white px-3 py-2 text-xs font-semibold text-primary-dark hover:bg-primary-tint">
                                        {{ __('Edit') }}
                                    </button>
                                    <button type="button" wire:click="toggle({{ $term->id }})" class="rounded-lg border border-primary-border bg-white px-3 py-2 text-xs font-semibold text-primary-dark hover:bg-primary-tint">
                                        {{ $term->is_active ? __('Deactivate') : __('Activate') }}
                                    </button>
                                    <button type="button" wire:click="delete({{ $term->id }})" wire:confirm="Delete this term?" class="rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700">
                                        {{ __('Delete') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-body-muted">{{ __('No terms yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        <div class="px-4 py-3">
            {{ $terms->links() }}
        </div>
    </div>
</div>
