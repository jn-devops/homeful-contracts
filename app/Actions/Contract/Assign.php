<?php

namespace App\Actions\Contract;

use Spatie\ModelStates\Exceptions\CouldNotPerformTransition;
use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\References\Models\Reference;
use Homeful\Contracts\States\Assigned;

class Assign
{
    use AsAction;

    /**
     * @param Reference $reference
     * @param array $attribs
     * @return Reference
     * @throws CouldNotPerformTransition
     */
    public function handle(Reference $reference, array $attribs): Reference
    {
        $contract = $reference->getContract();

        //TODO: set assigned_contact

        $contract->state->transitionTo(Assigned::class, $reference);

        return $reference;
    }
}
