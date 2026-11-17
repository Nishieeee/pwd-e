<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LocationCountry extends Model
{
    protected $fillable = ['iso_code', 'name', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function cities(): HasMany
    {
        return $this->hasMany(LocationCity::class, 'country', 'name')->orderBy('name');
    }
}

