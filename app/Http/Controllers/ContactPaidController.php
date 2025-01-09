<?php

namespace App\Http\Controllers;

use Homeful\References\Models\Reference;
use App\Actions\Contract\Onboard;

class ContactPaidController extends Controller
{
    public function __invoke(Reference $reference)
    {
        return redirect()->route('assign.create', ['reference_code' => $reference->code]);
    }
}
