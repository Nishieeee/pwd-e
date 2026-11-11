<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CandidateProfile extends Model
{
    protected $fillable = ['user_id', 'headline', 'bio', 'location', 'skills', 'fsl_video_path', 'fsl_caption_path', 'resume_pdf_path', 'disability_type', 'disability_description', 'phone', 'date_of_birth', 'gender', 'visibility_settings'];

    protected $casts = [
        'skills' => 'array',
        'disability_type' => 'encrypted',
        'disability_description' => 'encrypted',
        'date_of_birth' => 'date',
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

    public function workExperiences(): HasMany
    {
        return $this->hasMany(CandidateWorkExperience::class)->orderByDesc('start_date');
    }

    public function education(): HasMany
    {
        return $this->hasMany(CandidateEducation::class)->orderByDesc('start_date');
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(CandidateCertification::class)->orderByDesc('issued_at');
    }

    public function accommodations(): BelongsToMany
    {
        return $this->belongsToMany(Accommodation::class, 'accessibility_needs')
            ->withPivot('is_required', 'visible_to_employers');
    }
}
