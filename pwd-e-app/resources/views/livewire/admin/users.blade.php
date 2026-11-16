<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-heading text-2xl font-bold text-primary-dark">{{ __('User Management') }}</h1>
                <p class="text-sm text-body-muted">{{ __('Suspend or reinstate accounts.') }}</p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <flux:input wire:model.live.debounce.300ms="search" :placeholder="__('Search name or email...')" class="sm:w-64" />

                <flux:select wire:model.live="role" class="sm:w-40" :label="__('Role')">
                    <option value="all">{{ __('All roles') }}</option>
                    <option value="candidate">{{ __('Candidates') }}</option>
                    <option value="employer">{{ __('Employers') }}</option>
                    <option value="admin">{{ __('Admins') }}</option>
                </flux:select>
            </div>
        </div>

        <div class="rounded-2xl border border-primary-border bg-white shadow-sm">
            <table class="w-full text-sm">
                <caption class="sr-only">{{ __('Platform users') }}</caption>
                <thead>
                    <tr class="border-b border-primary-border text-left text-xs uppercase tracking-wide text-body-muted">
                        <th scope="col" class="px-4 py-3">{{ __('Name') }}</th>
                        <th scope="col" class="px-4 py-3">{{ __('Email') }}</th>
                        <th scope="col" class="px-4 py-3">{{ __('Role') }}</th>
                        <th scope="col" class="px-4 py-3">{{ __('Status') }}</th>
                        <th scope="col" class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-border/60">
                    @forelse ($users as $user)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-primary-dark">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-body-muted">{{ $user->email }}</td>
                            <td class="px-4 py-3">{{ ucfirst($user->role) }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold
                                    {{ $user->status === 'active' ? 'bg-secondary-tint text-secondary' : ($user->status === 'suspended' ? 'bg-red-100 text-red-700' : 'bg-primary-tint text-primary-dark') }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if (! $user->isAdmin())
                                    @if ($user->status === 'suspended')
                                        <button type="button" wire:click="reinstate({{ $user->id }})" class="rounded-lg border border-primary-border bg-white px-3 py-2 text-xs font-semibold text-primary-dark hover:bg-primary-tint">
                                            {{ __('Reinstate') }}
                                        </button>
                                    @else
                                        <button type="button" wire:click="suspend({{ $user->id }})" class="rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700">
                                            {{ __('Suspend') }}
                                        </button>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-body-muted">{{ __('No users found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        <div class="px-4 py-3">
            {{ $users->links() }}
        </div>
    </div>
</div>
