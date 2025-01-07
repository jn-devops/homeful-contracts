<?php

namespace App\Actions\Contract;

use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\References\Models\Reference;
use Homeful\Contracts\States\Onboarded;

class Onboard
{
    use AsAction;

    /**
     * @param Reference $reference
     * @return Reference
     * @throws \Spatie\ModelStates\Exceptions\CouldNotPerformTransition
     */
    public function handle(Reference $reference): Reference
    {
        $contract = $reference->getContract();
        $contract->state->transitionTo(Onboarded::class, $reference);

        return $reference;
    }
}
