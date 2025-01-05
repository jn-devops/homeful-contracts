<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class MatchDescriptionData extends Data
{
    public function __construct(
        public float $income_requirement,
        public float $cash_out,
        public float $dp_amortization,
        public float $dp_term,
        public float $loan_amortization,
        public float $bp_term,
    ) {}
}
