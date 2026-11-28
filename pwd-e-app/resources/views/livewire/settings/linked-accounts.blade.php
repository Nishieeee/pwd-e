<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('Linked role accounts') }}</flux:heading>

    <x-settings.layout
        :heading="__('Linked Role Accounts')"
        :subheading="$user->role === 'employer'
            ? __('Link a verified PWD Candidate account (such as an HR staff member\'s personal profile) to enable password-protected role switching.')
            : __('Link a verified Employer Company account to switch between your personal Candidate portal and Employer HR workspace.')"
    >
        <div class="space-y-6 my-6">
            @if ($statusMessage)
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3.5 text-xs sm:text-sm font-medium text-emerald-900" role="status">
                    {{ $statusMessage }}
                </div>
            @endif

            <!-- Security & Offboarding Notice -->
            <div class="rounded-xl border border-[#B8D0E2] bg-[#F4F7FA] p-4 text-xs leading-relaxed text-[#25313B] space-y-1.5">
                <div class="font-bold text-[#1A2840] flex items-center gap-1.5">
                    <svg class="size-4 text-[#244F73] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>{{ __('Password-Protected Role Switching & Instant Unlinking') }}</span>
                </div>
                <p>
                    {{ __('Both accounts must already be verified by an administrator. Switching roles always prompts for the destination account\'s password so shared company logins cannot access an HR member\'s personal Candidate profile. Either party can unlink below at any time.') }}
                </p>
            </div>

            <!-- Currently Linked Accounts -->
            <div class="space-y-3">
                <h3 class="text-sm font-bold text-[#1A2840]">
                    {{ __('Currently Linked :role Account(s)', ['role' => $targetRoleLabel]) }}
                </h3>

                @if ($links->isEmpty())
                    <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50/70 p-4 text-xs text-gray-600">
                        {{ __('No :role account is currently linked.', ['role' => strtolower($targetRoleLabel)]) }}
                    </div>
                @else
                    <div class="space-y-2.5">
                        @foreach ($links as $link)
                            @php
                                $counterpart = $user->role === 'employer' ? $link->candidateUser : $link->employerUser;
                                $subtitle = $user->role === 'employer'
                                    ? ($counterpart?->email ?? '—')
                                    : (($counterpart?->employerProfile?->company_name ? $counterpart->employerProfile->company_name.' · ' : '').($counterpart?->email ?? '—'));
                            @endphp
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-2xs">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-sm text-[#1A2840] truncate">
                                            {{ $counterpart?->name ?? __('Removed User') }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $user->role === 'employer' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-indigo-50 text-indigo-800 border border-indigo-200' }}">
                                            {{ $targetRoleLabel }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-600 truncate mt-0.5">{{ $subtitle }}</div>
                                    <div class="text-[11px] text-gray-400 mt-0.5">
                                        {{ __('Linked :date', ['date' => $link->created_at?->format('M d, Y') ?? '—']) }}
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    wire:click="unlinkAccount({{ $link->id }})"
                                    wire:confirm="{{ __('Unlink this account? Role switching between these accounts will be disabled immediately.') }}"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-3.5 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-100 transition cursor-pointer shrink-0"
                                >
                                    <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                    </svg>
                                    <span>{{ __('Unlink Account') }}</span>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Link New Counterpart Account Form -->
            <form wire:submit="linkAccount" class="space-y-4 pt-4 border-t border-gray-200">
                <div>
                    <h3 class="text-sm font-bold text-[#1A2840]">
                        {{ __('Link Verified :role Account', ['role' => $targetRoleLabel]) }}
                    </h3>
                    <p class="text-xs text-gray-600 mt-0.5">
                        {{ __('Enter the email and password of the verified :role account to authorize linking.', ['role' => strtolower($targetRoleLabel)]) }}
                    </p>
                </div>

                <flux:input
                    wire:model="email"
                    :label="__(':role Email Address', ['role' => $targetRoleLabel])"
                    type="email"
                    required
                    autocomplete="off"
                    placeholder="name@example.com"
                />

                <flux:input
                    wire:model="password"
                    :label="__(':role Account Password', ['role' => $targetRoleLabel])"
                    type="password"
                    required
                    autocomplete="new-password"
                />

                <div class="flex items-center gap-3 pt-1">
                    <flux:button variant="primary" type="submit">
                        {{ __('Verify & Link Account') }}
                    </flux:button>

                    <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-[#244F73] hover:underline">
                        {{ __('Return to Portal Dashboard') }}
                    </a>
                </div>
            </form>
        </div>
    </x-settings.layout>
</section>
