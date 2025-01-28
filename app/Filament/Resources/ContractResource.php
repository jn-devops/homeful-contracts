<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContractResource\Pages;
use App\Filament\Resources\ContractResource\RelationManagers;
use App\Helpers\LoanTermOptions;
use App\Models\Contact;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Homeful\Contacts\Data\ContactData;
use Homeful\Contracts\Models\Contract;
//use App\Models\Contract;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\HtmlString;
use stdClass;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->persistTabInQueryString()
                    ->contained(false)
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Personal Information')
                            ->icon('heroicon-m-user-circle')->schema([
                                Forms\Components\Section::make()
                                ->schema([
                                    //Personal Information
                                    Forms\Components\Fieldset::make('Personal')->schema([
                                        TextInput::make('buyer.last_name')
                                            ->label('Last Name')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(3),
                                        TextInput::make('buyer.first_name')
                                            ->label('First Name')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(3),

                                        TextInput::make('buyer.middle_name')
                                            ->label('Middle Name')
                                            ->maxLength(255)
                                            ->required(fn (Get $get): bool => ! $get('no_middle_name'))
                                            ->readOnly(fn (Get $get): bool => $get('no_middle_name'))
    //                                            ->hidden(fn (Get $get): bool =>  $get('no_middle_name'))
                                            ->columnSpan(3),
    //                                                Select::make('buyer.name_suffix')
    //                                                    ->label('Suffix')
    //                                                    ->required()
    //                                                    ->native(false)
    //                                                    ->options(NameSuffix::all()->pluck('description','code'))
    //                                                    ->columnSpan(2),
                                        TextInput::make('buyer.name_suffix')
                                            ->label('Suffix')
                                            ->maxLength(255)
                                            ->columnSpan(2),
                                        Forms\Components\Checkbox::make('no_middle_name')
                                            ->live()
                                            ->inline(false)
                                            ->afterStateUpdated(function(Get $get,Set $set){
                                                $set('buyer.middle_name',null);
    //                                                if ($get('no_middle_name')) {
    //                                                }
                                            })
                                            ->columnSpan(1),
                                        TextInput::make('buyer.civil_status')
                                            ->label('Civil Status')
                                            ->maxLength(255)
                                            ->columnSpan(3),
    //                                                Select::make('buyer.civil_status')
    //                                                    ->live()
    //                                                    ->label('Civil Status')
    //                                                    ->required()
    //                                                    ->native(false)
    //                                                    ->options(CivilStatus::all()->pluck('description','code'))
    //                                                    ->columnSpan(3),
                                        Select::make('buyer.sex')
                                            ->label('Gender')
                                            ->required()
                                            ->native(false)
                                            ->options([
                                                'Male'=>'Male',
                                                'Female'=>'Female'
                                            ])
                                            ->columnSpan(3),
                                        DatePicker::make('buyer.date_of_birth')
                                            ->label('Date of Birth')
                                            ->required()
                                            ->native(false)
                                            ->columnSpan(3),
                                        TextInput::make('buyer.nationality')
                                            ->label('Nationality')
                                            ->required()
                                            ->columnSpan(3),
    //                                                Select::make('buyer.nationality')
    //                                                    ->searchable()
    //                                                    ->label('Nationality')
    //                                                    ->required()
    //                                                    ->native(false)
    //                                                    ->options(Nationality::all()->sortBy(fn ($item)=>$item->description === 'Filipino' && $item->code === '076' ? 0 : 1)->pluck('description','code'))
    //                                                    ->columnSpan(3),
                                    ])->columns(12)->columnSpanFull(),
                                    \Filament\Forms\Components\Fieldset::make('Contact Information')
                                        ->schema([
                                            Forms\Components\TextInput::make('buyer.email')
                                                ->label('Email')
                                                // ->email()
                                                ->required()
                                                ->maxLength(255)
                                                ->live()
                                                ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                                    $livewire->validateOnly($component->getStatePath());
                                                })
                                                ->unique(ignoreRecord: true,table: Contact::class,column: 'email')
                                                ->columnSpan(3),

                                            Forms\Components\TextInput::make('buyer.mobile')
                                                ->label('Mobile')
                                                ->required()
                                                ->prefix('+63')
                                                ->regex("/^[0-9]+$/")
                                                ->minLength(10)
                                                ->maxLength(10)
                                                ->live()
                                                ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                                    $livewire->validateOnly($component->getStatePath());
                                                })
                                                ->columnSpan(3),

                                            Forms\Components\TextInput::make('buyer.other_mobile')
                                                ->label('Other Mobile')
                                                ->prefix('+63')
                                                ->regex("/^[0-9]+$/")
                                                ->minLength(10)
                                                ->maxLength(10)
                                                ->live()
                                                ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                                    $livewire->validateOnly($component->getStatePath());
                                                })
                                                ->columnSpan(3),

                                            Forms\Components\TextInput::make('buyer.landline')
                                                ->label('Landline')
                                                ->columnSpan(3),
                                        ])->columns(12)->columnSpanFull(),
                                    //Address
                                    \Filament\Forms\Components\Fieldset::make('Address')
                                        ->schema([
                                            Forms\Components\Fieldset::make('Present')->schema([
                                                TextInput::make('buyer.address.present.ownership')
                                                    ->label('Ownership')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpan(3),
    //                                                        Select::make('buyer.address.present.ownership')
    //                                                            ->options(HomeOwnership::all()->pluck('description','code'))
    //                                                            ->native(false)
    //                                                            ->required()
    //                                                            ->columnSpan(3),
                                                TextInput::make('buyer.address.present.country')
                                                    ->label('Country')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpan(3),
    //                                                        Select::make('buyer.address.present.country')
    //                                                            ->searchable()
    //                                                            ->options(Country::all()->sortBy(fn ($item)=>$item->description === 'Philippines' && $item->code === 'PH' ? 0 : 1)->pluck('description','code'))
    //                                                            ->native(false)
    //                                                            ->live()
    //                                                            ->required()
    //                                                            ->columnSpan(3),
                                                TextInput::make('buyer.address.present.postal_code')
                                                    ->minLength(4)
                                                    ->maxLength(4)
                                                    ->required()
                                                    ->columnSpan(3),
    //                                                        Checkbox::make('buyer.address.present.same_as_permanent')
    //                                                            ->label('Same as Permanent')
    //                                                            ->inline(false)
    //                                                            ->live()
    //                                                            ->columnSpan(3),
                                                TextInput::make('buyer.address.present.region')
                                                    ->label('Region')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpan(3),
    //                                                        Select::make('buyer.address.present.region')
    //                                                            ->searchable()
    //                                                            ->options(PhilippineRegion::all()->pluck('region_description', 'region_code'))
    //                                                            ->required(fn(Get $get):bool => $get('buyer.address.present.country') == 'PH')
    //                                                            ->hidden(fn(Get $get):bool => $get('buyer.address.present.country') != 'PH'&&$get('buyer.address.present.country')!=null)
    //                                                            ->native(false)
    //                                                            ->live()
    //                                                            ->afterStateUpdated(function (Set $set, $state) {
    //                                                                $set('buyer.address.present.administrative_area', '');
    //                                                                $set('buyer.address.present.locality', '');
    //                                                                $set('buyer.address.present.sublocality', '');
    //                                                            })
    //                                                            ->columnSpan(3),
                                                TextInput::make('buyer.address.present.administrative_area')
                                                    ->label('Province')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpan(3),
    //                                                        Select::make('buyer.address.present.administrative_area')
    //                                                            ->label('Province')
    //                                                            ->searchable()
    //                                                            ->options(fn(Get $get): Collection => PhilippineProvince::query()
    //                                                                ->where('region_code', $get('buyer.address.present.region'))
    //                                                                ->pluck('province_description', 'province_code'))
    //                                                            ->required(fn(Get $get):bool => $get('buyer.address.present.country') == 'PH')
    //                                                            ->hidden(fn(Get $get):bool => $get('buyer.address.present.country') != 'PH'&&$get('buyer.address.present.country')!=null)
    //                                                            ->native(false)
    //                                                            ->live()
    //                                                            ->afterStateUpdated(function (Set $set, $state) {
    //                                                                $set('buyer.address.present.locality', '');
    //                                                                $set('buyer.address.present.sublocality', '');
    //                                                            })
    //                                                            ->columnSpan(3),
                                                TextInput::make('buyer.address.present.locality')
                                                    ->label('City')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpan(3),
    //                                                        Select::make('buyer.address.present.locality')
    //                                                            ->label('City')
    //                                                            ->searchable()
    //                                                            ->required(fn(Get $get):bool => $get('buyer.address.present.country') == 'PH')
    //                                                            ->hidden(fn(Get $get):bool => $get('buyer.address.present.country') != 'PH'&&$get('buyer.address.present.country')!=null)
    //                                                            ->options(fn(Get $get): Collection => PhilippineCity::query()
    //                                                                ->where('province_code', $get('buyer.address.present.administrative_area'))
    //                                                                ->pluck('city_municipality_description', 'city_municipality_code'))
    //                                                            ->native(false)
    //                                                            ->live()
    //                                                            ->afterStateUpdated(function (Set $set, $state) {
    //                                                                $set('buyer.address.present.sublocality', '');
    //                                                            })
    //                                                            ->columnSpan(3),
                                                TextInput::make('buyer.address.present.sublocality')
                                                    ->label('Barangay')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpan(3),
    //                                                        Select::make('buyer.address.present.sublocality')
    //                                                            ->label('Barangay')
    //                                                            ->searchable()
    //                                                            ->options(fn(Get $get): Collection => PhilippineBarangay::query()
    //                                                                ->where('region_code', $get('buyer.address.present.region'))
    ////                                                    ->where('province_code', $get('buyer.address.present.province'))                                            ->where('province_code', $get('province'))
    //                                                                ->where('city_municipality_code', $get('buyer.address.present.locality'))
    //                                                                ->pluck('barangay_description', 'barangay_code')
    //                                                                ->mapWithKeys(function ($description, $code) {
    //                                                                    return [$code => $description]; // Transforming to title case
    //                                                                })
    //                                                            )
    //                                                            ->required(fn(Get $get):bool => $get('buyer.address.present.country') == 'PH')
    //                                                            ->hidden(fn(Get $get):bool => $get('buyer.address.present.country') != 'PH'&&$get('buyer.address.present.country')!=null)
    //                                                            ->native(false)
    //                                                            ->live()
    //                                                            ->columnSpan(3),
                                                TextInput::make('buyer.address.present.address1')
                                                    ->label(fn(Get $get)=>$get('buyer.address.present.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
    //                                        ->hint('Unit Number, House/Building/Street No, Street Name')
    //                                                            ->placeholder(fn(Get $get)=>$get('buyer.address.present.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
    //                                                            ->required(fn(Get $get):bool => $get('buyer.address.present.country') != 'PH')
                                                    ->autocapitalize('words')
                                                    ->maxLength(255)
                                                    ->live()
                                                    ->columnSpan(12),
    //                                                        Placeholder::make('buyer.address.present.full_address')
    //                                                            ->label('Full Address')
    //                                                            ->live()
    //                                                            ->content(function (Get $get): string {
    //                                                                $region = PhilippineRegion::where('region_code', $get('buyer.address.present.region'))->first();
    //                                                                $province = PhilippineProvince::where('province_code', $get('buyer.address.present.administrative_area'))->first();
    //                                                                $city = PhilippineCity::where('city_municipality_code', $get('buyer.address.present.locality'))->first();
    //                                                                $barangay = PhilippineBarangay::where('barangay_code', $get('buyer.address.present.sublocality'))->first();
    //                                                                $address = $get('buyer.address.present.address');
    //                                                                $addressParts = array_filter([
    //                                                                    $address,
    //                                                                    $barangay != null ? $barangay->barangay_description : '',
    //                                                                    $city != null ? $city->city_municipality_description : '',
    //                                                                    $province != null ? $province->province_description : '',
    //                                                                    // $region != null ? $region->region_description : '',
    //                                                                ]);
    //                                                                return implode(', ', $addressParts);
    //                                                            })->columnSpanFull()


                                            ])->columns(12)->columnSpanFull(),
                                            Group::make()->schema(
                                                fn(Get $get) => $get('buyer.address.present.same_as_permanent') == null ? [
                                                    Forms\Components\Fieldset::make('Permanent')->schema([
                                                        Group::make()->schema([
                                                            TextInput::make('buyer.address.permanent.ownership')
                                                                ->label('Ownership')
                                                                ->required()
                                                                ->maxLength(255)
                                                                ->columnSpan(3),
    //                                                                    Select::make('buyer.address.permanent.ownership')
    //                                                                        ->options(HomeOwnership::all()->pluck('description','code'))
    //                                                                        ->native(false)
    //                                                                        ->required()
    //                                                                        ->columnSpan(3),
                                                            TextInput::make('buyer.address.permanent.country')
                                                                ->label('Country')
                                                                ->required()
                                                                ->maxLength(255)
                                                                ->columnSpan(3),
    //                                                                    Select::make('buyer.address.permanent.country')
    //                                                                        ->searchable()
    //                                                                        ->options(Country::all()->sortBy(fn ($item)=>$item->description === 'Philippines' && $item->code === 'PH' ? 0 : 1)->pluck('description','code'))
    //                                                                        ->native(false)
    //                                                                        ->live()
    //                                                                        ->required()
    //                                                                        ->columnSpan(3),
                                                            TextInput::make('buyer.address.permanent.postal_code')
                                                                ->minLength(4)
                                                                ->maxLength(4)
                                                                ->required()
                                                                ->columnSpan(3),
                                                        ])
                                                            ->columns(12)->columnSpanFull(),

                                                        TextInput::make('buyer.address.permanent.region')
                                                            ->label('Country')
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->columnSpan(3),
    //                                                                Select::make('buyer.address.permanent.region')
    //                                                                    ->searchable()
    //                                                                    ->options(PhilippineRegion::all()->pluck('region_description', 'region_code'))
    //                                                                    ->required(fn(Get $get):bool => $get('buyer.address.permanent.country') == 'PH')
    //                                                                    ->hidden(fn(Get $get):bool => $get('buyer.address.permanent.country') != 'PH'&&$get('buyer.address.permanent.country')!=null)
    //                                                                    ->native(false)
    //                                                                    ->live()
    //                                                                    ->afterStateUpdated(function (Set $set, $state) {
    //                                                                        $set('buyer.address.permanent.province', '');
    //                                                                        $set('buyer.address.permanent.city', '');
    //                                                                        $set('buyer.address.permanent.barangay', '');
    //                                                                    })
    //                                                                    ->columnSpan(3),
                                                        TextInput::make('buyer.address.permanent.province')
                                                            ->label('Province')
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->columnSpan(3),
    //                                                                Select::make('buyer.address.permanent.province')
    //                                                                    ->searchable()
    //                                                                    ->options(fn(Get $get): Collection => PhilippineProvince::query()
    //                                                                        ->where('region_code', $get('buyer.address.permanent.region'))
    //                                                                        ->pluck('province_description', 'province_code'))
    //                                                                    ->required(fn(Get $get):bool => $get('buyer.address.permanent.country') == 'PH')
    //                                                                    ->hidden(fn(Get $get):bool => $get('buyer.address.permanent.country') != 'PH'&&$get('buyer.address.permanent.country')!=null)
    //                                                                    ->native(false)
    //                                                                    ->live()
    //                                                                    ->afterStateUpdated(function (Set $set, $state) {
    //                                                                        $set('buyer.address.permanent.city', '');
    //                                                                        $set('buyer.address.permanent.barangay', '');
    //                                                                    })
    //                                                                    ->columnSpan(3),
                                                        TextInput::make('buyer.address.permanent.city')
                                                            ->label('City')
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->columnSpan(3),
    //                                                                Select::make('buyer.address.permanent.city')
    //                                                                    ->searchable()
    //                                                                    ->options(fn(Get $get): Collection => PhilippineCity::query()
    //                                                                        ->where('province_code', $get('buyer.address.permanent.province'))
    //                                                                        ->pluck('city_municipality_description', 'city_municipality_code'))
    //                                                                    ->required(fn(Get $get):bool => $get('buyer.address.permanent.country') == 'PH')
    //                                                                    ->hidden(fn(Get $get):bool => $get('buyer.address.permanent.country') != 'PH'&&$get('buyer.address.permanent.country')!=null)
    //                                                                    ->native(false)
    //                                                                    ->live()
    //                                                                    ->afterStateUpdated(function (Set $set, $state) {
    //                                                                        $set('buyer.address.permanent.barangay', '');
    //                                                                    })
    //                                                                    ->columnSpan(3),
                                                        TextInput::make('buyer.address.permanent.barangay')
                                                            ->label('Barangay')
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->columnSpan(3),
    //                                                                Select::make('buyer.address.permanent.barangay')
    //                                                                    ->searchable()
    //                                                                    ->options(fn(Get $get): Collection => PhilippineBarangay::query()
    //                                                                        ->where('region_code', $get('buyer.address.permanent.region'))
    ////                                                    ->where('province_code', $get('buyer.address.present.province'))                                            ->where('province_code', $get('province'))
    //                                                                        ->where('city_municipality_code', $get('buyer.address.permanent.city'))
    //                                                                        ->pluck('barangay_description', 'barangay_code')
    //                                                                        ->mapWithKeys(function ($description, $code) {
    //                                                                            return [$code => $description]; // Transforming to title case
    //                                                                        })
    //                                                                    )
    //                                                                    ->required(fn(Get $get):bool => $get('buyer.address.permanent.country') == 'PH')
    //                                                                    ->hidden(fn(Get $get):bool => $get('buyer.address.permanent.country') != 'PH'&&$get('buyer.address.permanent.country')!=null)
    //                                                                    ->native(false)
    //                                                                    ->live()
    //                                                                    ->columnSpan(3),
                                                        TextInput::make('buyer.address.permanent.address1')
                                                            ->label(fn(Get $get)=>$get('buyer.address.permanent.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                                            ->placeholder(fn(Get $get)=>$get('buyer.address.permanent.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                                            ->required(fn(Get $get):bool => $get('buyer.address.permanent.country') != 'PH')
                                                            ->autocapitalize('words')
                                                            ->maxLength(255)
                                                            ->live()
                                                            ->columnSpan(12),
    //                                                                Placeholder::make('buyer.address.permanent.full_address')
    //                                                                    ->label('Full Address')
    //                                                                    ->live()
    //                                                                    ->content(function (Get $get): string {
    //                                                                        $region = PhilippineRegion::where('region_code', $get('buyer.address.permanent.region'))->first();
    //                                                                        $province = PhilippineProvince::where('province_code', $get('buyer.address.permanent.province'))->first();
    //                                                                        $city = PhilippineCity::where('city_municipality_code', $get('buyer.address.permanent.city'))->first();
    //                                                                        $barangay = PhilippineBarangay::where('barangay_code', $get('buyer.address.permanent.barangay'))->first();
    //                                                                        $address = $get('buyer.address.permanent.address');
    //                                                                        $addressParts = array_filter([
    //                                                                            $address,
    //                                                                            $barangay != null ? $barangay->barangay_description : '',
    //                                                                            $city != null ? $city->city_municipality_description : '',
    //                                                                            $province != null ? $province->province_description : '',
    //                                                                            // $region != null ? $region->region_description : '',
    //                                                                        ]);
    //                                                                        return implode(', ', $addressParts);
    //                                                                    })->columnSpan(12),


                                                    ])->columns(12)->columnSpanFull(),
                                                ] : []
                                            )->columns(12)->columnSpanFull(),
                                        ])->columns(12)->columnSpanFull(),
                                    //Employment
                                    \Filament\Forms\Components\Fieldset::make('Employment')->schema([
                                        TextInput::make('buyer_employment.employment_type')
                                            ->label('Employment Type')
                                            ->maxLength(255)
                                            ->columnSpan(3),
    //                                                Select::make('buyer_employment.employment_type')
    //                                                    ->label('Employment Type')
    //                                                    ->live()
    //                                                    ->required()
    //                                                    ->native(false)
    //                                                    ->options(EmploymentType::all()->pluck('description','code'))
    //                                                    ->columnSpan(3),
                                        TextInput::make('buyer_employment.employment_status')
                                            ->label('Employment Status')
                                            ->maxLength(255)
                                            ->columnSpan(3),
    //                                                Select::make('buyer_employment.employment_status')
    //                                                    ->label('Employment Status')
    //                                                    ->required(fn (Get $get): bool =>   $get('buyer_employment.employment_type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
    //                                                    ->hidden(fn (Get $get): bool =>   $get('buyer_employment.employment_type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
    //                                                    ->native(false)
    //                                                    ->options(EmploymentStatus::all()->pluck('description','code'))
    //                                                    ->columnSpan(3),
                                        TextInput::make('buyer_employment.years_in_service')
                                            ->label('Tenure')
                                            ->maxLength(255)
                                            ->columnSpan(3),
    //                                                Select::make('buyer_employment.years_in_service')
    //                                                    ->label('Tenure')
    //                                                    ->required(fn (Get $get): bool =>   $get('buyer_employment.employment_type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
    //                                                    ->hidden(fn (Get $get): bool =>   $get('buyer_employment.employment_type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
    //                                                    ->native(false)
    //                                                    ->options(Tenure::all()->pluck('description','code'))
    //                                                    ->columnSpan(3),
                                        TextInput::make('buyer_employment.current_position')
                                            ->label('Current Position')
                                            ->maxLength(255)
                                            ->columnSpan(3),
    //                                                Select::make('buyer_employment.current_position')
    //                                                    ->label('Current Position')
    //                                                    ->native(false)
    //                                                    ->options(CurrentPosition::all()->pluck('description','code'))
    //                                                    ->searchable()
    //                                                    ->required(fn (Get $get): bool =>   $get('buyer_employment.employment_type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
    //                                                    ->hidden(fn (Get $get): bool =>   $get('buyer_employment.employment_type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
    //                                                    ->columnSpan(3),

                                        TextInput::make('buyer_employment.rank')
                                            ->label('Rank')
    //                                        ->required(fn (Get $get): bool =>   $get('buyer_employment.employment_type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
    //                                        ->hidden(fn (Get $get): bool =>   $get('buyer_employment.employment_type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                            ->maxLength(255)
                                            ->columnSpan(3),
                                        TextInput::make('buyer_employment.employer.industry')
                                            ->label('Work Industry')
                                            ->maxLength(255)
                                            ->columnSpan(3),
    //                                                Select::make('buyer_employment.employer.industry')
    //                                                    ->label('Work Industry')
    //                                                    ->required()
    //                                                    ->native(false)
    //                                                    ->options(WorkIndustry::all()->pluck('description','code'))
    //                                                    ->searchable()
    //                                                    ->columnSpan(3),
                                        TextInput::make('buyer_employment.monthly_gross_income')
                                            ->label('Gross Monthly Income')
                                            ->numeric()
                                            // ->afterStateUpdated(function(Set $set, $state){
                                            //     $set('order.hdmf.input.GROSS_INCOME_PRINCIPAL', $state);
                                            // })
                                            ->prefix('PHP')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(3),
                                        Group::make()->schema([
                                            TextInput::make('buyer_employment.id.tin')
                                                ->label('Tax Identification Number')
                                                ->required()
                                                ->maxLength(255)
                                                ->columnSpan(3),
                                            TextInput::make('buyer_employment.id.pagibig')
                                                ->label('PAG-IBIG Number')
                                                ->required()
                                                ->maxLength(255)
                                                ->columnSpan(3),
                                            TextInput::make('buyer_employment.id.sss')
                                                ->label('SSS/GSIS Number')
                                                ->maxLength(255)
                                                ->columnSpan(3),
                                        ])->columnSpanFull()->columns(12),


                                    ])->columns(12)->columnSpanFull(),
                                    //Employer
                                    Forms\Components\Fieldset::make('Employer/Business')->schema([
                                        TextInput::make('buyer_employment.employer.name')
                                            ->label('Employer / Business Name')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(3),
    //                                        TextInput::make('buyer_employment.employer.contact_person')
    //                                            ->label('Contact Person')
    //                                            ->required(fn (Get $get): bool =>   $get('buyer_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
    //                                            ->hidden(fn (Get $get): bool =>   $get('buyer_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
    //                                            ->maxLength(255)
    //                                            ->columnSpan(3),
                                        TextInput::make('buyer_employment.employer.email')
                                            ->label('Email')
                                            // ->email()
    //                                                    ->required(fn (Get $get): bool =>   $get('buyer_employment.employment_type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
    //                                                    ->hidden(fn (Get $get): bool =>   $get('buyer_employment.employment_type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                            ->maxLength(255)
                                            ->columnSpan(3),
                                        TextInput::make('buyer_employment.employer.contact_no')
                                            ->label('Contact Number')
    //                                                    ->required(fn (Get $get): bool =>   $get('buyer_employment.employment_type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
    //                                                    ->hidden(fn (Get $get): bool =>   $get('buyer_employment.employment_type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
    //                                            ->prefix(fn (Get $get):String =>  ($get('buyer_employment.employment_type')==EmploymentType::where('description','Overseas Filipino Worker (OFW)')->first()->code) ? '' : '+63')
    //                                            ->regex("/^[0-9]+$/")
    //                                            ->minLength(10)
    //                                            ->maxLength(10)
                                            ->live()
    //                                        ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
    ////                                            $livewire->validateOnly($component->getStatePath());
    //                                        })
                                            ->columnSpan(3),
                                        TextInput::make('buyer_employment.employer.year_established')
                                            ->label('Year Established')
                                            ->required()
                                            ->numeric()
                                            ->columnSpan(3),
    //                                        Select::make('employment.employer.years_of_operation')
    //                                            ->label('Years of Operation')
    //                                            ->required()
    //                                            ->native(false)
    //                                            ->options(YearsOfOperation::all()->pluck('description','code'))
    //                                            ->columnSpan(3),
                                        Forms\Components\Fieldset::make('Address')->schema([
                                            Group::make()
                                                ->schema([
                                                    TextInput::make('buyer_employment.employer.address.country')
                                                        ->label('Country')
                                                        ->required()
                                                        ->maxLength(255)
                                                        ->columnSpan(3),
    //                                                            Select::make('buyer_employment.employer.address.country')
    //                                                                ->searchable()
    //                                                                ->options(Country::all()->sortBy(fn ($item)=>$item->description === 'Philippines' && $item->code === 'PH' ? 0 : 1)->pluck('description','code'))
    //                                                                ->native(false)
    //                                                                ->live()
    //                                                                ->required()
    //                                                                ->columnSpan(3),
                                                ])
                                                ->columns(12)
                                                ->columnSpanFull(),
                                            TextInput::make('buyer_employment.employer.address.region')
                                                ->label('Region')
                                                ->required()
                                                ->maxLength(255)
                                                ->columnSpan(3),
    //                                                    Select::make('buyer_employment.employer.address.region')
    //                                                        ->searchable()
    //                                                        ->options(PhilippineRegion::all()->pluck('region_description','region_code'))
    //                                                        ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') == 'PH')
    //                                                        ->hidden(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH'&&$get('buyer_employment.employer.address.country')!=null)
    //                                                        ->native(false)
    //                                                        ->live()
    //                                                        ->afterStateUpdated(function(Set $set, $state){
    //                                                            $set('buyer_employment.employer.address.administrative_area','');
    //                                                            $set('buyer_employment.employer.address.locality','');
    //                                                            $set('buyer_employment.employer.address.sublocality','');
    //                                                        })
    //                                                        ->columnSpan(3),
                                            TextInput::make('buyer_employment.employer.address.administrative_area')
                                                ->label('Province')
                                                ->required()
                                                ->maxLength(255)
                                                ->columnSpan(3),
    //                                                    Select::make('buyer_employment.employer.address.administrative_area')
    //                                                        ->label('Province')
    //                                                        ->searchable()
    //                                                        ->options(fn (Get $get): Collection => PhilippineProvince::query()
    //                                                            ->where('region_code', $get('buyer_employment.employer.address.region'))
    //                                                            ->pluck('province_description', 'province_code'))
    //                                                        ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') == 'PH')
    //                                                        ->hidden(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH'&&$get('buyer_employment.employer.address.country')!=null)
    //                                                        ->native(false)
    //                                                        ->live()
    //                                                        ->afterStateUpdated(function(Set $set, $state){
    //                                                            $set('buyer_employment.employer.address.locality','');
    //                                                            $set('buyer_employment.employer.address.sublocality','');
    //                                                        })
    //                                                        ->columnSpan(3),
                                            TextInput::make('buyer_employment.employer.address.locality')
                                                ->label('City')
                                                ->required()
                                                ->maxLength(255)
                                                ->columnSpan(3),
    //                                                    Select::make('buyer_employment.employer.address.locality')
    //                                                        ->label('City')
    //                                                        ->searchable()
    //                                                        ->options(fn (Get $get): Collection => PhilippineCity::query()
    //                                                            ->where('province_code', $get('buyer_employment.employer.address.administrative_area'))
    //                                                            ->pluck('city_municipality_description', 'city_municipality_code'))
    //                                                        ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') == 'PH')
    //                                                        ->hidden(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH'&&$get('buyer_employment.employer.address.country')!=null)
    //                                                        ->native(false)
    //                                                        ->live()
    //                                                        ->afterStateUpdated(function(Set $set, $state){
    //                                                            $set('buyer_employment.employer.address.sublocality','');
    //                                                        })
    //                                                        ->columnSpan(3),
                                            TextInput::make('buyer_employment.employer.address.sublocality')
                                                ->label('Barangay')
                                                ->required()
                                                ->maxLength(255)
                                                ->columnSpan(3),
    //                                                    Select::make('buyer_employment.employer.address.sublocality')
    //                                                        ->label('Barangay')
    //                                                        ->searchable()
    //                                                        ->options(fn (Get $get): Collection =>PhilippineBarangay::query()
    //                                                            ->where('region_code', $get('buyer_employment.employer.address.region'))
    ////                                                    ->where('province_code', $get('buyer.address.present.province'))                                            ->where('province_code', $get('province'))
    //                                                            ->where('city_municipality_code', $get('buyer_employment.employer.address.locality'))
    //                                                            ->pluck('barangay_description', 'barangay_code')
    //                                                            ->mapWithKeys(function ($description, $code) {
    //                                                                return [$code => $description]; // Transforming to title case
    //                                                            })
    //                                                        )
    //                                                        ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') == 'PH')
    //                                                        ->hidden(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH'&&$get('buyer_employment.employer.address.country')!=null)
    //                                                        ->native(false)
    //                                                        ->live()
    //                                                        ->columnSpan(3),
                                            TextInput::make('buyer_employment.employer.address.address1')
                                                ->label(fn(Get $get)=>$get('buyer_employment.employer.address.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                                ->placeholder(fn(Get $get)=>$get('buyer_employment.employer.address.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                                ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH')
                                                ->autocapitalize('words')
                                                ->maxLength(255)
                                                ->live()
                                                ->columnSpan(12),


                                        ])->columns(12)->columnSpanFull(),
                                    ])->columns(12)->columnSpanFull(),
                                ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Consultation')
                            ->icon('heroicon-m-home')
                            ->schema([

                            ]),
                        Forms\Components\Tabs\Tab::make('Upload Documents')
                            ->icon('heroicon-m-cloud-arrow-up')
                            ->schema([

                            ]),
                        Forms\Components\Tabs\Tab::make('Generated Documents')
                            ->icon('heroicon-m-document-duplicate')
                            ->schema([

                            ]),
                    ])
                    ->columnSpan(3),
                    Forms\Components\Section::make()
                        ->schema([
                            Placeholder::make('state')
                                ->label('Status')
                                ->content(fn ($record) => $record->state),
                            Placeholder::make('reference_code')
                                ->label('Reference Code')
                                ->content(fn ($record) => $record->reference_code),
                            Placeholder::make('created_at')
                                ->label('Date Created')
                                ->content(fn ($record) => $record?->created_at?->format('M d, Y') ?? new HtmlString('&mdash;')),
                            Placeholder::make('created_at')
                                ->label('Aging')
                                ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? new HtmlString('&mdash;')),
                        ])
                        ->columnSpan(1),
            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(50)
            ->columns([
                TextColumn::make('state'),
                TextColumn::make('created_at')
                    ->label('Date Created')
                    ->date(),
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->state(function (Model $record) {
                        return $record->created_at->addDays(7)->format('M d, Y');
                    }),
                TextColumn::make('aging')
                    ->label('Aging')
                    ->state(
                        static function (Model $record): string {
                            return $record->created_at->diffForHumans(['short' => true]);
                        }
                    ),
                TextColumn::make('contact.name')
                    ->label('Name'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContracts::route('/'),
            'create' => Pages\CreateContract::route('/create'),
            'edit' => Pages\EditContract::route('/{record}/edit'),
        ];
    }
}
