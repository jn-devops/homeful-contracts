<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use Illuminate\Support\Number;
use Brick\Money\Money;

class NumberSpellTransformer extends TransformerAbstract
{
    public function transform(array $data): array
    {
        $value = $data['value'];

        return [
            'value' => Number::spell($value instanceof Money ? $value->getAmount()->toFloat() : $value),
        ];
    }
}
