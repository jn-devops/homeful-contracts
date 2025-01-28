<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use App\Models\Contract;

uses(
//    RefreshDatabase::class,
    WithFaker::class);

test('contracts has metadata', function () {
dd(Contract::all()->first());
    dd(Contract::factory()->create());

});
