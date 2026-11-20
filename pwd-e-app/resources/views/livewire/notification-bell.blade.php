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
                @php
                    $type = $notification->data['type'] ?? '';
                    $isInvite = $type === 'job_invitation';
                    $isWithdrawn = $type === 'job_invitation_withdrawn';
                    $actionUrl = $notification->data['action_url'] ?? null;
                @endphp
                <flux:menu.item>
                    @if($isInvite && $actionUrl)
                        <a href="{{ $actionUrl }}" class="flex flex-col gap-1 w-full text-left">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-secondary">
                                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>{{ __('Job Invitation') }}</span>
                            </div>
                            <span class="text-sm font-semibold text-primary-dark">
                                {{ $notification->data['job_title'] ?? __('Job Position') }}
                            </span>
                            <span class="text-xs text-body-muted">
                                {{ $notification->data['employer_name'] ?? __('Verified Employer') }}
                            </span>
                            <div class="flex items-center justify-between pt-1 text-[11px]">
                                <span class="text-body-muted">{{ $notification->created_at?->diffForHumans() }}</span>
                                <span class="font-bold text-primary hover:underline">{{ __('Apply &rarr;') }}</span>
                            </div>
                        </a>
                    @elseif($isWithdrawn)
                        <div class="flex flex-col gap-1 w-full text-left">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-amber-700">
                                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>{{ __('Invitation Withdrawn') }}</span>
                            </div>
                            <span class="text-sm font-semibold text-primary-dark">
                                {{ $notification->data['job_title'] ?? __('Position') }}
                            </span>
                            <span class="text-xs text-body-muted">
                                {{ $notification->data['employer_name'] ?? __('Employer') }}: "{{ $notification->data['withdrawn_reason'] ?? 'Requisition updated' }}"
                            </span>
                            <span class="text-[11px] text-body-muted pt-0.5">{{ $notification->created_at?->diffForHumans() }}</span>
                        </div>
                    @elseif($type === 'accommodation_message' && $actionUrl)
                        <a href="{{ $actionUrl }}" class="flex flex-col gap-1 w-full text-left">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-sky-700">
                                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                <span>{{ __('Accommodation Inquiry') }}</span>
                            </div>
                            <span class="text-sm font-semibold text-primary-dark">
                                {{ $notification->data['job_title'] ?? __('Job Position') }}
                            </span>
                            <span class="text-xs text-body-muted">
                                {{ $notification->data['sender_name'] ?? __('User') }}: "{{ $notification->data['message_snippet'] ?? '' }}"
                            </span>
                            <div class="flex items-center justify-between pt-1 text-[11px]">
                                <span class="text-body-muted">{{ $notification->created_at?->diffForHumans() }}</span>
                                <span class="font-bold text-primary hover:underline">{{ __('View Message &rarr;') }}</span>
                            </div>
                        </a>
                    @elseif(($notification->data['status'] ?? '') === 'rejected')
                        <div class="flex flex-col gap-1 w-full text-left">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-red-600">
                                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>{{ __('Application Status') }}</span>
                            </div>
                            <span class="text-sm font-semibold text-primary-dark">
                                {{ $notification->data['job_title'] ?? __('Application') }}
                            </span>
                            <span class="text-xs text-body-muted">
                                {{ __('Status: Declined') }}
                                @if(!empty($notification->data['note']))
                                    — "{{ $notification->data['note'] }}"
                                @endif
                            </span>
                            <span class="text-[11px] text-body-muted pt-0.5">{{ $notification->created_at?->diffForHumans() }}</span>
                        </div>
                    @else
                        <div class="flex flex-col gap-0.5">
                            <span class="text-sm font-medium text-primary-dark">{{ $notification->data['status'] ?? $notification->data['job_title'] ?? __('Update') }}</span>
                            <span class="text-xs text-body-muted">{{ $notification->created_at?->diffForHumans() }}</span>
                        </div>
                    @endif
                </flux:menu.item>
            @empty
                <div class="px-3 py-4 text-center text-sm text-body-muted">{{ __('No notifications yet.') }}</div>
            @endforelse
        </flux:menu>
    </flux:dropdown>
</div>