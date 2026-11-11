<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxIncentiveSnapshot extends Model
{
    protected $fillable = [
        'employer_profile_id', 'snapshot_date', 'pwd_count',
        'total_employees', 'deduction_basis', 'report_path', 'generated_by',
    ];

    protected $casts = [
        'snapshot_date' => 'date',
        'deduction_basis' => 'array',
    ];

    public function employerProfile(): BelongsTo
    {
        return $this->belongsTo(EmployerProfile::class);
    }
}
