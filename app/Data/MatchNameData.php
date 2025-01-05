<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class MatchNameData extends Data
{
    public function __construct(
        public float $selling_price,
        public string $housing_type,
        public float $floor_area,
    ) {}
}
