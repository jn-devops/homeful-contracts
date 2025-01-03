<?php

namespace App\Actions;

use Illuminate\Support\Facades\Validator;
use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\References\Models\Reference;
use Homeful\Contracts\States\Availed;
use Illuminate\Support\Arr;

class Avail
{
    use AsAction;

    /**
     * @param Reference $reference
     * @param array $validated
     * @return Reference
     * @throws \Spatie\ModelStates\Exceptions\CouldNotPerformTransition
     */
    protected function avail(Reference $reference, array $validated): Reference
    {
        /** extract sku (in associative array) from attribs */
        $product_params = Arr::only($validated, 'sku');

        /** retrieve property attributes from inventory */
        if ($property_attributes = GetInventory::run($product_params)) {

            /** extract the contract model from reference */
            if ($contract = $reference->getContract()) {

                /** update the contract property attribute */
                $contract->property = $property_attributes;
                $contract->save();

                /** update the state to Availed */
                $contract->state->transitionTo(Availed::class, $reference);

                /** prepare the refresh model for a return */
                $reference->refresh();
            }
        }

        return $reference;
    }

    /**
     * @param Reference $reference
     * @param array $attribs
     * @return Reference
     * @throws \Spatie\ModelStates\Exceptions\CouldNotPerformTransition
     */
    public function handle(Reference $reference, array $attribs): Reference
    {
        $validated = Validator::validate($attribs, $this->rules());

        return $this->avail($reference, $validated);
    }

    /**
     * @return array[]
     */
    protected function rules(): array
    {
        return [
            'sku' => ['required', 'string', 'min:5']
        ];
    }
}
