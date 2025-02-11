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
