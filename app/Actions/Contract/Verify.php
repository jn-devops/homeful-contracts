<?php

namespace App\Actions\Contract;

use Homeful\KwYCCheck\Actions\ProcessLeadAction;
use Illuminate\Support\Facades\Validator;
use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\Contracts\Models\Contract;


class Verify
{
    use AsAction;

    public function __construct(protected ProcessLeadAction $processLeadAction){}

    public function handle(Contract $contract, array $checkin_payload)
    {
        Validator::validate($checkin_payload, $this->rules());
        $contract->checkin = $checkin_payload;
        $contract->save();
    }

    public function rules(): array
    {
        return $this->processLeadAction->rules();
    }
}
