<?php

namespace App\Livewire;


use App\Models\RequirementMatrix;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class RequirementsTable extends Component
{
    public $requirements=[];
    public $record;
    public function mount(Model $record)
    {
        $requirements = RequirementMatrix::first();
        $this->requirements = collect(json_decode($requirements->requirements, true))
        ->sort()
        ->values();
        $this->record = $record;
    }
    public function render()
    {
        return view('livewire.requirements-table');
    }
}
