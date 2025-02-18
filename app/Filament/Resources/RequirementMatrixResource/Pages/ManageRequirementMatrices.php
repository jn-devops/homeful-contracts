<?php

namespace App\Filament\Resources\RequirementMatrixResource\Pages;

use App\Filament\Resources\RequirementMatrixResource;
use App\Models\RequirementMatrix;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;

class ManageRequirementMatrices extends ManageRecords
{
    protected static string $resource = RequirementMatrixResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(function ( array $data): Model {
                    $record= RequirementMatrix::create([
                        'civil_status' => $data['civil_status'],
                        'employment_status' => $data['employment_status'],
                        'market_segment' => $data['market_segment'],
                        'requirements' => json_encode($data['requirements']),
                    ]);
                    $record->save();
                    return $record;
                }),
            ];
    }

}
