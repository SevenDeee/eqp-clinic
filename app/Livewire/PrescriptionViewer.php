<?php

namespace App\Livewire;

use Livewire\Component;

class PrescriptionViewer extends Component
{
    public $record;
    public $prescribedDate;
    public $prescriptions;
    public $prescriptionsForDropdown;

    public function mount($record)
    {
        // No need to query again - use the already-loaded relationships
        $this->record = $record;

        // Use the already-loaded and sorted prescriptions
        $this->prescriptionsForDropdown = $this->record->prescriptions;

        // Set initial selected prescription
        $this->prescribedDate = $this->prescriptionsForDropdown->first()?->id;

        $this->loadPrescription();
    }

    public function updatedPrescribedDate()
    {
        $this->loadPrescription();
    }

    protected function loadPrescription()
    {
        // Use the already-loaded collection instead of querying again
        $this->prescriptions = $this->prescriptionsForDropdown
            ->where('id', $this->prescribedDate)
            ->values();
    }

    public function render()
    {
        return view('livewire.prescription-viewer');
    }
}