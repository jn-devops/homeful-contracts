<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Illuminate\Support\Facades\{Notification};
use App\Actions\Contract\{Avail, Consult};
use App\Actions\GenerateContractPayloads;
use Homeful\References\Models\Reference;
use App\Models\{Mapping, Payload};

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
                    'transformer' => 'UpperCase, ReverseString'
                ],
                [
                    'code' => 'last_name',
                    'path' => 'contact.last_name',
                    'source' => 'array',
                    'title' => 'Last Name',
                    'type' => 'string',
                    'default' => 'Ramos',
                    'category' => 'buyer',
                    'transformer' => 'TitleCase'
                ],
                [
                    'code' => 'gmi',
                    'path' => 'contact.monthly_gross_income',
                    'source' => 'array',
                    'title' => 'GMI',
                    'type' => 'integer',
                    'default' => '537',
                    'category' => 'buyer',
                    'transformer' => 'ToMajorUnit'
                ],
                [
                    'code' => 'gmi_words',
                    'path' => 'contact.monthly_gross_income',
                    'source' => 'array',
                    'title' => 'GMI Words',
                    'type' => 'string',
                    'default' => '537',
                    'category' => 'buyer',
                    'transformer' => 'ToMajorUnit, NumberSpell, TitleCase'
                ],
            ])
        ]
    ];
});

test('generate contract property action works', function (Reference $reference, $mappings) {
    $mappings();
    $contract = $reference->getContract();
    $count = app(GenerateContractPayloads::class)->run($contract);
    expect($count)->toBe(4);
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
        ['title' => 'Last Name', 'value' => 'Tiu'],
        ['title' => 'GMI', 'value' => 14400],
        ['title' => 'GMI Words', 'value' => 'Fourteen Thousand Three Hundred Ninety-Nine Point Three Seven'],
    ];

    expect($payloads)->toMatchArray($expected);

})->with('reference', 'mappings');
