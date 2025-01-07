<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Homeful\References\Models\Reference;
use App\Actions\Contract\Onboard;
use Illuminate\Http\Request;

class ContactOnboardedController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = Validator::validate($request->all(), ['reference_code' => ['required', 'string', 'min:5']]);
        $reference_code = $validated['reference_code'];
        $reference = Reference::where('code', $reference_code)->firstOrFail();
        Onboard::run($reference);

        return redirect()->route('dashboard');
    }
}
