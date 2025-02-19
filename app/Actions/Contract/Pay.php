<?php

namespace App\Actions\Contract;

use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\References\Models\Reference;
use Homeful\Contracts\Models\Contract;
use Homeful\Contracts\States\Paid;

class Pay
{
    use AsAction;

    /**
     * @param Reference $reference
     * @param array $payment_payload
     * @return Reference
     * @throws \Spatie\ModelStates\Exceptions\CouldNotPerformTransition
     */
    public function handle(Reference $reference, array $payment_payload): Reference
    {
        $contract = $reference->getContract();
        if ($contract instanceof Contract) {
            $contract->payment = $payment_payload;
            $contract->save();
            $contract->state->transitionTo(Paid::class);
        }

        return $reference;
    }
}
