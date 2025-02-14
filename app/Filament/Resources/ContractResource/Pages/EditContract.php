<?php

namespace App\Filament\Resources\ContractResource\Pages;

use App\Actions\GenerateContractPayloads;
use App\Filament\Resources\ContractResource;
use App\Helpers\LoanTermOptions;
use App\Models\Payload;
use App\Models\RequirementMatrix;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\ActionSize;
use Homeful\Contacts\Actions\GetContactMetadataFromContactModel;
use Homeful\Contacts\Data\ContactData;
use Homeful\Contacts\Models\Customer as Contact;
use Homeful\Contracts\Models\Contract;
use Homeful\Properties\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Homeful\Contracts\States\ContractState;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

class EditContract extends EditRecord
{
    protected static string $resource = ContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\DeleteAction::make(),
            $this->getSaveFormAction()
                ->formId('form')
                ->requiresConfirmation(true)
                ->modalCancelActionLabel('No')
                ->modalSubmitActionLabel('Yes')
                ->visible(false)
                ->keyBindings(['command+s', 'ctrl+s']),
            Action::make('Save')
                ->color('primary')
                ->requiresConfirmation(true)
                ->modalCancelActionLabel('No')
                ->action(function(){
                    $this->save();
                })
                ->keyBindings(['command+s', 'ctrl+s']),
            Actions\ActionGroup::make(collect($this->record->state->transitionableStates())
                ->map(function ($state)  {
                    $stateInstance = new $state($this->record);

                    return Action::make($stateInstance->name()) // Ensure the action has a name
                    ->label($stateInstance->name())
                        ->icon($stateInstance->icon())
                        ->color($stateInstance->color())
                        ->action(function () use ($stateInstance) {
                            $this->record->state->transitionTo($stateInstance);
                            $this->record->save();
                        });
                })
                ->toArray())
                ->label('Update Status')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size(ActionSize::Small)
                ->color('primary')
                ->button(),
            Action::make('Update Status')
                ->color('primary')
                ->requiresConfirmation(true)
                ->modalCancelActionLabel('No')
                ->form([
                    Select::make('status')
                        ->options(function(Contract $record){
                           return collect($this->record->state->transitionableStates())
                                ->mapWithKeys(function ($state) {

                                    $stateInstance = new $state($this->record); // Pass the model instance
                                    return [$state => $stateInstance->name()]; // Key: name(), Value: class
                                })->toArray();
                        })->native(false)
                ])->action(function(array $data, Contract $record) {
                    $record->state->transitionTo($data['status']);
                    $record->save();
                }),
        ];
    }

    protected function resolveRecord(int|string $key): Model
    {
        return Contract::where('id', $key)->firstOrFail();
    }

    protected function beforeFill(): void
    {
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (!empty($data['property'])&& $data['property']!=null) {
            $data['desired_property']['project']=$data['property']['project']['name']??'';
            $data['desired_property']['unit_type']=$data['property']['unit_type']??'';
            $data['desired_property']['tcp']=$data['property']['tcp']??'';
            $data['desired_property']['payment_terms']=($this->record->mortgage->getBalancePaymentTerm() ?? 0) ? $this->record->mortgage->getBalancePaymentTerm() . ' years' : '';
            $data['desired_property']['monthly_amortization'] = $this->record->getData()->mortgage->loan_amortization??0;
        }

        if(!empty($data['payment'])&& $data['payment']!=null){
            $data['consult']['reference_code']=$data['payment']['data']['orderInformation']['orderId']??'';
            $data['consult']['fee']= isset($data['payment']['data']['orderInformation']['amount'])
                ? 'P' . number_format($data['payment']['data']['orderInformation']['amount'] / 100, 2)
                : '';
            $data['consult']['payment']=$data['payment']['data']['orderInformation']['paymentBrand']??'';
            $data['consult']['transaction_number']=$data['payment']['data']['orderInformation']['referencedId']??'';
            $data['consult']['transaction_date']=isset($data['payment']['data']['orderInformation']['responseDate'])
                ? \Carbon\Carbon::parse($data['payment']['data']['orderInformation']['responseDate'])->format('F j \a\t g:iA')
                : '';
        }

        if (!empty(optional($this->record->getData()->inventory)->toArray()))  {
            $new_data['order']['sku']=$this->record->getData()->inventory->toArray()['sku'];
            $new_data['order']['phase']=$this->record->getData()->inventory->toArray()['phase'];
            $new_data['order']['block']=$this->record->getData()->inventory->toArray()['block'];
            $new_data['order']['lot']=$this->record->getData()->inventory->toArray()['lot'];
            $new_data['order']['lot_area']=$this->record->getData()->inventory->toArray()['lot_area'];
            $new_data['order']['floor_area']=$this->record->getData()->inventory->toArray()['floor_area'];
            $new_data['order']['property_code']=$this->record->property_code;
            $new_data['order']['property_name'] = $this->record->getData()->inventory->toArray()['name'];
            $new_data['order']['property_type'] = $this->record->getData()->inventory->toArray()['type'];
            $new_data['order']['project_name'] = $this->record->getData()->inventory->toArray()['project']['name'];
            $new_data['order']['project_location'] = $this->record->getData()->inventory->toArray()['project']['location'];
            $new_data['order']['project_address'] = $this->record->getData()->inventory->toArray()['project']['address'];
            $new_data['order']['project_code'] = $this->record->getData()->inventory->toArray()['project']['code'];
            $new_data['order']['unit_type_interior'] = $this->record->getData()->inventory->toArray()['unit_type_interior'];
            $new_data['order']['unit_type'] = $this->record->getData()->inventory->toArray()['unit_type'];
            $new_data['order']['payment_scheme']['total_contract_price'] = $this->record->getData()->inventory->toArray()['tcp'];
        }
//        dd($this->record->getData()->inventory->toArray(),$data);

        $contact_data =$this->record->getData()->customer->toArray();

        $buyer_address_present = collect($contact_data['addresses'])
            ->filter(fn($address) => in_array($address['type'], ['Primary','Present']))
            ->first() ?? [];
        $buyer_address_permanent = collect($contact_data['addresses'])
            ->filter(fn($address) => in_array($address['type'], ['Sencondary','Permanent']))
            ->first() ?? [];
        $buyer_employment = collect($contact_data['employment'])->firstWhere('type', 'Primary') ?? [];
        $new_data['buyer_employment']=$buyer_employment;
        // Spouse details if available
        if( !empty($contact_data['spouse'])){
            $new_data['spouse'] = $contact_data['spouse'] ?? [];
            $new_data['spouse']['no_middle_name']=$new_data['spouse']['middle_name']==''||$new_data['spouse']['middle_name']==null;
        }

        if (!empty($contact_data['aif'])){
            $new_data['aif']=$contact_data['aif']??[];
            $new_data['aif']['first_name'] = $contact_data['aif']['aif_attorney_first_name']??'';
            $new_data['aif']['last_name'] = $contact_data['aif']['aif_attorney_last_name']??'';
            $new_data['aif']['middle_name'] = $contact_data['aif']['aif_attorney_middle_name']??'';
            $new_data['aif']['name_suffix'] = $contact_data['aif']['aif_attorney_name_suffix']??'';
            $new_data['aif']['no_middle_name'] = ($contact_data['aif']['aif_attorney_middle_name'] == '');
        }

//
////        $new_data['seller'] = $contact_data->order->toArray()['seller'] ?? [];
//        $new_data['reference_code'] = $contact_data->reference_code;
//        $new_data['uploads']=$contact_data->uploads->toArray();
        $new_data['buyer']['mobile'] = $contact_data['mobile'];
//        $new_data['buyer'] = $contact_data->profile->toArray();
        $new_data['buyer'] = [
            'first_name' => $contact_data['first_name']??'',
            'last_name' => $contact_data['last_name']??'',
            'middle_name' => $contact_data['middle_name']??'',
            'name_suffix' => $contact_data['name_suffix']??'',
            'mothers_maiden_name' => $contact_data['mothers_maiden_name']??'',
            'email' => $contact_data['email']??'',
            'mobile' => $contact_data['mobile']??'',
            'other_mobile' => $contact_data['other_mobile']??'',
            'help_number' => $contact_data['help_number']??'',
            'landline' => $contact_data['landline']??'',
            'civil_status' => $contact_data['civil_status']??'',
            'sex' => $contact_data['sex']??'',
            'nationality' => $contact_data['nationality']??'',
            'date_of_birth' => $contact_data['date_of_birth'],
        ];
//
        $new_data['buyer']['no_middle_name']=$new_data['buyer']['middle_name']==''||$new_data['buyer']['middle_name']==null;
        $new_data['buyer']['address']['present'] = $buyer_address_present;
        $new_data['buyer']['address']['permanent'] = $buyer_address_permanent;
        $buyer_address_permanent['address1']=$buyer_address_permanent['address1']??'';
        $buyer_address_present['address1']=$buyer_address_present['address1']??'';
        $buyer_address_present['locality']=$buyer_address_present['locality']??'';
        $buyer_address_permanent['locality']=$buyer_address_permanent['locality']??'';
        $buyer_address_present['administrative_area']=$buyer_address_present['administrative_area']??'';
        $buyer_address_permanent['administrative_area']=$buyer_address_permanent['administrative_area']??'';
        $buyer_address_present['region']=$buyer_address_present['region']??'';
        $buyer_address_permanent['region']=$buyer_address_permanent['region']??'';
        $buyer_address_present['country']= $buyer_address_present['country']??'';
        $buyer_address_permanent['country']= $buyer_address_permanent['country']??'';
//
        $new_data['buyer']['address']['present']['address1'] = $buyer_address_present['address1']??'';
        $new_data['buyer']['address']['present']['locality'] = $buyer_address_present['locality']??'';
//        $new_data['buyer']['address']['present']['sublocality'] = $buyer_address_present['sublocality'];
        $new_data['buyer']['address']['present']['administrative_area'] = $buyer_address_present['administrative_area']??'';
        $new_data['buyer']['address']['present']['region'] = $buyer_address_present['region']??'';
        $new_data['buyer']['address']['present']['country'] = $buyer_address_present['country']??'';
//
        $new_data['buyer']['address']['permanent']['address1'] = $buyer_address_permanent['address1']??'';
        $new_data['buyer']['address']['permanent']['city'] = $buyer_address_permanent['locality']??'';
//        $new_data['buyer']['address']['permanent']['barangay'] = $buyer_address_permanent['sublocality'];
        $new_data['buyer']['address']['permanent']['province'] = $buyer_address_permanent['administrative_area']??'';
        $new_data['buyer']['address']['permanent']['region'] = $buyer_address_permanent['region']??'';
        $new_data['buyer']['address']['permanent']['country'] = $buyer_address_permanent['country']??'';

        $is_same_address = (
            $buyer_address_present['address1'] === $buyer_address_permanent['address1'] &&
            $buyer_address_present['locality'] === $buyer_address_permanent['locality'] &&
//            $buyer_address_present['sublocality'] === $buyer_address_permanent['sublocality'] &&
            $buyer_address_present['administrative_area'] === $buyer_address_permanent['administrative_area']&&
            $buyer_address_present['region'] === $buyer_address_permanent['region'] &&
            $buyer_address_present['country'] === $buyer_address_permanent['country']
        );
//
        $new_data['address']['present']['same_as_permanent'] = $is_same_address;
//
//gi
//        $cobo_address=  collect($contact_data->addresses)
//            ->filter(fn($address) => in_array($address['type'], ['co_borrower']))
//            ->first() ?? [];
//        $cobo_employment = collect($contact_data->employment)->firstWhere('type', 'co_borrower') ?? [];
//        $new_data['co_borrowers'] =collect( $contact_data->co_borrowers)->map(function ($cobo) use($cobo_address,$cobo_employment){
//
//            return [
//                'name' => "{$cobo['first_name']} {$cobo['middle_name']} {$cobo['last_name']}",
//                'first_name' => $cobo['first_name'] ?? '',
//                'middle_name' => $cobo['middle_name'] ?? '',
//                'no_middle_name'=> $cobo['middle_name']==null?true:false,
//                'last_name' => $cobo['last_name'] ?? '',
//                'name_suffix' => $cobo['name_suffix'] ?? '',
//                'date_of_birth' => $cobo['date_of_birth'] ?? '',
//                'civil_status' => $cobo['civil_status'] ?? '',
//                'sex' => $cobo['sex'] ?? '',
//                'nationality' => $cobo['nationality'] ?? '',
//                'email' => $cobo['email'] ?? '',
//                'mobile' => $cobo['mobile'] ?? '',
//                'other_mobile' => $cobo['other_mobile'] ?? '',
//                'relationship_to_buyer' => $cobo['relationship_to_buyer'] ?? '',
//                'help_number' => $cobo['help_number'] ?? '',
//                'mothers_maiden_name' => $cobo['mothers_maiden_name'] ?? '',
//                'passport' => $cobo['passport'] ?? '',
//                'date_issued' => $cobo['date_issued'] ?? '',
//                'place_issued' => $cobo['place_issued'] ?? '',
//                'spouse' => [
//                    'first_name' => $this->record->co_borrowers[0]['spouse']['first_name'] ?? null,
//                    'middle_name' => $this->record->co_borrowers[0]['spouse']['middle_name'] ?? null,
//                    'last_name' => $this->record->co_borrowers[0]['spouse']['last_name'] ?? null,
//                    'name_suffix' => $this->record->co_borrowers[0]['spouse']['name_suffix'] ?? null,
//                    'civil_status' => null,
//                    'sex' => null,
//                    'nationality' => null,
//                    'date_of_birth' => null,
//                    'email' => null,
//                    'mobile' => null,
//                    'landline' => null,
//                    'mothers_maiden_name' => null,
//                    'tin' => $this->record->co_borrowers[0]['spouse']['tin'] ?? null,
//                ],
//                'coborrower'=>[
//                    'address'=>[
//                        'present' =>$cobo_address,
//                    ]
//                ],
//                'coborrower_employment'=>$cobo_employment,
//            ];
//        })->toArray();
//
//        $new_data['order']['hdmf']=$order['hdmf'] ?? [];
//        $new_data['order']['hdmf']['input']['SELLING_PRICE'] = $new_data['order']['payment_scheme']['net_total_contract_price'] ?? 0;
//        $new_data['order']['hdmf']['input']['DESIRED_LOAN'] = $new_data['order']['payment_scheme']['net_total_contract_price'] ?? 0;
//        $new_data['order']['payment_scheme']['net_total_contract_price'] = (isset($new_data['order']['payment_scheme']['net_total_contract_price']) && ($new_data['order']['payment_scheme']['net_total_contract_price'] != 0)) ? $new_data['order']['payment_scheme']['net_total_contract_price'] : $new_data['order']['hdmf']['input']['DESIRED_LOAN'];
//        $new_data['order']['loan_value_after_downpayment'] = (($new_data['order']['payment_scheme']['net_total_contract_price'] ?? 0) - ($new_data['order']['equity_1_amount'] ?? 0));
//        $new_data['order']['hdmf']['input']['PRINCIPAL_BORROWER'] = ($contact_data->profile->first_name ?? '').' '.($contact_data->profile->middle_name ?? '').' '.($contact_data->profile->last_name ?? '');
//        $new_data['order']['hdmf']['input']['BIRTH_DATE'] = $contact_data->profile->date_of_birth ?? '';
//        $new_data['order']['hdmf']['input']['GROSS_INCOME_PRINCIPAL'] =collect($contact_data['employment']) ->firstWhere('type', 'Primary')['monthly_gross_income']?? 0;
//        $new_data['order']['hdmf']['input']['COBORROWER_1'] = ($contact_data->co_borrowers[0]?->first_name ?? '').' '.($contact_data->co_borrowers[0]?->middle_name ?? '').' '.($contact_data->co_borrowers[0]?->last_name ?? '');
//        $new_data['order']['hdmf']['input']['BIRTH_DATE_COBORROWER_1'] = $contact_data->co_borrowers[0]?->date_of_birth ?? '';
//        $new_data['order']['hdmf']['input']['GROSS_INCOME_COBORROWER_1'] = $contact_data->employment?->toCollection()->firstWhere('type', 'co_borrower')->monthly_gross_income ?? null;
//        $new_data['order']['hdmf']['input']['TITLE'] = 'EVALUATION SHEET  V1.0.2';
//        $new_data['order']['hdmf']['input']['GUIDELINE'] = isset($new_data['order']['hdmf']['input']['GUIDELINE']) ? $new_data['order']['hdmf']['input']['GUIDELINE'] : ((($new_data['order']['payment_scheme']['total_contract_price'] ?? 0) <= 750000) ? '403/349' : '396/349');
//        $new_data['order']['hdmf']['input']['PROGRAM'] = isset($new_data['order']['hdmf']['input']['PROGRAM']) ? $new_data['order']['hdmf']['input']['PROGRAM'] : 'CTS';
//        $new_data['order']['hdmf']['input']['PAY_MODE'] = isset($new_data['order']['hdmf']['input']['PAY_MODE']) ? $new_data['order']['hdmf']['input']['PAY_MODE'] : 'Over-the-counter';
//        $new_data['order']['hdmf']['input']['TYPE_OF_DEVELOPMENT'] = isset($new_data['order']['hdmf']['input']['TYPE_OF_DEVELOPMENT']) ? $new_data['order']['hdmf']['input']['TYPE_OF_DEVELOPMENT'] : 'BP 220';
//        $new_data['order']['hdmf']['input']['HOUSING_TYPE'] = isset($new_data['order']['hdmf']['input']['HOUSING_TYPE']) ? $new_data['order']['hdmf']['input']['HOUSING_TYPE'] : 'CONDOMINIUM';
//        $loan_in_years = (!empty(LoanTermOptions::getDataByMonthsTerm($new_data['order']['loan_term'])) ? LoanTermOptions::getDataByMonthsTerm($new_data['order']['loan_term'])['loanable_years'] : '');
//        $new_data['order']['hdmf']['input']['LOAN_PERIOD_YEARS'] = isset($new_data['order']['hdmf']['input']['LOAN_PERIOD_YEARS']) ? $new_data['order']['hdmf']['input']['LOAN_PERIOD_YEARS'] : $loan_in_years ;
//        $new_data['order']['hdmf']['input']['TOTAL_FLOOR_AREA'] = isset($new_data['order']['hdmf']['input']['TOTAL_FLOOR_AREA']) ? $new_data['order']['hdmf']['input']['TOTAL_FLOOR_AREA'] : $new_data['order']['floor_area'] ;
//        $new_data['order']['hdmf']['input']['PROJECT_TYPE'] = ($new_data['order']['payment_scheme']['total_contract_price'] > 850000) ? 'ECONOMIC' : 'SOCIALIZED';
//        $new_data['order']['hdmf']['input']['WORK_AREA'] = ($new_data['buyer_employment']['employer']['address']['country'] !== 'PH' || ($new_data['buyer_employment']['employer']['address']['country'] === 'PH' && $new_data['buyer_employment']['employer']['address']['region'] === '13')) ? 'HUC' : 'REGION';
//        $new_data['order']['equity_1_interest_rate'] = isset($new_data['order']['equity_1_interest_rate']) ? (($new_data['order']['equity_1_interest_rate'] < 1) ? $new_data['order']['equity_1_interest_rate'] * 100 : $new_data['order']['equity_1_interest_rate']) : 0;
//        $new_data['order']['loan_interest_rate'] = isset($new_data['order']['loan_interest_rate']) ? (($new_data['order']['loan_interest_rate'] < 1) ? $new_data['order']['loan_interest_rate'] * 100 : $new_data['order']['loan_interest_rate']) : 0;
//        $new_data['order']['interest'] = isset($new_data['order']['interest']) ? (($new_data['order']['interest'] < 1) ? $new_data['order']['interest'] * 100 : $new_data['order']['interest']) : 0;
//        $new_data['order']['bp_1_interest_rate'] = isset($new_data['order']['bp_1_interest_rate']) ? (($new_data['order']['bp_1_interest_rate'] < 1) ? $new_data['order']['bp_1_interest_rate'] * 100 : $new_data['order']['bp_1_interest_rate']) : 0;
//
//        if(!isset($new_data['order']['hdmf']['input']['PRICE_CEILING'])){
//            $new_data['order']['hdmf']['input']['PRICE_CEILING'] = ($new_data['order']['hdmf']['input']['PROJECT_TYPE'] == 'ECONOMIC') ? config('property.market.ceiling.horizontal.economic') : config('property.market.ceiling.horizontal.socialized');
//        }
////        if($new_data['order']['project_code'] && $new_data['order']['lot_area']){
////            dd(Project::all());
////            $project = Project::where('code', $new_data['order']['project_code']);
////            if($project->count() > 0){
////                $new_data['order']['hdmf']['input']['APPRAISED_VALUE_LOT'] = $new_data['order']['lot_area'] * $project->first()->appraised_value_per_sqm;
////            }else{
////                $new_data['order']['hdmf']['input']['APPRAISED_VALUE_LOT'] = 0;
////            }
////        }else{
////            $new_data['order']['hdmf']['input']['APPRAISED_VALUE_LOT'] = 0;
////        }
//        if($new_data['order']['repricing_period']){
//            if($new_data['order']['repricing_period'] == 1){
//                $new_data['order']['hdmf']['input']['REPRICING_PERIOD'] = $new_data['order']['repricing_period'].' yr';
//            }else{
//                $new_data['order']['hdmf']['input']['REPRICING_PERIOD'] = $new_data['order']['repricing_period'].' yrs';
//            }
//        }
//
//        dd($new_data);


        $new_data['contact'] = $new_data;
        $data['contact_data']=$new_data;
        return $data;
    }
}
