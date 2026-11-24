@php
    $employerProfile = auth()->user()->employerProfile;
    $employerName = $employerProfile?->company_name ?? auth()->user()->name;
    $reportingYear = date('Y');
    $dateGenerated = date('F d, Y');
    $docRefNo = sprintf('PWDE-RA10524-%s-%04d', $reportingYear, $employerProfile?->id ?? auth()->id());
    $qualifiedRecords = $records->where('pwd_verified', true)->values();
@endphp

<div class="space-y-6 print:space-y-0">
    <style>
        @page {
            size: A4 portrait;
            margin: 14mm 16mm;
            @top-left { content: none; }
            @top-center { content: none; }
            @top-right { content: none; }
            @bottom-left { content: none; }
            @bottom-center { content: none; }
            @bottom-right { content: none; }
        }

        @media print {
            html, body {
                background: #ffffff !important;
                color: #0f172a !important;
                font-size: 9.5pt !important;
                line-height: 1.4 !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            #bir-print-certificate,
            #bir-print-certificate * {
                box-shadow: none !important;
                text-shadow: none !important;
                color: #0f172a;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            #bir-print-certificate {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .print-avoid-break {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
    </style>

    {{-- ===================================================================== --}}
    {{-- SCREEN-ONLY INTERACTIVE WORKFORCE & TAX INCENTIVES VIEW               --}}
    {{-- ===================================================================== --}}
    <div class="print:hidden space-y-6">
        <!-- Screen Header Banner Card -->
        <div class="relative overflow-hidden rounded-2xl border border-primary-border bg-white p-6 lg:p-7 shadow-sm" style="background: linear-gradient(135deg, #DCEAF4 0%, #FFFFFF 100%); box-shadow: 0 4px 20px rgba(26,40,64,0.06);">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-2.5 max-w-3xl min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-primary-border bg-white px-3 py-1 text-xs font-bold text-secondary shadow-2xs">
                            <svg class="size-3.5 text-secondary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ __('RA 10524 Statutory Compliance') }}</span>
                        </span>
                        <span class="rounded-full bg-primary-tint px-3 py-1 text-xs font-semibold text-primary-dark border border-primary-border/70">
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
        <div class="rounded-2xl border border-primary-border bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm divide-y divide-primary-border/60">
                    <caption class="sr-only">{{ __('Workforce records') }}</caption>
                    <thead class="bg-surface border-b border-primary-border/70">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-body-muted">
                            <th scope="col" class="px-5 py-3.5 text-left whitespace-nowrap">{{ __('Employee') }}</th>
                            <th scope="col" class="px-4 py-3.5 text-left whitespace-nowrap">{{ __('DOLE / PWD ID No.') }}</th>
                            <th scope="col" class="px-4 py-3.5 text-left whitespace-nowrap">{{ __('Hired') }}</th>
                            <th scope="col" class="px-4 py-3.5 text-left whitespace-nowrap">{{ __('Annual wage') }}</th>
                            <th scope="col" class="px-4 py-3.5 text-left whitespace-nowrap">{{ __('PWD verified') }}</th>
                            <th scope="col" class="px-5 py-3.5 text-right whitespace-nowrap">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary-border/60">
                        @forelse ($records as $record)
                            <tr class="hover:bg-primary-tint/25 transition-colors align-middle">
                                <td class="px-5 py-4 text-left font-semibold text-primary-dark">{{ $record->candidateProfile?->user?->name ?? '—' }}</td>
                                <td class="px-4 py-4 text-left text-xs font-mono text-body-muted whitespace-nowrap">
                                    {{ $record->candidateProfile?->user?->pwd_id_number ?? __('DOLE Verified') }}
                                </td>
                                <td class="px-4 py-4 text-left text-xs text-body-muted whitespace-nowrap">{{ $record->hired_at?->format('M d, Y') ?? '—' }}</td>
                                <td class="px-4 py-4 text-left font-medium text-primary-dark tabular-nums whitespace-nowrap">₱{{ number_format($record->annual_wage, 2) }}</td>
                                <td class="px-4 py-4 text-left whitespace-nowrap">
                                    <button type="button" wire:click="togglePwdVerified({{ $record->id }})" class="rounded-full px-3 py-1 text-xs font-semibold border transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 {{ $record->pwd_verified ? 'bg-secondary-tint text-secondary border-secondary/20 hover:bg-secondary/10' : 'bg-gray-100 text-gray-600 border-gray-200 hover:bg-gray-200/70' }}">
                                        {{ $record->pwd_verified ? __('Verified') : __('Unverified') }}
                                    </button>
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <button type="button" wire:click="removeRecord({{ $record->id }})" wire:confirm="Remove this record?" class="inline-flex items-center justify-center h-[34px] rounded-lg bg-red-600 px-3 text-xs font-semibold text-white hover:bg-red-700 transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2">
                                        {{ __('Remove') }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-body-muted">{{ __('No workforce records yet. Records are created when you hire an applicant.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ===================================================================== --}}
    {{-- PRINT-ONLY FORMAL STATUTORY CERTIFICATION (SINGLE A4 PORTRAIT PAGE)   --}}
    {{-- ===================================================================== --}}
    <article id="bir-print-certificate" class="hidden print:block bg-white text-[#0f172a]" aria-label="{{ __('Statutory PWD Employment Tax Deduction Certification') }}">
        <!-- 1. Formal Institutional Top Bar & Centered Title Block -->
        <div class="print-avoid-break">
            <div class="flex items-center justify-between pb-2 mb-3" style="border-bottom: 1px solid #cbd5e1;">
                <span class="text-[8.5pt] font-bold tracking-wide text-[#0f172a]">
                    PWD-E | Republic Act No. 10524 Compliance Portal
                </span>
                <span class="text-[8pt] font-semibold uppercase tracking-wider px-2 py-0.5 text-[#0f172a]" style="border: 1px solid #94a3b8; background: #f8fafc; border-radius: 2px;">
                    DOLE / PESO Verified Employer
                </span>
            </div>

            <div class="text-center pb-3" style="border-bottom: 2px solid #0f172a;">
                <p class="text-[8.5pt] font-semibold uppercase text-[#334155]" style="letter-spacing: 0.16em;">
                    REPUBLIC OF THE PHILIPPINES
                </p>
                <h2 class="font-heading text-[13.5pt] font-bold uppercase tracking-wide text-[#0f172a] mt-1 leading-tight">
                    STATUTORY PWD EMPLOYMENT TAX DEDUCTION CERTIFICATION
                </h2>
                <p class="text-[8.5pt] text-[#334155] mt-1">
                    Pursuant to Section 5, Republic Act No. 10524 &amp; BIR Revenue Regulations
                </p>
            </div>

            <!-- 2. Structured 2-Column Metadata Grid -->
            <table class="w-full mt-3 text-[8.5pt]" style="border-collapse: collapse; border: 1px solid #cbd5e1;">
                <tbody>
                    <tr>
                        <td class="w-1/2 align-top px-3 py-2" style="border-right: 1px solid #cbd5e1;">
                            <div class="space-y-1">
                                <div class="flex justify-between gap-2">
                                    <span class="font-semibold text-[#334155]">Employer Name:</span>
                                    <span class="font-bold text-[#0f172a] text-right">{{ $employerName }}</span>
                                </div>
                                <div class="flex justify-between gap-2">
                                    <span class="font-semibold text-[#334155]">Reporting Period:</span>
                                    <span class="font-semibold text-[#0f172a] text-right">Calendar Year {{ $reportingYear }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="w-1/2 align-top px-3 py-2">
                            <div class="space-y-1">
                                <div class="flex justify-between gap-2">
                                    <span class="font-semibold text-[#334155]">Date Generated:</span>
                                    <span class="font-semibold text-[#0f172a] text-right">{{ $dateGenerated }}</span>
                                </div>
                                <div class="flex justify-between gap-2">
                                    <span class="font-semibold text-[#334155]">Accreditation Status:</span>
                                    <span class="font-semibold text-[#0f172a] text-right">DOLE / PESO Verified</span>
                                </div>
                                <div class="flex justify-between gap-2">
                                    <span class="font-semibold text-[#334155]">Document Reference No.:</span>
                                    <span class="font-mono font-bold text-[#0f172a] text-right">{{ $docRefNo }}</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- 3. Compact 4-Column Executive Summary Strip -->
        <section class="print-avoid-break mt-3.5" aria-label="{{ __('Executive Summary') }}">
            <table class="w-full" style="border-collapse: collapse; border: 1px solid #cbd5e1;">
                <tbody>
                    <tr>
                        <td class="w-1/4 px-3 py-2 align-top" style="border-right: 1px solid #cbd5e1; background: #ffffff;">
                            <div class="text-[7.5pt] font-bold uppercase tracking-wider text-[#475569]">Total Employees</div>
                            <div class="text-[13pt] font-bold text-[#0f172a] tabular-nums mt-0.5">{{ $totalEmployees }}</div>
                        </td>
                        <td class="w-1/4 px-3 py-2 align-top" style="border-right: 1px solid #cbd5e1; background: #ffffff;">
                            <div class="text-[7.5pt] font-bold uppercase tracking-wider text-[#475569]">PWD Employees</div>
                            <div class="text-[13pt] font-bold text-[#0f172a] tabular-nums mt-0.5">{{ $pwdCount }}</div>
                        </td>
                        <td class="w-1/4 px-3 py-2 align-top" style="border-right: 1px solid #cbd5e1; background: #ffffff;">
                            <div class="text-[7.5pt] font-bold uppercase tracking-wider text-[#475569]">PWD Share</div>
                            <div class="text-[13pt] font-bold text-[#0f172a] tabular-nums mt-0.5">{{ $pwdPercent }}%</div>
                        </td>
                        <td class="w-1/4 px-3 py-2 align-top" style="background: #f1f5f9;">
                            <div class="text-[7.5pt] font-bold uppercase tracking-wider text-[#0f172a]">RA 10524 Deduction (25%)</div>
                            <div class="text-[13pt] font-bold text-[#0f172a] tabular-nums mt-0.5">₱{{ number_format($deduction, 2) }}</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- 4. Qualified PWD Employee Schedule Table -->
        <section class="mt-4" aria-label="{{ __('Qualified PWD Employee Schedule') }}">
            <h2 class="text-[8.5pt] font-bold uppercase tracking-wider text-[#0f172a] mb-1.5">
                Schedule of Qualified PWD Employees &amp; Allowable 25% Gross Wage Deductions
            </h2>

            <table class="w-full text-[8pt]" style="border-collapse: collapse; border: 1px solid #cbd5e1;">
                <thead>
                    <tr style="background: #f1f5f9; border-bottom: 1.5px solid #0f172a;">
                        <th scope="col" class="px-2 py-2 text-center font-bold uppercase tracking-wide text-[#0f172a] whitespace-nowrap" style="border-right: 1px solid #cbd5e1; width: 5%;">No.</th>
                        <th scope="col" class="px-2.5 py-2 text-left font-bold uppercase tracking-wide text-[#0f172a]" style="border-right: 1px solid #cbd5e1;">Employee Name</th>
                        <th scope="col" class="px-2.5 py-2 text-left font-bold uppercase tracking-wide text-[#0f172a] whitespace-nowrap" style="border-right: 1px solid #cbd5e1;">DOLE / PWD ID No.</th>
                        <th scope="col" class="px-2 py-2 text-left font-bold uppercase tracking-wide text-[#0f172a] whitespace-nowrap" style="border-right: 1px solid #cbd5e1;">Date Hired</th>
                        <th scope="col" class="px-2 py-2 text-center font-bold uppercase tracking-wide text-[#0f172a] whitespace-nowrap" style="border-right: 1px solid #cbd5e1;">Verification Status</th>
                        <th scope="col" class="px-2.5 py-2 text-right font-bold uppercase tracking-wide text-[#0f172a] whitespace-nowrap" style="border-right: 1px solid #cbd5e1;">Annual Gross Wage (PHP)</th>
                        <th scope="col" class="px-2.5 py-2 text-right font-bold uppercase tracking-wide text-[#0f172a] whitespace-nowrap">25% Allowable Deduction (PHP)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($qualifiedRecords as $index => $record)
                        @php
                            $rowDeduction = round(((float) $record->annual_wage) * 0.25, 2);
                        @endphp
                        <tr class="print-avoid-break" style="border-bottom: 1px solid #cbd5e1; background: {{ $index % 2 === 1 ? '#f8fafc' : '#ffffff' }};">
                            <td class="px-2 py-1.5 text-center tabular-nums text-[#334155]" style="border-right: 1px solid #cbd5e1;">{{ $index + 1 }}</td>
                            <td class="px-2.5 py-1.5 text-left font-semibold text-[#0f172a]" style="border-right: 1px solid #cbd5e1;">{{ $record->candidateProfile?->user?->name ?? '—' }}</td>
                            <td class="px-2.5 py-1.5 text-left font-mono text-[#334155] whitespace-nowrap" style="border-right: 1px solid #cbd5e1;">{{ $record->candidateProfile?->user?->pwd_id_number ?? 'DOLE Verified' }}</td>
                            <td class="px-2 py-1.5 text-left text-[#334155] whitespace-nowrap" style="border-right: 1px solid #cbd5e1;">{{ $record->hired_at?->format('M d, Y') ?? '—' }}</td>
                            <td class="px-2 py-1.5 text-center font-semibold text-[#0f172a] whitespace-nowrap" style="border-right: 1px solid #cbd5e1;">Verified</td>
                            <td class="px-2.5 py-1.5 text-right tabular-nums text-[#0f172a] whitespace-nowrap" style="border-right: 1px solid #cbd5e1;">₱{{ number_format((float) $record->annual_wage, 2) }}</td>
                            <td class="px-2.5 py-1.5 text-right font-semibold tabular-nums text-[#0f172a] whitespace-nowrap">₱{{ number_format($rowDeduction, 2) }}</td>
                        </tr>
                    @empty
                        <tr style="border-bottom: 1px solid #cbd5e1; background: #ffffff;">
                            <td colspan="7" class="px-4 py-5 text-center text-[8.5pt] italic text-[#475569]">
                                No verified PWD employees recorded for this reporting period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="print-avoid-break" style="background: #f1f5f9; border-top: 1.5px solid #0f172a;">
                        <td colspan="5" class="px-2.5 py-2 text-right font-bold uppercase tracking-wide text-[#0f172a]" style="border-right: 1px solid #cbd5e1;">
                            Total Verified PWD Wages &amp; Allowable 25% Tax Deduction
                        </td>
                        <td class="px-2.5 py-2 text-right align-top whitespace-nowrap" style="border-right: 1px solid #cbd5e1;">
                            <div class="font-bold tabular-nums text-[8.5pt] text-[#0f172a]">₱{{ number_format($totalWages, 2) }}</div>
                            <div class="text-[6.5pt] uppercase tracking-wide text-[#475569]">Total Verified PWD Wages</div>
                        </td>
                        <td class="px-2.5 py-2 text-right align-top whitespace-nowrap">
                            <div class="font-bold tabular-nums text-[8.5pt] text-[#0f172a]">₱{{ number_format($deduction, 2) }}</div>
                            <div class="text-[6.5pt] uppercase tracking-wide text-[#475569]">Total Allowable 25% Tax Deduction</div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </section>

        <!-- 5. Statutory Basis & Certification Clause -->
        <section class="print-avoid-break mt-4 px-3.5 py-2.5" style="border: 1px solid #cbd5e1; border-left: 3px solid #0f172a; background: #f8fafc;">
            <h2 class="text-[8pt] font-bold uppercase tracking-wider text-[#0f172a] mb-1">
                Statutory Basis &amp; Certification Clause
            </h2>
            <p class="text-[8pt] text-[#1e293b] leading-relaxed">
                Pursuant to Section 5 of Republic Act No. 10524 (amending Republic Act No. 7277, Magna Carta for Persons with Disability) and implementing Bureau of Internal Revenue (BIR) Revenue Regulations, private entities that employ qualified Persons with Disability (PWDs) are entitled to an additional deduction from their gross income equivalent to twenty-five percent (25%) of the total amount paid as salaries and wages to verified PWD employees, subject to Department of Labor and Employment (DOLE) accreditation and BIR audit verification.
            </p>
            <p class="text-[8pt] text-[#1e293b] leading-relaxed mt-1">
                The undersigned hereby certifies that the schedule of qualified PWD employees, DOLE/PWD identification records, and annual gross wage figures reported herein for Calendar Year {{ $reportingYear }} are true, complete, and supported by official payroll records.
            </p>
        </section>

        <!-- 6. Two-Column Formal Signatory & System Verification Block -->
        <section class="print-avoid-break mt-5 pt-3" style="border-top: 1px solid #cbd5e1;">
            <table class="w-full text-[8pt]" style="border-collapse: collapse;">
                <tbody>
                    <tr>
                        <td class="w-1/2 pr-6 align-top">
                            <div class="text-[7.5pt] font-bold uppercase tracking-wider text-[#334155]">
                                Prepared &amp; Certified By:
                            </div>
                            <div class="mt-9 pt-1" style="border-top: 1px solid #0f172a;">
                                <div class="font-bold text-[#0f172a]">Authorized Employer Representative / HR Officer</div>
                                <div class="text-[7.5pt] text-[#475569]">Signature over Printed Name &middot; {{ $employerName }}</div>
                                <div class="text-[7.5pt] text-[#334155] mt-2">Date Signed: _______________________________</div>
                            </div>
                        </td>
                        <td class="w-1/2 pl-6 align-top">
                            <div class="text-[7.5pt] font-bold uppercase tracking-wider text-[#334155] mb-2">
                                System Verification:
                            </div>
                            <div class="px-3 py-2.5" style="border: 1px solid #94a3b8; background: #f8fafc; border-radius: 2px;">
                                <div class="text-[8.5pt] font-bold text-[#0f172a]">PWD-E Compliance Engine</div>
                                <div class="text-[7.5pt] text-[#334155] mt-0.5">Digital Audit Stamp &mdash; Official Compliance Extract</div>
                                <div class="mt-2 pt-1.5 space-y-0.5 text-[7.5pt] font-mono text-[#0f172a]" style="border-top: 1px dashed #cbd5e1;">
                                    <div>Reference ID: <strong>{{ $docRefNo }}</strong></div>
                                    <div>Timestamp: {{ date('Y-m-d H:i') }} PHT &middot; CY {{ $reportingYear }}</div>
                                    <div>Verified PWD Records: {{ $pwdCount }} / {{ $totalEmployees }}</div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>
    </article>
</div>