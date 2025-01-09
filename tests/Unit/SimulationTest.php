<?php

use Homeful\Contracts\States\{Assigned, Availed, Consulted, Onboarded, Paid, Verified};
use App\Actions\Contract\{Assign, Avail, Consult, Pay, Onboard, Verify};
use Homeful\Notifications\Notifications\PaidToAssignedBuyerNotification;
use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Illuminate\Support\Facades\Notification;
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

dataset('payment_payload', function () {
    return [
        [fn() => json_decode('{"code":"00","data":{"orderInformation":{"amount":5000,"attach":"attach","currency":"PHP","goodsDetail":"Processing Fee","orderAmount":0,"orderId":"JN123456722","paymentBrand":"MasterCard","paymentType":"PAYMENT","qrTag":1,"referencedId":"202410302883035985507708928","responseDate":"2024-10-30T15:23:29+08:00","surcharge":0,"tipFee":0,"transactionResult":"SUCCESS"}},"message":"Success"}',true)]
    ];
});

beforeEach(function () {
    Notification::fake();
});

test('consult, avail, verify, paid actions work', function (array $contact_params, array $product_params, array $checkin_payload, array $payment_payload) {
    $response = Http::acceptJson()->post(config('homeful-contracts.end-points.api-register-contact'), $contact_params);
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

    expect($contract->onboarded)->toBeFalse();
    Onboard::run($reference);
    $contract->refresh();
    expect($contract->onboarded)->toBeTrue();

    expect($contract->paid)->toBeFalse();
    Pay::run($reference, $payment_payload);
    $contract->refresh();
    expect($contract->paid)->toBeTrue();

    expect($contract->assigned)->toBeFalse();
    $assign_payload = ['assigned_contact' => fake()->name()];
    Assign::run($reference, $assign_payload);
    $contract->refresh();
    expect($contract->state)->toBeInstanceOf(Assigned::class);
    expect($contract->assigned)->toBeTrue();
    Notification::assertSentTo($contract, PaidToAssignedBuyerNotification::class);

})->with('contact_params', 'product_params', 'checkin_payload', 'payment_payload');

test('consult, avail, verify, pay end points work', function (array $contact_params, array $product_params) {
    $response = Http::acceptJson()->post(config('homeful-contracts.end-points.api-register-contact'), $contact_params);
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

    $onboarding_payload = ['reference' => $reference_code];
    $response = $this->get(route('contact-onboarded', $onboarding_payload));
    expect($response->status())->toBe(302);
    $contract->refresh();
    expect($contract->state)->toBeInstanceOf(Onboarded::class);
    expect($contract->onboarded)->toBeTrue();
    $response->assertRedirect(route('pay.create', compact('reference_code')));
    $response = $this->postJson(route('pay.store'), compact('reference_code'));
    $response->assertRedirect(route('collect-contact', compact('reference_code')));
    expect($contract->paid)->toBeFalse();
    $payment_payload = json_decode(__('{"code":"00","data":{"orderInformation":{"amount":5000,"attach":"attach","currency":"PHP","goodsDetail":"Processing Fee","orderAmount":0,"orderId":":reference_code","paymentBrand":"MasterCard","paymentType":"PAYMENT","qrTag":1,"referencedId":"202410302883035985507708928","responseDate":"2024-10-30T15:23:29+08:00","surcharge":0,"tipFee":0,"transactionResult":"SUCCESS"}},"message":"Success"}', ['reference_code' => $reference_code]),true);
    $response = $this->postJson(route('payment-collected'), $payment_payload);
    expect($response->status())->toBe(200);
    $contract->refresh();
    expect($contract->state)->toBeInstanceOf(Paid::class);
    expect($contract->paid)->toBeTrue();

    $payment_payload = ['reference' => $reference_code];
    $response = $this->get(route('contact-paid', $payment_payload));
    expect($response->status())->toBe(302);
    $response->assertRedirect(route('assign.create', compact('reference_code')));
    $assign_payload = ['reference_code' => $reference_code, 'assigned_contact' => fake()->name()];

    $response = $this->postJson(route('assign.store'), $assign_payload);
    expect($response->status())->toBe(302);
    $contract->refresh();
    expect($contract->state)->toBeInstanceOf(Assigned::class);
    expect($contract->assigned)->toBeTrue();
    $response->assertRedirect(route('dashboard', compact('reference_code')));

    Notification::assertSentTo($contract, PaidToAssignedBuyerNotification::class);
})->with('contact_params', 'product_params');
