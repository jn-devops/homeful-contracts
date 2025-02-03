<?php

namespace App\Filament\Resources\RequirementMatrixResource\Pages;

use App\Filament\Resources\RequirementMatrixResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRequirementMatrices extends ManageRecords
{
    protected static string $resource = RequirementMatrixResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

}
