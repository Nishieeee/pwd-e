<div class="inline-flex items-center shrink-0">
    @if ($user && in_array($user->role, ['candidate', 'employer'], true))
        <button
            type="button"
            wire:click="openModal"
            class="inline-flex items-center gap-[5px] rounded-[8px] border px-[9px] py-[5px] text-[12px] leading-[16px] font-semibold transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#244F73] focus-visible:ring-offset-1 cursor-pointer whitespace-nowrap {{ $hasLinkedTarget ? 'border-[#244F73]/30 bg-[#DCEAF4]/60 text-[#1A2840] hover:bg-[#DCEAF4]' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-100' }}"
            aria-label="{{ __('Switch role to :role portal', ['role' => $targetRoleLabel]) }}"
            title="{{ $hasLinkedTarget ? __('Switch to linked :role account (password required)', ['role' => $targetRoleLabel]) : __('Link a verified :role account in Settings to switch roles', ['role' => $targetRoleLabel]) }}"
        >
            <svg class="w-[14px] h-[14px] text-[#244F73] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
            <span class="hidden xl:inline">{{ __('Switch to') }}</span>
            <span>{{ $targetRoleLabel }}</span>
            @if ($hasLinkedTarget)
                <span class="w-[6px] h-[6px] rounded-full bg-emerald-600 shrink-0" aria-hidden="true"></span>
            @endif
        </button>

        @if ($showModal)
            @teleport('body')
                <div
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="role-switcher-modal-title"
                    @keydown.escape.window="$wire.closeModal()"
                >
                    <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 shadow-2xl space-y-4 text-[#25313B]" wire:click.stop>
                        <div class="flex items-start justify-between gap-3 border-b border-gray-100 pb-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-[#DCEAF4] text-[#244F73] flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 id="role-switcher-modal-title" class="font-heading text-base font-bold text-[#1A2840]">
                                        {{ __('Switch to :role Portal', ['role' => $targetRoleLabel]) }}
                                    </h2>
                                    <p class="text-xs text-gray-500">
                                        {{ __('Password-protected role switch') }}
                                    </p>
                                </div>
                            </div>

                            <button
                                type="button"
                                wire:click="closeModal"
                                class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#244F73] cursor-pointer"
                                aria-label="{{ __('Close role switch dialog') }}"
                            >
                                &times;
                            </button>
                        </div>

                        @if (! $hasLinkedTarget)
                            <div class="space-y-3 text-sm text-gray-600 leading-relaxed">
                                <p>
                                    {{ __('No verified :role account is linked to this login yet.', ['role' => $targetRoleLabel]) }}
                                </p>
                                <div class="rounded-xl bg-[#F4F7FA] border border-[#B8D0E2] p-3.5 text-xs text-[#1A2840]">
                                    {{ __('To protect PWD candidate eligibility and company security, both accounts must be verified by an administrator and linked in Settings. Either party can unlink at any time.') }}
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-gray-100">
                                <button
                                    type="button"
                                    wire:click="closeModal"
                                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 cursor-pointer"
                                >
                                    {{ __('Cancel') }}
                                </button>
                                <a
                                    href="{{ route('linked-accounts.edit') }}"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-[#244F73] px-4 py-2 text-xs font-semibold text-white hover:bg-[#1A2840] transition"
                                >
                                    <span>{{ __('Link Account in Settings') }}</span>
                                    <span aria-hidden="true">&rarr;</span>
                                </a>
                            </div>
                        @else
                            <form wire:submit="confirmSwitch" class="space-y-4">
                                @if ($targets->count() > 1)
                                    <div>
                                        <label for="role-switch-target" class="block text-xs font-bold text-[#1A2840] mb-1.5">
                                            {{ __('Select Linked :role Account', ['role' => $targetRoleLabel]) }}
                                        </label>
                                        <select
                                            id="role-switch-target"
                                            wire:model.live="selectedTargetId"
                                            class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm text-[#1A2840] focus:border-[#244F73] focus:ring-2 focus:ring-[#244F73] focus:outline-none"
                                        >
                                            @foreach ($targets as $t)
                                                <option value="{{ $t->id }}">
                                                    {{ $t->role === 'employer' && $t->employerProfile?->company_name ? $t->employerProfile->company_name.' ('.$t->email.')' : $t->name.' ('.$t->email.')' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    @php $singleTarget = $targets->first(); @endphp
                                    <div class="rounded-xl border border-gray-200 bg-[#F8FAFC] p-3.5">
                                        <div class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">
                                            {{ __('Destination :role Account', ['role' => $targetRoleLabel]) }}
                                        </div>
                                        <div class="font-bold text-sm text-[#1A2840] mt-0.5">
                                            {{ $singleTarget->role === 'employer' && $singleTarget->employerProfile?->company_name ? $singleTarget->employerProfile->company_name : $singleTarget->name }}
                                        </div>
                                        <div class="text-xs text-gray-600">{{ $singleTarget->email }}</div>
                                    </div>
                                @endif

                                <div>
                                    <label for="role-switch-password" class="block text-xs font-bold text-[#1A2840] mb-1.5">
                                        {{ __('Enter :role Account Password', ['role' => $targetRoleLabel]) }}
                                        <span class="text-rose-600">*</span>
                                    </label>
                                    <input
                                        id="role-switch-password"
                                        type="password"
                                        wire:model="password"
                                        required
                                        autofocus
                                        autocomplete="current-password"
                                        placeholder="{{ __('Confirm destination account password') }}"
                                        class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-[#1A2840] focus:border-[#244F73] focus:ring-2 focus:ring-[#244F73] focus:outline-none"
                                    />
                                    @error('password')
                                        <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1.5 text-[11px] text-gray-500">
                                        {{ __('Required so other users on a shared workspace cannot open a linked personal profile.') }}
                                    </p>
                                </div>

                                <div class="flex items-center justify-between gap-2 pt-2 border-t border-gray-100">
                                    <a href="{{ route('linked-accounts.edit') }}" class="text-xs font-medium text-gray-500 hover:text-[#244F73] hover:underline">
                                        {{ __('Manage links') }}
                                    </a>

                                    <div class="flex items-center gap-2">
                                        <button
                                            type="button"
                                            wire:click="closeModal"
                                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 cursor-pointer"
                                        >
                                            {{ __('Cancel') }}
                                        </button>
                                        <button
                                            type="submit"
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-[#244F73] px-4 py-2 text-xs font-semibold text-white hover:bg-[#1A2840] transition cursor-pointer"
                                        >
                                            <span>{{ __('Verify & Switch') }}</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            @endteleport
        @endif
    @endif
</div>
