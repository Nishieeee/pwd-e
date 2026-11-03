<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use SoftDeletes;

    protected $fillable = ['candidate_profile_id', 'job_posting_id', 'status', 'fsl_video_path', 'fsl_caption_path', 'fit_score_at_apply', 'applied_at'];

    protected $casts = [
        'fit_score_at_apply' => 'decimal:2',
        'applied_at' => 'datetime',
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
