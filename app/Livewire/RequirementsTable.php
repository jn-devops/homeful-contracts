<?php

namespace App\Livewire;


use App\Models\RequirementMatrix;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Homeful\Contacts\Models\Customer as Contact;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;

class RequirementsTable extends Component
{
    use WithFileUploads;

    public $requirements=[];
    public $record;
    public $chosenFile;
    public $currentReq;

    #[Validate('max:1024')]
    public $document;

    public function mount(Model $record)
    {
        $this->record = $record;
        $contact = Contact::where('id', $record->contact_id)->first()->getData()->toArray();
        $employment_status = collect($contact['employment'])->firstWhere('type','Primary')['employment_type'];
        $this->chosenFile = "";
        $requirements = RequirementMatrix::first();
    $requirements = RequirementMatrix::where('civil_status',$contact['civil_status'])->where('employment_status',$employment_status)->first();
        $reqs = collect(json_decode($requirements->requirements, true))
        ->sort()
        ->values();
        $this->requirements = $reqs->map(function($requirement) {
            $uploader_label = $this->getUploaderName($requirement);
            return [
                'description' => $requirement,
                'status' => ($this->record->customer->$uploader_label !== null) ? 'Uploaded' : 'Pending',
            ];
        });
    }
    public function render()
    {
        return view('livewire.requirements-table');
    }

    public function chooseFile($file_desc)
    {
        $this->chosenFile = $file_desc;
    }

    public function uploadDoc($chosenFile){
        if($chosenFile)
        {
            $customer = $this->record->customer;
            $uploader_label = $this->getUploaderName($chosenFile);
            try {
                $customer->$uploader_label = $this->document->temporaryUrl();
                Notification::make()
                    ->success()
                    ->title('Saved successfully')
                    ->send();
            } catch (\Exception $e) {
                Notification::make()
                    ->danger()
                    ->title('File Upload Unsuccessful')
                    ->body($e->getMessage())
                    ->send();
            }

        }
    }

    public function viewImage($name){
        $customer = $this->record->customer;
        $uploader_label = $this->getUploaderName($name);
        try {
            if ($customer->$uploader_label !== null) {
                $url = $customer->$uploader_label->getUrl();
                $this->dispatch('openNewTab', $url);
            } else {
                throw new \Exception('There is no file found');
            }
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error Encountered')
                ->body($e->getMessage())
                ->send();
        }
    }

    public function getUploaderName($name){
        switch ($name) {
            case 'Government ID 1 Image':
                return 'governmentId1Image';
                break;
            case 'Government ID 2 Image':
                return 'governmentId2Image';
                break;
            case 'Certificate of Employment Document':
                return 'certificateOfEmploymentDocument';
                break;
            case 'One Month Latest Payslip Document':
                return 'oneMonthLatestPayslipDocument';
                break;
            case 'ESAV Document':
                return 'esavDocument';
                break;
            case 'Birth Certificate Document':
                return 'birthCertificateDocument';
                break;
            case 'Photo Image':
                return 'photoImage';
                break;
            case 'Proof of Billing Address Document':
                return 'proofOfBillingAddressDocument';
                break;
            case 'Letter of Consent Employer Document':
                return 'letterOfConsentEmployerDocument';
                break;
            case 'Three Months Certified Payslips Document':
                return 'threeMonthsCertifiedPayslipsDocument';
                break;
            case 'Employment Contract Document':
                return 'employmentContractDocument';
                break;
            case 'OFW Employment Certificate Document':
                return 'ofwEmploymentCertificateDocument';
                break;
            case 'Passport With Visa Image':
                return 'passportWithVisaImage';
                break;
            case 'Working Permit Document':
                return 'workingPermitDocument';
                break;
            case 'Notarized SPA Document':
                return 'notarizedSpaDocument';
                break;
            case 'Authorized Representative Info Sheet Document':
                return 'authorizedRepInfoSheetDocument';
                break;
            case 'Valid ID of AIF Image':
                return 'validIdAifImage';
                break;
            case 'Working Permit Card Document':
                return 'workingPermitCardDocument';
                break;
            case 'ITR BIR 1701 Document':
                return 'itrBir1701Document';
                break;
            case 'Audited Financial Statement Document':
                return 'auditedFinancialStatementDocument';
                break;
            case 'Official Receipt Tax Payment Document':
                return 'officialReceiptTaxPaymentDocument';
                break;
            case 'Business Mayor\'s Permit Document':
                return 'businessMayorsPermitDocument';
                break;
            case 'DTI Business Registration Document':
                return 'dtiBusinessRegistrationDocument';
                break;
            case 'Sketch of Business Location Document':
                return 'sketchOfBusinessLocationDocument';
                break;
            case 'Letter of Consent Credit Background Investigation Document':
                return 'letterOfConsentCreditBackgroundInvestigationDocument';
                break;
            case 'Marriage Certificate Document':
                return 'marriageCertificateDocument';
                break;
            case 'Government ID of Spouse Image':
                return 'governmentIdOfSpouseImage';
                break;
            case 'Court Decision Annulment Document':
                return 'courtDecisionAnnulmentDocument';
                break;
            case 'Marraige Contract Document':
                return 'marraigeContractDocument';
                break;
            case 'Death Certificate Document':
                return 'deathCertificateDocument';
                break;
        }
    }
}
