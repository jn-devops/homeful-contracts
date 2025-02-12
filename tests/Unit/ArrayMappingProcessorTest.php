<?php

use App\Mappings\Processors\ArrayMappingProcessor;
use App\Mappings\Transformers\ConcatTransformer;
use App\Models\Mapping;

test('ConcatTransformer trims extra spaces properly', function () {
    $transformer = new ConcatTransformer('before=  Welcome  &after=  ! ');

    $result = $transformer->transform(['value' => '   Home   ']);

    expect($result['value'])->toBe('Welcome Home !');
});

//test('ArrayMappingProcessor returns a single value for a single key (backward compatibility)', function () {
//    $data = ['first_name' => 'Anais'];
//
//    $mapping = Mapping::factory()->make([
//        'path' => 'first_name'
//    ]);
//
//    $processor = new ArrayMappingProcessor($data, $mapping);
//    $result = $processor->process();
//
//    expect($result)->toBe('Anais'); // Returns a single value, not an array
//});

//test('ArrayMappingProcessor retrieves multiple values with dot notation', function () {
//    $data = [
//        'first_name' => 'Anais',
//        'last_name' => 'Santos',
//        'spouse' => ['name' => ''], // Empty spouse name
//        'aif' => [
//            'spouse' => ['name' => 'Carlos']
//        ]
//    ];
//
//    $mapping = Mapping::factory()->make([
//        'path' => 'first_name,last_name,spouse.name,aif.spouse.name'
//    ]);
//
//    $processor = new ArrayMappingProcessor($data, $mapping);
//    $result = $processor->process();
//
//    expect($result)->toMatchArray([
//        'first_name' => 'Anais',
//        'last_name' => 'Santos',
//        'aif.spouse.name' => 'Carlos' // 'spouse.name' is filtered out (empty value)
//    ]);
//});
//
//test('ArrayMappingProcessor returns default value when keys are missing', function () {
//    $data = [];
//
//    $mapping = Mapping::factory()->make([
//        'path' => 'missing_key',
//        'default' => 'Unknown'
//    ]);
//
//    $processor = new ArrayMappingProcessor($data, $mapping);
//    $result = $processor->process();
//
//    expect($result)->toBe('Unknown');
//});
//
//test('ArrayMappingProcessor removes null and empty values', function () {
//    $data = [
//        'key1' => 'Value 1',
//        'key2' => '',
//        'key3' => null,
//        'key4' => 'Value 4'
//    ];
//
//    $mapping = Mapping::factory()->make([
//        'path' => 'key1,key2,key3,key4'
//    ]);
//
//    $processor = new ArrayMappingProcessor($data, $mapping);
//    $result = $processor->process();
//
//    expect($result)->toMatchArray([
//        'key1' => 'Value 1',
//        'key4' => 'Value 4'
//    ]);
//});
