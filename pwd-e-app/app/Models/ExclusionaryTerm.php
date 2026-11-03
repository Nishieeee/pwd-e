<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExclusionaryTerm extends Model
{
    protected $fillable = ['phrase', 'category', 'suggestion'];
}
