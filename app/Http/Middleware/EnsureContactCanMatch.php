<?php

namespace App\Http\Middleware;

use Homeful\Contacts\Models\Customer as Contact;
use Symfony\Component\HttpFoundation\Response;
use Homeful\References\Models\Reference;
use Illuminate\Http\Request;
use Closure;

class EnsureContactCanMatch
{
    public function handle(Request $request, Closure $next): Response
    {
        $reference_code = $request->route('reference_code');
        $reference = Reference::where('code', $reference_code)->first();
        if ($contact = $reference->getContract()->contact) {
           if ($contact instanceof Contact) {
               if (!$contact->getCanMatch())
                   return redirect('register-contact');
           }
        }

        return $next($request);
    }
}
