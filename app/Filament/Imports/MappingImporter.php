<?php

namespace App\Filament\Imports;

use App\Models\Mapping;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class MappingImporter extends Importer
{
    protected static ?string $model = Mapping::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('code')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('path')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('source')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('title')
                ->rules(['max:255']),
            ImportColumn::make('type')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('default')
                ->rules(['max:255']),
            ImportColumn::make('category')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('transformer')
                ->rules(['max:255']),
            ImportColumn::make('options'),
            ImportColumn::make('remarks')
                ->rules(['max:255']),
        ];
    }

    public function resolveRecord(): ?Mapping
    {
        // return Mapping::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Mapping();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your mapping import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
