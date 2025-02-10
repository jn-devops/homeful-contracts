<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Illuminate\Support\Facades\{Event, Notification};
use Homeful\Contracts\States\{Availed, Consulted};
use Homeful\Contacts\Models\Customer as Contact;
use App\Actions\Contract\{Avail, Consult};
use Homeful\Properties\Data\PropertyData;
use Homeful\References\Models\Reference;
use Homeful\Properties\Models\Property;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    Notification::fake();
});

dataset('reference', function() {
    return [
        [fn  () => app(Consult::class)->run(getHomefulId())]
    ];
});

/** TODO: test $seller_voucher_code */
test('avail action works', function (Reference $reference) {
    expect($reference->getContract()->customer)->toBeInstanceOf(Contact::class);
    expect($reference->getContract()->state)->toBeInstanceOf(Consulted::class);
    $sku = getProductSKU();
    $reference = app(Avail::class)->run($reference, compact('sku'));
    expect($reference)->toBeInstanceOf(Reference::class);
    $contract = $reference->getContract();
    expect($contract->property)->toBeInstanceOf(PropertyData::class);
    expect($contract->state)->toBeInstanceOf(Availed::class);
    expect($contract->inventory)->toBeInstanceOf(Property::class);
    expect(PropertyData::fromModel($contract->inventory))->toBeInstanceOf(PropertyData::class);
})->with('reference');
