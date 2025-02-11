<?php

use App\Mappings\Pipelines\TransformPipe;
use App\Models\Mapping;

test('it resolves and applies transformers correctly', function () {
    $mapping = Mapping::factory()->make([
        'transformer' => 'UpperCase, ReverseString',
    ]);

    $pipe = new TransformPipe($mapping);

    $result = $pipe->handle('hello world', fn($value) => $value);

    expect($result)->toBe('DLROW OLLEH');  // Assuming transformers work as expected
});

test('currency transformer works', function () {
    $mapping = Mapping::factory()->make([
        'transformer' => 'ToMajorUnit, Currency?currency_symbol=₱',
    ]);

    $pipe = new TransformPipe($mapping);

    $result = $pipe->handle('100000', fn($value) => $value);

    expect($result)->toBe('₱1,000.00');  // Assuming transformers work as expected
});

test('international currency transformer works', function () {
    $mapping = Mapping::factory()->make([
        'transformer' => 'ToMajorUnit, Currency?currency_symbol=PHP',
    ]);

    $pipe = new TransformPipe($mapping);

    $result = $pipe->handle('100000', fn($value) => $value);

    expect($result)->toBe('PHP 1,000.00');  // Assuming transformers work as expected
});
