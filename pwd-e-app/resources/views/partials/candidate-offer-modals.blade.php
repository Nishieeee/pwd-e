{{-- Shared Candidate Multi-Offer Popup & Already-Employed Confirmation Modal (REV-14) --}}
@if (! empty($showMultiOfferPopup) && isset($concurrentOffers) && $concurrentOffers->count() >= 2)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="multi-offer-popup-title">
        <div class="w-full max-w-2xl rounded-2xl border border-gray-200 bg-white p-6 shadow-2xl space-y-4 my-8" wire:click.stop>
            <div class="flex items-start justify-between gap-3 border-b border-gray-100 pb-3">
                <div class="space-y-1">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200 px-3 py-0.5 text-xs font-bold text-emerald-800">
                        🎉 {{ __(':count Active Job Offers Waiting', ['count' => $concurrentOffers->count()]) }}
                    </span>
                    <h2 id="multi-offer-popup-title" class="font-heading text-lg font-bold text-[#1A2840]">
                        {{ __('Multiple Job Offers Received') }}
                    </h2>
                    <p class="text-xs text-gray-600">
                        {{ __('Review your concurrent job offers below. You may accept an offer or ignore it for now to decide later.') }}
                    </p>
                </div>
                <button
                    type="button"
                    wire:click="dismissMultiOfferModal"
                    class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-700 cursor-pointer"
                    aria-label="{{ __('Dismiss multiple offers notification') }}"
                >
                    &times;
                </button>
            </div>

            @if (! empty($multiOfferHasScheduleOverlap))
                <div class="rounded-xl border border-amber-300 bg-amber-50 p-3.5 text-xs text-amber-950 flex items-start gap-2.5" role="alert">
                    <span class="text-sm shrink-0" aria-hidden="true">&#9888;&#65039;</span>
                    <div>
                        <strong class="font-bold">{{ __('Schedule Overlap Detected:') }}</strong>
                        <span>{{ __('Two or more of these job offers share overlapping working days and shift hours. Check the working schedules carefully before accepting multiple roles.') }}</span>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 max-h-[60vh] overflow-y-auto pr-1">
                @foreach ($concurrentOffers as $cOffer)
                    @php
                        $details = $cOffer->offer_details ?? [];
                        $salaryVal = $details['salary'] ?? ($cOffer->jobPosting?->salary_max ?? $cOffer->jobPosting?->salary_min ?? 35000);
                        $periodLabel = ucfirst($details['pay_period'] ?? 'monthly');
                        $scheduleLabel = \App\Support\EmploymentScheduleHelper::formatSchedule($details);
                    @endphp
                    <div class="rounded-xl border border-gray-200 bg-[#F8FAFC] p-4 flex flex-col justify-between gap-3">
                        <div class="space-y-2">
                            <div>
                                <h3 class="font-bold text-sm text-[#1A2840]">{{ $cOffer->jobPosting?->title ?? __('Position') }}</h3>
                                <p class="text-xs font-semibold text-[#244F73]">{{ $cOffer->jobPosting?->employerProfile?->company_name ?? __('Verified Employer') }}</p>
                            </div>

                            <div class="rounded-lg bg-white border border-gray-200 p-2.5 text-xs space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">{{ __('Compensation:') }}</span>
                                    <span class="font-bold text-[#1A2840] tabular-nums">₱{{ number_format((float) $salaryVal, 2) }} / {{ strtolower($periodLabel) }}</span>
                                </div>
                                <div class="flex items-start justify-between gap-2">
                                    <span class="text-gray-500 shrink-0">{{ __('Schedule:') }}</span>
                                    <span class="font-semibold text-gray-800 text-right">{{ $scheduleLabel }}</span>
                                </div>
                                @if (! empty($details['start_date']))
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-500">{{ __('Start Date:') }}</span>
                                        <span class="text-gray-800">{{ $details['start_date'] }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-200/80">
                            <button
                                type="button"
                                wire:click="ignoreOfferInPopup({{ $cOffer->id }})"
                                class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-100 cursor-pointer"
                            >
                                {{ __('Ignore') }}
                            </button>
                            <button
                                type="button"
                                wire:click="acceptOffer({{ $cOffer->id }})"
                                class="rounded-lg bg-emerald-600 px-3.5 py-1.5 text-xs font-bold text-white hover:bg-emerald-700 shadow-2xs cursor-pointer"
                            >
                                {{ __('Accept') }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

@if (! empty($confirmingMultiJobApp))
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/55 p-4" role="dialog" aria-modal="true" aria-labelledby="confirm-multi-job-title">
        <div class="w-full max-w-md rounded-2xl border border-amber-200 bg-white p-6 shadow-2xl space-y-4" wire:click.stop>
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 id="confirm-multi-job-title" class="font-heading text-base font-bold text-[#1A2840]">
                        {{ __('You Already Have an Active Job') }}
                    </h3>
                    <p class="text-xs text-gray-600 mt-0.5 leading-relaxed">
                        {{ __('Are you sure you want to accept this additional job offer while currently employed?') }}
                    </p>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-[#F8FAFC] p-3.5 text-xs space-y-2">
                <div>
                    <span class="font-bold text-gray-500 uppercase text-[10px] tracking-wider block">{{ __('Current Active Employment') }}</span>
                    @foreach ($activeEmploymentsForConfirm as $actRec)
                        <div class="font-semibold text-[#1A2840] mt-0.5">
                            {{ $actRec->application?->jobPosting?->title ?? __('Active Role') }} · {{ $actRec->employerProfile?->company_name ?? __('Employer') }}
                        </div>
                        <div class="text-[11px] text-gray-600">
                            {{ \App\Support\EmploymentScheduleHelper::formatSchedule($actRec->application?->offer_details ?? []) }}
                        </div>
                    @endforeach
                </div>

                <div class="pt-2 border-t border-gray-200">
                    <span class="font-bold text-emerald-700 uppercase text-[10px] tracking-wider block">{{ __('New Offer Being Accepted') }}</span>
                    <div class="font-semibold text-[#1A2840] mt-0.5">
                        {{ $confirmingMultiJobApp->jobPosting?->title }} · {{ $confirmingMultiJobApp->jobPosting?->employerProfile?->company_name }}
                    </div>
                    <div class="text-[11px] text-gray-600">
                        {{ \App\Support\EmploymentScheduleHelper::formatSchedule($confirmingMultiJobApp->offer_details ?? []) }}
                    </div>
                </div>
            </div>

            @if (! empty($multiJobHasScheduleOverlap))
                <div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-900 font-medium" role="alert">
                    &#9888;&#65039; {{ __('Warning: This offer\'s working days and shift hours overlap with your current active job schedule.') }}
                </div>
            @endif

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                <button
                    type="button"
                    wire:click="cancelMultiJobConfirmation"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 cursor-pointer"
                >
                    {{ __('Ignore / Cancel') }}
                </button>
                <button
                    type="button"
                    wire:click="confirmAcceptAdditionalJob"
                    class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700 shadow-xs cursor-pointer"
                >
                    {{ __('Yes, Accept Additional Job') }}
                </button>
            </div>
        </div>
    </div>
@endif
