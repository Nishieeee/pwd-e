<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessibilityLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['page', 'metric', 'details', 'created_at'];

    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime',
    ];
}
