<?php

namespace App\Http\Controllers\Contract;

use Illuminate\Http\{RedirectResponse, Request};
use Homeful\References\Models\Reference;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Inertia\{Inertia, Response};
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class PayController extends Controller
{
    public function create(Request $request): Response
    {
        $referenceCode = Arr::get($request->all(), 'reference_code');
        $reference = Reference::where('code', $referenceCode)->first();
        $contract = $reference->getContract();
        $property = $contract->property;
        // dd($property);
        $amount = Arr::get($request->all(), 'amount');
        $projectName = $property->project->name ?? '';
        $projectImgLink = $property->product->facade_url ?? '';
        $projectLocation = $property->project->location ?? '';
        return Inertia::render('Contract/Pay', compact('referenceCode','amount','projectName', 'projectImgLink','projectLocation'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = Validator::validate($request->all(), [
            'reference_code' => ['required', 'string', 'exists:vouchers,code'],
        ]);
        $reference_code = Arr::pull($validated, 'reference_code');

        return redirect()->route('collect-contact', compact('reference_code'));
    }
    public function confirmation($reference_code)
    {   
        // TODO: Create Validation for Request -> reference_code
        $reference = Reference::where('code', $reference_code)->first();
        $contract = $reference->getContract();
        $payment_details = $contract->payment;
        // dd($payment_details);
        return Inertia::render('Contract/PaySuccess', [
            'reference_code' => $reference_code,
            'payment_details' => $payment_details,
            'contract' => $contract,
        ]);
    }
}
