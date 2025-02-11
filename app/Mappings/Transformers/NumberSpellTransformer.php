<?php

namespace App\Mappings\Transformers;

use Illuminate\Support\Number;
use Brick\Money\Money;

class NumberSpellTransformer extends BaseTransformer
{
    public function transform(array $data): array
    {
        $value = $data['value'];

        return [
            'value' => Number::spell($value instanceof Money ? $value->getAmount()->toFloat() : $value),
        ];
    }
}
