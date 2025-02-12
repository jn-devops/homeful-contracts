<?php

namespace App\Filament\Resources\MappingResource\Pages;

use App\Filament\Exports\MappingExporter;
use App\Filament\Imports\MappingImporter;
use App\Filament\Resources\MappingResource;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ManageRecords;

class ManageMappings extends ManageRecords
{
    protected static string $resource = MappingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ImportAction::make('import')
                ->importer(MappingImporter::class),
            ExportAction::make('export')
                ->exporter(MappingExporter::class),
        ];
    }
}
