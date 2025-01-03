<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\Http;

class GetInventory
{
    use AsAction;

    /**
     * @param array $attribs
     * @return array|false|mixed
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function handle(array $attribs): mixed
    {
        $route = __(config('homeful-contracts.end-points.inventory'), $attribs);
        $response = Http::acceptJson()->get($route);

        return $response->ok() ? $response->json('data') : false;
    }
}
