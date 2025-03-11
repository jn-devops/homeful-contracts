<?php

namespace App\Http\Middleware;

use Homeful\Contracts\States\{Availed, Verified};
use Symfony\Component\HttpFoundation\Response;
use Homeful\References\Models\Reference;
use Homeful\Contracts\Models\Contract;
use Illuminate\Http\Request;
use Closure;


class ManualStateCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $reference_code = $request->route('reference')->code;
        $reference = Reference::where('code', $reference_code)->first();
        if ($reference instanceof Reference) {
            $contract = $reference->getContract();

            if ($contract->state instanceof Verified) {
                return $next($request);
            }
            elseif ($contract->state instanceof Availed) {
                app('redirect')->setIntendedUrl($request->path());
                return redirect()->route('manual-onboard', ['reference' => $reference_code]);
            }
        }

        return $next($request);
    }
}
