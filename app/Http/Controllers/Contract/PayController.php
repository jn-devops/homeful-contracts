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
        $amount = Arr::get($request->all(), 'amount');
        // dd($amount);
        return Inertia::render('Contract/Pay', compact('reference_code','amount'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = Validator::validate($request->all(), [
            'reference_code' => ['required', 'string', 'exists:vouchers,code'],
        ]);
        $reference_code = Arr::pull($validated, 'reference_code');
<<<<<<< HEAD
        return redirect()->route('dashboard');
=======

        return redirect()->route('collect-contact', compact('reference_code'));
>>>>>>> 294ce777c6eca8950eb21f59c2e71dc1427e77c9
    }
    public function confirmation(Request $request): RedirectResponse
    {   dd($request);
        $validated = Validator::validate($request->all(), [
            'reference_code' => ['required', 'string', 'min:4'],
        ]);
        $reference_code = Arr::pull($validated, 'reference_code');
        return Inertia::render('Contract/PaySuccess', compact('reference_code'));
    }
}
