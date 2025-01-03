<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use App\Actions\{GetContact, GetNextInventory};
use Homeful\Contacts\Classes\ContactMetaData;
use Homeful\References\Facades\References;
use Homeful\Properties\Data\PropertyData;
use Homeful\References\Models\Reference;
use Homeful\Contracts\Models\Contract;
use Illuminate\Support\Facades\Http;
use Homeful\Common\Classes\Amount;
use Homeful\Mortgage\Mortgage;

uses(RefreshDatabase::class, WithFaker::class);

dataset('contact_params', function () {
    return [
        [fn() => [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'mobile' => '0' . fake()->numberBetween(9000000000, 9999999999),
            'password' => 'password',
            'password_confirmation' => 'password',
            'date_of_birth' => '1999-03-17',
            'monthly_gross_income' => 15000
        ]]
    ];
});

dataset('product_params', function () {
    return [
        [fn() => [
            'sku' => 'JN-PVMP-HLDU-54-h'
        ]]
    ];
});

test('generate reference from new availment', function (array $contact_params, array $product_params) {
    $response = Http::acceptJson()->post('http://homeful-contacts.test/api/register', $contact_params);
    expect($response->status())->toBe(201);
    $membership_id = $response->json('code');
    $contact_attributes = GetContact::run($membership_id);
    $property_attributes = GetNextInventory::run($product_params);
    $contract = app(Contract::class)->create([
        'contact' => $contact_attributes,
        'property' => $property_attributes
    ]);
    if ($contract instanceof Contract) {
        expect($contract->contact)->toBeInstanceOf(ContactMetaData::class);
        expect($contract->property)->toBeInstanceOf(PropertyData::class);
        expect($contract->mortgage)->toBeInstanceOf(Mortgage::class);
        expect($contract->mortgage->getBorrower()->getBirthdate()->isSameDay($contact_params['date_of_birth']))->toBeTrue();
        expect($contract->mortgage->getBorrower()->getGrossMonthlyIncome()->inclusive()->compareTo($contact_params['monthly_gross_income']))->toBe(Amount::EQUAL);
        expect($contract->mortgage->getProperty()->getSKU())->toBe($property_attributes['sku']);
        expect($contract->mortgage->getProperty()->getTotalContractPrice()->inclusive()->compareTo($property_attributes['tcp']))->toBe(Amount::EQUAL);
    }

    $entities = [
        'contract' => $contract
    ];
    $reference = References::withEntities(...$entities)->withStartTime(now())->create();

    expect($reference)->toBeInstanceOf(Reference::class);
    expect($reference->getContract())->toBeInstanceOf(Contract::class);
    expect($reference->getContract()->is($contract))->toBeTrue();

})->with('contact_params', 'product_params');
