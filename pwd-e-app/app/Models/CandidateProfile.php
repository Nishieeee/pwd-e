<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CandidateProfile extends Model
{
    protected $fillable = ['user_id', 'headline', 'skills', 'bio', 'fsl_video_path', 'visibility_settings'];

    protected $casts = [
        'skills' => 'array',
        'visibility_settings' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function accessibilityNeeds(): HasMany
    {
        return $this->hasMany(AccessibilityNeed::class);
    }

    public function jobMatches(): HasMany
    {
        return $this->hasMany(JobMatch::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function accommodations(): BelongsToMany
    {
        return $this->belongsToMany(Accommodation::class, 'accessibility_needs')
            ->withPivot('is_required', 'visible_to_employers');
    }
}
