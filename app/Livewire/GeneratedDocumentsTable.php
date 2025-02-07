<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class GeneratedDocumentsTable extends Component
{
    public $record;
    public array $generatedDocuments=[];

    public array $sets=[];
    public string $selected_set;

    public function mount(Model $record){

        $response = Http::post('https://merge.homeful.ph/api/sets');
        if ($response->status() === 201 || $response->status() === 200) {
            $this->sets=$response->json()??[];
        }


//        $this->generatedDocuments=[
//            [
//                "name" => "Unilateral Deed Of Absolute Sale",
//                "url" => "https://merge.homeful.ph/storage/2089/unilateral-deed-of-absolute-sale.pdf"
//            ],
//            [
//                "name" => "Buyer's Conformity",
//                "url" => "https://merge.homeful.ph/storage/2091/buyer's-conformity.pdf"
//            ],
//            [
//                "name" => "Disclosure Statement On Loan Transaction No Cobo End User Affordable",
//                "url" => "https://merge.homeful.ph/storage/2093/disclosure-statement-on-loan-transaction-no-cobo-end-user-affordable.pdf"
//            ],
//            [
//                "name" => "Declaration Of Restrictions  Solar Project",
//                "url" => "https://merge.homeful.ph/storage/2095/declaration-of-restrictions--solar-project.pdf"
//            ],
//            [
//                "name" => "Solaris Voluntary Surrender And Waiver",
//                "url" => "https://merge.homeful.ph/storage/2097/solaris-voluntary-surrender-and-waiver.pdf"
//            ],
//            [
//                "name" => "Certificate Of Acceptance",
//                "url" => "https://merge.homeful.ph/storage/2099/certificate-of-acceptance.pdf"
//            ],
//            [
//                "name" => "Certificate Of House & Lot Acceptance Completion",
//                "url" => "https://merge.homeful.ph/storage/2101/certificate-of-house-&-lot-acceptance-completion.pdf"
//            ],
//            [
//                "name" => "Deed Of Conditional Sale",
//                "url" => "https://merge.homeful.ph/storage/2103/deed-of-conditional-sale.pdf"
//            ],
//            [
//                "name" => "Promissory Note",
//                "url" => "https://merge.homeful.ph/storage/2105/promissory-note.pdf"
//            ],
//            [
//                "name" => "Solaris Affidavit Of Consent",
//                "url" => "https://merge.homeful.ph/storage/2107/solaris-affidavit-of-consent.pdf"
//            ],
//            [
//                "name" => "Solaris Contract To Sell",
//                "url" => "https://merge.homeful.ph/storage/2109/solaris-contract-to-sell.pdf"
//            ],
//            [
//                "name" => "Solaris Usufruct Agreement",
//                "url" => "https://merge.homeful.ph/storage/2111/solaris-usufruct-agreement.pdf"
//            ]
//        ];
//        dd($this->generatedDocuments);
    }

    public function getGeneratedDocuments(){
        $response = Http::timeout(120) // Set timeout to 120 seconds
        ->retry(3, 5000) // Retry up to 3 times, with a 5-second delay
        ->post('https://merge.homeful.ph/api/folder-documents/'.$this->selected_set, [
            'code' => $this->selected_set,
            'data' => [
                "buyer_name" => "Renzo"
            ],
        ]);

        if ($response->status() === 201 || $response->status() === 200) {
            $this->generatedDocuments=$response->json()['documents']??[];
        }
    }

    public function downloadFile($url)
    {
        // Fetch the file content
        $response = Http::get($url);

        if ($response->failed()) {
            abort(404, 'File not found.');
        }

        // Get the original filename from the URL
        $filename = basename(parse_url($url, PHP_URL_PATH));

        // Return a download response
        return response()->streamDownload(function () use ($response) {
            echo $response->body();
        }, $filename);
    }

    public function render()
    {
        return view('livewire.generated-documents-table');
    }
}
