<?php

namespace App\Transformers;

class ReverseStringTransformer implements TransformerInterface
{
    public function transform(mixed $value): mixed
    {
        return strrev((string) $value);
    }
}
