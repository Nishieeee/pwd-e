<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkforceRecord extends Model
{
    protected $fillable = ['employer_profile_id', 'user_id', 'hired_at', 'pwd_verified'];

    protected $casts = [
        'hired_at' => 'datetime',
        'pwd_verified' => 'boolean',
    ];

    public function employerProfile(): BelongsTo
    {
        return $this->belongsTo(EmployerProfile::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
