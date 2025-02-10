<?php

namespace App\Actions;

use Illuminate\Contracts\Container\BindingResolutionException;
use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\Contracts\Models\Contract;
use App\Models\{Mapping, Payload};

class GenerateContractPayloads
{
    use AsAction;

    public function handle(Contract $contract): int
    {
        $processedCount = 0;

        Mapping::query()
            ->notDeprecated()
            ->notDisabled()
            ->each(function (Mapping $mapping) use ($contract, &$processedCount) {
                $value = app(GetContractPayload::class)->run($contract, $mapping);
                $payload = $this->persistPayload($contract, $mapping, $value);

                // Increment the counter
                $processedCount++;
            });

        return $processedCount;
    }

    /**
     * @throws BindingResolutionException
     */
    public function persistPayload(Contract $contract, Mapping $mapping, $value): Payload
    {
        $payload = app(Payload::class)->make([
            'mapping_code' => $mapping->code,
            'value' => $value??''
        ]);

        $payload->contract()->associate($contract);
        $payload->save();

        return $payload;
    }
}
