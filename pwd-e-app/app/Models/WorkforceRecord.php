<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkforceRecord extends Model
{
    protected $fillable = ['employer_profile_id', 'candidate_profile_id', 'application_id', 'hired_at', 'ended_at', 'pwd_verified', 'annual_wage'];

    protected $casts = [
        'hired_at' => 'date',
        'ended_at' => 'date',
        'pwd_verified' => 'boolean',
        'annual_wage' => 'decimal:2',
    ];

    public function employerProfile(): BelongsTo
    {
        return $this->belongsTo(EmployerProfile::class);
    }

    public function candidateProfile(): BelongsTo
    {
        return $this->belongsTo(CandidateProfile::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
