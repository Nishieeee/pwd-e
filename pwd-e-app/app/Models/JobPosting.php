<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobPosting extends Model
{
    use SoftDeletes;

    protected $fillable = ['employer_profile_id', 'title', 'description', 'required_skills', 'location', 'work_arrangement', 'employment_type', 'salary_min', 'salary_max', 'closes_at', 'status', 'scan_result', 'scanned_at', 'published_at'];

    protected $casts = [
        'required_skills' => 'array',
        'scan_result' => 'array',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'closes_at' => 'date',
        'scanned_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function employerProfile(): BelongsTo
    {
        return $this->belongsTo(EmployerProfile::class);
    }

    public function accommodations(): BelongsToMany
    {
        return $this->belongsToMany(Accommodation::class, 'job_accommodations');
    }

    public function jobAccommodations(): HasMany
    {
        return $this->hasMany(JobAccommodation::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(JobMatch::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function canPublish(): bool
    {
        return $this->scanned_at !== null && $this->employerProfile?->isMatrixComplete();
    }
}
