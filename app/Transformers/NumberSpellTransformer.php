<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use Illuminate\Support\Number;

class NumberSpellTransformer extends TransformerAbstract
{
    public function transform(array $data): array
    {
        return [
            'value' => Number::spell($data['value']),
        ];
    }
}
