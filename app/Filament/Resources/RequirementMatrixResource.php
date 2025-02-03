<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RequirementMatrixResource\Pages;
use App\Filament\Resources\RequirementMatrixResource\RelationManagers;
use App\Models\Requirement;
use App\Models\RequirementMatrix;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Table;
use Homeful\Contacts\Enums\CivilStatus;
use Homeful\Property\Enums\MarketSegment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RequirementMatrixResource extends Resource
{
    protected static ?string $model = RequirementMatrix::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('civil_status')
                    ->label('Civil Status')
                    ->options(collect(\Homeful\Contacts\Enums\CivilStatus::cases())->pluck('value', 'value'))
                    ->native(false)
                    ->required(),
                Forms\Components\Select::make('employment_status')
                    ->label('Employment Status')
                    ->options(collect(\Homeful\Contacts\Enums\EmploymentStatus::cases())->pluck('value', 'value'))
                    ->native(false)
                    ->required(),
                Forms\Components\TextInput::make('market_segment')
                    ->label('Market Segment')
                    ->maxLength(255)
                    ->required(),
                Forms\Components\Select::make('requirements')
                    ->native(false)
                    ->options(Requirement::all()->pluck('description','description'))
                    ->multiple()
                    ->distinct()
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('civil_status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('employment_status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('market_segment')
                    ->searchable(),
                Tables\Columns\TagsColumn::make('requirements')
                    ->label('Requirements')
                    ->getStateUsing(fn ($record) => json_decode($record->requirements)),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function (array $data,Model $record): array {
                        $data['requirements'] = json_decode($record->requirements);
                        return $data;
                    })->using(function (Model $record, array $data): Model {

                        $record->update($data);
                        $record->requirements= json_encode($data['requirements']);
                        $record->save();
                        return $record;
                    })
                ,
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRequirementMatrices::route('/'),
        ];
    }
}
