<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class MatchDescriptionData extends Data
{
    public function __construct(
        public float $dp_amortization,
        public float $dp_term,
        public float $partial_miscellaneous_fees,
        public float $loan_amortization,
        public float $bp_term,
        public float $down_payment,
        public array $property,
    ) {}
}
