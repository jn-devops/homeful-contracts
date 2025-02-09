<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Illuminate\Support\Facades\{Event, Notification};
use Homeful\Contacts\Models\Customer as Contact;
use Homeful\References\Events\ReferenceCreated;
use Homeful\Contracts\States\Consulted;
use Homeful\Contracts\Models\Contract;
use App\Actions\Contract\Consult;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
   Notification::fake();
});

test('consult action works', function () {
    Event::fake(ReferenceCreated::class);
    $ref = app(Consult::class)->run(getHomefulId());
    expect($contract = $ref->getContract())->toBeInstanceOf(Contract::class);
    expect($contract->customer)->toBeInstanceOf(Contact::class);
    expect($contract->inventory)->toBeNull();
    expect($contract->state)->toBeInstanceOf(Consulted::class);
    Event::assertDispatched(ReferenceCreated::class);
});
