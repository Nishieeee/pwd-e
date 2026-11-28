<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountLink extends Model
{
    protected $fillable = [
        'employer_user_id',
        'candidate_user_id',
        'linked_by_user_id',
    ];

    public function employerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employer_user_id');
    }

    public function candidateUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'candidate_user_id');
    }

    public function linkedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'linked_by_user_id');
    }
}
