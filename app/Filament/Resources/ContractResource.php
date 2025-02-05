<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContractResource\Pages;
use App\Filament\Resources\ContractResource\RelationManagers;
use App\Helpers\LoanTermOptions;
use App\Livewire\DocumentPreviewComponent;
use App\Livewire\RequirementsTable;
use App\Models\Contact;
use App\Models\Requirement;
use App\Models\RequirementMatrix;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
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
use http\Env\Response;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;
use Joaopaulolndev\FilamentPdfViewer\Forms\Components\PdfViewerField;
use Joaopaulolndev\FilamentPdfViewer\Infolists\Components\PdfViewerEntry;
use stdClass;
use Carbon\Carbon;

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
                                            ->required(fn (Get $get): bool => ! $get('contact_data.buyer.no_middle_name'))
                                            ->readOnly(fn (Get $get): bool => $get('contact_data.buyer.no_middle_name'))
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
                                        Forms\Components\Checkbox::make('contact_data.buyer.no_middle_name')
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
//                                    Consultation Details
                                    Forms\Components\Fieldset::make('Consultation Details')->schema([
                                        Forms\Components\TextInput::make('consult.reference_code')
                                            ->label('Ref. Code:')
                                            ->columnSpan(3),
                                        Forms\Components\TextInput::make('consult.fee')
                                            ->label('Consulting Fee:')
                                            ->columnSpan(3),
                                        Forms\Components\TextInput::make('consult.payment')
                                            ->label('Payment:')
                                            ->columnSpan(3),
                                        Forms\Components\TextInput::make('consult.transaction_number')
                                            ->label('Transaction Number:')
                                            ->columnSpan(3),
                                        Forms\Components\TextInput::make('consult.transaction_date')
                                            ->label('Transaction Date:')
                                            ->columnSpan(3),
                                    ])
                                        ->columns(12)
                                    ->columnSpanFull(),
//                                    Desired Property Details
                                    Forms\Components\Fieldset::make('Desired Property Details')->schema([
                                        Forms\Components\TextInput::make('consult.project')
                                            ->label('Project:')
                                            ->columnSpan(3),
                                        Forms\Components\TextInput::make('consult.unit_type')
                                            ->label('Unit Type:')
                                            ->columnSpan(3),
                                        Forms\Components\TextInput::make('consult.tcp')
                                            ->label('TCP:')
                                            ->columnSpan(3),
                                        Forms\Components\TextInput::make('consult.monthy_amortization')
                                            ->label('Monthy Amortization:')
                                            ->columnSpan(3),
                                        Forms\Components\TextInput::make('consult.payment_terms')
                                            ->label('Payment Terms:')
                                            ->columnSpan(3),
                                    ]) ->columns(12)->columnSpanFull(),
//                                    Property Allocation
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
                                        ->columnSpanFull(),
                                    Forms\Components\Section::make('Seller')
                                        ->collapsible()
                                        ->collapsed(false)
                                        ->schema([
                                            Forms\Components\TextInput::make('order.seller_commission_code')
                                                ->label('Seller Commission Code')
                                                ->columnSpan(3),
                                            Forms\Components\TextInput::make('order.seller.name')
                                                ->label('Seller Name')
                                                ->columnSpan(3),
                                            Forms\Components\TextInput::make('order.seller.id')
                                                ->label('Seller ID')
                                                ->columnSpan(3),
                                            Forms\Components\TextInput::make('order.seller.superior')
                                                ->label('Superior')
                                                ->columnSpan(3),
                                            Forms\Components\TextInput::make('order.seller.team_head')
                                                ->label('Team Head')
                                                ->columnSpan(3),
                                            Forms\Components\TextInput::make('order.seller.chief_seller_officer')
                                                ->label('Chief Sales Officer')
                                                ->columnSpan(3),
                                            Forms\Components\TextInput::make('order.exec_tin_no')
                                                ->label('CSO TIN')
                                                ->columnSpan(3),

                                            Forms\Components\TextInput::make('order.seller.deputy_chief_seller_officer')
                                                ->label('Deputy Chief Sales Officer')
                                                ->columnSpan(3),

                                            Forms\Components\TextInput::make('order.seller.unit')
                                                ->label('Seller Unit')
                                                ->columnSpan(3),
                                        ])
                                        ->columns(12)
                                        ->columnSpanFull(),
                                    Forms\Components\Section::make('Transaction')
                                        ->collapsible()
                                        ->schema([
                                            Group::make()
                                                ->schema([
                                                    Forms\Components\TextInput::make('order.payment_scheme.discount_rate')
                                                        ->label('Discount')
                                                        ->hint('In Pesos')
                                                        ->numeric()
                                                        ->afterStateUpdated(function(Set $set, Get $get, String $state = null, Contact $record){
                                                            $ntcp = ($get('order.payment_scheme.total_contract_price') ?? 0) - $state;
                                                            $set('order.payment_scheme.net_total_contract_price', $ntcp);
                                                            $set('order.hdmf.input.SELLING_PRICE', $ntcp);
                                                            $set('order.hdmf.input.DESIRED_LOAN', $ntcp);
                                                            $set('order.loan_value_after_downpayment', ($ntcp - ($get('order.equity_1_amount') ?? 0)));
                                                            $set('order.equity_1_percentage_rate',  number_format(($get('order.equity_1_amount') ?? 0) / $ntcp * 100, 2, '.', ''));
                                                            if($get('order.equity_1_terms')){
                                                                $set('order.equity_1_monthly_payment', number_format((($get('order.equity_1_amount') ?? 0) / ($get('order.equity_1_terms') ?? 0)), 2, '.', ''));
                                                            }else{
                                                                $set('order.equity_1_monthly_payment', $get('order.equity_1_amount'));
                                                            }
                                                        })
                                                        ->live(onBlur: true)
                                                        ->columnSpan(3),
                                                    Forms\Components\TextInput::make('order.payment_scheme.conditional_discount')
                                                        ->label('Conditional Discount')
                                                        ->numeric()
                                                        ->columnSpan(3),
                                                    Forms\Components\TextInput::make('order.promo_code')
                                                        ->label('Promo Code')
                                                        ->maxLength(255)
                                                        ->columnSpan(3),
                                                    Forms\Components\TextInput::make('order.payment_scheme.net_total_contract_price')
                                                        ->label('NTCP')
                                                        ->numeric()
                                                        ->hint(fn (Get $get ): string =>'TCP: '.$get('order.payment_scheme.total_contract_price'))
                                                        ->columnSpan(3),
                                                ])
                                                ->columns(12)
                                                ->columnSpanFull(),
                                            Forms\Components\Select::make('order.loan_term')
                                                ->label('Loan Term')
                                                ->options(fn()=>LoanTermOptions::getOptions())
                                                ->default(0)
                                                ->afterStateUpdated(function(Set $set, Get $get, String $state = null, Contact $record){
                                                    $set('order.loan_period_months', $state);
                                                    $set('order.hdmf.input.LOAN_PERIOD_YEARS', LoanTermOptions::getDataByMonthsTerm($state)['loanable_years']);
                                                    $option = LoanTermOptions::getDataByMonthsTerm($state);
                                                    $set('order.term_1', $option['term_1']);
                                                    $set('order.term_2', $option['term_2']);
                                                    $set('order.term_3', $option['term_3']);
                                                })
                                                ->live()
                                                ->native(false)
                                                ->helperText(fn($state)=>'equivalent to '.(($state != null && !empty(LoanTermOptions::getDataByMonthsTerm($state))) ? LoanTermOptions::getDataByMonthsTerm($state)['loanable_years'] : ' - ').' Years')
                                                ->hint('In Months')
                                                ->columnSpan(3),
                                            Forms\Components\TextInput::make('order.loan_interest_rate')
                                                ->label('Loan Interest Rate (%)')
                                                ->numeric()
                                                ->columnSpan(3),
                                            Forms\Components\DatePicker::make('order.reservation_date')
                                                ->native(false)
                                                ->label('Reservation Date')
                                                ->columnSpan(3),
                                            Forms\Components\TextInput::make('order.payment_scheme.scheme')
                                                ->label('Payment Scheme')
                                                ->columnSpan(3),
                                            Forms\Components\TextInput::make('order.payment_scheme.method')
                                                ->label('Payment Method')
                                                ->columnSpan(3),
                                            Forms\Components\Select::make('order.repricing_period')
                                                ->label('Repricing Period')
                                                ->hint('If End User')
                                                ->default(3)
                                                ->options([
                                                    1=>'1 year',
                                                    3=>'3 years',
                                                    5=>'5 years',
                                                    10=>'10 years',
                                                    15=>'15 years',
                                                    20=>'20 years',
                                                    25=>'25 years',
                                                ])
                                                ->native(false)
                                                ->columnSpan(3),

                                            Forms\Components\Section::make('Downpayment/Equity')
                                                ->collapsible()
                                                ->collapsed(true)
                                                ->schema([
                                                    //                                                Downpayment Equity Start
                                                    Forms\Components\TextInput::make('order.equity_1_amount')
                                                        ->label('Amount')
                                                        ->afterStateUpdated(function(Set $set, Get $get, String $state = null, Contact $record){
                                                            $ntcp = $get('order.payment_scheme.net_total_contract_price');
                                                            $set('order.loan_value_after_downpayment', ($ntcp - $state));
                                                            $set('order.equity_1_percentage_rate',  number_format($state / $ntcp * 100, 2, '.', ''));
                                                            if($get('order.equity_1_terms')){
                                                                $set('order.equity_1_monthly_payment', number_format(($state / ($get('order.equity_1_terms') ?? 0)), 2, '.', ''));
                                                            }else{
                                                                $set('order.equity_1_monthly_payment', $get('order.equity_1_amount'));
                                                            }
                                                        })
                                                        ->numeric()
                                                        ->live(onBlur: true)
                                                        ->default(0)
                                                        ->minValue(0)
                                                        ->columnSpan(3),
                                                    Forms\Components\TextInput::make('order.equity_1_percentage_rate')
                                                        ->label('Percentage Rate (%)')
                                                        ->numeric()
                                                        ->default(0)
                                                        ->disabled()
                                                        ->minValue(0)
                                                        ->columnSpan(3),
                                                    Forms\Components\TextInput::make('order.equity_1_interest_rate')
                                                        ->label('Interest Rate')
                                                        ->numeric()
                                                        ->disabled()
                                                        ->default(0)
                                                        ->columnSpan(3),
                                                    Forms\Components\TextInput::make('order.equity_1_terms')
                                                        ->label('Terms')
                                                        ->numeric()
                                                        ->afterStateUpdated(function(Set $set, Get $get, String $state = null, Contact $record){
                                                            if($state){
                                                                $set('order.equity_1_monthly_payment', number_format((($get('order.equity_1_amount') ?? 0) / ($state ?? 0)), 2, '.', ''));
                                                            }else{
                                                                $set('order.equity_1_monthly_payment', ($get('order.equity_1_amount')));
                                                            }
                                                        })
                                                        ->live(onBlur: true)
                                                        ->hint('In Months')
                                                        ->default(0)
                                                        ->columnSpan(3),
                                                    Forms\Components\TextInput::make('order.equity_1_monthly_payment')
                                                        ->label('Monthly Payment')
                                                        ->numeric()
                                                        ->default(0)
                                                        ->columnSpan(3),
                                                    Forms\Components\TextInput::make('order.loan_value_after_downpayment')
                                                        ->label('Loan Value After Downpayment')
                                                        ->numeric()
                                                        ->default(0)
                                                        ->disabled()
                                                        ->minValue(0)
                                                        ->columnSpan(3),

//                                                Downpayment Equity End
                                                ])
                                                ->columns(12)
                                                ->columnSpanFull(),
                                            Forms\Components\Section::make('Balance Payment')
                                                ->collapsible()
                                                ->collapsed(true)
                                                ->schema([
//                                                      BP Amount Start
                                                    Forms\Components\TextInput::make('order.bp_1_amount')
                                                        ->label('Loan BP Amount')
                                                        ->numeric()
                                                        ->default(0)
                                                        ->columnSpan(3),
                                                    Forms\Components\TextInput::make('order.bp_1_percentage_rate')
                                                        ->label('Loan BP Percentage Rate')
                                                        ->numeric()
                                                        ->default(0)
                                                        ->columnSpan(3),
                                                    Forms\Components\TextInput::make('order.bp_1_interest_rate')
                                                        ->label('Loan BP Interest Rate')
                                                        ->numeric()
                                                        ->default(0)
                                                        ->columnSpan(3),
                                                    Forms\Components\TextInput::make('order.bp_1_terms')
                                                        ->label('Loan BP Terms')
                                                        ->numeric()
                                                        ->default(30)
                                                        ->columnSpan(3),
                                                    Forms\Components\TextInput::make('order.bp_1_monthly_payment')
                                                        ->label('Loan BP Monthly Payment')
                                                        ->numeric()
                                                        ->default(0)
                                                        ->columnSpan(3),
                                                    Forms\Components\DatePicker::make('order.bp_1_effective_date')
                                                        ->label('Loan BP Effective Date')
                                                        ->native(false)
                                                        ->columnSpan(3),
//                                                BP Amount End
                                                ])
                                                ->columns(12)
                                                ->columnSpanFull(),
                                        ])
                                        ->columns(12)
                                        ->columnSpanFull(),
                                    //Transaction End
                                    Forms\Components\Textarea::make('order.technical_description')
                                        ->hintAction(Forms\Components\Actions\Action::make('Get Technical Description from MFiles')
                                            ->label('Get Technical Description from MFiles')
                                            ->icon('heroicon-m-clipboard')
                                            ->button()
                                            ->keyBindings(['command+2', 'ctrl+2'])
                                            ->action(function (Get $get, Set $set, $state) {
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

                                                    if ($response->successful()) {
//                                                            $set('order.technical_description', $response->json()['Technical Description']??'');
                                                        $set('order.tct_no', $response->json()['TCT No.']??'');
                                                        Notification::make()
                                                            ->title('MFILES Tech Decription Success')
                                                            ->body($response->json()['Technical Description'])
                                                            ->success()
                                                            ->persistent()
                                                            ->sendToDatabase(auth()->user())
                                                            ->send();
                                                    }
                                                    $response = Http::get($mfilesLink . '/api/mfiles/technical-description/'.($get("order.property_code")??"") );
                                                    if ($response->successful()){
                                                        $set('order.technical_description', $response->json()??'');
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
                                            }))
                                        ->label('Technical Description')
                                        ->rows(5)
                                        ->cols(10)
                                        ->autosize()
                                        ->columnSpanFull(),
                                    //Fees
                                    Forms\Components\Section::make()
                                        ->collapsible(true)
                                        ->collapsed()
                                        ->schema([
                                            Forms\Components\Repeater::make('order.payment_scheme.fees')
                                                ->collapsible(true)
                                                ->collapsed()
                                                ->label('Fees')
                                                ->schema([
                                                    Forms\Components\Select::make('name')
                                                        ->label('Fee Name')
                                                        ->options([
                                                            'retention_fee' => 'Retention Fee',
                                                            'service_fee' => 'Service Fee',
                                                            'processing_fee' => 'Processing Fee',
                                                            'home_utility_connection_fee' => 'Home Utility Connection Fee',
                                                            'balance' => 'Balance Fee',
                                                            'equity' => 'Equity Fee',
                                                            'rental' => 'Rental Fee',
                                                            'present_rental_fee' => 'Present Rental Fee',
                                                        ])
                                                        ->searchable()
                                                        ->native(false)
                                                        ->columnSpan(3),
                                                    Forms\Components\TextInput::make('amount')
                                                        ->label('Amount')
                                                        ->numeric()
                                                        ->columnSpan(3),
                                                ])->columns(6)
                                                ->columnSpanFull(),
                                        ])->columnSpanFull(),
                                    Forms\Components\Section::make('PAG-IBIG Evaluation Sheet')
                                        ->headerActions([
                                            \Filament\Forms\Components\Actions\Action::make('evaluate')
                                                ->label('Evaluate')
                                                ->action(function(Get $get, Set $set,Model $record){
                                                    try{
                                                        $record->middle_name = $record->middle_name??'';
                                                        $order=$record->order;
                                                        $order['sku'] = $order['sku']??'';
                                                        $order['seller_commission_code'] = $order['seller_commission_code']??'';
                                                        $order['property_code'] = $order['property_code']??'';
                                                        $record->order = $order;
                                                        $contact_data = ContactData::fromModel($record);
                                                        $buyer_employment = collect($contact_data->employment)->firstWhere('type', 'buyer') ?? [];
                                                        $cobo_employment = collect($contact_data->employment)->firstWhere('type', 'co_borrower') ?? [];
                                                        $work_country = $buyer_employment['employer']['address']['country'];
                                                        $work_region = $buyer_employment['employer']['address']['region'];
                                                        $work_area = ($work_country !== 'PH' || ($work_country === 'PH' && $work_region === '13'))
                                                            ? 'HUC'
                                                            : 'REGION';
                                                        $payload = array_filter([
                                                            'SELLING_PRICE'=> ($get('order.hdmf.input.SELLING_PRICE')) ?? $get('order.payment_scheme.net_total_contract_price'),
                                                            "TITLE" => $get('order.hdmf.input.TITLE'),
                                                            "GUIDELINE" => $get('order.hdmf.input.GUIDELINE'),
                                                            "PROGRAM" => $get('order.hdmf.input.PROGRAM'),
                                                            "APPLICATION_DATE" => Carbon::parse($get('order.hdmf.input.APPLICATION_DATE'))->format('m/d/Y'),
                                                            "PRINCIPAL_BORROWER" => $get('order.hdmf.input.PRINCIPAL_BORROWER'),
                                                            "COBORROWER_1" => $get('order.hdmf.input.COBORROWER_1'),
                                                            "COBORROWER_2" => $get('order.hdmf.input.COBORROWER_2'),
                                                            "BIRTH_DATE" => Carbon::parse($get('order.hdmf.input.BIRTH_DATE'))->format('m/d/Y') ?? $get('buyer.date_of_birth')->format('m/d/Y'),
                                                            "BIRTH_DATE_COBORROWER_1" => Carbon::parse($get('order.hdmf.input.BIRTH_DATE_COBORROWER_1'))->format('m/d/Y'),
                                                            "BIRTH_DATE_COBORROWER_2" => Carbon::parse($get('order.hdmf.input.BIRTH_DATE_COBORROWER_2'))->format('m/d/Y'),
                                                            "WORK_AREA" => $get('order.hdmf.input.WORK_AREA'),
                                                            "EMPLOYMENT" => $get('order.hdmf.input.EMPLOYMENT'),
                                                            "PAY_MODE" => $get('order.hdmf.input.PAY_MODE'),
                                                            "TYPE_OF_DEVELOPMENT" => $get('order.hdmf.input.TYPE_OF_DEVELOPMENT'),
                                                            "PROJECT_TYPE" => $get('order.hdmf.input.PROJECT_TYPE'),
                                                            "HOUSING_TYPE" => $get('order.hdmf.input.HOUSING_TYPE'),
                                                            "TOTAL_FLOOR_NUMBER" => $get('order.hdmf.input.TOTAL_FLOOR_NUMBER'),
                                                            "TOTAL_FLOOR_AREA" => $get('order.hdmf.input.TOTAL_FLOOR_AREA'),
                                                            "PRICE_CEILING" => $get('order.hdmf.input.PRICE_CEILING'),
                                                            "LTS_NUMBER" => $get('order.hdmf.input.LTS_NUMBER'),
                                                            "LTS_DATE" => $get('order.hdmf.input.LTS_DATE'),
                                                            "APPRAISED_VALUE_LOT" => $get('order.hdmf.input.APPRAISED_VALUE_LOT'),
                                                            "APPRAISED_VALUE_HOUSE" => $get('order.hdmf.input.APPRAISED_VALUE_HOUSE'),
                                                            "DESIRED_LOAN" => $get('order.hdmf.input.DESIRED_LOAN'),
                                                            "GROSS_INCOME_PRINCIPAL" => ($get('order.hdmf.input.GROSS_INCOME_PRINCIPAL')) ?? $get('buyer_employment.monthly_gross_income'),
                                                            "GROSS_INCOME_COBORROWER_1" => $get('order.hdmf.input.GROSS_INCOME_COBORROWER_1'),
                                                            "GROSS_INCOME_COBORROWER_2" => $get('order.hdmf.input.GROSS_INCOME_COBORROWER_2'),
                                                            "REPRICING_PERIOD" => $get('order.hdmf.input.REPRICING_PERIOD'),
                                                            "LOAN_PERIOD_YEARS" => $get('order.hdmf.input.LOAN_PERIOD_YEARS'),
                                                        ]);
                                                        $response = Http::timeout(240)->post('https://sheet.homeful.ph/api/evaluate', $payload);

                                                        if($response->successful()){
                                                            $order =  $record->order;
                                                            // $order['hdmf']['input'] = $response->json()['inputs'] ?? [];
                                                            // $record->order=$order;
                                                            // $record->save();
                                                            // $order['hdmf']['computed'] = $response->json()['computed'] ?? [];
                                                            // $record->order=$order;
                                                            // $record->save();
                                                            $order['hdmf']['file'] = $response->json()['file'] ?? '';
                                                            $record->order=$order;
                                                            $record->save();
                                                            $set('order.hdmf.file', $order['hdmf']['file']);
                                                            $set('order.amort_princ_int1', $response->json()['computed']['COMPUTATION_1_PRINCIPAL'] ?? '');
                                                            $set('order.payment_scheme.net_total_contract_price', $response->json()['computed']['DESIRED_LOAN'] ?? '');
                                                            $set('order.amort_mrisri1', $response->json()['computed']['COMPUTATION_2_PRINCIPAL'] ?? '');
                                                            $set('order.amort_nonlife1', $response->json()['computed']['COMPUTATION_3_PRINCIPAL'] ?? '');
                                                            $set('order.monthly_amort1', $response->json()['computed']['COMPUTATION_4_PRINCIPAL'] ?? '');
                                                            $set('order.amort_princ_int2', ($response->json()['computed']['COMPUTATION_1_COBORROWER_1']) ? $response->json()['computed']['COMPUTATION_1_COBORROWER_1'] : '***');
                                                            $set('order.amort_mrisri2', $response->json()['computed']['COMPUTATION_2_PRINCIPAL'] ?? '');
                                                            $set('order.amort_nonlife2', $response->json()['computed']['COMPUTATION_3_PRINCIPAL'] ?? '');
                                                            $set('order.monthly_amort2', '');
                                                            $set('order.amort_princ_int3', ($response->json()['computed']['COMPUTATION_1_COBORROWER_2']) ? $response->json()['computed']['COMPUTATION_1_COBORROWER_2'] : '***');
                                                            $set('order.amort_mrisri3', ($response->json()['computed']['COMPUTATION_2_COBORROWER_2']) ? $response->json()['computed']['COMPUTATION_2_COBORROWER_2'] : '***');
                                                            $set('order.amort_nonlife3', ($response->json()['computed']['COMPUTATION_3_COBORROWER_2']) ? $response->json()['computed']['COMPUTATION_3_COBORROWER_2'] : '***');
                                                            $set('order.monthly_amort3', '');
                                                            $set('order.mrisri_docstamp_total', $response->json()['computed']['MRI_SRI_TOTAL_1'] ?? '');
                                                            $set('order.non_life_insurance', $response->json()['computed']['NON_LIFE_INSURANCE'] ?? '');
                                                            $set('order.loan_base', $response->json()['computed']['RECOMMENDED_LOAN_BASE'] ?? '');
                                                            $amount =  ($response->json()['inputs']['SELLING_PRICE'] ?? 0) - ($response->json()['computed']['RECOMMENDED_LOAN_BASE'] ?? 0);
                                                            $set('order.equity_1_amount', $amount);
                                                            $set('order.equity_1_percentage_rate', number_format(($amount ?? 0) / ($get('order.payment_scheme.net_total_contract_price') ?? 0) * 100, 2, '.', ''));
                                                            if($get('order.equity_1_terms')){
                                                                $set('order.equity_1_monthly_payment', number_format((($amount ?? 0) / ($get('order.equity_1_terms') ?? 0)), 2, '.', ''));
                                                            }else{
                                                                $set('order.equity_1_monthly_payment', $amount);
                                                            }
                                                            $set('order.equity_1_interest_rate', ($response->json()['computed']['ANNUAL_INTEREST_RATE'] ?? 0) * 100);
                                                            $set('order.loan_interest_rate', ($response->json()['computed']['ANNUAL_INTEREST_RATE'] ?? 0) * 100);
                                                            $set('order.interest', ($response->json()['computed']['ANNUAL_INTEREST_RATE'] ?? 0) * 100);
                                                            $loan_value_after_dp = ($get('order.payment_scheme.net_total_contract_price') ?? 0) - $amount;
                                                            $set('order.loan_value_after_downpayment', $loan_value_after_dp);
                                                            $set('order.bp_1_amount', $loan_value_after_dp);
                                                            $set('order.bp_1_percentage_rate', ($loan_value_after_dp ==  ($response->json()['computed']['RECOMMENDED_LOAN_BASE'] ?? 0)) ? 100 : 0);
                                                            $set('order.bp_1_interest_rate', ($response->json()['computed']['ANNUAL_INTEREST_RATE'] ?? 0) * 100);
                                                            $set('order.bp_1_monthly_payment', $response->json()['computed']['COMPUTATION_4_TOTAL'] ?? 0);
                                                            Notification::make()
                                                                ->title('Successful Evaluation')
                                                                ->success()
                                                                ->icon('heroicon-o-check')
                                                                ->sendToDatabase(auth()->user())
                                                                ->send();
                                                        }else{
                                                            Notification::make()
                                                                ->title('Error Evaluate')
                                                                ->body(json_encode($payload))
                                                                ->danger()
                                                                ->icon('heroicon-o-x-mark')
                                                                ->sendToDatabase(auth()->user())
                                                                ->send();
                                                        }

                                                    }catch (Exception $e){
                                                        Notification::make()
                                                            ->title('Error Evaluate')
                                                            ->body($e->getMessage())
                                                            ->danger()
                                                            ->icon('heroicon-o-x-mark')
                                                            ->sendToDatabase(auth()->user())
                                                            ->send();
                                                    }
                                                }),
                                        ])
                                        ->schema([
                                            Forms\Components\Fieldset::make('Inputs')
                                                ->schema([
                                                    Forms\Components\TextInput::make('order.hdmf.input.TITLE')
                                                        ->label('Title')
                                                        ->inlineLabel(true)
                                                        ->default('EVALUATION SHEET  V1.0.2')
                                                        ->columnSpan(1),
                                                    Forms\Components\Select::make('order.hdmf.input.GUIDELINE')
                                                        ->label('Guideline')
                                                        ->options([
                                                            '396/349' => '396/349',
                                                            '403/349' => '403/349',
                                                        ])
                                                        ->native(false)
                                                        ->inlineLabel(true)
                                                        ->columnSpan(1),
                                                    Forms\Components\Select::make('order.hdmf.input.PROGRAM')
                                                        ->label('Program')
                                                        ->options([
                                                            'CTS' => 'CTS',
                                                            'CTS-EL' => 'CTS-EL',
                                                            'DCS' => 'DCS',
                                                            'DCS-EL' => 'DCS-EL',
                                                            'REM' => 'REM',
                                                            'REM-EL' => 'REM-EL',
                                                        ])
                                                        ->inlineLabel(true)
                                                        ->native(false)
                                                        ->columnSpan(1),
                                                    Forms\Components\DatePicker::make('order.hdmf.input.APPLICATION_DATE')
                                                        ->label('Application Date')
                                                        ->native(false)
                                                        ->inlineLabel(true)
                                                        ->formatStateUsing(fn(Get $get)=>($get('order.hdmf.input.APPLICATION_DATE')) ? $get('order.hdmf.input.APPLICATION_DATE') : Carbon::now()->format('Y-m-d'))
                                                        ->columnSpan(1),
                                                    Forms\Components\TextInput::make('order.hdmf.input.PRINCIPAL_BORROWER')
                                                        ->label('Principal Borrower')
                                                        ->inlineLabel(true)
                                                        ->default('')
                                                        ->columnSpan(1),
                                                    Forms\Components\TextInput::make('order.hdmf.input.COBORROWER_1')
                                                        ->label('Co-Borrower 1')
                                                        ->inlineLabel(true)
                                                        ->default('')
                                                        ->columnSpan(1),
                                                    Forms\Components\TextInput::make('order.hdmf.input.COBORROWER_2')
                                                        ->label('Co-Borrower 2')
                                                        ->inlineLabel(true)
                                                        ->default('')
                                                        ->columnSpan(1),
                                                    Forms\Components\DatePicker::make('order.hdmf.input.BIRTH_DATE')
                                                        ->label('Birth Date')
                                                        ->native(false)
                                                        ->inlineLabel(true)
                                                        ->default('')
                                                        ->columnSpan(1),
                                                    Forms\Components\DatePicker::make('order.hdmf.input.BIRTH_DATE_COBORROWER_1')
                                                        ->label('Birth Date (Co-Borrower 1)')
                                                        ->native(false)
                                                        ->inlineLabel(true)
                                                        ->default('')
                                                        ->columnSpan(1),
                                                    Forms\Components\DatePicker::make('order.hdmf.input.BIRTH_DATE_COBORROWER_2')
                                                        ->label('Birth Date (Co-Borrower 2)')
                                                        ->native(false)
                                                        ->inlineLabel(true)
                                                        ->default('')
                                                        ->columnSpan(1),
                                                    Forms\Components\Select::make('order.hdmf.input.WORK_AREA')
                                                        ->label('Work Area')
                                                        ->options([
                                                            'HUC' => 'HUC',
                                                            'REGION' => 'REGION',
                                                        ])
                                                        ->inlineLabel(true)
                                                        ->native(false)
                                                        ->columnSpan(1),
                                                    Forms\Components\Select::make('order.hdmf.input.EMPLOYMENT')
                                                        ->label('Employment')
                                                        ->options([
                                                            'PRIVATE' => 'PRIVATE',
                                                            'GOVERNMENT' => 'GOVERNMENT',
                                                        ])
                                                        ->inlineLabel(true)
                                                        ->native(false)
                                                        ->default('PRIVATE')
                                                        ->columnSpan(1),
                                                    Forms\Components\Select::make('order.hdmf.input.PAY_MODE')
                                                        ->label('Pay Mode')
                                                        ->options([
                                                            'Salary deduction' => 'Salary deduction',
                                                            'Over-the-counter' => 'Over-the-counter',
                                                        ])
                                                        ->inlineLabel(true)
                                                        ->native(false)
                                                        ->columnSpan(1),
                                                    Forms\Components\Select::make('order.hdmf.input.TYPE_OF_DEVELOPMENT')
                                                        ->label('Type of Development')
                                                        ->options([
                                                            'PD 957' => 'PD 957',
                                                            'BP 220' => 'BP 220',
                                                        ])
                                                        ->inlineLabel(true)
                                                        ->native(false)
                                                        ->columnSpan(1),
                                                    Forms\Components\Select::make('order.hdmf.input.PROJECT_TYPE')
                                                        ->label('Project Type')
                                                        ->options([
                                                            'ECONOMIC' => 'ECONOMIC',
                                                            'SOCIALIZED' => 'SOCIALIZED',
                                                        ])
                                                        ->inlineLabel(true)
                                                        ->afterStateUpdated(function(Set $set, $state){
                                                            if($state == 'ECONOMIC'){
                                                                $set('order.hdmf.input.PRICE_CEILING', config('property.market.ceiling.horizontal.economic'));
                                                            }else{
                                                                $set('order.hdmf.input.PRICE_CEILING', config('property.market.ceiling.horizontal.socialized'));
                                                            }
                                                        })
                                                        ->native(false)
                                                        ->columnSpan(1),
                                                    Forms\Components\Select::make('order.hdmf.input.HOUSING_TYPE')
                                                        ->label('Housing Type')
                                                        ->options([
                                                            'CONDOMINIUM' => 'CONDOMINIUM',
                                                            'DUPLEX' => 'DUPLEX',
                                                            'ROW / TOWN HOUSE' => 'ROW / TOWN HOUSE',
                                                            'SINGLE ATTACHED' => 'SINGLE ATTACHED',
                                                            'QUADRUPLEX' => 'QUADRUPLEX',
                                                        ])
                                                        ->live()
                                                        ->native(false)
                                                        ->inlineLabel(true)
                                                        ->default('')
                                                        ->columnSpan(1),
                                                    Forms\Components\TextInput::make('order.hdmf.input.TOTAL_FLOOR_NUMBER')
                                                        ->label('Total Floor Number')
                                                        ->inlineLabel(true)
                                                        ->default('2')
                                                        ->columnSpan(1),
                                                    Forms\Components\TextInput::make('order.hdmf.input.TOTAL_FLOOR_AREA')
                                                        ->label('Total Floor Area')
                                                        ->inlineLabel(true)
                                                        ->default('')
                                                        ->columnSpan(1),
                                                    Forms\Components\TextInput::make('order.hdmf.input.PRICE_CEILING')
                                                        ->label('Price Ceiling')
                                                        ->inlineLabel(true)
                                                        ->columnSpan(1),
                                                    Forms\Components\TextInput::make('order.hdmf.input.LTS_NUMBER')
                                                        ->label('LTS Number')
                                                        ->inlineLabel(true)
                                                        ->default('')
                                                        ->columnSpan(1),
                                                    Forms\Components\DatePicker::make('order.hdmf.input.LTS_DATE')
                                                        ->label('LTS Date')
                                                        ->native(false)
                                                        ->inlineLabel(true)
                                                        ->default('')
                                                        ->columnSpan(1),
                                                    Forms\Components\TextInput::make('order.hdmf.input.SELLING_PRICE')
                                                        ->label('Selling Price')
                                                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Auto-Generated: Equal to TCP')
                                                        ->afterStateUpdated(function(Set $set, $state){
                                                            // $set('order.hdmf.input.DESIRED_LOAN', $state);
                                                        })
                                                        ->live(onBlur: true)
                                                        ->live()
                                                        ->inlineLabel(true)
                                                        ->default(0)
                                                        ->columnSpan(1),
                                                    Forms\Components\TextInput::make('order.hdmf.input.APPRAISED_VALUE_LOT')
                                                        ->label('Appraised Value Lot')
                                                        ->inlineLabel(true)
                                                        ->default('')
                                                        ->columnSpan(1),
                                                    Forms\Components\TextInput::make('order.hdmf.input.APPRAISED_VALUE_HOUSE')
                                                        ->label('Appraised Value House')
                                                        ->inlineLabel(true)
                                                        ->default('')
                                                        ->columnSpan(1),
                                                    Forms\Components\TextInput::make('order.hdmf.input.DESIRED_LOAN')
                                                        ->label('Desired Loan')
                                                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Auto-Generated: TCP - Discount Rate and Equity')
                                                        ->afterStateUpdated(function(Set $set, $state){
                                                            $set('order.loan_value_after_downpayment', $state);
                                                        })
                                                        ->live(onBlur: true)
                                                        ->inlineLabel(true)
                                                        ->default('')
                                                        ->columnSpan(1),
                                                    Forms\Components\TextInput::make('order.hdmf.input.GROSS_INCOME_PRINCIPAL')
                                                        ->label('Gross Income Principal')
                                                        ->inlineLabel(true)
                                                        ->default('')
                                                        ->columnSpan(1),
                                                    Forms\Components\TextInput::make('order.hdmf.input.GROSS_INCOME_COBORROWER_1')
                                                        ->label('Gross Income Co-Borrower 1')
                                                        ->inlineLabel(true)
                                                        ->default('')
                                                        ->columnSpan(1),
                                                    Forms\Components\TextInput::make('order.hdmf.input.GROSS_INCOME_COBORROWER_2')
                                                        ->label('Gross Income Co-Borrower 2')
                                                        ->inlineLabel(true)
                                                        ->default('')
                                                        ->columnSpan(1),
                                                    Forms\Components\Select::make('order.hdmf.input.REPRICING_PERIOD')
                                                        ->label('Repricing Period')
                                                        ->options([
                                                            '1 yr' => '1 yr',
                                                            '3 yrs' => '3 yrs',
                                                            '5 yrs' => '5 yrs',
                                                            '10 yrs' => '10 yrs',
                                                            '15 yrs' => '15 yrs',
                                                            '20 yrs' => '20 yrs',
                                                            '25 yrs' => '25 yrs',
                                                            '30 yrs' => '30 yrs',
                                                        ])
                                                        ->inlineLabel(true)
                                                        ->columnSpan(1),
                                                    Forms\Components\TextInput::make('order.hdmf.input.LOAN_PERIOD_YEARS')
                                                        ->label('Loan Period Year')
                                                        ->numeric()
                                                        ->rules(['max:30'])
                                                        ->inlineLabel(true)
                                                        ->default('')
                                                        ->columnSpan(1),
                                                ])
                                                ->columns(2)
                                                ->columnSpan(2),
                                            // Forms\Components\Fieldset::make('Computed')
                                            //         ->schema([
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.GMI_PRINCIPAL')
                                            //                 ->label('GMI Principal')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.GMI_PERCENT_PRINCIPAL')
                                            //                 ->label('GMI Percent Principal')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.NET_GMI_PRINCIPAL')
                                            //                 ->label('Net GMI Principal')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.GMI_FACTOR_RATE_PRINCIPAL')
                                            //                 ->label('GMI Factor Rate Principal')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.TOTAL_GMI_LOANABLE')
                                            //                 ->label('Total GMI Loanable')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.MAXIMUM_LOANABLE_AMOUNT_PRINCIPAL')
                                            //                 ->label('Maximum Loanable Amount Principal')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.NET_LOANABLE_AMOUNT_PRINCIPAL')
                                            //                 ->label('Net Loanable Amount Principal')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.NET_LOANABLE_AMOUNT_TOTAL')
                                            //                 ->label('Net Loanable Amount Total')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_LABEL_1_PRINCIPAL')
                                            //                 ->label('Computation Label 1 Principal')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_LABEL_2_PRINCIPAL')
                                            //                 ->label('Computation Label 2 Principal')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_LABEL_3_PRINCIPAL')
                                            //                 ->label('Computation Label 3 Principal')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_LABEL_4_PRINCIPAL')
                                            //                 ->label('Computation Label 4 Principal')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_1_PRINCIPAL')
                                            //                 ->label('Computation 1 Principal')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_2_PRINCIPAL')
                                            //                 ->label('Computation 2 Principal')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_3_PRINCIPAL')
                                            //                 ->label('Computation 3 Principal')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_4_PRINCIPAL')
                                            //                 ->label('Computation 4 Principal')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_LABEL_1_COBORROWER_1')
                                            //                 ->label('Computation Label 1 Co-Borrower 1')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_LABEL_2_COBORROWER_1')
                                            //                 ->label('Computation Label 2 Co-Borrower 1')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_LABEL_3_COBORROWER_1')
                                            //                 ->label('Computation Label 3 Co-Borrower 1')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_LABEL_4_COBORROWER_1')
                                            //                 ->label('Computation Label 4 Co-Borrower 1')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_1_COBORROWER_1')
                                            //                 ->label('Computation 1 Co-Borrower 1')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_2_COBORROWER_1')
                                            //                 ->label('Computation 2 Co-Borrower 1')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_3_COBORROWER_1')
                                            //                 ->label('Computation 3 Co-Borrower 1')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_4_COBORROWER_1')
                                            //                 ->label('Computation 4 Co-Borrower 1')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_LABEL_1_COBORROWER_2')
                                            //                 ->label('Computation Label 1 Co-Borrower 2')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_LABEL_2_COBORROWER_2')
                                            //                 ->label('Computation Label 2 Co-Borrower 2')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_LABEL_3_COBORROWER_2')
                                            //                 ->label('Computation Label 3 Co-Borrower 2')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_LABEL_4_COBORROWER_2')
                                            //                 ->label('Computation Label 4 Co-Borrower 2')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_1_COBORROWER_2')
                                            //                 ->label('Computation 1 Co-Borrower 2')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_2_COBORROWER_2')
                                            //                 ->label('Computation 2 Co-Borrower 2')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_3_COBORROWER_2')
                                            //                 ->label('Computation 3 Co-Borrower 2')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_4_COBORROWER_2')
                                            //                 ->label('Computation 4 Co-Borrower 2')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.TOTAL_LABEL_1_PRINCIPAL')
                                            //                 ->label('Total Label 1 Principal')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.TOTAL_LABEL_2_PRINCIPAL')
                                            //                 ->label('Total Label 2 Principal')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.TOTAL_LABEL_3_PRINCIPAL')
                                            //                 ->label('Total Label 3 Principal')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.TOTAL_LABEL_4_PRINCIPAL')
                                            //                 ->label('Total Label 4 Principal')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_1_TOTAL')
                                            //                 ->label('Computation 1 Total')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_2_TOTAL')
                                            //                 ->label('Computation 2 Total')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_3_TOTAL')
                                            //                 ->label('Computation 3 Total')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.COMPUTATION_4_TOTAL')
                                            //                 ->label('Computation 4 Total')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.MRI_SRI')
                                            //                 ->label('MRI/SRI')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.DOC_STAMP')
                                            //                 ->label('Doc Stamp')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.MRI_SRI_TOTAL_1')
                                            //                 ->label('MRI/SRI Total 1')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.NON_LIFE_INSURANCE')
                                            //                 ->label('Non Life Insurance')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.FACTOR')
                                            //                 ->label('Factor')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.MONTHLY_P_I_PRINCIPAL')
                                            //                 ->label('Monthly PI Principal')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.BLANK_F132_PRINCIPAL')
                                            //                 ->label('Blank F132 Principal')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.MRI_SRI_PRINCIPAL')
                                            //                 ->label('MRI/SRI Principal')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.BLANK_F135_PRINCIPAL')
                                            //                 ->label('Blank F135 Principal')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.AAP_PRINCIPAL')
                                            //                 ->label('AAP Principal')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.DOC_STAMP_PRINCIPAL')
                                            //                 ->label('Doc Stamp Principal')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.ANNUAL_PREMIUM_PRINCIPAL')
                                            //                 ->label('Annual Premium Principal')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.MONTHLY__P_I_TOTAL')
                                            //                 ->label('Monthly PI Total')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.BLANK_F132_TOTAL')
                                            //                 ->label('Blank F132 Total')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.MRI_SRI_TOTAL_2')
                                            //                 ->label('MRI/SRI Total 2')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.BLANK_F162_PRINCIPAL')
                                            //                 ->label('Blank F162 Principal')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.AP_TOTAL')
                                            //                 ->label('AP Total')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.DOC_STAMP_TOTAL')
                                            //                 ->label('Doc Stamp Total')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.ANNUAL_PREMIUM_TOTAL')
                                            //                 ->label('Annual Premium Total')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.BUILDING_VALUE')
                                            //                 ->label('Building Value')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.FIRE_COVERAGE')
                                            //                 ->label('Fire Coverage')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.ZONE')
                                            //                 ->label('Zone')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.TARIFF')
                                            //                 ->label('Tariff')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.AUP_1')
                                            //                 ->label('AUP 1')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.DOC_STAMP_PERCENT_FIRE_INSURANCE')
                                            //                 ->label('Doc Stamp Percent Fire Insurance')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.DOC_STAMP_FIRE_INSURANCE')
                                            //                 ->label('Doc Stamp Fire Insurance')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.FIRE_SERVICE_TAX_PERCENT')
                                            //                 ->label('Fire Service Tax Percent')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.FIRE_SERVICE_TAX')
                                            //                 ->label('Fire Service Tax')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.VALUE_ADDED_TAX_PERCENT_FIRE_INSURANCE')
                                            //                 ->label('Value Addeda Tax Percent Fire Insurance')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.VALUE_ADDED_TAX_FIRE_INSURANCE')
                                            //                 ->label('Value Added Tax Fire Insurance')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.LGU_TAX_PERCENT_FIRE_INSURANCE')
                                            //                 ->label('LGU Tax Percent Fire Insurance')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.LGU_TAX_FIRE_INSURANCE')
                                            //                 ->label('LGU Tax Fire Insurance')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.AUP_2')
                                            //                 ->label('AUP 2')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.AAP_LINE_1')
                                            //                 ->label('AAP Line 1')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.AAP_LINE_2')
                                            //                 ->label('AAP Line 2')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.TOTAL_FIRE_INSURANCE')
                                            //                 ->label('Total Fire Insurance')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.SELLING_PRICE')
                                            //                 ->label('Selling Price')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.PRICE_CEILING')
                                            //                 ->label('Price Ceiling')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.APPRAISED_VALUE')
                                            //                 ->label('Appraised Value')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.DESIRED_LOAN')
                                            //                 ->label('Desired Loan')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.GROSS_INCOME')
                                            //                 ->label('Gross Income')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.MAX_LOAN')
                                            //                 ->label('Max Loan')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //             Forms\Components\TextInput::make('order.hdmf.computed.RECOMMENDED_LOAN_BASE')
                                            //                 ->label('Recommended Loan Base')
                                            //                 ->inlineLabel(true)
                                            //                 ->default('')
                                            //                 ->columnSpan(1),
                                            //         ])
                                            //         ->columns(1)
                                            //         ->columnSpan(1),
                                        ])
                                        ->columns(2)
                                        ->columnSpanFull(),
                                    Forms\Components\Fieldset::make('Evaluation')
                                        ->schema([

                                            Forms\Components\TextInput::make('order.net_loan_proceeds')
                                                ->label('Net Loan Proceeds')
                                                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Auto-Generated: Loan Base - Deduction from Loan Proceeds')
                                                ->default(0)
                                                ->columnSpan(3),
                                            Forms\Components\TextInput::make('order.disclosure_statement_on_loan_transaction_total')
                                                ->label('Deduction from Loan Proceeds')
                                                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Auto-Generated: MRISRI Docstamp Total + Non Life Insurance + Retention Fee + 5,000 + 2,000 + 1,000')
                                                ->default(0)
                                                ->columnSpan(3),
                                            Forms\Components\TextInput::make('order.interest')
                                                ->label('Interest')
                                                ->default(0)
                                                ->columnSpan(3),
                                            Forms\Components\Select::make('order.loan_period_months')
                                                ->label('Loan Period in Months')
                                                ->afterStateUpdated(function(Set $set, Get $get, String $state = null, Contact $record){
                                                    $set('order.loan_term', $state);
                                                    $set('order.hdmf.input.LOAN_PERIOD_YEARS', LoanTermOptions::getDataByMonthsTerm($state)['loanable_years']);
                                                    $option = LoanTermOptions::getDataByMonthsTerm($state);
                                                    $set('order.term_1', $option['term_1']);
                                                    $set('order.term_2', $option['term_2']);
                                                    $set('order.term_3', $option['term_3']);
                                                })
                                                ->helperText(fn($state)=>'equivalent to '.(($state != null && !empty(LoanTermOptions::getDataByMonthsTerm($state))) ? LoanTermOptions::getDataByMonthsTerm($state)['loanable_years'] : ' - ').' Years')
                                                ->options(fn()=>LoanTermOptions::getOptions())
                                                ->default(0)
                                                ->live()
                                                ->native(false)
                                                ->columnSpan(3),
                                            Forms\Components\TextInput::make('order.comencement_period')
                                                ->label('Comencement Period')
                                                ->hint('If Affordable')
                                                ->default(0)
                                                ->columnSpan(3),
                                            Forms\Components\TextInput::make('order.repricing_period_affordable')
                                                ->label('Repricing Period Affordable')
                                                ->hint('If Affordable')
                                                ->default(0)
                                                ->columnSpan(3),
                                            Forms\Components\TextInput::make('order.non_life_insurance')
                                                ->label('Non Life Insurance')
                                                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Auto-Generated: Based on Evaluation')
                                                ->numeric()
                                                ->default(0)
                                                ->columnSpan(3),
                                            Forms\Components\TextInput::make('order.mrisri_docstamp_total')
                                                ->label('MRISRI Docstamp Total')
                                                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Auto-Generated: Based on Evaluation')
                                                ->numeric()
                                                ->default(0)
                                                ->columnSpan(3),
                                            Group::make()->schema([
                                                Forms\Components\TextInput::make('order.term_1')
                                                    ->label('Term 1')
                                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Auto-Generated: Based on Loan Term/Period')
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('order.term_2')
                                                    ->label('Term 2')
                                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Auto-Generated: Based on Loan Term/Period')
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('order.term_3')
                                                    ->label('Term 3')
                                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Auto-Generated: Based on Loan Term/Period')
                                                    ->columnSpan(3),
                                            ])->columns(12)
                                                ->columnSpanFull(),
                                            Group::make()->schema([
                                                Forms\Components\TextInput::make('order.amort_princ_int1')
                                                    ->label('Amort PRINC INT 1')
                                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Auto-Generated: Based on Evaluation')
                                                    ->numeric()
                                                    ->default(0)
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('order.amort_princ_int2')
                                                    ->label('Amort PRINC INT 2')
                                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Auto-Generated: Based on Evaluation')
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('order.amort_princ_int3')
                                                    ->label('Amort PRINC INT 3')
                                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Auto-Generated: Based on Evaluation')
                                                    ->columnSpan(3),
                                            ])->columns(12)
                                                ->columnSpanFull(),
                                            Group::make()->schema([
                                                Forms\Components\TextInput::make('order.amort_nonlife1')
                                                    ->label('Amort Nonlife 1')
                                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Auto-Generated: Based on Evaluation')
                                                    ->numeric()
                                                    ->default(0)
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('order.amort_nonlife2')
                                                    ->label('Amort Nonlife 2')
                                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Auto-Generated: Based on Evaluation')
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('order.amort_nonlife3')
                                                    ->label('Amort Nonlife 3')
                                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Auto-Generated: Based on Evaluation')
                                                    ->columnSpan(3),
                                            ])->columns(12)
                                                ->columnSpanFull(),
                                            Group::make()->schema([
                                                Forms\Components\TextInput::make('order.amort_mrisri1')
                                                    ->label('Amort MRISRI 1')
                                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Auto-Generated: Based on Evaluation')
                                                    ->numeric()
                                                    ->default(0)
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('order.amort_mrisri2')
                                                    ->label('Amort MRISRI 2')
                                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Auto-Generated: Based on Evaluation')
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('order.amort_mrisri3')
                                                    ->label('Amort MRISRI 3')
                                                    ->columnSpan(3),
                                            ])->columns(12)
                                                ->columnSpanFull(),
                                            Group::make()->schema([
                                                Forms\Components\TextInput::make('order.monthly_amort1')
                                                    ->label('Monthly Amort 1')
                                                    ->numeric()
                                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Auto-Generated: Based on Evaluation')
                                                    ->default(0)
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('order.monthly_amort2')
                                                    ->label('Monthly Amort 2')
                                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Auto-Generated: Based on Evaluation')
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('order.monthly_amort3')
                                                    ->label('Monthly Amort 3')
                                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Auto-Generated: Based on Evaluation')
                                                    ->columnSpan(3),
                                            ])->columns(12)
                                                ->columnSpanFull(),
                                            Forms\Components\TextInput::make('order.loan_base')
                                                ->label('Loan Base')
                                                ->numeric()
                                                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Auto-Generated: Based on Evaluation')
                                                ->default(0)
                                                ->columnSpan(3),
                                        ])
                                        ->columns(12)
                                        ->columnSpanFull(),
                                ])->columns(12)->columnSpanFull(),

                            ]),
                        Forms\Components\Tabs\Tab::make('Upload Documents')
                            ->icon('heroicon-m-cloud-arrow-up')
                            ->schema(function(Model $record) {

                                return [
                                    Forms\Components\Section::make()
                                        ->schema([
                                            Livewire::make(RequirementsTable::class, ['record' => $record])
                                                ->key(Carbon::now()->format('Y-m-d H:i:s'))
                                                ->columnSpanFull(),
                                        ]),
                                ];
                            }),
                        Forms\Components\Tabs\Tab::make('Generated Documents')
                            ->icon('heroicon-m-document-duplicate')
                            ->schema(function(Get $get){

                                $maped_documents=collect($get('documents'))
                                    ->map(function($document) {
                                       return Forms\Components\Section::make($document['name'])
                                           ->headerActions([
                                               Forms\Components\Actions\Action::make('download')
                                               ->icon('heroicon-m-arrow-down-tray')
                                            ->url(fn () => route('download.pdf', ['url' => $document['url']]), true),
                                           ])
                                            ->schema([
                                                Placeholder::make($document['name'])
                                                    ->label('')
                                                    ->content(fn () => new HtmlString(
                                                        '<iframe src="https://docs.google.com/gview?url=' . urlencode($document['url']) . '&embedded=true" width="100%" height="1200px"></iframe>'
                                                    )),
                                            ])->collapsible()->collapsed();
                                    })->toArray();
                                return $maped_documents;
                            }),

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
