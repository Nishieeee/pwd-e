<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkforceRecord extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employer_profile_id',
        'candidate_profile_id',
        'application_id',
        'hired_at',
        'ended_at',
        'ended_reason',
        'ended_notes',
        'separation_status',
        'separation_notice_at',
        'candidate_response_at',
        'candidate_dispute_reason',
        'resolution_notes',
        'resolved_at',
        'pwd_verified',
        'annual_wage',
    ];

    public function isActive(): bool
    {
        return $this->ended_at === null;
    }

    public function isEnded(): bool
    {
        return $this->ended_at !== null;
    }

    public function isSeparationPending(): bool
    {
        return $this->separation_status === 'pending_review';
    }

    public function isSeparationDisputed(): bool
    {
        return $this->separation_status === 'disputed';
    }

    public function isSeparationAcknowledged(): bool
    {
        return $this->separation_status === 'acknowledged';
    }

    protected $casts = [
        'hired_at' => 'date',
        'ended_at' => 'date',
        'separation_notice_at' => 'datetime',
        'candidate_response_at' => 'datetime',
        'resolved_at' => 'datetime',
        'pwd_verified' => 'boolean',
        'annual_wage' => 'decimal:2',
    ];

    public function employerProfile(): BelongsTo
    {
        return $this->belongsTo(EmployerProfile::class);
    }

    public function candidateProfile(): BelongsTo
    {
        return $this->belongsTo(CandidateProfile::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
