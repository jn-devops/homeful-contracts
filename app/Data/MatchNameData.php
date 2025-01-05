<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class MatchNameData extends Data
{
    public function __construct(
        public string $name,//include project and brand
        public string $brand,
        public string $category,
    ) {}
}
