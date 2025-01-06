<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use App\Actions\Contract\{Avail, Consult, Verify};
use Homeful\Contracts\States\{Availed, Consulted};
use Homeful\Contacts\Classes\ContactMetaData;
use Homeful\Properties\Data\PropertyData;
use Homeful\References\Models\Reference;
use Homeful\KwYCCheck\Data\CheckinData;
use Homeful\Contracts\Models\Contract;
use Illuminate\Support\Facades\Http;
use Homeful\KwYCCheck\Models\Lead;
use Homeful\Common\Classes\Amount;
use Homeful\Mortgage\Mortgage;
use App\Actions\GetMatches;

use Illuminate\Support\Arr;
use App\Classes\ProductOptions;
use App\Data\MatchDescriptionData;

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

dataset('checkin_payload', function () {
    return [
        [fn() => Lead::factory()->getCheckinPayload([
            'email' => fake()->email(),
            'mobile' => '09171234567',
            'code' => fake()->word(),
            'identifier' => fake()->word(),
            'choice' => fake()->word(),
            'location' => fake()->latitude() .',' . fake()->longitude(),
            'fullName' => fake()->name(),
            'address' => fake()->city(),
            'dateOfBirth' => '1999-03-17',
            'idType' => 'phl_dl',
            'idNumber' => 'ID-123456'
        ])]
    ];
});

test('generate reference from a new contract', function (array $contact_params, array $product_params, array $checkin_payload) {
    $response = Http::acceptJson()->post('http://homeful-contacts.test/api/register', $contact_params);
    expect($response->status())->toBe(201);
    $contact_reference_code = $response->json('code');
    $reference = Consult::run($contact_reference_code);
    expect($reference)->toBeInstanceOf(Reference::class);
    $contract = $reference->getContract();
    expect($contract)->toBeInstanceOf(Contract::class);
    expect($contract->state)->toBeInstanceOf(Consulted::class);
    expect($contract->contact)->toBeInstanceOf(ContactMetaData::class);
    expect($contract->property)->toBeNull();
    expect($contract->mortgage)->toBeNull();

    Avail::run($reference, $product_params);
    if ($contract instanceof Contract) {
        $contract->refresh();
        expect($contract->property)->toBeInstanceOf(PropertyData::class);
        expect($contract->mortgage)->toBeInstanceOf(Mortgage::class);
        expect($contract->state)->toBeInstanceOf(Availed::class);
        expect($contract->mortgage->getBorrower()->getBirthdate()->isSameDay($contact_params['date_of_birth']))->toBeTrue();
        expect($contract->mortgage->getBorrower()->getGrossMonthlyIncome()->inclusive()->compareTo($contact_params['monthly_gross_income']))->toBe(Amount::EQUAL);
        expect($contract->mortgage->getProperty()->getSKU())->toBe($product_params['sku']);
    }

    expect($contract->checkin)->toBeNull();
    Verify::run($contract, $checkin_payload);
    expect($contract->checkin)->toBeInstanceOf(CheckinData::class);
})->with('contact_params', 'product_params', 'checkin_payload');
