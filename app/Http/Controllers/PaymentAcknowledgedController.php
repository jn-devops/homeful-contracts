<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentAcknowledgedRequest;
use Homeful\References\Models\Reference;
use App\Actions\Contract\Pay;
use Illuminate\Support\Arr;

class PaymentAcknowledgedController extends Controller
{
    public function __invoke(PaymentAcknowledgedRequest $request): \Illuminate\Http\JsonResponse
    {
        $payment_payload = $request->all();
        $reference_code = Arr::get($payment_payload, 'data.orderInformation.orderId');
        $reference = Reference::where('code', $reference_code)->firstOrFail();
        Pay::run($reference, $payment_payload);

        $response = [
            'reference_code' => $reference_code,
            'status' => true
        ];

        return response()->json($response);
    }
}
