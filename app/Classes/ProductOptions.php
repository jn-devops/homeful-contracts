<?php

namespace App\Classes;

use App\Data\{MatchDescriptionData, MatchNameData};
use Homeful\Common\Classes\OptionsType;
use Illuminate\Support\Arr;

class ProductOptions extends OptionsType
{
    protected static array $matches;

    public static function setMatches(array $matches): void
    {
        static::$matches = $matches;
    }

    public static function records(array $filter = []): array
    {
        $recs = [];
        foreach (static::$matches as $match) {
            $key = Arr::get($match, 'property.sku');
            $name = MatchNameData::from(Arr::get($match, 'property'))->toJson();
            tap(new ProductOptions($key, $name), function (ProductOptions $rec) use (&$recs, $key, $match) {
                $data = MatchDescriptionData::from($match);
                $rec->description($data->toJson());
                $recs[$key] = $rec->jsonSerialize();
            });
        }

        return $recs;
    }
}
