<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'company_type',
        'business_permit_number',
        'business_permit_path',
        'contact_phone',
        'verified_at',
        'industry',
        'location',
        'street_address',
        'country',
        'province',
        'city',
        'barangay',
        'zip_code',
        'about',
        'website',
        'accessibility_highlights',
        'workforce_total',
        'workforce_updated_at',
        'matrix_completed_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'workforce_updated_at' => 'datetime',
        'matrix_completed_at' => 'datetime',
        'accessibility_highlights' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobPostings(): HasMany
    {
        return $this->hasMany(JobPosting::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(EmployerBranch::class)->orderByDesc('is_headquarters')->orderBy('name');
    }

    public function accommodations(): BelongsToMany
    {
        return $this->belongsToMany(Accommodation::class, 'employer_accommodations');
    }

    public function workforceRecords(): HasMany
    {
        return $this->hasMany(WorkforceRecord::class);
    }

    public function taxIncentiveSnapshots(): HasMany
    {
        return $this->hasMany(TaxIncentiveSnapshot::class)->orderByDesc('snapshot_date');
    }

    public function jobInvitations(): HasMany
    {
        return $this->hasMany(JobInvitation::class);
    }

    public function isMatrixComplete(): bool
    {
        return $this->matrix_completed_at !== null;
    }

    public function getAboutSummaryAttribute(): string
    {
        if (! empty($this->about)) {
            return $this->about;
        }

        return 'A certified inclusive workplace committed to equal employment opportunities, reasonable accommodations, and barrier-free career growth for Persons with Disabilities (PWDs) under Philippine Republic Act No. 10524 and RA 7277.';
    }
}
