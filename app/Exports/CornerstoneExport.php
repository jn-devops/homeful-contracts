<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Homeful\Contracts\Data\ContractData;
use Spatie\LaravelData\DataCollection;
use Homeful\Contracts\Models\Contract;

class CornerstoneExport implements FromCollection, WithHeadings
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
                'FirstName' => $contract->customer?->first_name ?? 'N/A',
                'MiddleName' => $contract->customer?->middle_name ?? 'N/A',
                'LastName' => $contract->customer?->last_name ?? 'N/A',
                'CivilStatus' => $contract->customer?->civil_status?->value ?? 'N/A',
                'Sex' => $contract->customer?->sex?->value ?? 'N/A',
                'DateOfBirth' => $contract->customer->date_of_birth->format('Y-m-d'),
                'Ownership' => $contract->customer->addresses->first()->ownership->value
            ];
        });
    }

    public function headings(): array
    {
       return [
           'FirstName',
           'MiddleName',
           'LastName',
           'CivilStatus',
           'Sex',
           'DateOfBirth',
           'Ownership'
       ];
    }
}
