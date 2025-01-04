<?php

namespace App\Http\Controllers;

use Illuminate\Http\{RedirectResponse, Request};
use Inertia\Inertia;
use Spatie\Url\Url;

class RegisterContactController extends Controller
{
    public function __invoke(Request $request)
    {
        $url = Url::fromString('http://homeful-contacts.test/register');
        $showExtra = true;
        $callback = route('consult.create');
        $location = $url->withQueryParameters(compact('showExtra', 'callback'));

//        $url = 'http://homeful-contacts.test/register?showExtra=1&callback=http://homeful-contracts.test/consult/create';

        return Inertia::location($location);
    }
}
