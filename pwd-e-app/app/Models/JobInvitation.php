<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobInvitation extends Model
{
    protected $fillable = [
        'employer_profile_id',
        'job_posting_id',
        'candidate_profile_id',
        'status',
        'personal_message',
        'withdrawn_reason',
        'withdrawn_at',
    ];

    protected $casts = [
        'withdrawn_at' => 'datetime',
    ];

    public function employerProfile(): BelongsTo
    {
        return $this->belongsTo(EmployerProfile::class);
    }

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function candidateProfile(): BelongsTo
    {
        return $this->belongsTo(CandidateProfile::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isWithdrawn(): bool
    {
        return $this->status === 'withdrawn';
    }

    public function withdraw(string $reason): bool
    {
        return $this->update([
            'status' => 'withdrawn',
            'withdrawn_reason' => $reason,
            'withdrawn_at' => now(),
        ]);
    }
}
