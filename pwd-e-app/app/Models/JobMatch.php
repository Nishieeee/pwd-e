<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobMatch extends Model
{
    protected $table = 'matches';

    protected $fillable = ['candidate_profile_id', 'job_posting_id', 'accessibility_score', 'skills_score', 'total_score', 'computed_at'];

    protected $casts = [
        'accessibility_score' => 'decimal:2',
        'skills_score' => 'decimal:2',
        'total_score' => 'decimal:2',
        'computed_at' => 'datetime',
    ];

    public function candidateProfile(): BelongsTo
    {
        return $this->belongsTo(CandidateProfile::class);
    }

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }
}
