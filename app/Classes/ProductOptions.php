<?php

namespace App\Classes;

use App\Data\{MatchDescriptionData, MatchNameData};
use Homeful\Common\Classes\OptionsType;
use Illuminate\Support\Arr;

/**
 * Usage:
 *
 * Set the static::$matches variable first then get the static::records() i.e.,
 *
 * $matches = ....
 * ProductOptions::setMatches($matches)
 * ProductOptions::records()
 *
 * This class is used to populate the button options in the Inertia component ButtonOptions.vue.
 * The key has to the product SKU. The name should come from the combination of properties of
 * of MatchNameData class - which can be edited to suit the requirement. And the description
 * should come from the properties of MatchDescriptionData. Both data classes rely on the
 * array response from Homeful Match i.e., MortgageData.
 *
 */
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
