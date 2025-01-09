<?php

namespace App\Http\Controllers\Contract;

use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Inertia\{Inertia, Response};
use Illuminate\Support\Arr;

class VerifyController extends Controller
{
    public function create(Request $request): Response
    {
        $reference_code = Arr::get($request->all(), 'reference_code');

        return Inertia::render('Contract/Verify', compact('reference_code'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = Validator::validate($request->all(), [
            'reference_code' => ['required', 'string', 'exists:vouchers,code'],
        ]);
        $reference_code = Arr::pull($validated, 'reference_code');

        return redirect()->route('verify-contact', compact('reference_code'));
    }
}
