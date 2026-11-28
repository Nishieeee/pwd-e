<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateCertification extends Model
{
    protected $fillable = [
        'candidate_profile_id',
        'category',
        'name',
        'issuing_organization',
        'year_obtained',
        'issued_at',
        'expires_at',
        'credential_id',
        'credential_url',
        'file_path',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'expires_at' => 'date',
    ];

    public function candidateProfile(): BelongsTo
    {
        return $this->belongsTo(CandidateProfile::class);
    }
}
