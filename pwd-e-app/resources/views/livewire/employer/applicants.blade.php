<div class="space-y-6">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-primary-dark">{{ __('Applicants') }} — {{ $job->title }}</h1>
            <p class="text-sm text-body-muted">{{ $job->employerProfile?->company_name }} · {{ $applications->count() }} {{ __('total') }}</p>
        </div>
        <a href="{{ route('employer.jobs.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-primary-border bg-white px-4 py-2 text-sm font-semibold text-primary-dark hover:bg-primary-tint">
            {{ __('Back to postings') }}
        </a>
    </div>

    <div class="rounded-2xl border border-primary-border bg-white shadow-sm">
        <table class="w-full text-sm">
            <caption class="sr-only">{{ __('Applicants for') }} {{ $job->title }}</caption>
            <thead>
                <tr class="border-b border-primary-border text-left text-xs uppercase tracking-wide text-body-muted">
                    <th scope="col" class="px-4 py-3">{{ __('Candidate') }}</th>
                    <th scope="col" class="px-4 py-3">{{ __('Fit score') }}</th>
                    <th scope="col" class="px-4 py-3">{{ __('Status') }}</th>
                    <th scope="col" class="px-4 py-3">{{ __('Applied') }}</th>
                    <th scope="col" class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary-border/60">
                @forelse ($applications as $app)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-primary-dark">{{ $app->candidateProfile?->user?->name ?? '—' }}</div>
                            <div class="text-xs text-body-muted">{{ $app->candidateProfile?->headline }}</div>
                        </td>
                        <td class="px-4 py-3 tabular-nums font-semibold text-primary-dark">{{ $app->fit_score_at_apply !== null ? number_format($app->fit_score_at_apply, 0).'%' : '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold
                                {{ $app->status === 'hired' ? 'bg-secondary-tint text-secondary' : ($app->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-primary-tint text-primary-dark') }}">
                                {{ ucfirst(str_replace('_', ' ', $app->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-body-muted">{{ $app->applied_at?->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex flex-wrap items-center gap-2">
                                <button type="button" wire:click="viewDetails({{ $app->id }})" class="rounded-lg border border-primary-border bg-white px-3 py-1.5 text-xs font-semibold text-primary-dark hover:bg-primary-tint">
                                    {{ __('View details') }}
                                </button>
                                <select wire:change="updateStatus({{ $app->id }}, $event.target.value)" class="rounded-lg border border-primary-border bg-white px-2 py-1 text-xs">
                                    @foreach (['applied','under_review','interview','hired','rejected'] as $st)
                                        <option value="{{ $st }}" @selected($app->status === $st)>{{ ucfirst(str_replace('_', ' ', $st)) }}</option>
                                    @endforeach
                                </select>
                                @if ($app->status !== 'hired')
                                    <button type="button" wire:click="hire({{ $app->id }})" wire:confirm="Mark as hired and create workforce record?" class="rounded-lg bg-secondary px-3 py-1.5 text-xs font-semibold text-white hover:bg-secondary/90">
                                        {{ __('Hire') }}
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-body-muted">{{ __('No applicants yet.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($viewing)
        @php $cp = $viewing->candidateProfile; $u = $cp?->user; @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" wire:click="closeDetails" role="dialog" aria-modal="true" aria-label="{{ __('Applicant details') }}">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 shadow-xl" wire:click.stop>
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div>
                        <h2 class="font-heading text-xl font-bold text-primary-dark">{{ $u?->name ?? '—' }}</h2>
                        <p class="text-sm text-body-muted">{{ $cp?->headline }} · {{ $cp?->location }}</p>
                        <p class="text-xs text-body-muted mt-1">{{ $u?->email }}</p>
                    </div>
                    <button type="button" wire:click="closeDetails" class="rounded-lg border border-primary-border bg-white px-3 py-1.5 text-xs font-semibold text-primary-dark hover:bg-primary-tint">
                        {{ __('Close') }}
                    </button>
                </div>

                <div class="space-y-4 text-sm">
                    <div>
                        <h3 class="font-semibold text-primary-dark mb-1">{{ __('Fit score') }}</h3>
                        <p class="text-body-muted">{{ $viewing->fit_score_at_apply !== null ? number_format($viewing->fit_score_at_apply, 0).'%' : '—' }} · {{ __('Status') }}: {{ ucfirst(str_replace('_', ' ', $viewing->status)) }}</p>
                    </div>

                    @if ($viewing->cover_letter)
                        <div>
                            <h3 class="font-semibold text-primary-dark mb-1">{{ __('Cover letter') }}</h3>
                            <p class="text-body-muted whitespace-pre-line">{{ $viewing->cover_letter }}</p>
                        </div>
                    @endif

                    <div class="flex flex-wrap gap-3">
                        @if ($viewing->candidateProfile?->resume_pdf_path)
                            <a href="{{ route('employer.applicants.document', [$viewing, 'resume']) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg border border-primary-border bg-white px-4 py-2 text-sm font-semibold text-primary-dark hover:bg-primary-tint">
                                {{ __('View résumé (PDF)') }}
                            </a>
                        @endif
                        @if ($viewing->fsl_video_path || $viewing->candidateProfile?->fsl_video_path)
                            <a href="{{ route('employer.applicants.document', [$viewing, 'fsl']) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg border border-primary-border bg-white px-4 py-2 text-sm font-semibold text-primary-dark hover:bg-primary-tint">
                                {{ __('View FSL video') }}
                            </a>
                        @endif
                    </div>

                    @if ($cp?->skills)
                        <div>
                            <h3 class="font-semibold text-primary-dark mb-1">{{ __('Skills') }}</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($cp->skills as $skill)
                                    <span class="rounded-full bg-primary-tint px-2.5 py-0.5 text-xs font-semibold text-primary-dark">{{ $skill }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($cp?->disability_type)
                        <div>
                            <h3 class="font-semibold text-primary-dark mb-1">{{ __('Disability type') }}</h3>
                            <p class="text-body-muted">{{ $cp->disability_type }}</p>
                        </div>
                    @endif

                    @if ($cp?->workExperiences?->isNotEmpty())
                        <div>
                            <h3 class="font-semibold text-primary-dark mb-1">{{ __('Work experience') }}</h3>
                            <ul class="space-y-2">
                                @foreach ($cp->workExperiences as $exp)
                                    <li class="rounded-lg border border-primary-border/60 p-3">
                                        <div class="font-semibold text-primary-dark">{{ $exp->job_title }}</div>
                                        <div class="text-xs text-body-muted">{{ $exp->company }} · {{ $exp->start_date }} – {{ $exp->end_date ?? __('Present') }}</div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($cp?->education?->isNotEmpty())
                        <div>
                            <h3 class="font-semibold text-primary-dark mb-1">{{ __('Education') }}</h3>
                            <ul class="space-y-2">
                                @foreach ($cp->education as $edu)
                                    <li class="rounded-lg border border-primary-border/60 p-3">
                                        <div class="font-semibold text-primary-dark">{{ $edu->degree }} — {{ $edu->school }}</div>
                                        <div class="text-xs text-body-muted">{{ $edu->year }}</div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>