<?php

namespace App\Http\Controllers\Contract;

use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Inertia\{Inertia, Response};
use Illuminate\Support\Arr;

class PayController extends Controller
{
    public function create(Request $request): Response
    {
        $reference_code = Arr::get($request->all(), 'reference_code');

        return Inertia::render('Contract/Pay', compact('reference_code'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = Validator::validate($request->all(), [
            'reference_code' => ['required', 'string', 'min:4'],
        ]);
        $reference_code = Arr::pull($validated, 'reference_code');

        return redirect()->route('dashboard');
    }
}
