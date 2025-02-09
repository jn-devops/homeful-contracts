<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payload extends Model
{
    /** @use HasFactory<\Database\Factories\PayloadFactory> */
    use HasFactory;

    protected $fillable = [
        'mapping_code',
        'value'
    ];

    public function mapping(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Mapping::class, 'mapping_code', 'code');
    }
}
