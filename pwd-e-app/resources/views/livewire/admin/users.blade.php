<div class="space-y-6">
    <!-- Header Banner -->
    <div class="relative overflow-hidden rounded-2xl border border-primary-border bg-white p-6 sm:p-8 shadow-sm" style="background: linear-gradient(135deg, #DCEAF4 0%, #FFFFFF 100%); box-shadow: 0 4px 20px rgba(26,40,64,0.06);">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-2 max-w-2xl">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-primary-border bg-white px-3 py-1 text-xs font-bold text-secondary shadow-xs">
                    <svg class="size-3.5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    {{ __('Account Governance') }}
                </span>

                <h1 class="font-heading text-2xl sm:text-3xl font-bold text-primary-dark">
                    {{ __('User Management') }}
                </h1>

                <p class="text-sm sm:text-base text-body-muted leading-relaxed">
                    {{ __('Search, filter, and moderate candidate, employer, and administrative accounts.') }}
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

    <!-- Search & Filter Controls -->
    <div class="rounded-2xl border border-primary-border bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="flex-1 relative">
                <label for="admin-search" class="sr-only">{{ __('Search users') }}</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input
                        id="admin-search"
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('Search by name or email address...') }}"
                        class="w-full rounded-xl border border-primary-border bg-surface py-2.5 pl-10 pr-4 text-sm text-primary-dark placeholder-gray-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary"
                    />
                </div>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <label for="admin-role-filter" class="text-xs font-semibold text-body-muted whitespace-nowrap">{{ __('Filter Role:') }}</label>
                <select
                    id="admin-role-filter"
                    wire:model.live="role"
                    class="rounded-xl border border-primary-border bg-surface px-3.5 py-2.5 text-sm font-medium text-primary-dark focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary min-w-[140px]"
                >
                    <option value="all">{{ __('All Roles') }}</option>
                    <option value="candidate">{{ __('Candidates') }}</option>
                    <option value="employer">{{ __('Employers') }}</option>
                    <option value="admin">{{ __('Administrators') }}</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="overflow-hidden rounded-2xl border border-primary-border bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <caption class="sr-only">{{ __('Platform registered users') }}</caption>
                <thead>
                    <tr class="border-b border-primary-border bg-surface/60 text-left text-xs uppercase tracking-wider text-body-muted">
                        <th scope="col" class="px-6 py-3.5 font-bold">{{ __('User') }}</th>
                        <th scope="col" class="px-6 py-3.5 font-bold">{{ __('Email') }}</th>
                        <th scope="col" class="px-6 py-3.5 font-bold">{{ __('Role') }}</th>
                        <th scope="col" class="px-6 py-3.5 font-bold">{{ __('Account Status') }}</th>
                        <th scope="col" class="px-6 py-3.5 text-right font-bold">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-border/60">
                    @forelse ($users as $user)
                        <tr class="transition hover:bg-surface/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-[#244F73] text-white flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div class="font-semibold text-primary-dark">{{ $user->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-body-muted font-mono text-xs">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : ($user->role === 'employer' ? 'bg-indigo-100 text-indigo-800' : 'bg-emerald-100 text-emerald-800') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold
                                    {{ $user->status === 'active' ? 'bg-secondary-tint text-secondary border border-emerald-200' : ($user->status === 'suspended' ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-primary-tint text-primary-dark') }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $user->status === 'active' ? 'bg-secondary' : ($user->status === 'suspended' ? 'bg-red-600' : 'bg-primary') }}"></span>
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if (! $user->isAdmin())
                                    @if ($user->status === 'suspended')
                                        <button
                                            type="button"
                                            wire:click="reinstate({{ $user->id }})"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-primary-border bg-white px-3.5 py-1.5 text-xs font-semibold text-primary-dark shadow-2xs transition hover:bg-primary-tint focus:outline-none focus:ring-2 focus:ring-primary"
                                        >
                                            <svg class="size-3.5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            {{ __('Reinstate') }}
                                        </button>
                                    @else
                                        <button
                                            type="button"
                                            wire:click="suspend({{ $user->id }})"
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-rose-600 px-3.5 py-1.5 text-xs font-semibold text-white shadow-2xs transition hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600"
                                        >
                                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            {{ __('Suspend') }}
                                        </button>
                                    @endif
                                @else
                                    <span class="text-xs text-body-muted italic">{{ __('Protected') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-body-muted">
                                {{ __('No users found matching your criteria.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-primary-border">
            {{ $users->links() }}
        </div>
    </div>
</div>
