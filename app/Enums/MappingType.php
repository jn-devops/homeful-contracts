<?php

namespace App\Enums;

use Homeful\Common\Traits\EnumUtils;

enum MappingType: string
{
    use EnumUtils;

    case STRING = 'string';
    case INTEGER = 'integer';
    case FLOAT = 'float';
    case ARRAY = 'array';
    case JSON = 'json';

    static function default(): self {
        return self::STRING;
    }

    public function castValue(mixed $value): mixed
    {
        return match ($this) {
            self::STRING => (string) $value,
            self::INTEGER => (int) $value,
            self::FLOAT => (float) $value,
            self::ARRAY => is_array($value) ? $value : json_decode($value, true) ?? [],
            self::JSON => json_encode($value),
            default => $value,
        };
    }
}
