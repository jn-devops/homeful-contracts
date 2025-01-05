<?php

namespace App\Actions;

use Illuminate\Http\Client\ConnectionException;
use Homeful\Contacts\Classes\ContactMetaData;
use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\References\Models\Reference;
use Illuminate\Support\Facades\Http;

class GetMatches
{
    use AsAction;

    /**
     * @param Reference $reference
     * @param int $limit
     * @param int $verbose
     * @return array|mixed
     * @throws ConnectionException
     */
    public function handle(Reference $reference, int $limit = 3, int $verbose = 0): mixed
    {
        $contract = $reference->getContract();
        $contact = $contract->contact;
        if ($contact instanceof ContactMetaData) {
            $payload = [
                'date_of_birth' =>  $contact->date_of_birth->format('Y-m-d'),
                'monthly_gross_income' => $contact->employment->first()?->monthly_gross_income,
                'region' => $contact->addresses?->first()?->region ?? 'NCR',
                'limit' => $limit,
                'verbose' => $verbose
            ];
            $response = Http::acceptJson()->get(config('homeful-contracts.end-points.matches'), $payload);

            return $response->json();
        }

        return [];
    }
}
