<?php

namespace App\Actions;

use Illuminate\Http\Client\ConnectionException;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\Http;

class GetSellerCommissionCode
{
    use AsAction;

    /**
     * @throws ConnectionException
     */
    public function handle(string $seller_voucher_code): ?string
    {
        $route = __(config('homeful-contracts.end-points.redeem-voucher'), ['voucher' => $seller_voucher_code]);
        $response = Http::acceptJson()->post($route);

        return $response->ok() ? $response->json('seller_commission_code') : null;
    }
}
