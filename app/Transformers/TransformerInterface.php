<?php

namespace App\Transformers;

/** @deprecated */
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
