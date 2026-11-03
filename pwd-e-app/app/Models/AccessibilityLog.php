<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessibilityLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'source', 'page', 'metric', 'severity', 'details', 'reviewed_by', 'reviewed_at', 'created_at'];

    protected $casts = [
        'details' => 'array',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
