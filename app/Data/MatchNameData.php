<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class MatchNameData extends Data
{
    public function __construct(
        public string $name,
        public string $brand,
        public string $category,
    ) {}
}
