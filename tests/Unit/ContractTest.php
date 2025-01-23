<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Homeful\Contracts\Models\Contract;

uses(RefreshDatabase::class, WithFaker::class);

test('contracts has metadata', function () {
    dd(Contract::factory()->create());
});
