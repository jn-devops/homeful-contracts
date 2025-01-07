<?php

namespace App\Actions\Contract;

use Homeful\KwYCCheck\Actions\ProcessLeadAction;
use Illuminate\Support\Facades\Validator;
use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\Contracts\Models\Contract;
use Homeful\Contracts\States\Verified;

class Verify
{
    use AsAction;

    public function __construct(protected ProcessLeadAction $processLeadAction){}

    /**
     * This would have been better if we use reference
     * instead of contract, but there seems to be a
     * problem if we use it. Something to do with
     * the checkin payload. Instead of an array
     * the attribute cast is a string. Weird.
     *
     * @param Contract $contract
     * @param array $checkin_payload
     * @return Contract
     * @throws \Spatie\ModelStates\Exceptions\CouldNotPerformTransition
     */
    public function handle(Contract $contract, array $checkin_payload): Contract
    {
        Validator::validate($checkin_payload, $this->rules());
        $contract->checkin = $checkin_payload;
        $contract->save();
        $contract->state->transitionTo(Verified::class);

        return $contract;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return $this->processLeadAction->rules();
    }
}
