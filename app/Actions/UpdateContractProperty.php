<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\References\Models\Reference;
use Homeful\Contracts\Models\Contract;

class UpdateContractProperty
{
    use AsAction;

    protected function update(Reference $reference, array $validated): ?Contract
    {
        if ($property_attributes = GetInventory::run($validated)) {
            $contract = $reference->getContract();
            $contract->property = $property_attributes;
            $contract->forceFill(['property_code' => $contract->property->code]);
            $contract->save();

            return $contract;
        }

        return null;
    }

    public function handle(Reference $reference, $attribs): ?Contract
    {
        $validated = validator($attribs, $this->rules())->validate();


        return $this->update($reference, $validated);
    }

    public function rules(): array
    {
        return [
            'sku' => ['required', 'string', 'min:2'],
        ];
    }
}
