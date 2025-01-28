<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContractResource\Pages;
use App\Filament\Resources\ContractResource\RelationManagers;
use App\Helpers\LoanTermOptions;
use App\Models\Contact;
use Exception;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
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
use Illuminate\Support\Facades\Http;
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
                                        TextInput::make('contact_data.buyer.last_name')
                                            ->label('Last Name')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(3),
                                        TextInput::make('contact_data.buyer.first_name')
                                            ->label('First Name')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(3),

                                        TextInput::make('contact_data.buyer.middle_name')
                                            ->label('Middle Name')
                                            ->maxLength(255)
                                            ->required(fn (Get $get): bool => ! $get('contact_data.no_middle_name'))
                                            ->readOnly(fn (Get $get): bool => $get('contact_data.no_middle_name'))
    //                                            ->hidden(fn (Get $get): bool =>  $get('no_middle_name'))
                                            ->columnSpan(3),
    //                                                Select::make('buyer.name_suffix')
    //                                                    ->label('Suffix')
    //                                                    ->required()
    //                                                    ->native(false)
    //                                                    ->options(NameSuffix::all()->pluck('description','code'))
    //                                                    ->columnSpan(2),
                                        TextInput::make('contact_data.buyer.name_suffix')
                                            ->label('Suffix')
                                            ->maxLength(255)
                                            ->columnSpan(2),
                                        Forms\Components\Checkbox::make('contact_data.no_middle_name')
                                            ->live()
                                            ->inline(false)
                                            ->afterStateUpdated(function(Get $get,Set $set){
                                                $set('buyer.middle_name',null);
    //                                                if ($get('no_middle_name')) {
    //                                                }
                                            })
                                            ->columnSpan(1),
                                        TextInput::make('contact_data.buyer.civil_status')
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
                                        Select::make('contact_data.buyer.sex')
                                            ->label('Gender')
                                            ->required()
                                            ->native(false)
                                            ->options([
                                                'Male'=>'Male',
                                                'Female'=>'Female'
                                            ])
                                            ->columnSpan(3),
                                        DatePicker::make('contact_data.buyer.date_of_birth')
                                            ->label('Date of Birth')
                                            ->required()
                                            ->native(false)
                                            ->columnSpan(3),
                                        TextInput::make('contact_data.buyer.nationality')
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
                                            Forms\Components\TextInput::make('contact_data.buyer.email')
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

                                            Forms\Components\TextInput::make('contact_data.buyer.mobile')
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

                                            Forms\Components\TextInput::make('contact_data.buyer.other_mobile')
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

                                            Forms\Components\TextInput::make('contact_data.buyer.landline')
                                                ->label('Landline')
                                                ->columnSpan(3),
                                        ])->columns(12)->columnSpanFull(),
                                    //Address
                                    \Filament\Forms\Components\Fieldset::make('Address')
                                        ->schema([
                                            Forms\Components\Fieldset::make('Present')->schema([
                                                TextInput::make('contact_data.buyer.address.present.ownership')
                                                    ->label('Ownership')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpan(3),
    //                                                        Select::make('buyer.address.present.ownership')
    //                                                            ->options(HomeOwnership::all()->pluck('description','code'))
    //                                                            ->native(false)
    //                                                            ->required()
    //                                                            ->columnSpan(3),
                                                TextInput::make('contact_data.buyer.address.present.country')
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
                                                TextInput::make('contact_data.buyer.address.present.postal_code')
                                                    ->minLength(4)
                                                    ->maxLength(4)
                                                    ->required()
                                                    ->columnSpan(3),
    //                                                        Checkbox::make('buyer.address.present.same_as_permanent')
    //                                                            ->label('Same as Permanent')
    //                                                            ->inline(false)
    //                                                            ->live()
    //                                                            ->columnSpan(3),
                                                TextInput::make('contact_data.buyer.address.present.region')
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
                                                TextInput::make('contact_data.buyer.address.present.administrative_area')
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
                                                TextInput::make('contact_data.buyer.address.present.locality')
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
                                                TextInput::make('contact_data.buyer.address.present.sublocality')
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
                                                TextInput::make('contact_data.buyer.address.present.address1')
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
                                                fn(Get $get) => $get('contact_data.buyer.address.present.same_as_permanent') == null ? [
                                                    Forms\Components\Fieldset::make('Permanent')->schema([
                                                        Group::make()->schema([
                                                            TextInput::make('contact_data.buyer.address.permanent.ownership')
                                                                ->label('Ownership')
                                                                ->required()
                                                                ->maxLength(255)
                                                                ->columnSpan(3),
    //                                                                    Select::make('buyer.address.permanent.ownership')
    //                                                                        ->options(HomeOwnership::all()->pluck('description','code'))
    //                                                                        ->native(false)
    //                                                                        ->required()
    //                                                                        ->columnSpan(3),
                                                            TextInput::make('contact_data.buyer.address.permanent.country')
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
                                                            TextInput::make('contact_data.buyer.address.permanent.postal_code')
                                                                ->minLength(4)
                                                                ->maxLength(4)
                                                                ->required()
                                                                ->columnSpan(3),
                                                        ])
                                                            ->columns(12)->columnSpanFull(),

                                                        TextInput::make('contact_data.buyer.address.permanent.region')
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
                                                        TextInput::make('contact_data.buyer.address.permanent.province')
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
                                                        TextInput::make('contact_data.buyer.address.permanent.city')
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
                                                        TextInput::make('contact_data.buyer.address.permanent.barangay')
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
                                                        TextInput::make('contact_data.buyer.address.permanent.address1')
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
                                        TextInput::make('contact_data.buyer_employment.employment_type')
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
                                        TextInput::make('contact_data.buyer_employment.employment_status')
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
                                        TextInput::make('contact_data.buyer_employment.years_in_service')
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
                                        TextInput::make('contact_data.buyer_employment.current_position')
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

                                        TextInput::make('contact_data.buyer_employment.rank')
                                            ->label('Rank')
    //                                        ->required(fn (Get $get): bool =>   $get('buyer_employment.employment_type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
    //                                        ->hidden(fn (Get $get): bool =>   $get('buyer_employment.employment_type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                            ->maxLength(255)
                                            ->columnSpan(3),
                                        TextInput::make('contact_data.buyer_employment.employer.industry')
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
                                        TextInput::make('contact_data.buyer_employment.monthly_gross_income')
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
                                            TextInput::make('contact_data.buyer_employment.id.tin')
                                                ->label('Tax Identification Number')
                                                ->required()
                                                ->maxLength(255)
                                                ->columnSpan(3),
                                            TextInput::make('contact_data.buyer_employment.id.pagibig')
                                                ->label('PAG-IBIG Number')
                                                ->required()
                                                ->maxLength(255)
                                                ->columnSpan(3),
                                            TextInput::make('contact_data.buyer_employment.id.sss')
                                                ->label('SSS/GSIS Number')
                                                ->maxLength(255)
                                                ->columnSpan(3),
                                        ])->columnSpanFull()->columns(12),


                                    ])->columns(12)->columnSpanFull(),
                                    //Employer
                                    Forms\Components\Fieldset::make('Employer/Business')->schema([
                                        TextInput::make('contact_data.buyer_employment.employer.name')
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
                                        TextInput::make('contact_data.buyer_employment.employer.email')
                                            ->label('Email')
                                            // ->email()
    //                                                    ->required(fn (Get $get): bool =>   $get('buyer_employment.employment_type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
    //                                                    ->hidden(fn (Get $get): bool =>   $get('buyer_employment.employment_type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                            ->maxLength(255)
                                            ->columnSpan(3),
                                        TextInput::make('contact_data.buyer_employment.employer.contact_no')
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
                                        TextInput::make('contact_data.buyer_employment.employer.year_established')
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
                                                    TextInput::make('contact_data.buyer_employment.employer.address.country')
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
                                            TextInput::make('contact_data.buyer_employment.employer.address.region')
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
                                            TextInput::make('contact_data.buyer_employment.employer.address.administrative_area')
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
                                            TextInput::make('contact_data.buyer_employment.employer.address.locality')
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
                                            TextInput::make('contact_data.buyer_employment.employer.address.sublocality')
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
                                            TextInput::make('contact_data.buyer_employment.employer.address.address1')
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
                                Forms\Components\Section::make()
                                ->schema([
                                    Group::make()
                                    ->schema([

                                    ])
                                    ->columnSpan(3),
                                    Forms\Components\Fieldset::make('Property Allocation')
                                        ->schema([
                                            Group::make()->schema([
                                                Forms\Components\TextInput::make('contact_data.order.property_code')
                                                    ->label('Input Property Code'),
                                                Forms\Components\Actions::make([
                                                    Forms\Components\Actions\Action::make('Sync Property Details')
                                                        ->label('Sync Property Details')
                                                        ->icon('heroicon-m-arrow-path')
                                                        ->button()
                                                        ->keyBindings(['command+1', 'ctrl+1'])
                                                        ->action(function (Get $get, Set $set, $state,Model $record) {
                                                            if(Contact::where('order->property_code', $get('contact_data.order.property_code'))->count() > 0 && Contact::where('order->property_code', $get('contact_data.order.property_code'))->first()->id != $record->contact_id){
                                                                Notification::make()
                                                                    ->title('Order has already been reserved')
                                                                    ->body('Sorry, this order has already been reserved for somebody, please choose another property.')
                                                                    ->danger()
                                                                    ->persistent()
                                                                    ->sendToDatabase(auth()->user())
                                                                    ->send();
                                                            }else{
                                                                try {
                                                                    $response = Http::get('https://properties.homeful.ph/api/property-details/'.$get('contact_data.order.property_code'));
                                                                    if ($response->successful()) {
                                                                        $set('contact_data.order.sku', $response->json()['data']['sku']??'');
                                                                        $set('contact_data.order.project_name', $response->json()['data']['product']['brand']??'');
                                                                        $set('contact_data.order.project_location', $response->json()['data']['project_location']??'');
                                                                        $set('contact_data.order.property_type', $response->json()['data']['type']??'');
                                                                        $set('contact_data.order.property_name', $response->json()['data']['name']??'');
                                                                        $set('contact_data.order.project_code', $response->json()['data']['project_code']??'');
                                                                        $set('contact_data.order.block', $response->json()['data']['block']??'');
                                                                        $set('contact_data.order.lot', $response->json()['data']['lot']??'');
                                                                        $set('contact_data.order.lot_area', $response->json()['data']['lot_area']??'');
                                                                        $set('contact_data.order.floor_area', $response->json()['data']['floor_area']??'');
                                                                        $set('contact_data.order.project_address', $response->json()['data']['project_address']??'');
                                                                        $set('contact_data.order.unit_type', $response->json()['data']['unit_type']??'');
                                                                        $set('contact_data.order.unit_type_interior', $response->json()['data']['unit_type_interior']??'');
                                                                        $set('contact_data.order.payment_scheme.total_contract_price', $response->json()['data']['tcp']??'');

                                                                        $ntcp = ($response->json()['data']['tcp'] ?? 0) - ($get('contact_data.order.payment_scheme.discount_rate') ?? 0);
                                                                        $set('contact_data.order.payment_scheme.net_total_contract_price', $ntcp);
                                                                        $set('contact_data.order.hdmf.input.SELLING_PRICE', $ntcp);
                                                                        $set('contact_data.order.hdmf.input.DESIRED_LOAN', $ntcp);
                                                                        $set('contact_data.order.loan_value_after_downpayment', ($ntcp - ($get('contact_data.order.equity_1_amount') ?? 0)));
                                                                        $set('contact_data.order.equity_1_percentage_rate',  number_format(($get('contact_data.order.equity_1_amount') ?? 0) / $ntcp * 100, 2, '.', ''));
                                                                        if($get('contact_data.order.equity_1_terms')){
                                                                            $set('contact_data.order.equity_1_monthly_payment', number_format((($get('contact_data.order.equity_1_amount') ?? 0) / ($get('contact_data.order.equity_1_terms') ?? 0)), 2, '.', ''));
                                                                        }else{
                                                                            $set('contact_data.order.equity_1_monthly_payment', $get('contact_data.order.equity_1_amount'));
                                                                        }

                                                                        Notification::make()
                                                                            ->title('Property Details Fetch '.$response->status())
                                                                            ->success()
                                                                            ->sendToDatabase(auth()->user())
                                                                            ->send();
                                                                    }else{
                                                                        Notification::make()
                                                                            ->title('Property Not Found')
                                                                            ->body('No system record has been found for this property code')
                                                                            ->danger()
                                                                            ->sendToDatabase(auth()->user())
                                                                            ->send();
                                                                    }
                                                                }catch (Exception $e){
                                                                    Notification::make()
                                                                        ->title('Property Details Fetch Error')
                                                                        ->body($e->getMessage())
                                                                        ->danger()
                                                                        ->persistent()
                                                                        ->sendToDatabase(auth()->user())
                                                                        ->send();
                                                                }

                                                                //                                                            try{
                                                                //                                                                $property = (new Property)
                                                                //                                                                    ->setTotalContractPrice(new Price(Money::of($tcp =750000, 'PHP')))
                                                                //                                                                    ->setAppraisedValue(new Price(Money::of($tcp, 'PHP')));
                                                                //                                                                $borrower = (new Borrower($property))
                                                                //                                                                    ->setRegional(false)
                                                                //                                                                    ->setAge(25)
                                                                //                                                                    ->setGrossMonthlyIncome($get('buyer_employment.monthly_gross_income')??0);
                                                                //
                                                                //                                                                $mortgage= new Mortgage(property: $property, borrower: $borrower, params: [
                                                                //                                                                    Input::WAGES => $get('buyer_employment.monthly_gross_income'),
                                                                ////                                                                    Input::TCP => $get('order.payment_scheme.total_contract_price'),
                                                                //                                                                    Input::TCP => 750000,
                                                                //                                                                    Input::PERCENT_DP => 5 / 100,
                                                                //                                                                    Input::DP_TERM => 12,
                                                                //                                                                    Input::BP_INTEREST_RATE => 7 / 100,
                                                                //                                                                    Input::PERCENT_MF => 8.5 / 100,
                                                                //                                                                    Input::LOW_CASH_OUT => 0.0,
                                                                //                                                                    Input::BP_TERM => 20,
                                                                //                                                                ]);
                                                                //                                                                $data = MortgageData::fromObject($mortgage);
                                                                //                                                            }catch (Exception $e){
                                                                //                                                                Notification::make()
                                                                //                                                                    ->title($e->getMessage() )
                                                                //                                                                    ->danger()
                                                                //                                                                    ->persistent()
                                                                //                                                                    ->sendToDatabase(auth()->user())
                                                                //                                                                    ->send();
                                                                //                                                            }

                                                                try {
                                                                    $mfilesLink = config('gnc.mfiles_link');
                                                                    $credentials = config('gnc.mfiles_credentials');

                                                                    // Prepare the data to send in the POST request
                                                                    $payload = [
                                                                        "Credentials" => [
                                                                            "Username" => $credentials['username'],  // Fetching from config
                                                                            "Password" => $credentials['password'],  // Fetching from config
                                                                        ],
                                                                        "objectID" => 119,
                                                                        "propertyID" => 1105,
                                                                        "name" => $get('order.property_code')??'',
                                                                        "property_ids"=>[1105,1050,1109,1203,1204,1202,1285,1024,1290],
                                                                    ];
                                                                    $response = Http::post($mfilesLink . '/api/mfiles/document/search/properties', $payload);
                                                                    //                                                                dd($response->json());
                                                                    if ($response->successful()) {
                                                                        //                                                            $set('order.technical_description', $response->json()['Technical Description']??'');
                                                                        $set('contact_data.order.tct_no', $response->json()['TCT No.']??'');
                                                                        Notification::make()
                                                                            ->title('MFILES Tech Decription Success')
                                                                            ->body($response->json()['Technical Description'])
                                                                            ->success()
                                                                            ->persistent()
                                                                            ->sendToDatabase(auth()->user())
                                                                            ->send();
                                                                    }
                                                                    $response = Http::get($mfilesLink . '/api/mfiles/technical-description/'.($get("contact_data.order.property_code")??""));
                                                                    if ($response->successful()){
                                                                        $set('contact_data.order.technical_description', $response->json()??'');
                                                                    }else{
                                                                        Notification::make()
                                                                            ->title('No technical description has been found for this property code :'.($get("order.property_code")??""))
                                                                            ->danger()
                                                                            ->persistent()
                                                                            ->sendToDatabase(auth()->user())
                                                                            ->send();
                                                                    }
                                                                }catch (Exception $e){
                                                                    Notification::make()
                                                                        ->title('MFILES Tech Decription Error')
                                                                        ->body($e->getMessage())
                                                                        ->danger()
                                                                        ->persistent()
                                                                        ->sendToDatabase(auth()->user())
                                                                        ->send();
                                                                }

                                                                try {
                                                                    $mfilesLink = config('gnc.mfiles_link');
                                                                    $credentials = config('gnc.mfiles_credentials');

                                                                    // Prepare the data to send in the POST request
                                                                    $payload = [
                                                                        "Credentials" => [
                                                                            "Username" => $credentials['username'],  // Fetching from config
                                                                            "Password" => $credentials['password'],  // Fetching from config
                                                                        ],
                                                                        "objectID" => 101,
                                                                        "propertyID" => 1050,
                                                                        "name" => $get('contact_data.order.project_code')??'',
                                                                        "property_ids"=>[1293,1294],
                                                                    ];
                                                                    $response = Http::post($mfilesLink . '/api/mfiles/document/search/properties', $payload);
                                                                    //                                                                dd($response->json());
                                                                    if ($response->successful()) {
                                                                        $set('contact_data.order.company_name', $response->json()['Project Developer']??'');
                                                                        $set('contact_data.order.registry_of_deeds_address', $response->json()['Deed of Registry']??'');
                                                                        Notification::make()
                                                                            ->title('MFILES PROJECT DETAILS Success')
                                                                            ->body($response->json()['Project Developer'])
                                                                            ->success()
                                                                            ->persistent()
                                                                            ->sendToDatabase(auth()->user())
                                                                            ->send();
                                                                    }

                                                                }catch (Exception $e){
                                                                    Notification::make()
                                                                        ->title('MFILES PROJECT DETAILS ERROR')
                                                                        ->body($e->getMessage())
                                                                        ->danger()
                                                                        ->persistent()
                                                                        ->sendToDatabase(auth()->user())
                                                                        ->send();
                                                                }

                                                            }
                                                        })
                                                ])->columnSpanFull()->fullWidth(),
                                            ])->columnSpan(3)
                                                ->columns(1),
                                            Group::make()->schema([
                                                Forms\Components\TextInput::make('contact_data.order.sku')
                                                    ->label('SKU')
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('contact_data.order.tct_no')
                                                    ->label('TCT No.')
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('contact_data.order.property_name')
                                                    ->label('Property Name')
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('contact_data.order.property_type')
                                                    ->label('Property Type')
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('contact_data.order.company_name')
                                                    ->label('Developer Name')
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('contact_data.order.registry_of_deeds_address')
                                                    ->label('Registry of Deeds Address')
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('contact_data.order.project_name')
                                                    ->label('Project Name')
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('contact_data.order.project_code')
                                                    ->label('Project Code')
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('contact_data.order.block')
                                                    ->label('Block')
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('contact_data.order.lot')
                                                    ->label('Lot')
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('contact_data.order.lot_area')
                                                    ->label('Lot Area (sqm)')
                                                    ->numeric()
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('contact_data.order.floor_area')
                                                    ->label('Floor Area (sqm)')
                                                    ->afterStateUpdated(function(Set $set, $state){
                                                        $set('contact_data.order.hdmf.input.TOTAL_FLOOR_AREA', $state);
                                                    })
                                                    ->live(onBlur: true)
                                                    ->numeric()
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('contact_data.order.tct_no')
                                                    ->label('TCT Number')
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('contact_data.order.project_location')
                                                    ->label('Project Location')
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('contact_data.order.project_address')
                                                    ->label('Project Address')
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('contact_data.order.unit_type')
                                                    ->label('Unit Type')
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('contact_data.order.unit_type_interior')
                                                    ->label('Unit Type (Interior)')
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('contact_data.order.payment_scheme.total_contract_price')
                                                    ->label('Total Contract Price')
                                                    ->afterStateUpdated(function(Contact $record, Get $get, Set $set, String $state=null){
                                                        $ntcp = $state - ($get('contact_data.order.payment_scheme.discount_rate') ?? 0);
                                                        $set('contact_data.order.payment_scheme.net_total_contract_price', $ntcp);
                                                        $set('contact_data.order.hdmf.input.SELLING_PRICE', $ntcp);
                                                        $set('contact_data.order.hdmf.input.DESIRED_LOAN', $ntcp);
                                                        $set('contact_data.order.loan_value_after_downpayment', ($ntcp - ($get('contact_data.order.equity_1_amount') ?? 0)));
                                                        $set('contact_data.order.equity_1_percentage_rate',  number_format(($get('contact_data.order.equity_1_amount') ?? 0) / $ntcp * 100, 2, '.', ''));
                                                        if($get('contact_data.order.equity_1_terms')){
                                                            $set('contact_data.order.equity_1_monthly_payment', number_format((($get('contact_data.order.equity_1_amount') ?? 0) / ($get('contact_data.order.equity_1_terms') ?? 0)), 2, '.', ''));
                                                        }else{
                                                            $set('contact_data.order.equity_1_monthly_payment', $get('order.equity_1_amount'));
                                                        }
                                                    })
                                                    ->live(onBlur: true)
                                                    ->hint('')
                                                    ->numeric()
                                                    ->columnSpan(3),
                                            ])->columnSpan(9)
                                                ->columns(9),






                                            // Forms\Components\TextInput::make('order.hdmf.input.SELLING_PRICE')
                                            //     ->label('Selling Price')
                                            //     ->default(0)
                                            //     ->numeric()
                                            //     ->columnSpan(3),
                                        ])
                                        ->columns(12)
                                        ->columnSpan(9),
                                ])->columns(12)->columnSpanFull()
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
