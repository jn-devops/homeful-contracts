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
                    'code' => 'first_name', //this is the variable name used in the template for document generation
                    'path' => 'contact.first_name', //this is the index of the source, see source below
                    'source' => 'array', //usually array, but check out see /App/Enums/MappingSource
                    'title' => 'First Name', // the title of the variable, purely for display
                    'type' => 'string', // the final type of the result, see /App/Enums/MappingType::class
                    'default' => 'Christian', //default if path in source is non-existent, this is usually an empty string
                    'category' => 'buyer', //see /App/Enums/MappingCategory
                    'transformer' => 'UpperCase, ReverseString' //operation applied to the result in succession, see /App/Enums/MappingTransformers
                ],
                [
                    'code' => 'last_name',
                    'path' => 'contact.first_name, contact.middle_name, contact.last_name',
                    'source' => 'array',
                    'title' => 'Full Name',
                    'type' => 'string',
                    'default' => 'Ramos',
                    'category' => 'buyer',
                    'transformer' => 'Join, Concat?before=Mr.&after=Jr.'
                ],
                [
                    'code' => 'gmi',
                    'path' => 'contact.monthly_gross_income',
                    'source' => 'array',
                    'title' => 'GMI',
                    'type' => 'string',
                    'default' => '537',
                    'category' => 'buyer',
                    'transformer' => 'ToMajorUnit?type=float, NumberFormat?precision=2',
                ],
                [
                    'code' => 'gmi_peso',
                    'path' => 'contact.monthly_gross_income',
                    'source' => 'array',
                    'title' => 'GMI Peso',
                    'type' => 'string',
                    'default' => '537',
                    'category' => 'buyer',
                    'transformer' => 'ToMajorUnit?type=float, Currency',
                ],
                [
                    'code' => 'gmi_words',
                    'path' => 'contact.monthly_gross_income',
                    'source' => 'array',
                    'title' => 'GMI Words',
                    'type' => 'string',
                    'default' => '537',
                    'category' => 'buyer',
                    'transformer' => 'ToMajorUnit, ToWords, TitleCase'
                ],
                [
                    'code' => 'order_interest',
                    'path' => 'order.interest',
                    'source' => 'array',
                    'title' => 'Order Interest',
                    'type' => 'string',
                    'default' => '7',
                    'category' => 'buyer',
                    'transformer' => 'NumberPercent?precision=2',
                ],
                [
                    'code' => 'project_code',
                    'path' => 'inventory.project_code',
                    'source' => 'mfiles',
                    'title' => 'Project Code',
                    'type' => 'string',
                    'default' => 'N/A',
                    'category' => 'buyer',
                    'transformer' => '',
                ],
                [
                    'code' => 'technical_description',
                    'path' => 'inventory.technical_description',
                    'source' => 'mfiles',
                    'title' => 'Tech Desc',
                    'type' => 'string',
                    'default' => 'N/A',
                    'category' => 'buyer',
                    'transformer' => '',
                ],
            ])
        ]
    ];
});

test('generate contract property action works', function (Reference $reference, $mappings) {
    $mappings();
    $contract = $reference->getContract();
    $count = app(GenerateContractPayloads::class)->run($contract);
    expect($count)->toBe(8);
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
        ['title' => 'Full Name', 'value' => 'Mr. Rommel Posadas Tiu Jr.'],
        ['title' => 'GMI', 'value' => '14,399.37'],
        ['title' => 'GMI Peso', 'value' => '₱14,399.37'],
        ['title' => 'GMI Words', 'value' => 'Fourteen Thousand Three Hundred Ninety-Nine And 37/100'],
        ['title' => 'Order Interest', 'value' => '7.00%'],
        ['title' => 'Project Code', 'value' => 'PVT3_DEV'],
        ['title' => 'Tech Desc', 'value' => "A parcel of land ( Lot 21, Blk 29 of the subdivision plan Psd-04-282073,  being a portion of the SUBD. OF LOT 1725-A, PSD-04-045803, LRC Record no. 8340 ), situated in the Barangay of MUZON, Municipality of NAIC, Province of CAVITE, Island of LUZON.  Bounded on the XXXX to the point of beginning, containing an area of FORTY EIGHT (48) SQUARE METERS. All corners are P.S cyl. conc. mons. 15x40 cms.; bearings True; date of original survey August 1906 - November 1907 and that of the subdivision survey on May 21-31, 2024 by JOEL J. HUBAC, Geodetic Engineer  & was approved on August 16, 2024.\r\n"],
    ];

    expect($payloads)->toMatchArray($expected);

})->with('reference', 'mappings');
