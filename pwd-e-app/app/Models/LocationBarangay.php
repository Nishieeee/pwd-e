<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationBarangay extends Model
{
    protected $fillable = [
        'psgc_code',
        'location_city_id',
        'city_psgc_code',
        'name',
        'urban_rural',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(LocationCity::class, 'location_city_id');
    }
}
