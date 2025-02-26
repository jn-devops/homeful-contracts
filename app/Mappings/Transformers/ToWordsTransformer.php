<?php

namespace App\Mappings\Transformers;

use Illuminate\Support\Arr;
use Brick\Money\Money;

class ToWordsTransformer extends BaseTransformer
{
    protected string $command = 'spell';

    public function transform(array $data): array
    {
        $value = Arr::get($data, 'value');
        $value = $value instanceof Money ? $value->getAmount()->toFloat() : (float) $value;

        return [
            'value' => convertNumberToWords($value),
        ];
    }
}
