<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProjects extends Model
{
    protected $fillable = [
        'user_id',
        'project_code',
    ];
}
