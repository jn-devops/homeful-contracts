<?php

namespace App\Transformers;

class UpperCaseTransformer implements TransformerInterface
{
    /**
     * Transform the value to uppercase.
     *
     * @param mixed $value
     * @return mixed
     */
    public function transform(mixed $value): mixed
    {
        return is_string($value) ? strtoupper($value) : $value;
    }
}
