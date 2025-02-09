<?php

namespace App\Actions;

use App\Exceptions\UpdateContractContactException;
use Homeful\Contacts\Classes\ContactMetaData;
use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\Contracts\Models\Contract;

class UpdateContractContact
{
    use AsAction;

    /**
     * @throws UpdateContractContactException
     */
    public function handle(Contract $contract, string $contact_reference_code): ?Contract
    {
        try {
            //retrieve contact metadata from contacts server
            $contact_metadata = ContactMetaData::from(GetCustomer::run(compact('contact_reference_code')));
            //assign contact metadata to contract contact json attribute
            $contract->contact = $contact_metadata;

            //associate contact to contract customer directly, no need to instantiate customer
            $contract->forceFill([
                'contact_id' => $contact_metadata->id
            ]);
            $contract->save();

        } catch (\Exception $exception) {
            throw new UpdateContractContactException(
                $contact_reference_code,
                'An error occurred while updating the contact for the contract.',
                $exception->getCode(),
                $exception
            );
        }

        $contract->refresh();

        return $contract;
    }
}
