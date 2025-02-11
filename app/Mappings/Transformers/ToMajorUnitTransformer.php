<?php

namespace App\Mappings\Transformers;

use Brick\Math\RoundingMode;
use Brick\Money\Money;
use League\Fractal\TransformerAbstract;

class ToMajorUnitTransformer extends TransformerAbstract
{
    public function transform(array $data): array
    {
        return [
            'value' => Money::ofMinor($data['value'], 'PHP', roundingMode: RoundingMode::UP),
        ];
    }
}
