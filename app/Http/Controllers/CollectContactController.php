<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Spatie\Url\Url;

class CollectContactController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $url = Url::fromString(config('homeful-contracts.end-points.collect-contact'));
        $identifier = Arr::get($request->all(), 'reference_code');
        $location = $url->withQueryParameters(compact('identifier'));

        return Inertia::location($location);
    }
}
