<?php

namespace App\Models;

use App\Enums\{MappingCategory, MappingSource, MappingType};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Mapping
 *
 * @property string $code
 * @property string $path
 * @property MappingSource $source
 * @property string $title
 * @property MappingType $type
 * @property string $default
 * @property string $remarks
 *
 * @method int getKey()
 */
class Mapping extends Model
{
    /** @use HasFactory<\Database\Factories\MappingFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'path',
        'source',
        'title',
        'type',
        'default',
        'category',
        'transformer',
        'options',
        'remarks',
    ];

    protected $casts = [
        'source' => MappingSource::class,
        'type' => MappingType::class,
        'category' => MappingCategory::class,
        'options' => 'array'
    ];
}
