<?php

namespace App\Filament\Resources\MappingResource\Pages;

use App\Filament\Resources\MappingResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMappings extends ManageRecords
{
    protected static string $resource = MappingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
