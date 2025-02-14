<?php

namespace App\Enums;

use Homeful\Common\Traits\EnumUtils;

enum MappingSource: string
{
    use EnumUtils;

    case ARRAY = 'array';
    case CONFIG = 'config';
    case ENVIRONMENT = 'environment';
    case CONTEXT = 'context';
    case SEARCH_PARAMS = 'search_params';
    case EVALUATION_SHEET = 'evaluation_sheet';
    case API = 'api';
    case MFILES = 'mfiles';

    static function default(): self {
        return self::ARRAY;
    }
}
