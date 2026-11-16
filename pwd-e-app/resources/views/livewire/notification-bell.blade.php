<div class="relative">
    <flux:dropdown position="bottom" align="end">
        <button type="button" class="relative inline-flex items-center justify-center rounded-lg p-2 text-dark-neutral hover:bg-primary-tint" aria-label="{{ __('Notifications') }}">
            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            @if ($unreadCount > 0)
                <span class="absolute -top-0.5 -right-0.5 inline-flex min-h-[18px] min-w-[18px] items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold text-white">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
            @endif
        </button>

        <flux:menu class="w-80">
            <div class="flex items-center justify-between px-2 py-1.5">
                <span class="text-sm font-semibold text-primary-dark">{{ __('Notifications') }}</span>
                @if ($unreadCount > 0)
                    <button type="button" wire:click="markAllRead" class="text-xs font-semibold text-primary hover:underline">{{ __('Mark all read') }}</button>
                @endif
            </div>
            <flux:menu.separator />

            @forelse ($notifications as $notification)
                <flux:menu.item>
                    <div class="flex flex-col gap-0.5">
                        <span class="text-sm font-medium text-primary-dark">{{ $notification->data['status'] ?? $notification->data['job_title'] ?? __('Update') }}</span>
                        <span class="text-xs text-body-muted">{{ $notification->created_at?->diffForHumans() }}</span>
                    </div>
                </flux:menu.item>
            @empty
                <div class="px-3 py-4 text-center text-sm text-body-muted">{{ __('No notifications yet.') }}</div>
            @endforelse
        </flux:menu>
    </flux:dropdown>
</div>