<?php

namespace App\Transformers\Test;

use League\Fractal\TransformerAbstract;

class ReverseStringTransformer extends TransformerAbstract
{
    public function transform(array $data): array
    {
        return [
            'value' => strrev($data['value']),
        ];
    }
}
