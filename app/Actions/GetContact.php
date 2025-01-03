<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\Http;

class GetContact
{
    use AsAction;

    public function handle(string $membership_id)
    {
        $route = 'http://homeful-contacts.test/api/references/'.$membership_id;
        $response = Http::acceptJson()->get($route);

        return $response->ok() ? $response->json('contact') : false;
    }
}
