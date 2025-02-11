<?php

namespace App\Filament\Exports;

use App\Models\Mapping;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class MappingExporter extends Exporter
{
    protected static ?string $model = Mapping::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('code'),
            ExportColumn::make('path'),
            ExportColumn::make('source')
                ->state(function (Mapping $mapping) {
                    return $mapping->source->value??'';
                }),
            ExportColumn::make('title'),
            ExportColumn::make('type')
                ->state(function (Mapping $mapping) {
                    return $mapping->type->value??'';
                }),
            ExportColumn::make('default'),
            ExportColumn::make('category')
                ->state(function (Mapping $mapping) {
                    return $mapping->category->value??'';
                }),
            ExportColumn::make('transformer')
                ->state(function (Mapping $mapping) {
                    return $mapping->transformer->value??'';
                }),
            ExportColumn::make('options'),
            ExportColumn::make('remarks'),
            ExportColumn::make('disabled_at'),
            ExportColumn::make('deprecated_at'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your mapping export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
