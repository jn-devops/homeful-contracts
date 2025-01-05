<?php

namespace App\Http\Controllers\Contract;

use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\Validator;
use Homeful\References\Models\Reference;
use App\Http\Controllers\Controller;
use Inertia\{Inertia, Response};
use App\Actions\Contract\Avail;
use Illuminate\Support\Arr;
use App\Actions\GetMatches;
use App\Classes\ProductOptions;

class AvailController extends Controller
{
    public function create(Request $request): Response
    {
        $matches = [];
        $options = [];
        if ($reference_code = $request->get('reference')) {
            if ($reference = Reference::where('code', $reference_code)->first()) {
                $matches = GetMatches::run($reference, 5, 2);
                ProductOptions::setMatches($matches);
                $options = ProductOptions::records();
            }
        }

        return Inertia::render('Contract/Avail', [
            'matches' => $matches,
            'buttonOptions' => $options
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = Validator::validate($request->all(), [
            'reference_code' => ['required', 'string', 'min:4'],
            'sku' => ['required', 'string', 'min:4'],
        ]);
        $reference_code = Arr::pull($validated, 'reference_code');
        $reference = Reference::where('code', $reference_code)->firstOrFail();
        Avail::run($reference, $validated);

        return redirect()->route('dashboard');
    }
}
