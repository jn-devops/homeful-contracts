<?php

namespace App\Http\Controllers\Contract;

use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\Validator;
use Homeful\References\Models\Reference;
use App\Http\Controllers\Controller;
use App\Actions\Contract\Assign;
use Inertia\{Inertia, Response};
use Illuminate\Support\Arr;

class AssignController extends Controller
{
    public function create(Request $request): Response
    {
        $reference_code = Arr::get($request->all(), 'reference_code');

        return Inertia::render('Contract/Assign', compact('reference_code'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = Validator::validate($request->all(), [
            'reference_code' => ['required', 'string', 'exists:vouchers,code'],
            'assigned_contact' => ['required', 'string', 'min:4'],
        ]);
        $reference_code = Arr::pull($validated, 'reference_code');
        $reference = Reference::where('code', $reference_code)->first();
        Assign::run($reference, $validated);

        return redirect()->route('dashboard', compact('reference_code'));//TODO: change this to contract preview
    }
}
