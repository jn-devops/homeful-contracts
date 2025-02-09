<?php

namespace App\Enums;

use Homeful\Common\Traits\EnumUtils;

enum MappingCategory: string
{
    use EnumUtils;

    case BUYER = 'buyer';
    case SPOUSE = 'spouse';
    case EMPLOYMENT = 'employment';
    case ADDRESS = 'address';
    case PROPERTY = 'property';

    static function default(): self {
        return self::BUYER;
    }
}
