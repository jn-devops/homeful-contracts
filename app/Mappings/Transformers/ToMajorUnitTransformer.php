<?php

namespace App\Mappings\Transformers;

use Illuminate\Support\Number;
use Brick\Math\RoundingMode;
use Brick\Money\Money;

class ToMajorUnitTransformer extends BaseTransformer
{
    public function transform(array $data): array
    {
        return [
            'value' => Money::ofMinor($data['value'], Number::defaultCurrency(), roundingMode: RoundingMode::UP),
        ];
    }
}
