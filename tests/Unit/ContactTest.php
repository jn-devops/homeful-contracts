<?php
use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use App\Models\{Contact};
use Illuminate\Support\Facades\Http;

uses(
//    RefreshDatabase::class,
    WithFaker::class);

test('external contact model works', function () {
    $email = 'vivar.gari@gmail.com.ph';
    $contact = Contact::where('email', $email)->first();

    if ($contact instanceof Contact) {
        dd($contact);
        expect($contact->mobile)->toBe($mobile);
    }
});
