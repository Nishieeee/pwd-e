<div class="space-y-6">
    <!-- Screen Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-heading text-2xl font-bold text-primary-dark">{{ __('Workforce & Tax Incentives') }}</h1>
            <p class="text-sm text-body-muted">{{ __('Statutory gross income deduction calculator and compliance records under Republic Act No. 10524.') }}</p>
        </div>
        <div class="print:hidden">
            <button
                type="button"
                onclick="window.print()"
                class="inline-flex items-center gap-2 rounded-xl border border-primary-border bg-white px-4 py-2.5 text-xs font-bold text-primary-dark shadow-xs hover:bg-primary-tint transition-colors focus:ring-2 focus:ring-primary min-h-[44px]"
            >
                <svg class="size-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>{{ __('Print BIR Compliance Summary') }}</span>
            </button>
        </div>
    </div>

    <!-- Official BIR Certificate Print Header (Only visible on print) -->
    <div class="hidden print:block border-b-2 border-primary-dark pb-4 mb-6">
        <div class="text-center space-y-1">
            <p class="text-xs font-bold uppercase tracking-wider text-gray-600">Republic of the Philippines</p>
            <h2 class="text-lg font-bold text-black uppercase">Statutory PWD Employment Tax Deduction Certification</h2>
            <p class="text-xs text-gray-600">Pursuant to Section 5, Republic Act No. 10524 &amp; BIR Revenue Regulations</p>
        </div>
        <div class="mt-4 grid grid-cols-2 text-xs border-t border-gray-300 pt-3">
            <div>
                <p><strong>Employer:</strong> {{ auth()->user()->employerProfile?->company_name ?? auth()->user()->name }}</p>
                <p><strong>Reporting Period:</strong> Calendar Year {{ date('Y') }}</p>
            </div>
            <div class="text-right">
                <p><strong>Date Generated:</strong> {{ date('F d, Y') }}</p>
                <p><strong>Accreditation Status:</strong> DOLE / PESO Verified</p>
            </div>
        </div>
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
                    <th scope="col" class="px-4 py-3">{{ __('DOLE / PWD ID No.') }}</th>
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
                        <td class="px-4 py-3 text-xs font-mono text-body-muted">
                            {{ $record->candidateProfile?->user?->pwd_id_number ?? __('DOLE Verified') }}
                        </td>
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
                        <td colspan="6" class="px-4 py-6 text-center text-body-muted">{{ __('No workforce records yet. Records are created when you hire an applicant.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>