<?php

namespace App\Actions;

use Homeful\References\Facades\References;
use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\References\Models\Reference;
use Homeful\Contracts\States\Consulted;
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
        //create a blank contract
        $contract = app(Contract::class)->create();

        //get customer from contact reference code and assign to contract contact json attribute
        $contract->contact = GetCustomer::run(compact('contact_reference_code'));
        $contract->save();

        //prepare entities e.g., contract
        $entities = compact('contract');

        //create a blank reference, add the prepared entities tot he reference
        $reference = References::withEntities(...$entities)->withStartTime(now())->create();

        //update the state to Consulted
        $contract->state->transitionTo(Consulted::class, $reference);

        return $reference;
    }
}
