<div class="space-y-6">
    <!-- Screen Header Banner Card -->
    <div class="print:hidden relative overflow-hidden rounded-2xl border border-primary-border bg-white p-6 lg:p-7 shadow-sm" style="background: linear-gradient(135deg, #DCEAF4 0%, #FFFFFF 100%); box-shadow: 0 4px 20px rgba(26,40,64,0.06);">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-2.5 max-w-3xl min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-primary-border bg-white px-3 py-1 text-xs font-bold text-secondary shadow-2xs whitespace-nowrap">
                        <svg class="size-3.5 text-secondary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ __('RA 10524 Statutory Compliance') }}</span>
                    </span>
                    <span class="rounded-full bg-primary-tint px-3 py-1 text-xs font-semibold text-primary-dark border border-primary-border/70 whitespace-nowrap">
                        {{ __('DOLE / BIR Tax Deduction Tracker') }}
                    </span>
                </div>

                <h1 class="font-heading text-2xl sm:text-3xl font-bold text-primary-dark tracking-tight">
                    {{ __('Workforce & Tax Incentives') }}
                </h1>
                <p class="text-xs sm:text-sm text-body-muted leading-relaxed max-w-2xl">
                    {{ __('Statutory gross income deduction calculator and compliance records under Republic Act No. 10524.') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <button
                    type="button"
                    onclick="window.print()"
                    class="inline-flex items-center justify-center gap-2 h-11 rounded-xl border border-primary-border bg-white px-4 text-xs sm:text-sm font-semibold text-primary-dark shadow-2xs hover:bg-primary-tint/60 transition-colors whitespace-nowrap cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2"
                >
                    <svg class="size-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>{{ __('Print BIR Compliance Summary') }}</span>
                </button>
            </div>
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
            <p class="text-xs font-semibold uppercase tracking-wide text-body-muted">{{ __('Active employees') }}</p>
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
    <div class="rounded-2xl border border-primary-border bg-white p-6 shadow-sm space-y-2">
        <div class="flex items-center justify-between">
            <h2 class="font-heading text-lg font-bold text-primary-dark">{{ __('RA 10524 Statutory Tax Benefit') }}</h2>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                BIR Audit Aligned
            </span>
        </div>
        <p class="text-sm text-body-muted leading-relaxed">
            {{ __('Eligible employers may claim an additional 25% gross income deduction on the total amount of salaries and wages paid to qualified PWD employees holding an official Government PWD ID. The deduction above is computed from verified active PWD wages (₱:wage).', ['wage' => number_format($totalWages, 2)]) }}
        </p>
        @if($medCertOnlyCount > 0)
            <div class="mt-2 p-3 rounded-xl bg-purple-50 border border-purple-200 text-xs text-purple-900 flex items-start gap-2">
                <span class="text-base">🩺</span>
                <div>
                    <strong>{{ __(':count employee(s) verified via Clinical Medical Certificate (Fit to Work):', ['count' => $medCertOnlyCount]) }}</strong>
                    <span>{{ __(' They are fully compliant for inclusive employment under R.A. 7277. To include their wages in your BIR R.A. 10524 gross wage tax deduction, request their official PWD ID card via the Applicants / Documents panel once issued by their LGU.') }}</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Records tabs -->
    <div class="flex items-center justify-between gap-4">
        <div class="inline-flex rounded-xl p-1 bg-surface border border-primary-border flex-wrap" role="tablist" aria-label="{{ __('Workforce roster tabs') }}">
            <button type="button"
                    wire:click="setWorkforceTab('active')"
                    role="tab"
                    aria-selected="{{ $workforceTab === 'active' ? 'true' : 'false' }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary {{ $workforceTab === 'active' ? 'bg-primary-dark text-white shadow-xs' : 'text-body-muted hover:text-primary-dark' }}">
                <span>{{ __('Active Workforce') }}</span>
                <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold rounded-full {{ $workforceTab === 'active' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-700' }}">{{ $activeCount }}</span>
            </button>
            <button type="button"
                    wire:click="setWorkforceTab('disputed')"
                    role="tab"
                    aria-selected="{{ $workforceTab === 'disputed' ? 'true' : 'false' }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary {{ $workforceTab === 'disputed' ? 'bg-red-700 text-white shadow-xs' : 'text-body-muted hover:text-primary-dark' }}">
                <span>{{ __('Disputed by Candidate') }}</span>
                <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold rounded-full {{ $disputedCount > 0 ? ($workforceTab === 'disputed' ? 'bg-white text-red-700' : 'bg-red-100 text-red-700') : 'bg-gray-200 text-gray-700' }}">{{ $disputedCount }}</span>
            </button>
            <button type="button"
                    wire:click="setWorkforceTab('separated')"
                    role="tab"
                    aria-selected="{{ $workforceTab === 'separated' ? 'true' : 'false' }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary {{ $workforceTab === 'separated' ? 'bg-primary-dark text-white shadow-xs' : 'text-body-muted hover:text-primary-dark' }}">
                <span>{{ __('Separated / Former Staff') }}</span>
                <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold rounded-full {{ $workforceTab === 'separated' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-700' }}">{{ $separatedCount }}</span>
            </button>
        </div>
    </div>

    <!-- Records table -->
    <div class="rounded-2xl border border-primary-border bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm divide-y divide-primary-border/60">
                <caption class="sr-only">{{ __('Workforce records') }}</caption>
                <thead class="bg-surface border-b border-primary-border/70">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-body-muted">
                        <th scope="col" class="px-5 py-3.5 text-left whitespace-nowrap">{{ __('Employee') }}</th>
                        <th scope="col" class="px-4 py-3.5 text-left whitespace-nowrap">{{ __('DOLE / PWD ID No.') }}</th>
                        <th scope="col" class="px-4 py-3.5 text-left whitespace-nowrap">{{ __('Hired') }}</th>
                        @if($workforceTab === 'active')
                            <th scope="col" class="px-4 py-3.5 text-left whitespace-nowrap">{{ __('Due Process Status') }}</th>
                        @elseif($workforceTab === 'disputed')
                            <th scope="col" class="px-4 py-3.5 text-left whitespace-nowrap">{{ __('Contested On') }}</th>
                            <th scope="col" class="px-4 py-3.5 text-left whitespace-nowrap">{{ __('Candidate Statement') }}</th>
                        @elseif($workforceTab === 'separated')
                            <th scope="col" class="px-4 py-3.5 text-left whitespace-nowrap">{{ __('Separated Date') }}</th>
                            <th scope="col" class="px-4 py-3.5 text-left whitespace-nowrap">{{ __('Reason') }}</th>
                        @endif
                        <th scope="col" class="px-4 py-3.5 text-left whitespace-nowrap">{{ __('Annual wage') }}</th>
                        <th scope="col" class="px-4 py-3.5 text-left whitespace-nowrap">{{ __('PWD verified') }}</th>
                        <th scope="col" class="px-5 py-3.5 text-right whitespace-nowrap">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-border/60">
                    @forelse ($records as $record)
                        <tr class="hover:bg-primary-tint/25 transition-colors align-middle">
                            <td class="px-5 py-4 text-left">
                                <div class="font-semibold text-primary-dark">{{ $record->candidateProfile?->user?->name ?? '—' }}</div>
                                @if($record->application?->jobPosting)
                                    <div class="text-xs text-body-muted">{{ $record->application->jobPosting->title }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-left text-xs whitespace-nowrap">
                                @php
                                    $u = $record->candidateProfile?->user;
                                    $isMedCert = $u?->pwd_document_type === 'medical_certificate';
                                @endphp
                                @if($isMedCert)
                                    <div class="space-y-0.5">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-800 border border-purple-200">
                                            🩺 {{ __('Med Cert (Fit to Work)') }}
                                        </span>
                                        <div class="text-[10px] text-body-muted font-mono">{{ $u?->pwd_id_number }}</div>
                                        <div class="text-[9px] text-amber-700 font-medium">{{ __('PWD ID required for BIR 25% credit') }}</div>
                                    </div>
                                @else
                                    <div class="space-y-0.5">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                            🪪 {{ __('Official PWD ID') }}
                                        </span>
                                        <div class="text-[10px] text-primary-dark font-mono font-semibold">{{ $u?->pwd_id_number ?? __('DOLE Verified') }}</div>
                                        <div class="text-[9px] text-emerald-700 font-medium">{{ __('BIR RA 10524 Tax Eligible') }}</div>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-left text-xs text-body-muted whitespace-nowrap">{{ $record->hired_at?->format('M d, Y') ?? '—' }}</td>
                            
                            {{-- Active tab dynamic status --}}
                            @if($workforceTab === 'active')
                                <td class="px-4 py-4 text-left whitespace-nowrap">
                                    @if($record->separation_status === 'pending_review')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium border bg-amber-50 text-amber-800 border-amber-200" title="{{ __('Notice sent on :date. Awaiting candidate review.', ['date' => $record->separation_notice_at?->format('M d, Y')]) }}">
                                            <span class="size-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            {{ __('Notice Pending Review') }}
                                        </span>
                                    @elseif($record->separation_status === 'disputed')
                                        <button type="button" wire:click="viewDispute({{ $record->id }})" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold border bg-red-50 text-red-700 border-red-200 hover:bg-red-100 transition-colors cursor-pointer" title="{{ __('Candidate contested this separation. Click to review dispute.') }}">
                                            <span class="size-1.5 rounded-full bg-red-500"></span>
                                            {{ __('Contested / Disputed') }} &rarr;
                                        </button>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium border bg-emerald-50 text-emerald-800 border-emerald-200">
                                            <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                            {{ __('Active Staff') }}
                                        </span>
                                    @endif
                                </td>
                            @elseif($workforceTab === 'disputed')
                                <td class="px-4 py-4 text-left text-xs text-body-muted whitespace-nowrap">
                                    {{ $record->candidate_response_at?->format('M d, Y') ?? '—' }}
                                </td>
                                <td class="px-4 py-4 text-left text-xs max-w-xs">
                                    <div class="truncate text-red-900 font-medium" title="{{ $record->candidate_dispute_reason }}">
                                        {{ $record->candidate_dispute_reason ?? __('No grounds provided') }}
                                    </div>
                                </td>
                            @elseif($workforceTab === 'separated')
                                <td class="px-4 py-4 text-left text-xs text-body-muted whitespace-nowrap">
                                    {{ $record->ended_at?->format('M d, Y') ?? '—' }}
                                </td>
                                <td class="px-4 py-4 text-left whitespace-nowrap">
                                    @php
                                        $reasonConfig = match($record->ended_reason) {
                                            'resigned' => ['bg' => 'bg-amber-50 text-amber-800 border-amber-200', 'label' => 'Resigned / Left'],
                                            'relieved' => ['bg' => 'bg-red-50 text-red-700 border-red-200', 'label' => 'Relieved / Fired'],
                                            'contract_ended' => ['bg' => 'bg-blue-50 text-blue-800 border-blue-200', 'label' => 'Contract Ended'],
                                            default => ['bg' => 'bg-gray-100 text-gray-700 border-gray-200', 'label' => 'Separated'],
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $reasonConfig['bg'] }}" @if($record->ended_notes) title="{{ $record->ended_notes }}" @endif>
                                        {{ __($reasonConfig['label']) }}
                                    </span>
                                </td>
                            @endif

                            <td class="px-4 py-4 text-left font-medium text-primary-dark tabular-nums whitespace-nowrap">₱{{ number_format($record->annual_wage, 2) }}</td>
                            <td class="px-4 py-4 text-left whitespace-nowrap">
                                <button type="button" wire:click="togglePwdVerified({{ $record->id }})" class="rounded-full px-3 py-1 text-xs font-semibold border transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 {{ $record->pwd_verified ? 'bg-secondary-tint text-secondary border-secondary/20 hover:bg-secondary/10' : 'bg-gray-100 text-gray-600 border-gray-200 hover:bg-gray-200/70' }}">
                                    {{ $record->pwd_verified ? __('Verified') : __('Unverified') }}
                                </button>
                            </td>
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    @if($workforceTab === 'active')
                                        @if($record->separation_status === 'pending_review')
                                            {{-- Cancel Pending Notice --}}
                                            <button type="button"
                                                    wire:click="openConfirmModal('cancelNotice', {{ $record->id }}, '{{ __('Withdraw Separation Notice?') }}', '{{ __('Are you sure you want to withdraw this separation notice and retain this employee in active workforce?') }}', '{{ __('Withdraw Notice & Retain') }}', 'bg-emerald-600')"
                                                    class="inline-flex items-center justify-center h-[34px] rounded-lg border border-gray-300 bg-white px-3 text-xs font-semibold text-body-muted hover:text-primary-dark hover:bg-gray-50 transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                                                    title="{{ __('Withdraw separation notice') }}">
                                                {{ __('Cancel Notice') }}
                                            </button>
                                        @elseif($record->separation_status === 'disputed')
                                            {{-- Review Dispute --}}
                                            <button type="button"
                                                    wire:click="viewDispute({{ $record->id }})"
                                                    class="inline-flex items-center justify-center h-[34px] rounded-lg border border-red-300 bg-red-50 px-3 text-xs font-semibold text-red-700 hover:bg-red-100 transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500"
                                                    title="{{ __('Review candidate contestation') }}">
                                                {{ __('Review Dispute') }}
                                            </button>
                                            <button type="button"
                                                    wire:click="openConfirmModal('cancelNotice', {{ $record->id }}, '{{ __('Withdraw Separation Notice?') }}', '{{ __('Are you sure you want to withdraw this separation notice and retain this employee in active workforce?') }}', '{{ __('Withdraw Notice & Retain') }}', 'bg-emerald-600')"
                                                    class="inline-flex items-center justify-center h-[34px] rounded-lg border border-primary-border bg-white px-2.5 text-xs font-semibold text-primary hover:bg-primary-tint/60 transition-colors cursor-pointer"
                                                    title="{{ __('Retain employee') }}">
                                                {{ __('Retain Staff') }}
                                            </button>
                                        @else
                                            {{-- End Employment / Relieve --}}
                                            <button type="button"
                                                    wire:click="openEndEmploymentModal({{ $record->id }})"
                                                    class="inline-flex items-center justify-center h-[34px] rounded-lg border border-amber-300 bg-amber-50 px-3 text-xs font-semibold text-amber-800 hover:bg-amber-100 transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2"
                                                    title="{{ __('Issue Notice of Separation (resigned or relieved)') }}">
                                                {{ __('End Employment') }}
                                            </button>
                                        @endif
                                    @elseif($workforceTab === 'disputed')
                                        <button type="button"
                                                wire:click="viewDispute({{ $record->id }})"
                                                class="inline-flex items-center justify-center h-[34px] rounded-lg border border-red-300 bg-red-50 px-3 text-xs font-semibold text-red-700 hover:bg-red-100 transition-colors cursor-pointer">
                                            {{ __('Review Dispute') }}
                                        </button>
                                        <button type="button"
                                                wire:click="openConfirmModal('cancelNotice', {{ $record->id }}, '{{ __('Withdraw Separation Notice?') }}', '{{ __('Are you sure you want to withdraw this separation notice and retain this employee in active workforce?') }}', '{{ __('Withdraw Notice & Retain') }}', 'bg-emerald-600')"
                                                class="inline-flex items-center justify-center h-[34px] rounded-lg border border-primary-border bg-white px-3 text-xs font-semibold text-primary hover:bg-primary-tint transition-colors cursor-pointer">
                                            {{ __('Retain Employee') }}
                                        </button>
                                    @else
                                        {{-- Reactivate to Active Workforce --}}
                                        <button type="button"
                                                wire:click="openConfirmModal('reactivateRecord', {{ $record->id }}, '{{ __('Reactivate Employee?') }}', '{{ __('Reactivate this candidate back into active workforce?') }}', '{{ __('Reactivate') }}', 'bg-primary-dark')"
                                                class="inline-flex items-center justify-center h-[34px] rounded-lg border border-primary-border bg-white px-3 text-xs font-semibold text-primary hover:bg-primary-tint/60 transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2"
                                                title="{{ __('Re-hire or reactivate employee') }}">
                                            {{ __('Reactivate') }}
                                        </button>
                                    @endif

                                    {{-- Delete Record --}}
                                    <button type="button"
                                            wire:click="openConfirmModal('removeRecord', {{ $record->id }}, '{{ __('Permanently Delete Record?') }}', '{{ __('Are you sure you want to permanently remove this workforce record? This action cannot be undone.') }}', '{{ __('Delete Record') }}', 'bg-red-600')"
                                            class="inline-flex items-center justify-center h-[34px] rounded-lg border border-red-200 bg-white px-2.5 text-xs font-semibold text-red-600 hover:bg-red-50 hover:border-red-300 transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2"
                                            title="{{ __('Permanently delete record') }}">
                                        {{ __('Delete') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $workforceTab === 'separated' || $workforceTab === 'disputed' ? '8' : '7' }}" class="px-6 py-10 text-center text-body-muted">
                                @if($workforceTab === 'separated')
                                    {{ __('No separated or departed employees. All workforce members are currently active.') }}
                                @elseif($workforceTab === 'disputed')
                                    <div class="flex flex-col items-center justify-center gap-1.5 py-4">
                                        <svg class="size-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <p class="font-semibold text-primary-dark">{{ __('No Contested Separations') }}</p>
                                        <p class="text-xs text-body-muted">{{ __('There are currently no separation disputes filed by employees.') }}</p>
                                    </div>
                                @else
                                    {{ __('No active workforce records yet. Records are created when you hire an applicant.') }}
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Offboarding / End Employment Modal -->
    @if ($showEndEmploymentModal && $endingRecord)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" role="dialog" aria-modal="true" aria-label="{{ __('End employment') }}">
            <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl bg-white p-6 sm:p-8 shadow-xl space-y-5" wire:click.stop>
                <div class="flex items-start justify-between border-b border-primary-border pb-3">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200 mb-1">
                            {{ __('Offboarding & Statutory Due Process') }}
                        </div>
                        <h2 class="font-heading text-lg font-bold text-primary-dark">{{ __('Issue Notice of Separation') }}</h2>
                        <p class="text-xs text-body-muted mt-0.5">{{ $endingRecord->candidateProfile?->user?->name }}</p>
                    </div>
                    <button type="button" wire:click="closeEndEmploymentModal" class="text-body-muted hover:text-primary-dark text-lg">&times;</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-primary-dark mb-1.5">{{ __('Reason for Separation') }} <span class="text-red-500">*</span></label>
                        <select wire:model="endedReason" class="w-full rounded-lg border border-primary-border px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-primary focus:outline-none bg-white">
                            <option value="resigned">{{ __('Candidate Resigned / Decided to Leave') }}</option>
                            <option value="relieved">{{ __('Relieved / Terminated by Employer') }}</option>
                            <option value="contract_ended">{{ __('Employment Contract Completed') }}</option>
                            <option value="other">{{ __('Other / Mutual Separation') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-primary-dark mb-1.5">{{ __('Effective End Date') }} <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="endedAt" class="w-full rounded-lg border border-primary-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary focus:outline-none" />
                        @error('endedAt') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-primary-dark mb-1.5">{{ __('Notes / Stated Grounds for Departure (Required for Relieved/Other)') }}</label>
                        <textarea wire:model="endedNotes" rows="3" placeholder="{{ __('Provide detailed grounds or context regarding this departure for due process compliance...') }}" class="w-full rounded-lg border border-primary-border p-3 text-xs focus:ring-2 focus:ring-primary focus:outline-none"></textarea>
                        @error('endedNotes') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="rounded-xl border border-sky-200 bg-sky-50/70 p-3 text-xs text-sky-900 leading-relaxed">
                        <strong class="font-bold block mb-0.5">{{ __('Philippine Labor Law & RA 7277 Protection:') }}</strong>
                        {{ __('Under R.A. 7277 (Magna Carta for Disabled Persons) and Security of Tenure standards, issuing this notice delivers a formal notification to the employee. The employee has the statutory opportunity to review, acknowledge, or contest/dispute this separation.') }}
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-primary-border">
                    <button type="button" wire:click="closeEndEmploymentModal" class="rounded-lg border border-primary-border px-4 py-2 text-xs font-semibold text-primary-dark hover:bg-primary-tint">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" wire:click="confirmEndEmployment" class="rounded-lg bg-amber-600 px-4 py-2 text-xs font-semibold text-white hover:bg-amber-700 shadow-xs cursor-pointer">
                        {{ __('Issue Notice of Separation') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Dispute Review Modal -->
    @if ($viewingDisputeRecord)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" role="dialog" aria-modal="true" aria-label="{{ __('Review Separation Dispute') }}">
            <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl bg-white p-6 sm:p-8 shadow-xl space-y-5" wire:click.stop>
                <div class="flex items-start justify-between border-b border-primary-border pb-3">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200 mb-1">
                            {{ __('Candidate Contestation / Dispute') }}
                        </div>
                        <h2 class="font-heading text-lg font-bold text-primary-dark">{{ __('Contested Employment Separation') }}</h2>
                        <p class="text-xs text-body-muted mt-0.5">{{ $viewingDisputeRecord->candidateProfile?->user?->name }} &bull; {{ $viewingDisputeRecord->application?->jobPosting?->title ?? 'Position' }}</p>
                    </div>
                    <button type="button" wire:click="closeDisputeModal" class="text-body-muted hover:text-primary-dark text-lg">&times;</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div class="rounded-xl border border-red-200 bg-red-50/50 p-4 space-y-2">
                        <div class="flex items-center justify-between text-xs text-red-900 font-bold border-b border-red-200/60 pb-1.5">
                            <span>{{ __('Candidate Formal Statement:') }}</span>
                            <span class="font-normal text-body-muted">{{ $viewingDisputeRecord->candidate_response_at?->format('M d, Y h:i A') }}</span>
                        </div>
                        <p class="text-xs text-red-950 whitespace-pre-wrap leading-relaxed italic">
                            "{{ $viewingDisputeRecord->candidate_dispute_reason ?? __('No written statement provided.') }}"
                        </p>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-surface p-3.5 space-y-1.5 text-xs text-body-muted">
                        <div class="flex justify-between">
                            <span class="font-semibold text-primary-dark">{{ __('Notice Issued:') }}</span>
                            <span>{{ $viewingDisputeRecord->separation_notice_at?->format('M d, Y') ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-semibold text-primary-dark">{{ __('Proposed Reason:') }}</span>
                            <span class="capitalize">{{ $viewingDisputeRecord->ended_reason }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-semibold text-primary-dark">{{ __('Effective Date:') }}</span>
                            <span>{{ $viewingDisputeRecord->ended_at?->format('M d, Y') ?? '—' }}</span>
                        </div>
                        @if($viewingDisputeRecord->ended_notes)
                            <div class="pt-1 border-t border-gray-200/70">
                                <span class="font-semibold text-primary-dark block mb-0.5">{{ __('Employer Notice Remarks:') }}</span>
                                <p class="text-xs italic">{{ $viewingDisputeRecord->ended_notes }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="rounded-xl border border-amber-200 bg-amber-50/70 p-3 text-xs text-amber-900 leading-relaxed">
                        <strong class="font-bold block mb-0.5">{{ __('DOLE & RA 7277 Legal Advisory:') }}</strong>
                        {{ __('Republic Act No. 7277 prohibits arbitrary termination of persons with disability. While under dispute, this employee record is maintained for compliance auditing. You may withdraw the separation notice to retain the employee, or coordinate with the candidate / DOLE mediator.') }}
                    </div>
                </div>

                <div class="flex items-center justify-between gap-2 pt-3 border-t border-primary-border">
                    <button type="button" wire:click="closeDisputeModal" class="rounded-lg border border-primary-border px-4 py-2 text-xs font-semibold text-primary-dark hover:bg-primary-tint">
                        {{ __('Close') }}
                    </button>
                    <button type="button" wire:click="openConfirmModal('cancelNotice', {{ $viewingDisputeRecord->id }}, '{{ __('Withdraw Separation Notice?') }}', '{{ __('Are you sure you want to withdraw this notice of separation and retain this employee in active workforce?') }}', '{{ __('Withdraw Notice & Retain Staff') }}', 'bg-emerald-600')" class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-semibold text-white hover:bg-emerald-700 shadow-xs cursor-pointer">
                        {{ __('Withdraw Notice & Retain Staff') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Universal Workforce Action Confirmation Modal --}}
    @if ($showConfirmModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-xs" role="dialog" aria-modal="true" aria-label="{{ $confirmTitle }}">
            <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-2xl bg-white p-6 sm:p-7 shadow-2xl space-y-4" wire:click.stop>
                <div class="flex items-start justify-between border-b border-primary-border pb-3">
                    <h2 class="font-heading text-lg font-bold text-primary-dark">{{ $confirmTitle }}</h2>
                    <button type="button" wire:click="closeConfirmModal" class="text-body-muted hover:text-primary-dark text-xl leading-none cursor-pointer">&times;</button>
                </div>

                <p class="text-xs text-body-muted leading-relaxed">
                    {{ $confirmDescription }}
                </p>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-primary-border">
                    <button type="button" wire:click="closeConfirmModal" class="rounded-xl border border-primary-border px-4 py-2 text-xs font-semibold text-primary-dark hover:bg-primary-tint transition-colors cursor-pointer">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" wire:click="executeConfirmedAction" class="rounded-xl {{ $confirmButtonClass }} px-4 py-2 text-xs font-bold text-white shadow-xs transition-colors cursor-pointer">
                        {{ $confirmButtonText }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
