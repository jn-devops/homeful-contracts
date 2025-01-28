<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Homeful\Contacts\Models\Customer as Contact;

uses(RefreshDatabase::class, WithFaker::class);

test('contact db configuration works', function () {
    $contact = app(Contact::class);
    expect($contact->getConnectionName())->toBe(config('contacts.models.contact.connection'));
    expect($contact->getTable())->toBe(config('contacts.models.contact.table'));
});

dataset('email', function () {
    return [
        ['ROMMELTIU@GMAIL.COM'],
        ['msdavid1004@gmail.com'],
        ['sherwinteves50612@gmail.com']
    ];
});

test('external product model works', function (string $email) {
    $contact = app(Contact::class)->whereEmail($email)->first();
    expect($contact->email)->toBe($email);
})->with('email');

test('external contact has factory', function () {
    $contact = Contact::factory()->create();
    expect($contact)->toBeInstanceOf(Contact::class);
})->skip();
