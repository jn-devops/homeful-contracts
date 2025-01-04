<?php

namespace App\Http\Controllers\Contract;

use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Actions\Contract\Consult;
use Inertia\{Inertia, Response};
use Illuminate\Support\Arr;

class ConsultController extends Controller
{
    public function create(Request $request): Response
    {
        $props = Arr::only($request->all(), 'contact_reference_code');

        return Inertia::render('Contract/Consult', $props);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = Validator::validate($request->all(), [
            'contact_reference_code' => ['required', 'string', 'min:4']
        ]);
        $contact_reference_code = Arr::get($validated, 'contact_reference_code');
        $reference = Consult::run($contact_reference_code);

        return redirect()->route('avail.create')->with('event', [
            'name' => 'reference',
            'data' => $reference->code
        ]);
    }
}
