<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'candidate_profile_id',
        'job_posting_id',
        'status',
        'status_subtext',
        'cover_letter',
        'employer_notes',
        'fsl_video_path',
        'fsl_caption_path',
        'fit_score_at_apply',
        'applied_at',
        'interview_at',
        'interview_location',
        'interview_url',
        'interview_accommodations',
        'offer_details',
        'application_materials',
        'resume_path',
    ];

    protected $casts = [
        'cover_letter' => 'encrypted',
        'fit_score_at_apply' => 'decimal:2',
        'applied_at' => 'datetime',
        'interview_at' => 'datetime',
        'offer_details' => 'array',
        'application_materials' => 'array',
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
