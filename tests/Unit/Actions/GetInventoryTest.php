<?php

use Homeful\Properties\Data\PropertyData;
use App\Actions\GetInventory;

test('get inventory action works', function () {
    $sku = getProductSKU();
    $property_data = app(GetInventory::class)->run(compact('sku'));
    expect(PropertyData::from($property_data)->product->sku)->toBe($sku);
});
