<?php

namespace App\Http\Controllers;

use Homeful\Contracts\Models\Contract;
use Homeful\Contracts\States\Availed;
use Homeful\Contracts\States\Onboarded;
use Homeful\Contracts\States\Verified;
use Homeful\References\Models\Reference;
use Illuminate\Http\Request;

class ManualOnboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $reference_code = $request->get('reference');
        $reference = Reference::where('code', $reference_code)->first();
        $contract = $reference->getContract();
        if ($contract instanceof Contract) {
            if ($contract->state instanceof Verified) {
                $contract->state->transitionTo(Onboarded::class);
            }
            elseif ($contract->state instanceof Availed) {
                return back()->withErrors([
                    'reference' => $reference_code
                ]);
            }
        }

        return redirect()->intended();
    }
}
