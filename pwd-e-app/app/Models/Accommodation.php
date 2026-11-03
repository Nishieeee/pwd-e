<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Accommodation extends Model
{
    protected $fillable = ['name', 'category'];

    public function accessibilityNeeds()
    {
        return $this->hasMany(AccessibilityNeed::class);
    }

    public function employerProfiles(): BelongsToMany
    {
        return $this->belongsToMany(EmployerProfile::class, 'employer_accommodations');
    }
}
