<?php

namespace App\Exports;

use Homeful\Contracts\Data\ContractData;
use Homeful\Contracts\Models\Contract;
use Maatwebsite\Excel\Concerns\FromCollection;
use Spatie\LaravelData\DataCollection;

class CornerstoneExport implements FromCollection
{
    protected array $uuids;

    public function __construct(array $uuids)
    {
        $this->uuids = $uuids;
    }


    public function collection()
    {
        // Filter contracts where ID exists in $this->uuids
        $contracts = Contract::whereIn('id', $this->uuids)->get();

        // Convert to Spatie Data Collection
        $dataCollection = new DataCollection(ContractData::class, $contracts);

        // Transform the collection to pick only required fields
        return $dataCollection->toCollection()->map(function (ContractData $contract) {
            return [
                'First Name' => $contract->customer?->first_name ?? 'N/A',
                'Last Name' => $contract->customer?->last_name ?? 'N/A',
                'Civil Status' => $contract->customer?->civil_status?->value ?? 'N/A',
            ];
        });
    }
}
