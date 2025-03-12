<?php

namespace App\Filament\Resources\ContractResource\Pages;

use App\Filament\Resources\ContractResource;
use App\Actions\GenerateContractPayloads;
use App\Models\Payload;
use Filament\Resources\Pages\EditRecord;
use Homeful\Contacts\Models\Contact;
use Illuminate\Database\Eloquent\Model;
use Homeful\Contracts\Models\Contract;
use Filament\Forms\Components\Select;
use Filament\Actions\Action;




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
//            Actions\ActionGroup::make(collect($this->record->state->transitionableStates())
//                ->map(function ($state)  {
//                    $stateInstance = new $state($this->record);
//
//                    return Action::make($stateInstance->name()) // Ensure the action has a name
//                    ->label($stateInstance->name())
//                        ->icon($stateInstance->icon())
//                        ->color($stateInstance->color())
//                        ->action(function () use ($stateInstance) {
//                            $this->record->state->transitionTo($stateInstance);
//                            $this->record->save();
//                        });
//                })
//                ->toArray())
//                ->label('Update Status')
//                ->icon('heroicon-m-ellipsis-vertical')
//                ->size(ActionSize::Small)
//                ->color('primary')
//                ->button(),
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
        GenerateContractPayloads::dispatch($this->record);

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


        if($this->record->mortgage !=null){
            $new_data['order']['net_loan_proceeds'] = $this->record->getData()->mortgage->add_on_fees_to_payment??'';
//            $new_data['order']['non_life_insurance'] = $this->record->getData()->mortgage->loan[0][''];
//            $new_data['order']['mrisri_docstamp_total'] = $this->record->getData()->mortgage->add_on_fees_to_payment??'';
        }

        if ($this->record->inventory !=null)  {
            $payloads = Payload::with(['mapping' => function ($query) {
                $query->select('code', 'title', 'category');
            }])
                ->get(['mapping_code', 'value'])
                ->pluck('value', 'mapping_code')->toArray();

            $new_data['order']['tct_no']=$payloads['tct_no']??'';
            $new_data['order']['technical_description']=$payloads['technical_description']??'';
            $new_data['order']['registry_of_deeds_address']=$payloads['registry_of_deeds_address']??'';



            $new_data['order']['sku']=$this->record->getData()->inventory->toArray()['sku']??'';
            $new_data['order']['phase']=$this->record->getData()->inventory->toArray()['phase']??'';
            $new_data['order']['block']=$this->record->getData()->inventory->toArray()['block']??'';
            $new_data['order']['lot']=$this->record->getData()->inventory->toArray()['lot']??'';
            $new_data['order']['lot_area']=$this->record->getData()->inventory->toArray()['lot_area']??'';
            $new_data['order']['floor_area']=$this->record->getData()->inventory->toArray()['floor_area']??'';
            $new_data['order']['property_code']=$this->record->property_code??'';
            $new_data['order']['property_name'] = $this->record->getData()->inventory->toArray()['name']??'';
            $new_data['order']['property_type'] = $this->record->getData()->inventory->toArray()['type']??'';
            $new_data['order']['project_name'] = $this->record->getData()->inventory->toArray()['project']['name']??'';
            $new_data['order']['project_location'] = $this->record->getData()->inventory->toArray()['project']['location']??'';
            $new_data['order']['project_address'] = $this->record->getData()->inventory->toArray()['project']['address']??'';
            $new_data['order']['project_code'] = $this->record->getData()->inventory->toArray()['project']['code']??'';
            $new_data['order']['unit_type_interior'] = $this->record->getData()->inventory->toArray()['unit_type_interior']??'';
            $new_data['order']['unit_type'] = $this->record->getData()->inventory->toArray()['unit_type']??'';
            $new_data['order']['payment_scheme']['total_contract_price'] = $this->record->getData()->inventory->toArray()['tcp']??'';
        }

        $contact_data= $this->record->customer->toArray();

        if($contact_data["co_borrowers"]){
            foreach($this->record->customer->co_borrowers->toArray() as $co_borrower){
//                dd($co_borrower,$co_borrower['employment'][0]);
                $co_borrower['address']['primary']= $co_borrower['addresses'][0];
                $co_borrower["coborrower_employment"]=$co_borrower["employment"][0];
                $new_data['co_borrowers'][] = $co_borrower;
            }
        }

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
            $new_data['aif']=$this->record->customer->aif->toArray()??[];
        }

        $data['misc']['input']=$this->record->misc_inputs;


        $new_data['buyer']['mobile'] = $contact_data['mobile'];
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
        $new_data['address']['present']['same_as_permanent'] = $is_same_address;



        $new_data['contact'] = $new_data;
        $data['contact_data']=$new_data;
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        dd($data);
        return parent::mutateFormDataBeforeSave($data); // TODO: Change the autogenerated stub
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        dd($data);
        $record->update($data['misc']);
        return $record;
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        dd($this->record,$this->data);

        $this->record->misc_inputs=$this->data['misc']['input'];
        $this->record->property_code = $this->data['property_code'];
        $contact = Contact::where('id',$this->record->contact_id)->first();

        $data= $this->data;
        $contact_attribs=[
            'reference_code'=> $contact->reference_code,
            'first_name' => $data['contact_data']['buyer']['first_name'],
            'middle_name' => $data['contact_data']['buyer']['middle_name'],
            'last_name' => $data['contact_data']['buyer']['last_name'],
            'name_suffix' => $data['contact_data']['buyer']['name_suffix'],
            'civil_status' => $data['contact_data']['buyer']['civil_status'],
            'sex' => $data['contact_data']['buyer']['sex'],
            'nationality' => $data['contact_data']['buyer']['nationality'],
            'date_of_birth' => $data['contact_data']['buyer']['date_of_birth'],
            'email' => $data['contact_data']['buyer']['email'],
            'mobile' => $data['contact_data']['buyer']['mobile'],
            'other_mobile' => $data['contact_data']['buyer']['other_mobile'],
            'landline' => $data['contact_data']['buyer']['landline'],
            'help_number' => $data['contact_data']['buyer']['help_number'],
            'employment' => [
                [
                    'type'=>'Primary',
                    'employment_status' => $data['contact_data']['buyer_employment']['employment_status'] ?? null,
                    'monthly_gross_income' => $data['contact_data']['buyer_employment']['monthly_gross_income'] ?? null,
                    'current_position' => $data['contact_data']['buyer_employment']['current_position'] ?? null,
                    'employment_type' => $data['contact_data']['buyer_employment']['employment_type'] ?? null,
                    'years_in_service' => $data['contact_data']['buyer_employment']['years_in_service'] ?? null,
                    'rank' => $data['contact_data']['buyer_employment']['rank'] ?? null,
                    'industry' => $data['contact_data']['buyer_employment']['industry'] ?? null,
                    'employer' => [
                        'name' => $data['contact_data']['buyer_employment']['employer']['name'] ?? null,
                        'industry' => $data['contact_data']['buyer_employment']['employer']['industry'] ?? null,
                        'nationality' => $data['contact_data']['buyer_employment']['employer']['nationality'] ?? null,
                        'contact_no' => $data['contact_data']['buyer_employment']['employer']['contact_no'] ?? null,
                        'year_established' => $data['contact_data']['buyer_employment']['employer']['year_established'] ?? null,
                        'total_number_of_employees' => $data['contact_data']['buyer_employment']['employer']['total_number_of_employees'] ?? null,
                        'email' => $data['contact_data']['buyer_employment']['employer']['email'] ?? null,
                        'fax' => $data['contact_data']['buyer_employment']['employer']['fax'] ?? null,

                        // Expanding the employer address structure
                        'address' => [
                            'full_address' => $data['contact_data']['buyer_employment']['employer']['address']['full_address'] ?? null,
                            'address1' => $data['contact_data']['buyer_employment']['employer']['address']['address1'] ?? null,
                            'sublocality' => $data['contact_data']['buyer_employment']['employer']['address']['sublocality'] ?? null,
                            'locality' => $data['contact_data']['buyer_employment']['employer']['address']['locality'] ?? null,
                            'administrative_area' => $data['contact_data']['buyer_employment']['employer']['address']['administrative_area'] ?? null,
                            'country' => $data['contact_data']['buyer_employment']['employer']['address']['country'] ?? null,
                            'region' => $data['contact_data']['buyer_employment']['employer']['address']['region'] ?? null,
                            'ownership' =>'company',
                            'type'=>'company',
                        ]
                    ],
                    'id' => [
                        'tin' => $data['contact_data']['buyer_employment']['id']['tin'] ?? null,
                        'pagibig' => $data['contact_data']['buyer_employment']['id']['pagibig'] ?? null,
                        'sss' => $data['contact_data']['buyer_employment']['id']['sss'] ?? null,
                        'gsis' => $data['contact_data']['buyer_employment']['id']['sss'] ?? null,
                    ],
                    'character_reference'=> $data['contact_data']['buyer_employment']['character_reference']??[],
                ]
            ],
            'addresses' => [
                [
                    'type' => 'Primary',
                    'ownership' => $data['contact_data']['buyer']['address']['present']['ownership'] ?? null,
                    'address1'=>$data['contact_data']['buyer']['address']['present']['address1'] ?? null,
                    'sublocality' => $data['contact_data']['buyer']['address']['present']['sublocality'] ?? null,
                    'locality' => $data['contact_data']['buyer']['address']['present']['locality'] ?? null,
                    'administrative_area' => $data['contact_data']['buyer']['address']['present']['administrative_area'] ?? null,
                    'postal_code' => $data['contact_data']['buyer']['address']['present']['postal_code'] ?? null,
                    'region' => $data['contact_data']['buyer']['address']['present']['region'] ?? '',
                    'country' => $data['contact_data']['buyer']['address']['present']['country'] ?? '',
                ],
                [
                    'type' => 'Secondary',
                    'ownership' => $data['contact_data']['buyer']['address']['permanent']['ownership'] ?? null,
                    'address1'=>$data['contact_data']['buyer']['address']['permanent']['address1'] ?? null,
                    'sublocality' => $data['contact_data']['buyer']['address']['permanent']['sublocality'] ?? null,
                    'locality' => $data['contact_data']['buyer']['address']['permanent']['locality'] ?? null,
                    'administrative_area' => $data['contact_data']['buyer']['address']['permanent']['administrative_area'] ?? null,
                    'postal_code' => $data['contact_data']['buyer']['address']['permanent']['postal_code'] ?? null,
                    'region' => $data['contact_data']['buyer']['address']['permanent']['region'] ?? '',
                    'country' => $data['contact_data']['buyer']['address']['permanent']['country'] ?? '',
                ]
            ]
        ];

        if($data['contact_data']['buyer']['civil_status']=='Married'){
            $contact_attribs['spouse']= [
                'first_name' => $data['contact_data']['spouse']['first_name'] ?? null,
                'middle_name' => $data['contact_data']['spouse']['middle_name'] ?? null,
                'last_name' => $data['contact_data']['spouse']['last_name'] ?? null,
                'name_suffix' => $data['contact_data']['spouse']['name_suffix'] ?? null,
                'civil_status' => $data['contact_data']['spouse']['civil_status'] ?? null,
                'sex' => $data['contact_data']['spouse']['sex'] ?? null,
                'nationality' => $data['contact_data']['spouse']['nationality'] ?? null,
                'date_of_birth' => $data['contact_data']['spouse']['date_of_birth'] ?? null,
                'email' => $data['contact_data']['spouse']['email'] ?? null,
                'mobile' => $data['contact_data']['spouse']['mobile'] ?? null,
                'other_mobile' => $data['contact_data']['spouse']['other_mobile'] ?? null,
                'landline' => $data['contact_data']['spouse']['landline'] ?? null,
                'mothers_maiden_name' => $data['contact_data']['spouse']['mothers_maiden_name'] ?? null,
            ];
        }else{
            $contact_attribs['spouse']= [
                'first_name' =>  null,
                'middle_name' =>  null,
                'last_name' =>  null,
                'name_suffix' =>  null,
                'civil_status' =>  null,
                'sex' =>  null,
                'nationality' => null,
                'date_of_birth' => null,
                'email' =>  null,
                'mobile' =>  null,
                'landline' =>  null,
                'mothers_maiden_name' =>  null,
            ];
        }

        $contact->update($data);



        $this->record->save();

        if ($shouldSendSavedNotification) {
            $this->getSavedNotification()?->send();
        }

        if ($shouldRedirect && ($redirectUrl = $this->getRedirectUrl())) {
            $this->redirect($redirectUrl, navigate: false);
        }
    }
}
