<?php

namespace App\Actions\Contract;

use App\Actions\GenerateContractPayloads;
use App\Actions\Sync;
use Homeful\Notifications\Notifications\OnboardedToPaidBuyerNotification;
use Homeful\References\Data\ReferenceData;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\References\Models\Reference;
use Homeful\Contracts\Models\Contract;
use Homeful\Contracts\States\Paid;
use Homeful\Contacts\Models\Customer as Contact;

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
        try {
            $contract = $reference->getContract();
            GenerateContractPayloads::dispatch($contract);
            if ($contract instanceof Contract) {
                $contract->payment = $payment_payload;
                $contract->save();
                $contract->state->transitionTo(Paid::class);
                $contract->notify(new OnboardedToPaidBuyerNotification(ReferenceData::fromModel($reference) ));
            }
            logger('lazarus consultations');
            try {
                $request= Http::withToken(config('lazarus.lazarus_api_token'))->post(config('lazarus.lazarus_url').'api/admin/consultations', [
                    'contact_id' => $contract->contact_id,
                    'contract_id' => $contract->id,
                    'reference_code' => $contract->payment->data->orderInformation->orderId,
                    'consulting_fee' => $contract->payment->data->orderInformation->amount,
                    'payment_method' =>  $contract->payment->data->orderInformation->paymentBrand.':'.$contract->payment->data->orderInformation->paymentType,
                    'transaction_number' => $contract->payment->data->orderInformation->referencedId,
                    'transaction_date' => $contract->payment->data->orderInformation->responseDate,
                    'project' => $contract->property->project->name,
                    'unit_type' => $contract->property->unit_type,
                    'total_contract_price' => $contract->property->tcp,
                    'monthly_amortization' => $contract->mortgage->getLoan()->getMonthlyAmortization()->inclusive()->getAmount()->toFloat(),
                    'payment_terms' => $contract->mortgage->getBalancePaymentTerm(),
                    'current_state' => $contract->state,
                ]);

                logger($request->status());
                logger($request->json());
            }catch (\Exception $exception){
                logger($exception->getMessage());
                Log::error('Error on Create Consultation API', [
                    'message' => $exception->getMessage(),
                    'trace' => $exception->getTraceAsString(),
                ]);
            }
            logger('lazarus set contacts');
            logger($contract->contact_id);
            try {
                $request= Http::withToken(config('lazarus.contact_server_api_token'))->post(config('lazarus.contact_server_url').'set-lazarus-contact/'.$contract->contact_id, [
                    'contact_id' => $contract->contact_id,
                ]);

                logger($request->url());
                logger($request->status());
                logger($request->json());
            }catch (\Exception $exception){
                logger($exception->getMessage());
                Log::error('Error on Set Lazarus Contact API', [
                    'message' => $exception->getMessage(),
                    'trace' => $exception->getTraceAsString(),
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }



        return $reference;
    }
}
