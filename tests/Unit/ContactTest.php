<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Homeful\Contacts\Models\Customer as Contact;
use Homeful\Contacts\Classes\ContactMetaData;

uses(RefreshDatabase::class, WithFaker::class);

test('contact db configuration works', function () {
    $contact = app(Contact::class);
    expect($contact->getConnectionName())->toBe(config('contacts.models.contact.connection'));
    expect($contact->getTable())->toBe(config('contacts.models.contact.table'));
});

dataset('email', function () {
    return [
        ['leepampauleen25@gmail.com'],
//        ['johananieko@gmail.com'],
//        ['vorobia08162016@gmail.com'],
//        ['geoffrey22gg@gmaail.com'],
//        ['hazielorlina15@yahoo.com'],
//        ['khaelalhorraine072418@gmail.com']
    ];
});

test('external contact model works', function (string $email) {
    $contact = app(Contact::class)->whereEmail($email)->first();
    expect($contact->email)->toBe($email);
    expect($contact->getData())->toBeInstanceOf(ContactMetaData::class);
})->with('email');

test('external contact has factory', function () {
    $contact = Contact::factory()
        ->state([
            'date_of_birth' => '1999-03-17'
        ])
        ->withId($uuid = Str::uuid()->toString())
        ->withEmployment([
            0 => [
                'type' => 'Primary',
                'monthly_gross_income' => 60000.0,
                'current_position' => 'Developer',
            ],
            1 => [
                'type' => 'Sideline',
                'monthly_gross_income' => 20000.0,
                'current_position' => 'Freelancer',
            ]
        ])
        ->withCoBorrowers([
            0 => [
                'date_of_birth' => '1998-08-12',
                'employment' => [
                    0 => [
                        'type' => 'Primary',
                        'monthly_gross_income' => 50000.0,
                        'current_position' => 'Engineer',
                    ]
                ]
            ],
            1 => [
                'date_of_birth' => '1995-01-24',
                'employment' => [
                    0 => [
                        'type' => 'Sideline',
                        'monthly_gross_income' => 40000.0,
                        'current_position' => 'Developer',
                    ]
                ]
            ]
        ])->create();
    expect($contact)->toBeInstanceOf(Contact::class);
    expect($contact->getData())->toBeInstanceOf(ContactMetaData::class);
});
