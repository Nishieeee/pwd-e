<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessibilityNeed extends Model
{
    protected $fillable = ['candidate_profile_id', 'accommodation_id', 'is_required', 'visible_to_employers'];

    protected $casts = [
        'is_required' => 'boolean',
        'visible_to_employers' => 'boolean',
    ];

    public function candidateProfile(): BelongsTo
    {
        return $this->belongsTo(CandidateProfile::class);
    }

    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }
}
