<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Homeful\Contracts\States\{Availed, Consulted, Verified};
use App\Actions\Contract\{Avail, Consult, Verify};
use Homeful\Contacts\Classes\ContactMetaData;
use Homeful\Properties\Data\PropertyData;
use Homeful\References\Models\Reference;
use Homeful\KwYCCheck\Data\CheckinData;
use Homeful\Contracts\Models\Contract;
use Illuminate\Support\Facades\Http;
use Homeful\KwYCCheck\Models\Lead;
use Homeful\Common\Classes\Amount;
use Homeful\Mortgage\Mortgage;
use Illuminate\Support\Arr;

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

test('consult, avail, verify, paid actions work', function (array $contact_params, array $product_params, array $checkin_payload) {
    $response = Http::acceptJson()->post('http://homeful-contacts.test/api/register', $contact_params);
    expect($response->status())->toBe(201);
    $contact_reference_code = $response->json('code');
    $reference = Consult::run($contact_reference_code);
    expect($reference)->toBeInstanceOf(Reference::class);
    $contract = $reference->getContract();
    expect($contract)->toBeInstanceOf(Contract::class);
    expect($contract->state)->toBeInstanceOf(Consulted::class);
    expect($contract->consulted)->toBeTrue();
    expect($contract->contact)->toBeInstanceOf(ContactMetaData::class);
    expect($contract->property)->toBeNull();
    expect($contract->mortgage)->toBeNull();

    Avail::run($reference, $product_params);
    if ($contract instanceof Contract) {
        $contract->refresh();
        expect($contract->property)->toBeInstanceOf(PropertyData::class);
        expect($contract->mortgage)->toBeInstanceOf(Mortgage::class);
        expect($contract->state)->toBeInstanceOf(Availed::class);
        expect($contract->availed)->toBeTrue();
        expect($contract->mortgage->getBorrower()->getBirthdate()->isSameDay($contact_params['date_of_birth']))->toBeTrue();
        expect($contract->mortgage->getBorrower()->getGrossMonthlyIncome()->inclusive()->compareTo($contact_params['monthly_gross_income']))->toBe(Amount::EQUAL);
        expect($contract->mortgage->getProperty()->getSKU())->toBe($product_params['sku']);
    }

    expect($contract->checkin)->toBeNull();
    Verify::run($reference, $checkin_payload);
    $contract->refresh();
    expect($contract->checkin)->toBeInstanceOf(CheckinData::class);
    expect($contract->state)->toBeInstanceOf(Verified::class);
    expect($contract->verified)->toBeTrue();
})->with('contact_params', 'product_params', 'checkin_payload');

test('consult, avail, verify, paid end points work', function (array $contact_params, array $product_params) {
    $response = Http::acceptJson()->post('http://homeful-contacts.test/api/register', $contact_params);
    expect($response->status())->toBe(201);
    $contact_reference_code = $response->json('code');

    expect(session('event'))->toBeNull();
    $response = $this->postJson(route('consult.store'), compact('contact_reference_code'));
    expect($response->status())->toBe(302);
    expect($flashData = session('event'))->toBeArray();
    $reference_code = Arr::get($flashData, 'data');
    $response->assertRedirect(route('avail.create', ['reference' => $reference_code]));

    $reference = Reference::where('code', $reference_code)->first();
    expect($reference)->toBeInstanceOf(Reference::class);
    $contract = $reference->getContract();
    expect($contract)->toBeInstanceOf(Contract::class);
    expect($contract->state)->toBeInstanceOf(Consulted::class);
    expect($contract->consulted)->toBeTrue();
    expect($contract->contact)->toBeInstanceOf(ContactMetaData::class);
    expect($contract->property)->toBeNull();
    expect($contract->mortgage)->toBeNull();

    $sku = Arr::get($product_params, 'sku');
    $response = $this->postJson(route('avail.store'), compact('reference_code', 'sku'));
    expect($response->status())->toBe(302);
    $response->assertRedirect(route('verify.create', compact('reference_code')));
    if ($contract instanceof Contract) {
        $contract->refresh();
        expect($contract->property)->toBeInstanceOf(PropertyData::class);
        expect($contract->mortgage)->toBeInstanceOf(Mortgage::class);
        expect($contract->state)->toBeInstanceOf(Availed::class);
        expect($contract->availed)->toBeTrue();
        expect($contract->mortgage->getBorrower()->getBirthdate()->isSameDay($contact_params['date_of_birth']))->toBeTrue();
        expect($contract->mortgage->getBorrower()->getGrossMonthlyIncome()->inclusive()->compareTo($contact_params['monthly_gross_income']))->toBe(Amount::EQUAL);
        expect($contract->mortgage->getProperty()->getSKU())->toBe($product_params['sku']);
    }
    expect($contract->checkin)->toBeNull();
    $response = $this->postJson(route('verify.store'), compact('reference_code'));
    expect($response->status())->toBe(302);
    $response->assertRedirect(route('verify-contact', compact('reference_code')));
    expect($contract->state)->toBeInstanceOf(Availed::class);

    expect($contract->checkin)->toBeNull();
    $checkin_payload = Lead::factory()->getCheckinPayload([
        'email' => fake()->email(),
        'mobile' => '09171234567',
        'code' => fake()->word(),
        'identifier' => $reference_code,
        'choice' => fake()->word(),
        'location' => fake()->latitude() .',' . fake()->longitude(),
        'fullName' => fake()->name(),
        'address' => fake()->city(),
        'dateOfBirth' => '1999-03-17',
        'idType' => 'phl_dl',
        'idNumber' => 'ID-123456'
    ]);
    $response = $this->postJson(route('contact-verified'), $checkin_payload);
    expect($response->status())->toBe(200);
    $contract->refresh();
    expect($contract->checkin)->toBeInstanceOf(CheckinData::class);
    expect($contract->state)->toBeInstanceOf(Verified::class);
    expect($contract->verified)->toBeTrue();
})->with('contact_params', 'product_params');
