<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CandidateProfile extends Model
{
    protected $fillable = [
        'user_id',
        'avatar_path',
        'suffix',
        'headline',
        'education_badge',
        'highest_education',
        'input_mode',
        'bio',
        'location',
        'address',
        'country',
        'province',
        'location_city_id',
        'city',
        'location_barangay_id',
        'barangay',
        'zip_code',
        'spoken_languages',
        'other_language',
        'skills',
        'fsl_video_path',
        'fsl_caption_path',
        'resume_pdf_path',
        'disability_type',
        'disability_types',
        'disability_description',
        'phone',
        'date_of_birth',
        'gender',
        'visibility_settings',
        'accommodation_notes',
    ];

    protected $casts = [
        'skills' => 'array',
        'spoken_languages' => 'array',
        'disability_type' => 'encrypted',
        'disability_types' => 'encrypted:array',
        'disability_description' => 'encrypted',
        'date_of_birth' => 'date',
        'visibility_settings' => 'array',
    ];

    /**
     * @return array<int, string>
     */
    public function resolvedDisabilityTypes(): array
    {
        if (is_array($this->disability_types) && ! empty($this->disability_types)) {
            return array_values(array_filter(array_map('trim', $this->disability_types)));
        }

        if (! empty($this->disability_type)) {
            return array_values(array_filter(array_map('trim', explode(',', (string) $this->disability_type))));
        }

        return [];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function locationCity(): BelongsTo
    {
        return $this->belongsTo(LocationCity::class, 'location_city_id');
    }

    public function locationBarangay(): BelongsTo
    {
        return $this->belongsTo(LocationBarangay::class, 'location_barangay_id');
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

    public function jobInvitations(): HasMany
    {
        return $this->hasMany(JobInvitation::class);
    }

    public function workforceRecords(): HasMany
    {
        return $this->hasMany(WorkforceRecord::class);
    }
}
