<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Url\Url;

class RegisterContactController extends Controller
{
    public function __invoke(Request $request)
    {
        $url = Url::fromString(config('homeful-contracts.end-points.register-contact'));
        $showExtra = true;
        $callback = route('consult.create');
        $location = $url->withQueryParameters(compact('showExtra', 'callback'));

        return Inertia::location($location);
    }
}
