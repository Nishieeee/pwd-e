<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-primary-dark">{{ __('Audit Log') }}</h1>
            <p class="text-sm text-body-muted">{{ __('Activity recorded across the platform.') }}</p>
        </div>
        <button type="button" wire:click="resetFilters" class="rounded-lg border border-primary-border bg-white px-4 py-2 text-sm font-semibold text-primary-dark hover:bg-primary-tint">
            {{ __('Reset filters') }}
        </button>
    </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-4">
            <flux:input wire:model.live.debounce.300ms="userFilter" :placeholder="__('User name or email')" />
            <flux:input wire:model.live.debounce.300ms="actionFilter" :placeholder="__('Action or description')" />
            <flux:input wire:model.live="fromDate" type="date" :label="__('From')" />
            <flux:input wire:model.live="toDate" type="date" :label="__('To')" />
        </div>

        <div class="rounded-2xl border border-primary-border bg-white shadow-sm">
            <table class="w-full text-sm">
                <caption class="sr-only">{{ __('Activity log') }}</caption>
                <thead>
                    <tr class="border-b border-primary-border text-left text-xs uppercase tracking-wide text-body-muted">
                        <th scope="col" class="px-4 py-3">{{ __('When') }}</th>
                        <th scope="col" class="px-4 py-3">{{ __('Causer') }}</th>
                        <th scope="col" class="px-4 py-3">{{ __('Subject') }}</th>
                        <th scope="col" class="px-4 py-3">{{ __('Event') }}</th>
                        <th scope="col" class="px-4 py-3">{{ __('Description') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-border/60">
                    @forelse ($activities as $activity)
                        <tr>
                            <td class="px-4 py-3 text-body-muted">{{ $activity->created_at?->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3 font-semibold text-primary-dark">{{ $activity->causer?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-body-muted">{{ $activity->subject_type ? class_basename($activity->subject_type).' #'.$activity->subject_id : '—' }}</td>
                            <td class="px-4 py-3">{{ $activity->event ?? '—' }}</td>
                            <td class="px-4 py-3 text-body-muted">{{ $activity->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-body-muted">{{ __('No activity recorded yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="px-4 py-3">
                {{ $activities->links() }}
            </div>
        </div>
    </div>
</div>
