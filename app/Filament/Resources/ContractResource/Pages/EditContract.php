<?php

namespace App\Filament\Resources\ContractResource\Pages;

use App\Filament\Resources\ContractResource;
use App\Actions\GenerateContractPayloads;
use App\Models\Payload;
use Filament\Resources\Pages\EditRecord;
use Homeful\Contacts\Enums\Ownership;
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
            $data['desired_property']['monthly_amortization'] = $this->record->mortgage->getLoan()->getMonthlyAmortization()->inclusive()->getAmount()->toFloat()??0;
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
            $new_data['order']['net_loan_proceeds'] = $this->record->mortgage->getBalancePayment()->getAmount()->toFloat()??'';
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


            $inventory = $this->record->inventory;
            $project = $inventory->project ?? null;

            $new_data['order']['sku'] = $inventory->sku ?? '';
            $new_data['order']['phase'] = $inventory->phase ?? '';

            $new_data['order']['block'] = $inventory->block ?? '';
            $new_data['order']['lot'] = $inventory->lot ?? '';
            $new_data['order']['lot_area'] = $inventory->lot_area ?? '';
            $new_data['order']['floor_area'] = $inventory->floor_area ?? '';
            $new_data['order']['property_code'] = $this->record->property_code ?? '';
            $new_data['order']['property_name'] = $inventory->name ?? '';
            $new_data['order']['property_type'] = $inventory->type ?? '';

            $new_data['order']['project_name'] = $project->name ?? '';
            $new_data['order']['project_location'] = $project->location ?? '';
            $new_data['order']['project_address'] = $project->address ?? '';
            $new_data['order']['project_code'] = $project->code ?? '';

            $new_data['order']['unit_type_interior'] = $inventory->unit_type_interior ?? '';
            $new_data['order']['unit_type'] = $inventory->unit_type ?? '';

            $new_data['order']['payment_scheme']['total_contract_price'] = $inventory->tcp ?? '';
        }



        if($this->record->customer->co_borrowers!=null){
            foreach($this->record->customer->co_borrowers->toArray() as $co_borrower){
//                dd($co_borrower,$co_borrower['employment'][0]);
                $co_borrower['address']['primary']= $co_borrower['addresses'][0];
                $co_borrower["coborrower_employment"]=$co_borrower["employment"][0];
                $new_data['co_borrowers'][] = $co_borrower;
            }
        }

        $contact_data['addresses']=$this->record->customer->addresses->toArray();


        $buyer_address_present = collect($contact_data['addresses'])
            ->filter(fn($address) => in_array($address['type'], ['Primary','Present']))
            ->first() ?? [];
        $buyer_address_permanent = collect($contact_data['addresses'])
            ->filter(fn($address) => in_array($address['type'], ['Sencondary','Permanent']))
            ->first() ?? [];

        $contact_data['employment']=$this->record->customer->employment->toArray();

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


        $customer = $this->record->customer;

        $new_data['buyer'] = [
            'first_name' => $customer->first_name ?? '',
            'last_name' => $customer->last_name ?? '',
            'middle_name' => $customer->middle_name ?? '',
            'name_suffix' => $customer->name_suffix ?? '',
            'mothers_maiden_name' => $customer->mothers_maiden_name ?? '',
            'email' => $customer->email ?? '',
            'mobile' => $customer->mobile ?? '',
            'other_mobile' => $customer->other_mobile ?? '',
            'help_number' => $customer->help_number ?? '',
            'landline' => $customer->landline ?? '',
            'civil_status' => $customer->civil_status ?? '',
            'sex' => $customer->sex ?? '',
            'nationality' => $customer->nationality ?? '',
            'date_of_birth' => $customer->date_of_birth ?? '',
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
        $this->form->validate();
        dd('test');
        $contact = Contact::where('id',$this->record->contact_id)->first();

        $this->record->misc_inputs=$this->data['misc']['input'];
        $this->record->property_code = $this->data['property_code'];


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
                    'employment_status' => $data['contact_data']['buyer_employment']['employment_status'] ?? '',
                    'monthly_gross_income' => $data['contact_data']['buyer_employment']['monthly_gross_income'] ?? '',
                    'current_position' => $data['contact_data']['buyer_employment']['current_position'] ?? '',
                    'employment_type' => $data['contact_data']['buyer_employment']['employment_type'] ?? '',
                    'years_in_service' => $data['contact_data']['buyer_employment']['years_in_service'] ?? '',
                    'rank' => $data['contact_data']['buyer_employment']['rank'] ?? '',
                    'industry' => $data['contact_data']['buyer_employment']['industry'] ?? '',
                    'employer' => [
                        'name' => $data['contact_data']['buyer_employment']['employer']['name'] ?? '',
                        'industry' => $data['contact_data']['buyer_employment']['employer']['industry'] ?? '',
                        'nationality' => $data['contact_data']['buyer_employment']['employer']['nationality'] ?? '',
                        'contact_no' => $data['contact_data']['buyer_employment']['employer']['contact_no'] ?? '',
                        'year_established' => $data['contact_data']['buyer_employment']['employer']['year_established'] ?? '',
                        'total_number_of_employees' => $data['contact_data']['buyer_employment']['employer']['total_number_of_employees'] ?? '',
                        'email' => $data['contact_data']['buyer_employment']['employer']['email'] ?? '',
                        'fax' => $data['contact_data']['buyer_employment']['employer']['fax'] ?? '',

                        // Expanding the employer address structure
                        'address' => [
                            'full_address' => $data['contact_data']['buyer_employment']['employer']['address']['full_address'] ?? '',
                            'address1' => $data['contact_data']['buyer_employment']['employer']['address']['address1'] ?? '',
                            'sublocality' => $data['contact_data']['buyer_employment']['employer']['address']['sublocality'] ?? '',
                            'locality' => $data['contact_data']['buyer_employment']['employer']['address']['locality'] ?? '',
                            'administrative_area' => $data['contact_data']['buyer_employment']['employer']['address']['administrative_area'] ?? '',
                            'country' => $data['contact_data']['buyer_employment']['employer']['address']['country'] ?? '',
                            'region' => $data['contact_data']['buyer_employment']['employer']['address']['region'] ?? '',
                            'ownership' =>'Owned',
                            'type'=>'Primary',
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
                    'ownership' => $data['contact_data']['buyer']['address']['present']['ownership'] ?? Ownership::default(),
                    'address1'=>$data['contact_data']['buyer']['address']['present']['address1'] ?? '',
                    'sublocality' => $data['contact_data']['buyer']['address']['present']['sublocality'] ?? '',
                    'locality' => $data['contact_data']['buyer']['address']['present']['locality'] ?? '',
                    'administrative_area' => $data['contact_data']['buyer']['address']['present']['administrative_area'] ?? '',
                    'postal_code' => $data['contact_data']['buyer']['address']['present']['postal_code'] ?? '',
                    'region' => $data['contact_data']['buyer']['address']['present']['region'] ?? '',
                    'country' => $data['contact_data']['buyer']['address']['present']['country'] ?? '',
                ],
                [
                    'type' => 'Secondary',
                    'ownership' => $data['contact_data']['buyer']['address']['permanent']['ownership'] ?? Ownership::default(),
                    'address1'=>$data['contact_data']['buyer']['address']['permanent']['address1'] ?? '',
                    'sublocality' => $data['contact_data']['buyer']['address']['permanent']['sublocality'] ?? '',
                    'locality' => $data['contact_data']['buyer']['address']['permanent']['locality'] ?? '',
                    'administrative_area' => $data['contact_data']['buyer']['address']['permanent']['administrative_area'] ?? '',
                    'postal_code' => $data['contact_data']['buyer']['address']['permanent']['postal_code'] ?? '',
                    'region' => $data['contact_data']['buyer']['address']['permanent']['region'] ?? '',
                    'country' => $data['contact_data']['buyer']['address']['permanent']['country'] ?? '',
                ]
            ],
            'aif'=>$data['contact_data']['aif']??[],
        ];

        if($data['contact_data']['buyer']['civil_status']=='Married'){
            $contact_attribs['spouse']= [
                'first_name' => $data['contact_data']['spouse']['first_name'] ?? '',
                'middle_name' => $data['contact_data']['spouse']['middle_name'] ?? '',
                'last_name' => $data['contact_data']['spouse']['last_name'] ?? '',
                'name_suffix' => $data['contact_data']['spouse']['name_suffix'] ?? '',
                'civil_status' => $data['contact_data']['spouse']['civil_status'] ?? '',
                'sex' => $data['contact_data']['spouse']['sex'] ?? '',
                'nationality' => $data['contact_data']['spouse']['nationality'] ?? '',
                'date_of_birth' => $data['contact_data']['spouse']['date_of_birth'] ?? '',
                'email' => $data['contact_data']['spouse']['email'] ?? '',
                'mobile' => $data['contact_data']['spouse']['mobile'] ?? '',
                'other_mobile' => $data['contact_data']['spouse']['other_mobile'] ?? '',
                'landline' => $data['contact_data']['spouse']['landline'] ?? '',
                'mothers_maiden_name' => $data['contact_data']['spouse']['mothers_maiden_name'] ?? '',
            ];
        }
//        else{
//            $contact_attribs['spouse']= [
//                'first_name' =>  '',
//                'middle_name' => '',
//                'last_name' =>  '',
//                'name_suffix' => '',
//                'civil_status' => '',
//                'sex' =>  '',
//                'nationality' => '',
//                'date_of_birth' =>'',
//                'email' =>  '',
//                'mobile' =>  '',
//                'landline' =>  '',
//                'mothers_maiden_name' =>  '',
//            ];
//        }

        if ($data['contact_data']['co_borrowers']) {
            $cobo_data=[];
            foreach ($data['contact_data']['co_borrowers'] as $coborrower) {
                $address = $coborrower['address'];
                $employment = $coborrower['coborrower_employment'];

                $coborrower['addresses'][0] = $address;
                $coborrower['employment'][0] = $employment;
                $cobo_data[] = $coborrower;
            }
            $contact_attribs['co_borrowers'] = $cobo_data;
        }

        $contact->update($contact_attribs);
        $this->record->save();




        if ($shouldSendSavedNotification) {
            $this->getSavedNotification()?->send();
        }

        if ($shouldRedirect && ($redirectUrl = $this->getRedirectUrl())) {
            $this->redirect($redirectUrl, navigate: false);
        }
    }
}
