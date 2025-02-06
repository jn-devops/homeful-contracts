<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class GeneratedDocumentsTable extends Component
{
    public $record;
    public $generatedDocuments=[];

    public function mount(Model $record){
        $response = Http::post('https://merge.homeful.ph/api/folder-documents/RRC-SHDG-S', [
            'code' => 'RRC-SHDG-S-2',
            'data' => [
                "buyer_name"=>"Renzo"
            ],
        ]);
//        dd($response->json(), $response);
        if ($response->status() === 200) {
            $this->generatedDocuments=$response->json()['generatedFiles']??[];
        }
    }
    public function render()
    {
        return view('livewire.generated-documents-table');
    }
}
