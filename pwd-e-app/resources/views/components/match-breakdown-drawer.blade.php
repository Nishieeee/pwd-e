@props([
    'breakdown',
    'buttonLabel' => 'View Match Breakdown',
])

<div x-data="{ openBreakdown: false }" class="w-full">
    <button
        type="button"
        @click="openBreakdown = !openBreakdown"
        :aria-expanded="openBreakdown.toString()"
        class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#244F73] hover:text-[#1A2840] hover:underline focus:outline-none focus:ring-2 focus:ring-[#244F73] rounded px-1 py-0.5 cursor-pointer"
    >
        <svg class="w-3.5 h-3.5 transition-transform duration-150" :class="openBreakdown ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
        <span x-text="openBreakdown ? 'Hide Match Breakdown' : '{{ $buttonLabel }}'">{{ $buttonLabel }}</span>
    </button>

    <div
        x-show="openBreakdown"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="mt-3 rounded-xl border border-[#B8D0E2] bg-[#F8FAFC] p-4 space-y-3.5 text-left"
        role="region"
        aria-label="Detailed skill, achievement, and accessibility match breakdown"
    >
        <!-- Top 2-Dimension Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            <div class="rounded-lg border border-emerald-200 bg-emerald-50/70 px-3 py-2.5 flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wide text-emerald-800">Accessibility Fit</div>
                    <div class="text-xs text-emerald-900">Workplace accommodations</div>
                </div>
                <span class="text-lg font-bold text-emerald-800 tabular-nums">{{ $breakdown['accessibility_pct'] }}%</span>
            </div>

            <div class="rounded-lg border border-[#B8D0E2] bg-white px-3 py-2.5 flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wide text-[#244F73]">Skills &amp; Achievements</div>
                    <div class="text-xs text-gray-600">Résumé, trainings &amp; credentials</div>
                </div>
                <span class="text-lg font-bold text-[#1A2840] tabular-nums">{{ $breakdown['skills_achievement_pct'] }}%</span>
            </div>
        </div>

        <!-- 4 Pillar Rows -->
        <div class="space-y-2.5">
            @foreach ($breakdown['pillars'] as $pillar)
                <div class="rounded-lg border border-gray-200 bg-white p-3 space-y-1.5">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="text-xs font-bold text-[#1A2840]">{{ $pillar['label'] }}</span>
                        <div class="flex items-center gap-2">
                            <span class="rounded-full bg-[#DCEAF4]/70 px-2 py-0.5 text-[10px] font-bold text-[#1A2840]">{{ $pillar['badge'] }}</span>
                            <span class="text-xs font-bold text-[#244F73] tabular-nums">{{ $pillar['pct'] }}%</span>
                        </div>
                    </div>

                    <div class="w-full h-1.5 rounded-full bg-gray-100 overflow-hidden" aria-hidden="true">
                        <div class="h-full rounded-full {{ $pillar['key'] === 'accommodations' ? 'bg-[#176B47]' : 'bg-[#244F73]' }}" style="width: {{ max(8, min(100, $pillar['pct'])) }}%"></div>
                    </div>

                    <p class="text-[11px] text-gray-600">{{ $pillar['summary'] }}</p>

                    @if (! empty($pillar['matched_items']) || ! empty($pillar['extra_items']))
                        <div class="flex flex-wrap gap-1.5 pt-1">
                            @foreach ($pillar['matched_items'] as $item)
                                <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 border border-emerald-200 px-2 py-0.5 text-[11px] font-medium text-emerald-900">
                                    <span aria-hidden="true">&#10003;</span>
                                    <span>{{ $item }}</span>
                                </span>
                            @endforeach
                            @foreach ($pillar['extra_items'] as $extra)
                                <span class="inline-flex items-center rounded-md bg-amber-50 border border-amber-200 px-2 py-0.5 text-[11px] font-medium text-amber-900">
                                    {{ $extra }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
