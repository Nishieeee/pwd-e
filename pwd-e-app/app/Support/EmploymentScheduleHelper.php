<?php

namespace App\Support;

use App\Models\WorkforceRecord;
use Illuminate\Support\Collection;

class EmploymentScheduleHelper
{
    public const ALL_DAYS = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

    /**
     * Format an offer_details schedule into a readable human string.
     */
    public static function formatSchedule(?array $offerDetails): string
    {
        if (empty($offerDetails)) {
            return 'Standard schedule (unspecified hours)';
        }

        $empType = ! empty($offerDetails['employment_type'])
            ? ucfirst(str_replace('_', '-', (string) $offerDetails['employment_type']))
            : null;

        if (! empty($offerDetails['is_flexible'])) {
            return trim(($empType ? $empType.' · ' : '').'Flexible / Output-based hours');
        }

        $days = is_array($offerDetails['work_days'] ?? null) ? array_values(array_filter($offerDetails['work_days'])) : [];
        $dayText = $days === ['Mon', 'Tue', 'Wed', 'Thu', 'Fri']
            ? 'Mon–Fri'
            : (! empty($days) ? implode(', ', $days) : 'Mon–Fri');

        $start = $offerDetails['shift_start'] ?? '';
        $end = $offerDetails['shift_end'] ?? '';
        $timeText = ($start && $end) ? self::formatTime($start).'–'.self::formatTime($end) : '';

        return trim(implode(' · ', array_filter([$empType, $dayText, $timeText])));
    }

    public static function formatTime(string $hhmm): string
    {
        $parts = explode(':', $hhmm);
        if (count($parts) < 2) {
            return $hhmm;
        }
        $h = (int) $parts[0];
        $m = $parts[1];
        $ampm = $h >= 12 ? 'PM' : 'AM';
        $h12 = $h % 12 ?: 12;

        return sprintf('%d:%s %s', $h12, substr($m, 0, 2), $ampm);
    }

    public static function toMinutes(?string $hhmm): ?int
    {
        if (! $hhmm || ! str_contains($hhmm, ':')) {
            return null;
        }
        [$h, $m] = explode(':', $hhmm, 2);

        return ((int) $h * 60) + (int) $m;
    }

    /**
     * Detect whether two structured offer_details schedules overlap on at least one day and time window.
     */
    public static function schedulesOverlap(?array $a, ?array $b): bool
    {
        if (empty($a) || empty($b) || ! empty($a['is_flexible']) || ! empty($b['is_flexible'])) {
            return false;
        }

        $daysA = is_array($a['work_days'] ?? null) && ! empty($a['work_days']) ? $a['work_days'] : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
        $daysB = is_array($b['work_days'] ?? null) && ! empty($b['work_days']) ? $b['work_days'] : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];

        if (empty(array_intersect($daysA, $daysB))) {
            return false;
        }

        $startA = self::toMinutes($a['shift_start'] ?? null);
        $endA = self::toMinutes($a['shift_end'] ?? null);
        $startB = self::toMinutes($b['shift_start'] ?? null);
        $endB = self::toMinutes($b['shift_end'] ?? null);

        if ($startA === null || $endA === null || $startB === null || $endB === null) {
            return false;
        }

        if ($endA <= $startA || $endB <= $startB) {
            return true;
        }

        return $startA < $endB && $startB < $endA;
    }

    /**
     * Active WorkforceRecords for a candidate profile.
     *
     * @return Collection<int, WorkforceRecord>
     */
    public static function activeEmployments(int $candidateProfileId): Collection
    {
        return WorkforceRecord::with(['employerProfile', 'application.jobPosting.branch'])
            ->where('candidate_profile_id', $candidateProfileId)
            ->whereNull('ended_at')
            ->where(function ($q) {
                $q->whereNull('separation_status')
                    ->orWhereIn('separation_status', ['active', 'pending_review', 'disputed']);
            })
            ->get();
    }

    /**
     * Build a privacy-preserving notice for an employer extending an offer to an already-hired candidate (REV-14).
     * Never exposes external employer names; reveals role/branch only if employed within the same employer_profile_id.
     *
     * @return array{has_active_jobs: bool, is_internal_only: bool, items: array<int, array{is_internal: bool, label: string, schedule: string, offer_details: array}>}|null
     */
    public static function buildEmployerOfferWarning(int $candidateProfileId, int $viewingEmployerProfileId): ?array
    {
        $records = self::activeEmployments($candidateProfileId);
        if ($records->isEmpty()) {
            return null;
        }

        $items = [];
        $allInternal = true;

        foreach ($records as $rec) {
            $isInternal = (int) $rec->employer_profile_id === $viewingEmployerProfileId;
            if (! $isInternal) {
                $allInternal = false;
            }

            $offerDetails = is_array($rec->application?->offer_details) ? $rec->application->offer_details : [];
            $schedule = self::formatSchedule($offerDetails);

            if ($isInternal) {
                $roleTitle = $rec->application?->jobPosting?->title ?? 'Active Team Role';
                $branchLabel = $rec->application?->jobPosting?->branch?->name
                    ?? ($offerDetails['branch_name'] ?? $rec->employerProfile?->company_name ?? 'Your Company');
                $label = "Employed at your company ({$branchLabel}) as {$roleTitle}";
            } else {
                // Strictly hide external company name and external job title!
                $label = 'Currently employed with another verified employer on PWD-E';
            }

            $items[] = [
                'is_internal' => $isInternal,
                'label' => $label,
                'schedule' => $schedule,
                'offer_details' => $offerDetails,
            ];
        }

        return [
            'has_active_jobs' => true,
            'is_internal_only' => $allInternal,
            'items' => $items,
        ];
    }
}
