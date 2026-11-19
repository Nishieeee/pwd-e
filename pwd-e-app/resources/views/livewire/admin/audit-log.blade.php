<div class="space-y-6">
    <!-- Header Banner -->
    <div class="relative overflow-hidden rounded-2xl border border-primary-border bg-white p-6 sm:p-8 shadow-sm" style="background: linear-gradient(135deg, #DCEAF4 0%, #FFFFFF 100%); box-shadow: 0 4px 20px rgba(26,40,64,0.06);">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-2 max-w-2xl">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-primary-border bg-white px-3 py-1 text-xs font-bold text-secondary shadow-xs">
                    <svg class="size-3.5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    {{ __('Compliance & Security Audit Trail') }}
                </span>

                <h1 class="font-heading text-2xl sm:text-3xl font-bold text-primary-dark">
                    {{ __('Platform Audit Log') }}
                </h1>

                <p class="text-sm sm:text-base text-body-muted leading-relaxed">
                    {{ __('Immutable event history tracking authentication, profile changes, and administrative moderation across the ecosystem.') }}
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

    <!-- Filters Bar -->
    <div class="rounded-2xl border border-primary-border bg-white p-5 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xs font-bold uppercase tracking-wider text-body-muted">{{ __('Filter Audit Trail') }}</h2>
            <button
                type="button"
                wire:click="resetFilters"
                class="inline-flex items-center gap-1.5 rounded-lg border border-primary-border bg-white px-3 py-1.5 text-xs font-semibold text-primary-dark hover:bg-primary-tint transition"
            >
                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                {{ __('Reset Filters') }}
            </button>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label for="audit-user-filter" class="block text-xs font-semibold text-body-muted mb-1">{{ __('User / Actor') }}</label>
                <input
                    id="audit-user-filter"
                    type="text"
                    wire:model.live.debounce.300ms="userFilter"
                    placeholder="{{ __('Search user name or email...') }}"
                    class="w-full rounded-xl border border-primary-border bg-surface px-3 py-2 text-sm text-primary-dark placeholder-gray-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary"
                />
            </div>

            <div>
                <label for="audit-action-filter" class="block text-xs font-semibold text-body-muted mb-1">{{ __('Action / Keyword') }}</label>
                <input
                    id="audit-action-filter"
                    type="text"
                    wire:model.live.debounce.300ms="actionFilter"
                    placeholder="{{ __('Action or description...') }}"
                    class="w-full rounded-xl border border-primary-border bg-surface px-3 py-2 text-sm text-primary-dark placeholder-gray-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary"
                />
            </div>

            <div>
                <label for="audit-from-date" class="block text-xs font-semibold text-body-muted mb-1">{{ __('From Date') }}</label>
                <input
                    id="audit-from-date"
                    type="date"
                    wire:model.live="fromDate"
                    class="w-full rounded-xl border border-primary-border bg-surface px-3 py-2 text-sm text-primary-dark focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary"
                />
            </div>

            <div>
                <label for="audit-to-date" class="block text-xs font-semibold text-body-muted mb-1">{{ __('To Date') }}</label>
                <input
                    id="audit-to-date"
                    type="date"
                    wire:model.live="toDate"
                    class="w-full rounded-xl border border-primary-border bg-surface px-3 py-2 text-sm text-primary-dark focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary"
                />
            </div>
        </div>
    </div>

    <!-- Audit Table -->
    <div class="overflow-hidden rounded-2xl border border-primary-border bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <caption class="sr-only">{{ __('Platform audit log records') }}</caption>
                <thead>
                    <tr class="border-b border-primary-border bg-surface/60 text-left text-xs uppercase tracking-wider text-body-muted">
                        <th scope="col" class="px-6 py-3.5 font-bold">{{ __('Timestamp') }}</th>
                        <th scope="col" class="px-6 py-3.5 font-bold">{{ __('Actor (Causer)') }}</th>
                        <th scope="col" class="px-6 py-3.5 font-bold">{{ __('Subject Record') }}</th>
                        <th scope="col" class="px-6 py-3.5 font-bold">{{ __('Event') }}</th>
                        <th scope="col" class="px-6 py-3.5 font-bold">{{ __('Description') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-border/60">
                    @forelse ($activities as $activity)
                        <tr class="transition hover:bg-surface/50">
                            <td class="px-6 py-4 text-xs text-body-muted whitespace-nowrap font-mono">
                                {{ $activity->created_at?->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-[#244F73] text-white flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($activity->causer?->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div class="font-semibold text-primary-dark text-xs sm:text-sm">
                                        {{ $activity->causer?->name ?? 'System' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs text-body-muted font-mono">
                                {{ $activity->subject_type ? class_basename($activity->subject_type).' #'.$activity->subject_id : '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-primary-tint text-primary-dark">
                                    {{ $activity->event ?? 'event' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-body-muted text-xs sm:text-sm leading-relaxed">
                                {{ $activity->description }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-body-muted">
                                {{ __('No activity recorded matching your filters.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-primary-border">
            {{ $activities->links() }}
        </div>
    </div>
</div>
