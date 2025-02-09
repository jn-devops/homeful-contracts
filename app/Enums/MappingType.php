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
}
