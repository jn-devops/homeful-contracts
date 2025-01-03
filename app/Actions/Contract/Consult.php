<?php

namespace App\Actions\Contract;

use Homeful\References\Events\ReferenceCreated;
use Homeful\References\Facades\References;
use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\References\Models\Reference;
use Homeful\Contracts\States\Consulted;
use App\Actions\UpdateContractContact;
use Homeful\Contracts\Models\Contract;

class Consult
{
    use AsAction;

    /**
     * @param string $contact_reference_code
     * @return Reference
     */
    public function handle(string $contact_reference_code): Reference
    {
        /** create a blank contract */
        $contract = app(Contract::class)->create();

        /** update the contract contact */
        UpdateContractContact::run($contract, $contact_reference_code);

        /** prepare entities e.g., contract */
        $entities = compact('contract');

        /** create a blank reference, add the prepared entities to the reference model */
        $reference = References::withEntities(...$entities)->withStartTime(now())->create();
        ReferenceCreated::dispatch($reference);

        /** update the contract state from Pending to Consulted */
        $contract->state->transitionTo(Consulted::class, $reference);

        return $reference;
    }
}
