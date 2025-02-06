<?php

namespace App\Livewire;


use App\Models\RequirementMatrix;
use Filament\Forms\Components\FileUpload;
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

    #[Validate('max:1024')]
    public $document;

    public function mount(Model $record)
    {
        $requirements = RequirementMatrix::first();
        $this->requirements = collect(json_decode($requirements->requirements, true))
        ->sort()
        ->values();
        $this->record = $record;
        $this->chosenFile = "";
        $contact = $this->record->customer;
        // dd($contact->photo4x1WhiteBackground);
    }
    public function render()
    {
        return view('livewire.requirements-table');
    }

    public function chooseFile($file_desc)
    {
        $this->chosenFile = $file_desc;
    }

    public function uploadDoc(){
        if($this->chosenFile == "4 pcs. of 1x1 Photo with White Background")
        {
            $customer = $this->record->customer;
            $uploader_label = $this->getUploaderName($this->chosenFile);
            dd($customer->$uploader_label->getUrl());
        }
    }

    public function viewImage($name){
        $this->dispatch('openNewTab', 'https://www.google.com');
    }

    public function getUploaderName($name){
        switch ($name) {
            case '4 pcs. of 1x1 Photo with White Background':
                return 'photoImage';
                break;
        }
    }
}
