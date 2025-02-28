<?php

namespace App\Actions\Contract;

use App\Actions\GenerateContractPayloads;
use Homeful\KwYCCheck\Actions\ProcessLeadAction;
use Illuminate\Support\Facades\Validator;
use Homeful\References\Models\Reference;
use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\Contracts\States\Verified;

class Verify
{
    use AsAction;

    public function __construct(protected ProcessLeadAction $processLeadAction){}

    /**
     * @param Reference $reference
     * @param array $checkin_payload
     * @return Reference
     * @throws \Spatie\ModelStates\Exceptions\CouldNotPerformTransition
     */
    public function handle(Reference $reference, array $checkin_payload): Reference
    {
        Validator::validate($checkin_payload, $this->rules());
        $contract = $reference->getContract();
        GenerateContractPayloads::dispatch($contract);
        $contract->checkin = $checkin_payload;
        $contract->save();
        $contract->state->transitionTo(Verified::class);

        return $reference;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return $this->processLeadAction->rules();
    }
}
