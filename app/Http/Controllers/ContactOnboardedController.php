<?php

namespace App\Http\Controllers;

use Homeful\References\Models\Reference;
use App\Actions\Contract\Onboard;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactOnboardedController extends Controller
{
    public function __invoke(Reference $reference)
    {
        Onboard::run($reference);

        return redirect()->route('pay.create', ['reference_code' => $reference->code, 'amount'=> 600]);
    }

    public function manual_onboard($reference){
        return Inertia::render('Booking/Onboard', ['reference' => $reference]);
    }
}
