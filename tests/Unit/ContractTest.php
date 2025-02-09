<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use App\Actions\{GetCustomer, UpdateContractContact};
use Homeful\Contacts\Models\Customer as Contact;
use Homeful\Contracts\Models\Contract;

uses(RefreshDatabase::class, WithFaker::class);

$homeful_id = 'H-KEF4SE';

dataset('contract', function () use ($homeful_id) {
    return [
        [
            fn() => tap(app(Contract::class)->create(), function (Contract $contract) use ($homeful_id){
                app(UpdateContractContact::class)->run($contract, $homeful_id);

            })
        ]
    ];
});

test('contracts has metadata', function (Contract $contract) use ($homeful_id) {
    expect($contract->getData())->toBeInstanceOf(\Homeful\Contracts\Data\ContractData::class);
    dd($contract->mortgage);
})->with('contract');
