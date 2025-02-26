<?php

namespace App\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\Contracts\Models\Contract;
use Lorisleiva\Actions\Concerns\AsJob;
use App\Models\{Mapping, Payload};

class GenerateContractPayloads implements ShouldQueue
{
    use AsAction, AsJob, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(Contract $contract): int
    {
        $processedCount = 0;

        Mapping::query()
            ->notDeprecated()
            ->notDisabled()
            ->each(function (Mapping $mapping) use ($contract, &$processedCount) {
                $value = app(GetContractPayload::class)->run($contract, $mapping);
                $this->persistPayload($contract, $mapping, $value);

                // Increment the counter
                $processedCount++;
            });

        return $processedCount;
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function persistPayload(Contract $contract, Mapping $mapping, $value): Payload
    {
        $payload = app(Payload::class)->make([
            'mapping_code' => $mapping->code,
            'value' => $value ?? ''
        ]);

        $payload->contract()->associate($contract);
        $payload->save();

        return $payload;
    }
}
