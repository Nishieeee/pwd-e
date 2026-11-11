<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployerProfile extends Model
{
    protected $fillable = ['user_id', 'company_name', 'business_permit_number', 'business_permit_path', 'verified_at', 'industry', 'location', 'workforce_total', 'workforce_updated_at', 'matrix_completed_at'];

    protected $casts = [
        'verified_at' => 'datetime',
        'workforce_updated_at' => 'datetime',
        'matrix_completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobPostings(): HasMany
    {
        return $this->hasMany(JobPosting::class);
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

    public function isMatrixComplete(): bool
    {
        return $this->matrix_completed_at !== null;
    }
}
