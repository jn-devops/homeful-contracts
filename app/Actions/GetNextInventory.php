<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\Http;

class GetNextInventory
{
    use AsAction;

    public function handle(array $attribs)
    {
        $route = __('https://properties.homeful.ph/api/next-property-details/:sku', $attribs);
        $response = Http::acceptJson()->get($route);

        return $response->ok() ? $response->json('data') : false;
    }
}
