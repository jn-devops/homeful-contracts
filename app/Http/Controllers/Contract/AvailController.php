<?php

namespace App\Http\Controllers\Contract;

use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\Validator;
use Homeful\References\Models\Reference;
use App\Http\Controllers\Controller;
use Inertia\{Inertia, Response};
use App\Actions\Contract\Avail;
use App\Classes\ProductOptions;
use Illuminate\Support\Arr;
use App\Actions\GetMatches;

class AvailController extends Controller
{
    /**
     *  Verbosity
     *
     *  0 - SKU only
     *  1 - Product Attributes
     *  2 - Mortgage Attributes
     *
     * */
    const VERBOSE = 2;

    public function create(Request $request): Response
    {
        $reference_code = $request->get('reference');
        $reference = Reference::where('code', $reference_code)->first();
        return Inertia::render('Contract/Avail', [
            'buttonOptions' => $this->getOptions($request),
            'contactData' => $reference->getContract()->getData()->contact,
            'ref_code' => $reference_code,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = Validator::validate($request->all(), [
            'reference_code' => ['required', 'string', 'exists:vouchers,code'],
            'sku' => ['required', 'string', 'min:4'],
            'seller_voucher_code' => ['nullable', 'string', 'min:4'],
        ]);
        $reference_code = Arr::pull($validated, 'reference_code');
        $reference = Reference::where('code', $reference_code)->first();
        Avail::run($reference, $validated);

        return redirect()->route('verify.create', compact('reference_code'));
    }

    protected function getOptions(Request $request): array
    {
        $options = [];
        if ($reference_code = $request->get('reference')) {
            if ($reference = Reference::where('code', $reference_code)->first()) {
                //TODO: cache this
                $matches = GetMatches::run($reference, config('homeful-contracts.records-limit', 3), self::VERBOSE);
                ProductOptions::setMatches($matches);
                $options = ProductOptions::records();
            }
        }

        return $options;
    }

    public function verifyPromoCode(Request $request)
    {
        $validated = Validator::validate($request->all(), [
            'promo_code' => ['required', 'string'],
        ]);
        return response()->json([
            'valid' => true,
            'message' => 'Promo code is valid',
            'data' => [
                'promo_code' => $validated['promo_code'],
            ],
        ], 200);
        // return response()->json([
        //     'valid' => false,
        //     'message' => 'Promo code is invalid',
        //     'data' => [
        //         'promo_code' => $validated['promo_code'],
        //     ],
        // ], 422);
    }
}
