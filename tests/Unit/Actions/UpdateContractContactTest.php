<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Homeful\Contacts\Models\Customer as Contact;
use Homeful\Contacts\Classes\ContactMetaData;
use App\Actions\UpdateContractContact;
use Homeful\Contracts\Models\Contract;
use App\Actions\GetCustomer;

uses(RefreshDatabase::class, WithFaker::class);

dataset('buyer_metadata', function () {
    return [
        [fn() => app(GetCustomer::class)->run(['contact_reference_code' => getHomefulId()])]
    ];
});

test('update contact contact works', function (array $buyer_metadata) {
    $contract = app(UpdateContractContact::class)->run(new Contract, getHomefulId());
    expect($contract)->toBeInstanceOf(Contract::class);
    expect($contract->contact)->toBeInstanceOf(ContactMetaData::class);
    expect($contract->contact->id)->toBe($buyer_metadata['id']);
    expect($contract->contact->email)->toBe($buyer_metadata['email']);
    expect($contract->customer)->toBeInstanceOf(Contact::class);
    expect($contract->customer->id)->toBe($buyer_metadata['id']);
    expect($contract->customer->email)->toBe($buyer_metadata['email']);
})->with('buyer_metadata');
