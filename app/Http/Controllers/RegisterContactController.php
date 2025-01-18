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

        $callback = route('consult.create');
        $showExtra = true;
        $hidePassword = true;
        $location = $url->withQueryParameters(compact('showExtra', 'callback', 'hidePassword'));

        return Inertia::location($location);
    }
}
