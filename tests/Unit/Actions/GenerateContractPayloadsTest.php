<?php

use App\Models\Payload;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Illuminate\Support\Facades\{Event, Notification};
use App\Actions\Contract\{Avail, Consult};
use App\Transformers\UpperCaseTransformer;
use App\Actions\GenerateContractPayloads;
use Homeful\References\Models\Reference;
use Homeful\Contracts\Models\Contract;
use App\Models\Mapping;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    Notification::fake();
});

dataset('reference', function() {
    return [
        [
            fn () => with(app(Consult::class)->run(getHomefulId()), function (Reference $reference) {
               return app(Avail::class)->run($reference, ['sku' => getProductSKU()]);
            })
        ]
    ];
});

dataset('mapping_attributes', function () {
   return [
       [ fn() => ['code' => 'first_name', 'path' => 'contact.first_name', 'source' => 'array', 'title' => 'First Name', 'type' => 'string', 'default' => 'Christian', 'category' => 'buyer', 'transformer' => 'UpperCaseTransformer, ReverseStringTransformer']]
   ];
});

dataset('mappings', function () {
    return [
        [
            fn() => Mapping::factory()->createMany([
                [
                    'code' => 'first_name',
                    'path' => 'contact.first_name',
                    'source' => 'array',
                    'title' => 'First Name',
                    'type' => 'string',
                    'default' => 'Christian',
                    'category' => 'buyer',
                    'transformer' => 'UpperCaseTransformer, ReverseStringTransformer'
                ],
                [
                    'code' => 'last_name',
                    'path' => 'contact.last_name',
                    'source' => 'array',
                    'title' => 'Last Name',
                    'type' => 'string',
                    'default' => 'Ramos',
                    'category' => 'buyer',
                    'transformer' => 'UpperCaseTransformer, ReverseStringTransformer'
                ]
            ])
        ]
    ];
});

test('generate contract property action works', function (Reference $reference, array $mapping_attributes, $mappings) {
    $mappings();
    $contract = $reference->getContract();
    $count = app(GenerateContractPayloads::class)->run($contract);
    expect($count)->toBe(2);
    $contract->refresh();
    $payloads = Payload::with(['mapping' => function ($query) {
        $query->select('code', 'title', 'category');  // Select only title and category, and 'code' for join
    }])
        ->get(['mapping_code', 'value'])  // Select only necessary columns from the payloads table
        ->map(function ($payload) {
            return [
                'title' => $payload->mapping->title,
                'value' => $payload->value,
            ];
        })->toArray();

    $expected = [
        ['title' => 'First Name', 'value' => 'LEMMOR'],
        ['title' => 'Last Name', 'value' => 'UIT'],
    ];

    expect($payloads)->toMatchArray($expected);

})->with('reference', 'mapping_attributes', 'mappings');
