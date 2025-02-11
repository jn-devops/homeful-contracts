<?php

namespace App\Filament\Resources;

use App\Enums\MappingCategory;
use App\Enums\MappingSource;
use App\Enums\MappingTransformers;
use App\Enums\MappingType;
use App\Filament\Resources\MappingResource\Pages;
use App\Filament\Resources\MappingResource\RelationManagers;
use App\Models\Mapping;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MappingResource extends Resource
{
    protected static ?string $model = Mapping::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Maintenance';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('path')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('source')
                    ->required()
                    ->options(collect(MappingSource::cases())->mapWithKeys(fn($case) => [$case->value => ucfirst(strtolower(str_replace('_', ' ', $case->name)))])->toArray())
                    ->default(MappingSource::default()->value)
                    ->native(false),
                Forms\Components\TextInput::make('title')
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->required()
                    ->native(false)
                    ->options(collect(MappingType::cases())->mapWithKeys(fn($case) => [$case->value => ucfirst(strtolower(str_replace('_', ' ', $case->name)))])->toArray())
                    ->default(MappingType::default()->value),
                Forms\Components\TextInput::make('default')
                    ->maxLength(255),
                Forms\Components\Select::make('category')
                    ->required()
                    ->native(false)
                    ->options(collect(MappingCategory::cases())->mapWithKeys(fn($case) => [$case->value => ucfirst(strtolower(str_replace('_', ' ', $case->name)))])->toArray())
                    ->default(MappingCategory::default()->value),
                Forms\Components\Select::make('transformer')
                    ->options(collect(MappingTransformers::cases())
                        ->mapWithKeys(fn($case) => [$case->name => ucfirst(strtolower(str_replace('_', ' ', $case->name)))])
                        ->toArray()
                    )->native(false),
                Forms\Components\TextInput::make('options'),
                Forms\Components\TextInput::make('remarks')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('disabled_at'),
                Forms\Components\DateTimePicker::make('deprecated_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('code', 'asc')
            ->defaultPaginationPageOption(50)
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('path')
                    ->searchable(),
                Tables\Columns\TextColumn::make('source')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('default')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->searchable(),
                Tables\Columns\TextColumn::make('transformer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('remarks')
                    ->searchable(),
                Tables\Columns\TextColumn::make('disabled_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deprecated_at')
                    ->dateTime()
                    ->sortable(),
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
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ManageMappings::route('/'),
        ];
    }
}
