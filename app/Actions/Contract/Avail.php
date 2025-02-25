<?php

namespace App\Actions\Contract;

use Illuminate\Support\Facades\Validator;
use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\References\Models\Reference;
use App\Actions\GetSellerCommissionCode;
use Homeful\Contracts\States\Availed;
use App\Actions\GetInventory;
use Illuminate\Support\Arr;
use App\Actions\UpdateContractProperty;

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
        try {
            /** extract sku (in associative array) from attribs */
            $product_params = Arr::only($validated, 'sku');

            $contract = app(UpdateContractProperty::class)->run($reference, $validated);
            if ($seller_voucher_code = Arr::get($validated, 'seller_voucher_code'))
                $contract->seller_commission_code = $this->getSellerCommissionCodeFromSellerVoucherCode($seller_voucher_code);

            $contract->state->transitionTo(Availed::class, $reference);
        } catch (\Throwable $th) {
            throw $th;
        }


//        /** retrieve property attributes from inventory */
//        if ($property_attributes = GetInventory::run($product_params)) {
//
//            /** extract the contract model from reference */
//            if ($contract = $reference->getContract()) {
//
//                /** update the contract property attribute */
//                $contract->property = $property_attributes;
//
//                /** update the contract seller_commission_code attribute if otp is valid */
//                if ($seller_voucher_code = Arr::get($validated, 'seller_voucher_code'))
//                    $contract->seller_commission_code = $this->getSellerCommissionCodeFromSellerVoucherCode($seller_voucher_code);
//#
//                $contract->save();
//
//                /** update the state to Availed */
//                $contract->state->transitionTo(Availed::class, $reference);
//
//                /** prepare the refresh model for a return */
//                $reference->refresh();
//            }
//        }

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
            'sku' => ['required', 'string', 'min:5'],
            'seller_voucher_code' => ['nullable', 'string', 'min:4'],
        ];
    }

    protected function getSellerCommissionCodeFromSellerVoucherCode($seller_voucher_code): ?string
    {
        return GetSellerCommissionCode::run($seller_voucher_code);
    }
}
