<?php

namespace App\Transformers;

interface TransformerInterface
{
    /**
     * Transform the given value.
     *
     * @param mixed $value
     * @return mixed
     */
    public function transform(mixed $value): mixed;
}
