<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LocationCity extends Model
{
    protected $fillable = [
        'psgc_code',
        'country',
        'region_code',
        'region_name',
        'province_code',
        'province_name',
        'name',
        'city_class',
        'zip_code',
    ];

    public function locationCountry(): BelongsTo
    {
        return $this->belongsTo(LocationCountry::class, 'country', 'name');
    }

    public function barangays(): HasMany
    {
        return $this->hasMany(LocationBarangay::class, 'location_city_id')->orderBy('name');
    }
}
