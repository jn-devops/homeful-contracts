<?php

use App\Mappings\Transformers\ToMajorUnitTransformer;
use App\Mappings\Transformers\NumberSpellTransformer;
use App\Mappings\Transformers\UpperCaseTransformer;
use App\Mappings\Transformers\TitleCaseTransformer;
use App\Mappings\Transformers\CurrencyTransformer;
use App\Mappings\Transformers\ConcatTransformer;
use App\Mappings\Transformers\NumberTransformer;
use App\Enums\MappingTransformers;

test('MappingTransformers enum resolves transformer classes correctly', function () {
    $transformers = [
        'upper_case' => UpperCaseTransformer::class,
        'UpperCase' => UpperCaseTransformer::class,
        'UPPER_CASE' => UpperCaseTransformer::class,
        'title_case' => TitleCaseTransformer::class,
        'TitleCase' => TitleCaseTransformer::class,
        'currency' => CurrencyTransformer::class,
        'Currency' => CurrencyTransformer::class,
        'to_major_unit' => ToMajorUnitTransformer::class,
        'ToMajorUnit' => ToMajorUnitTransformer::class,
        'number_spell' => NumberSpellTransformer::class,
        'NumberSpell' => NumberSpellTransformer::class,
        'concat' => ConcatTransformer::class,
        'Concat' => ConcatTransformer::class,
        'number' => NumberTransformer::class,
        'Number' => NumberTransformer::class,
    ];

    foreach ($transformers as $input => $expectedClass) {
        $enum = MappingTransformers::find($input);
        expect($enum)->not->toBeNull();
        expect($enum->transformer())->toBe($expectedClass);
    }
});

test('MappingTransformers::default() returns UpperCase transformer', function () {
    $defaultEnum = MappingTransformers::default();
    expect($defaultEnum)->toBe(MappingTransformers::UPPER_CASE);
    expect($defaultEnum->transformer())->toBe(UpperCaseTransformer::class);
});

test('MappingTransformers::isValid() validates transformer names correctly', function () {
    expect(MappingTransformers::isValid('Currency'))->toBeTrue();
    expect(MappingTransformers::isValid('InvalidTransformer'))->toBeFalse();
    expect(MappingTransformers::isValid('NumberSpell'))->toBeTrue();
    expect(MappingTransformers::isValid('Concat'))->toBeTrue();
    expect(MappingTransformers::isValid('Number'))->toBeTrue();
});

