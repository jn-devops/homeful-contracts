<?php

namespace App\Filament\Imports;

use App\Enums\MappingCategory;
use App\Enums\MappingSource;
use App\Enums\MappingType;
use App\Models\Mapping;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Validator;

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
        $rules = [
            'code' => ['required', 'max:255'],
            'path' => ['required', 'max:255'],
            'source' => ['required', 'max:255'],
            'type' => ['required', 'max:255'],
            'category' => ['required', 'max:255'],
        ];

        // Validate the data
        $validator = Validator::make($this->data, $rules);

        if ($validator->fails()) {
            return null; // Skip invalid records
        }

        // Ensure all fields are present in the updateOrCreate method
        return Mapping::updateOrCreate(
            ['code' => $this->data['code']], // Find by 'code'
            [
                'path' => $this->data['path'] ?? '',
                'source' => $this->data['source'] ?? MappingSource::default()->value,
                'title' => $this->data['title'] ?? '',
                'type' => $this->data['type'] ?? MappingType::default()->value,
                'default' => $this->data['default'] ?? '',
                'category' => $this->data['category'] ?? MappingCategory::default()->value,
                'transformer' => $this->data['transformer'] ?? '',
                'options' => $this->data['options'] ?? '',
                'remarks' => $this->data['remarks'] ?? '',
            ]
        );
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
