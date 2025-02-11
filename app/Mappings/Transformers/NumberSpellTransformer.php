<?php

namespace App\Mappings\Transformers;

use Brick\Money\Money;
use Illuminate\Support\Number;
use League\Fractal\TransformerAbstract;

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
