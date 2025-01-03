<?php

namespace App\Http\Controllers\Contract;

use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\Validator;
use Homeful\References\Models\Reference;
use App\Http\Controllers\Controller;
use Inertia\{Inertia, Response};
use App\Actions\Contract\Avail;
use Illuminate\Support\Arr;

class AvailController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Contract/Avail');
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
