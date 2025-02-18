<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequirementMatrix extends Model
{
    protected $fillable = [
        'civil_status',
        'employment_status',
        'market_segment',
        'requirements',
    ];

    protected $casts = [
//        'requirements' => 'array',
    ];

}
