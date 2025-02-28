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
        // Filter Contracts where ID exists in $this->uuids
        $contracts = Contract::whereIn('id', $this->uuids)->get();

        // Convert to Spatie Data Collection
        return (new DataCollection(ContractData::class, $contracts))->toCollection();
    }
}
