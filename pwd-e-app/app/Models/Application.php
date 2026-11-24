<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'candidate_profile_id',
        'job_posting_id',
        'reapplied_from_id',
        'status',
        'status_subtext',
        'cover_letter',
        'cover_letter_pdf_path',
        'employer_notes',
        'fsl_video_path',
        'fsl_caption_path',
        'fit_score_at_apply',
        'applied_at',
        'interview_at',
        'interview_location',
        'interview_url',
        'interview_accommodations',
        'interview_checklist',
        'interview_accepted_at',
        'offer_details',
        'offer_extended_at',
        'offer_accepted_at',
        'offer_declined_at',
        'offer_decline_reason',
        'application_materials',
        'resume_path',
        'rejection_reason',
        'rejected_at',
    ];

    protected $casts = [
        'cover_letter' => 'encrypted',
        'fit_score_at_apply' => 'decimal:2',
        'applied_at' => 'datetime',
        'interview_at' => 'datetime',
        'interview_accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'offer_extended_at' => 'datetime',
        'offer_accepted_at' => 'datetime',
        'offer_declined_at' => 'datetime',
        'offer_details' => 'array',
        'application_materials' => 'array',
        'interview_checklist' => 'array',
    ];

    public const STATUS_APPLIED = 'applied';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_SHORTLISTED = 'shortlisted';
    public const STATUS_INTERVIEW = 'interview';
    public const STATUS_OFFERED = 'offered';
    public const STATUS_OFFER_DECLINED = 'offer_declined';
    public const STATUS_HIRED = 'hired';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_WITHDRAWN = 'withdrawn';
    public const STATUS_CLOSED = 'closed';

    public const TERMINAL_STATUSES = [
        self::STATUS_HIRED,
        self::STATUS_REJECTED,
        self::STATUS_WITHDRAWN,
        self::STATUS_OFFER_DECLINED,
        self::STATUS_CLOSED,
    ];

    public const TRANSITIONS = [
        self::STATUS_APPLIED => [
            self::STATUS_UNDER_REVIEW,
            self::STATUS_SHORTLISTED,
            self::STATUS_INTERVIEW,
            self::STATUS_OFFERED,
            self::STATUS_REJECTED,
            self::STATUS_WITHDRAWN,
            self::STATUS_CLOSED,
        ],
        self::STATUS_UNDER_REVIEW => [
            self::STATUS_APPLIED,
            self::STATUS_SHORTLISTED,
            self::STATUS_INTERVIEW,
            self::STATUS_OFFERED,
            self::STATUS_REJECTED,
            self::STATUS_WITHDRAWN,
        ],
        self::STATUS_SHORTLISTED => [
            self::STATUS_UNDER_REVIEW,
            self::STATUS_INTERVIEW,
            self::STATUS_OFFERED,
            self::STATUS_REJECTED,
            self::STATUS_WITHDRAWN,
        ],
        self::STATUS_INTERVIEW => [
            self::STATUS_UNDER_REVIEW,
            self::STATUS_OFFERED,
            self::STATUS_REJECTED,
            self::STATUS_WITHDRAWN,
        ],
        self::STATUS_OFFERED => [
            self::STATUS_HIRED,
            self::STATUS_INTERVIEW,
            self::STATUS_OFFER_DECLINED,
            self::STATUS_REJECTED,
            self::STATUS_WITHDRAWN,
        ],
        self::STATUS_HIRED => [],
        self::STATUS_REJECTED => [],
        self::STATUS_WITHDRAWN => [],
        self::STATUS_OFFER_DECLINED => [],
        self::STATUS_CLOSED => [],
    ];

    public function canTransitionTo(string $targetStatus): bool
    {
        $current = $this->status;
        $allowed = self::TRANSITIONS[$current] ?? [];

        return in_array($targetStatus, $allowed, true);
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, self::TERMINAL_STATUSES, true);
    }

    public static function computeStatusSubtext(string $status, ?\Carbon\CarbonInterface $timestamp = null): string
    {
        $time = $timestamp ? $timestamp->format('M j, Y') : 'recently';

        return match ($status) {
            self::STATUS_APPLIED => 'Application submitted and under review',
            self::STATUS_UNDER_REVIEW => 'Under review by hiring team',
            self::STATUS_SHORTLISTED => 'Shortlisted for candidate review',
            self::STATUS_INTERVIEW => 'Interview scheduled · ' . $time,
            self::STATUS_OFFERED => 'Offer extended · Awaiting candidate decision',
            self::STATUS_HIRED => 'Offer accepted and hired · ' . $time,
            self::STATUS_OFFER_DECLINED => 'Offer declined · ' . $time,
            self::STATUS_REJECTED => 'Application declined',
            self::STATUS_WITHDRAWN, self::STATUS_CLOSED => 'Withdrawn by candidate · ' . $time,
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    public const CHECKLIST_ITEMS = [
        'audio_video_fsl' => [
            'label' => 'Audio & Video Lighting',
            'desc' => 'High-contrast lighting and clear camera framing verified for FSL interpretation or lip-reading.',
        ],
        'screen_reader_tasks' => [
            'label' => 'Task Materials Format',
            'desc' => 'Assessment materials verified in accessible plain text, HTML, or tagged PDF format.',
        ],
        'venue_accessibility' => [
            'label' => 'Platform & Route Access',
            'desc' => 'Virtual meeting link tested with assistive software or physical step-free route confirmed.',
        ],
        'pacing_rest_breaks' => [
            'label' => 'Pacing & Rest Breaks',
            'desc' => 'Agreed interview pacing, live captioning channel, or scheduled rest breaks ready.',
        ],
    ];

    public function getChecklistStatusAttribute(): array
    {
        $stored = $this->interview_checklist ?? [];
        $result = [];
        foreach (self::CHECKLIST_ITEMS as $key => $meta) {
            $result[$key] = [
                'key' => $key,
                'label' => $meta['label'],
                'desc' => $meta['desc'],
                'checked' => ! empty($stored[$key]['checked']),
                'checked_at' => $stored[$key]['checked_at'] ?? null,
            ];
        }

        return $result;
    }

    public function isInterviewChecklistComplete(): bool
    {
        $status = $this->checklist_status;
        foreach ($status as $item) {
            if (! $item['checked']) {
                return false;
            }
        }

        return count($status) > 0;
    }

    public function isInterviewAccepted(): bool
    {
        return $this->interview_accepted_at !== null;
    }

    public function googleCalendarUrl(): string
    {
        $start = $this->interview_at ?? now()->addDay();
        $end = (clone $start)->addHour();

        $title = urlencode("Interview: {$this->jobPosting?->title} with {$this->jobPosting?->employerProfile?->company_name}");
        $dates = $start->format('Ymd\THis\Z') . '/' . $end->format('Ymd\THis\Z');
        $details = urlencode("Accessible Interview for {$this->jobPosting?->title}.\nMeeting URL: {$this->interview_url}\nAccommodations: {$this->interview_accommodations}");
        $location = urlencode($this->interview_url ?: ($this->interview_location ?: 'Accessible Virtual Room'));

        return "https://calendar.google.com/calendar/render?action=TEMPLATE&text={$title}&dates={$dates}&details={$details}&location={$location}";
    }

    public function interviewChecklistProgress(): array
    {
        $status = $this->checklist_status;
        $completed = 0;
        foreach ($status as $item) {
            if ($item['checked']) {
                $completed++;
            }
        }

        return [
            'completed' => $completed,
            'total' => count($status),
            'percent' => count($status) > 0 ? (int) round(($completed / count($status)) * 100) : 0,
        ];
    }

    public function candidateProfile(): BelongsTo
    {
        return $this->belongsTo(CandidateProfile::class);
    }

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ApplicationMessage::class)->orderBy('created_at', 'asc');
    }

    public function reappliedFrom(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reapplied_from_id');
    }

    public function reapplications(): HasMany
    {
        return $this->hasMany(self::class, 'reapplied_from_id');
    }

    public function workforceRecord(): HasOne
    {
        return $this->hasOne(WorkforceRecord::class);
    }

    public function unreadMessagesFor(?User $user): int
    {
        if (! $user) {
            return 0;
        }

        return $this->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->count();
    }
}
