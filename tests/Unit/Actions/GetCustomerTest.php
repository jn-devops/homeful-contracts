<?php

use Homeful\Contacts\Classes\ContactMetaData;
use App\Actions\GetCustomer;

test('get customer action works', function () {
    $contact_reference_code = getHomefulId();
    $data = app(GetCustomer::class)->run(compact('contact_reference_code'));
    expect(ContactMetaData::from($data)->id)->toBeUuid();
});
