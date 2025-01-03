<?php

namespace App\Actions;

use Homeful\Contacts\Classes\ContactMetaData;
use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\Contracts\Models\Contract;

class UpdateContractContact
{
    use AsAction;

    public function handle(Contract $contract, string $contact_reference_code): ContactMetaData
    {
        //get customer from contact reference code
        $metadata = GetCustomer::run(compact('contact_reference_code'));

        //assign metadata to contract contact json attribute
        $contract->contact = $metadata;
        $contract->save();

        return $contract->contact;
    }
}
