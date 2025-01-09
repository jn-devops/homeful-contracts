<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentCollectedRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string'],
            'data' => ['required', 'array'],
            'data.orderInformation' => ['required', 'array'],
            'data.orderInformation.amount' => ['required', 'integer'],
            'data.orderInformation.attach' => ['required', 'string'],
            'data.orderInformation.currency' => ['required', 'string'],
            'data.orderInformation.goodsDetail' => ['required', 'string'],
            'data.orderInformation.orderAmount' => ['required', 'integer'],
            'data.orderInformation.orderId' => ['required', 'string'],
            'data.orderInformation.paymentBrand' => ['required', 'string'],
            'data.orderInformation.paymentType' => ['required', 'string'],
            'data.orderInformation.qrTag' => ['required', 'integer'],
            'data.orderInformation.referencedId' => ['required', 'string'],
            'data.orderInformation.responseDate' => ['required', 'string'],
            'data.orderInformation.tipFee' => ['required', 'integer'],
            'data.orderInformation.transactionResult' => ['required', 'string'],
            'message' => ['required', 'string'],
        ];
    }
}
