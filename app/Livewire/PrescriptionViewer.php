<?php

namespace App\Livewire;

use App\Models\Prescription;
use Livewire\Component;

class PrescriptionViewer extends Component
{
    public $record;
    public $prescriptions = [];

    public $prescribedDate;

    public function updatedPrescribedDate()
    {
        $this->prescriptions = Prescription::query()->where('id', $this->prescribedDate)->get();
    }

    public function mount($record)
    {
        $this->record = $record;
        $this->prescribedDate = $record->prescriptions->sortByDesc('created_at')->first()?->id; // or any default value

        $this->prescriptions = Prescription::query()->where('id', $this->prescribedDate)->get();
    }


    public function render()
    {
        return view('livewire.prescription-viewer');
    }
}
