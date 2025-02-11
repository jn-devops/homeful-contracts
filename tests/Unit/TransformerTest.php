<?php

use App\Mappings\Transformers\TitleCaseTransformer;
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
        'transformer' => 'ToMajorUnit, Currency?symbol=₱&decimals=1',
    ]);

    $pipe = new TransformPipe($mapping);

    $result = $pipe->handle('100000', fn($value) => $value);

    expect($result)->toBe('₱1,000.0');  // Assuming transformers work as expected
});

test('international currency transformer works', function () {
    $mapping = Mapping::factory()->make([
        'transformer' => 'ToMajorUnit, Currency?symbol=PHP',
    ]);

    $pipe = new TransformPipe($mapping);

    $result = $pipe->handle('100000', fn($value) => $value);

    expect($result)->toBe('PHP 1,000.00');  // Assuming transformers work as expected
});

test('it applies title case and preserves suffixes', function () {
    $transformer = new TitleCaseTransformer();

    // Test cases
    $testCases = [
        ['input' => '  JOHN DOE JR.  ', 'expected' => 'John Doe Jr.'],
        ['input' => 'JANE SMITH III', 'expected' => 'Jane Smith III'],
        ['input' => '  lester hurtado  ', 'expected' => 'Lester Hurtado'],
        ['input' => 'MICHAEL BROWN SR.', 'expected' => 'Michael Brown Sr.'],
        ['input' => 'CHRISTIAN SANTOS II', 'expected' => 'Christian Santos II'],
        ['input' => 'JUAN DELA CRUZ V', 'expected' => 'Juan Dela Cruz V'],
        ['input' => '  anaïs santos ', 'expected' => 'Anaïs Santos'],
        ['input' => 'KAREN DOE', 'expected' => 'Karen Doe'],
    ];

    foreach ($testCases as $case) {
        $result = $transformer->transform(['value' => $case['input']]);
        expect($result['value'])->toBe($case['expected']);
    }
});

test('it handles names with no suffixes correctly', function () {
    $transformer = new TitleCaseTransformer();

    $result = $transformer->transform(['value' => 'john smith']);
    expect($result['value'])->toBe('John Smith');
});

test('it trims leading and trailing spaces', function () {
    $transformer = new TitleCaseTransformer();

    $result = $transformer->transform(['value' => '   MARY JANE DOE   ']);
    expect($result['value'])->toBe('Mary Jane Doe');
});

test('it works with empty or already formatted input', function () {
    $transformer = new TitleCaseTransformer();

    expect($transformer->transform(['value' => '']))->toBe(['value' => '']);
    expect($transformer->transform(['value' => 'John Doe Jr.']))->toBe(['value' => 'John Doe Jr.']);
});
