<?php

namespace App\Http\Controllers;

use App\Actions\GenerateContractPayloads;
use App\Actions\GetContractPayload;
use App\Mappings\Processors\MFilesMappingProcessor;
use App\Models\Mapping;
use App\Models\Payload;
use Homeful\Contracts\Models\Contract;
use Illuminate\Http\Request;

class PayloadChecker extends Controller
{
    public function checkPayload(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'contract_id' => ['required', 'exists:contracts,id'],
        ]);

        $contract = Contract::find($request->contract_id);

        if (!$contract) {
            return response()->json(['error' => 'Contract not found'], 404);
        }

        app(GenerateContractPayloads::class)->run($contract);

        $payloads = Payload::with(['mapping' => function ($query) {
            $query->select('code', 'title', 'category');
        }])
            ->where('contract_id', $contract->id)
            ->get(['mapping_code', 'value'])
            ->pluck('value', 'mapping_code')
            ->toArray();

        return response()->json([
            'contract' => $contract->getData(),
            'payloads' => $payloads,
        ]);
    }

    public function MFilesProcessorCheck(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'contract_id' => ['required', 'exists:contracts,id'],
        ]);
        $contract = Contract::find($request->contract_id);

        if (!$contract) {
            return response()->json(['error' => 'Contract not found'], 404);
        }


        $mapping= Mapping::query()
            ->notDeprecated()
            ->notDisabled()
            ->where('source','mfiles')
            ->get();
        // ->each(function (Mapping $mapping) use ($contract, &$processedCount) {
        //     $value[] = [$mapping->code,app(GetContractPayload::class)->run($contract, $mapping)];
        // });


        $mapping= Mapping::query()
            ->notDeprecated()
            ->notDisabled()
            ->where('source','mfiles')
            ->get();
        // ->each(function (Mapping $mapping) use ($contract, &$processedCount) {
        //     $value[] = [$mapping->code,app(GetContractPayload::class)->run($contract, $mapping)];
        // });

        $value= $mapping->map(function ($mapping) use (&$value,$contract) {
            return [$mapping->code,app(GetContractPayload::class)->run($contract, $mapping)];
        });


        return response()->json([
            'mapping'=>$mapping,
            'values'=>$value
        ]);
    }
}
