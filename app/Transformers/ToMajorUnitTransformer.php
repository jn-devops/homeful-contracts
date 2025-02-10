<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use Brick\Math\RoundingMode;
use Brick\Money\Money;

class ToMajorUnitTransformer extends TransformerAbstract
{
    public function transform(array $data): array
    {
        return [
            'value' => Money::ofMinor($data['value'], 'PHP', roundingMode: RoundingMode::UP),
        ];
    }
}
