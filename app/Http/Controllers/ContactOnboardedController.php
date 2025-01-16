<?php

namespace App\Http\Controllers;

use Homeful\References\Models\Reference;
use App\Actions\Contract\Onboard;

class ContactOnboardedController extends Controller
{
    public function __invoke(Reference $reference)
    {
        Onboard::run($reference);

        return redirect()->route('pay.create', ['reference_code' => $reference->code, 'amount'=> 600]);
    }
}
