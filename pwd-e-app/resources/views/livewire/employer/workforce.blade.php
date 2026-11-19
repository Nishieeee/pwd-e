<div class="space-y-6">
    <div>
        <h1 class="font-heading text-2xl font-bold text-primary-dark">{{ __('Workforce & Tax Incentives') }}</h1>
        <p class="text-sm text-body-muted">{{ __('RA 10524 deduction tracker for PWD employees.') }}</p>
    </div>

    <!-- Summary widget -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-2xl border border-primary-border bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-body-muted">{{ __('Total employees') }}</p>
            <p class="mt-2 text-3xl font-bold text-primary-dark tabular-nums">{{ $totalEmployees }}</p>
        </div>
        <div class="rounded-2xl border border-primary-border bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-body-muted">{{ __('PWD employees') }}</p>
            <p class="mt-2 text-3xl font-bold text-secondary tabular-nums">{{ $pwdCount }}</p>
        </div>
        <div class="rounded-2xl border border-primary-border bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-body-muted">{{ __('PWD share') }}</p>
            <p class="mt-2 text-3xl font-bold text-primary-dark tabular-nums">{{ $pwdPercent }}%</p>
        </div>
        <div class="rounded-2xl border border-secondary bg-secondary-tint p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-secondary">{{ __('RA 10524 deduction') }}</p>
            <p class="mt-2 text-3xl font-bold text-secondary tabular-nums">₱{{ number_format($deduction, 2) }}</p>
        </div>
    </div>

    <!-- Benefit explanation -->
    <div class="rounded-2xl border border-primary-border bg-white p-6 shadow-sm">
        <h2 class="font-heading text-lg font-bold text-primary-dark mb-2">{{ __('RA 10524 benefit') }}</h2>
        <p class="text-sm text-body-muted leading-relaxed">
            {{ __('Eligible employers may claim an additional 25% gross income deduction on the total amount of salaries and wages paid to qualified PWD employees. The deduction above is computed from verified PWD wages (₱:wage).', ['wage' => number_format($totalWages, 2)]) }}
        </p>
    </div>

    <!-- Records table -->
    <div class="rounded-2xl border border-primary-border bg-white shadow-sm">
        <table class="w-full text-sm">
            <caption class="sr-only">{{ __('Workforce records') }}</caption>
            <thead>
                <tr class="border-b border-primary-border text-left text-xs uppercase tracking-wide text-body-muted">
                    <th scope="col" class="px-4 py-3">{{ __('Employee') }}</th>
                    <th scope="col" class="px-4 py-3">{{ __('Hired') }}</th>
                    <th scope="col" class="px-4 py-3">{{ __('Annual wage') }}</th>
                    <th scope="col" class="px-4 py-3">{{ __('PWD verified') }}</th>
                    <th scope="col" class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary-border/60">
                @forelse ($records as $record)
                    <tr>
                        <td class="px-4 py-3 font-semibold text-primary-dark">{{ $record->candidateProfile?->user?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-body-muted">{{ $record->hired_at?->format('M d, Y') ?? '—' }}</td>
                        <td class="px-4 py-3 tabular-nums">₱{{ number_format($record->annual_wage, 2) }}</td>
                        <td class="px-4 py-3">
                            <button type="button" wire:click="togglePwdVerified({{ $record->id }})" class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $record->pwd_verified ? 'bg-secondary-tint text-secondary' : 'bg-gray-100 text-gray-600' }}">
                                {{ $record->pwd_verified ? __('Verified') : __('Unverified') }}
                            </button>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button type="button" wire:click="removeRecord({{ $record->id }})" wire:confirm="Remove this record?" class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-700">
                                {{ __('Remove') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-body-muted">{{ __('No workforce records yet. Records are created when you hire an applicant.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>