<?php

namespace App\Actions\Contract;

use Illuminate\Support\Facades\Validator;
use Homeful\References\Models\Reference;
use Lorisleiva\Actions\Concerns\AsAction;
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
        Validator::validate($payment_payload, $this->rules());
        $contract = $reference->getContract();
        $contract->payment = $payment_payload;
        $contract->save();
        $contract->state->transitionTo(Paid::class);

        return $reference;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'amount' => ['nullable', 'numeric'],
            'paid_at' => ['nullable', 'date'],
        ];
    }
}
