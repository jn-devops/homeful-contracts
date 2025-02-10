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
    case EVALUATION_SHEET = 'evaluation_sheet';
    case SYSTEM = 'system';

    static function default(): self {
        return self::BUYER;
    }
}
