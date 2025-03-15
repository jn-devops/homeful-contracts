<?php

namespace App\Actions;

use Homeful\Contracts\Models\Contract;
use Homeful\References\Models\Reference;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use Lorisleiva\Actions\Concerns\AsJob;

class Sync implements ShouldQueue
{
    use AsAction, AsJob, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(Contract $contract, Reference $reference)
    {
        try {
            Http::withToken(config('lazarus.lazarus_api_token'))->post(config('lazarus.lazarus_url').'api/admin/consultations', [
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
        }catch (\Exception $exception){
            Log::error('Error on Create Consultation API', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
        }

        try {
            Http::withToken(config('lazarus.contact_server_api_token'))->post(config('lazarus.contact_server_url').'set-lazarus-contact/'.$contract->contact_id, [
                'contact_id' => $contract->contact_id,
            ]);
        }catch (\Exception $exception){
            Log::error('Error on Set Lazarus Contact API', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
        }

    }
}
