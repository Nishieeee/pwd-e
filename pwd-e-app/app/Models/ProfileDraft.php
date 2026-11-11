<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileDraft extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'step', 'data', 'updated_at'];

    protected $casts = ['data' => 'array', 'updated_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
