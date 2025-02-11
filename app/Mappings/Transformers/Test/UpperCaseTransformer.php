<?php

namespace App\Mappings\Transformers\Test;

use League\Fractal\TransformerAbstract;

class UpperCaseTransformer extends TransformerAbstract
{
    public function transform(array $data): array
    {
        return [
            'value' => strtoupper($data['value']),
        ];
    }
}
