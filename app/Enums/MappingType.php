<?php

namespace App\Enums;

use Brick\Math\Exception\MathException;
use Homeful\Common\Traits\EnumUtils;
use Brick\Math\RoundingMode;
use Brick\Money\Money;

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

    /**
     * @throws MathException
     */
    public function castValue(mixed $value): mixed
    {
        return match ($this) {
            self::STRING => (string) $value,
            self::INTEGER => $value instanceof Money
                ? $value->getAmount()->toScale(0, RoundingMode::UP)->toInt()  // Safely convert to integer
                : (int) $value,
            self::FLOAT => $value instanceof Money ? $value->getAmount()->toFloat() : (float) $value,
            self::ARRAY => is_array($value) ? $value : json_decode($value, true) ?? [],
            self::JSON => json_encode($value),
        };
    }

    public function toType(): string
    {
        return match($this) {
            self::INTEGER => 'toInt',
            self::FLOAT => 'toFloat',
            default => 'toBigInt'
        };
    }
}
