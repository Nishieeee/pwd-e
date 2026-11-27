<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployerBranch extends Model
{
    protected $fillable = [
        'employer_profile_id',
        'name',
        'street_address',
        'country',
        'province',
        'city',
        'barangay',
        'zip_code',
        'contact_phone',
        'is_headquarters',
    ];

    protected $casts = [
        'is_headquarters' => 'boolean',
    ];

    public function employerProfile(): BelongsTo
    {
        return $this->belongsTo(EmployerProfile::class);
    }

    public function jobPostings(): HasMany
    {
        return $this->hasMany(JobPosting::class);
    }

    public function formattedAddress(): string
    {
        return implode(', ', array_filter([
            $this->street_address,
            $this->barangay,
            $this->city,
            $this->province,
            $this->country,
            $this->zip_code ? 'ZIP '.$this->zip_code : null,
        ]));
    }

    public function shortLocation(): string
    {
        return implode(', ', array_filter([
            $this->barangay,
            $this->city,
            $this->province,
            $this->country,
        ]));
    }
}
