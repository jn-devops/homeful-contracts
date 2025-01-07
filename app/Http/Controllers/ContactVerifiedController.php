<?php

namespace App\Http\Controllers;

use Homeful\References\Models\Reference;
use Homeful\KwYCCheck\Data\CheckinData;
use App\Actions\Contract\Verify;
use Illuminate\Http\Request;

class ContactVerifiedController extends Controller
{
    public function __invoke(Request $request): \Illuminate\Http\JsonResponse
    {
        $checkin_payload = $request->all();
        $reference_code = CheckinData::fromObject($checkin_payload)->inputs->identifier;
        $reference = Reference::where('code', $reference_code)->firstOrFail();
        Verify::run($reference, $checkin_payload);

        $response = [
            'reference_code' => $reference_code,
            'status' => true
        ];

        return response()->json($response);
    }
}
