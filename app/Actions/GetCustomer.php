<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\Http;

class GetCustomer
{
    use AsAction;

    /**
     * @param array $attribs
     * @return mixed
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function handle(array $attribs): mixed
    {
        $route = __(config('homeful-contracts.end-points.customer'), $attribs);
        $response = Http::acceptJson()->get($route);

        return $response->ok() ? $response->json('contact') : false;
    }
}
