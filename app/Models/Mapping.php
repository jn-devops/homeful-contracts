<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
 * @property string $transformer
 * @property array $options
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

    protected $appends = [
        'disabled',
        'deprecated'
    ];

    public function setDisabledAttribute(bool $value): self
    {
        $this->setAttribute('disabled_at', $value ? now() : null);

        return $this;
    }

    public function getDisabledAttribute(): bool
    {
        return $this->getAttribute('disabled_at')
            && $this->getAttribute('disabled_at') <= now();
    }

    public function setDeprecatedAttribute(bool $value): self
    {
        $this->setAttribute('deprecated_at', $value ? now() : null);

        return $this;
    }

    public function getDeprecatedAttribute(): bool
    {
        return $this->getAttribute('deprecated_at')
            && $this->getAttribute('deprecated_at') <= now();
    }

    public function scopeNotDeprecated($query)
    {
        return $query->whereNull('deprecated_at')
            ->orWhere('deprecated_at', '>', now());
    }

    public function scopeNotDisabled($query)
    {
        return $query->whereNull('disabled_at')
            ->orWhere('disabled_at', '>', now());
    }
}
