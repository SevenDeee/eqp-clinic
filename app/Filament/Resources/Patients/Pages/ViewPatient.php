<?php

namespace App\Filament\Resources\Patients\Pages;

use App\Action\UpdatePatientActions;
use App\Filament\Resources\Patients\PatientResource;
use App\Filament\Resources\Prescriptions\Schemas\PrescriptionForm;
use App\Models\Patient;
use App\Models\Prescription;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewPatient extends ViewRecord
{
    protected static string $resource = PatientResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        // Load the count first
        $this->record->loadCount('prescriptions');

        // Store patient data in session
        session([
            'prescriptions_count' => $this->record->prescriptions_count
        ]);
    }

    public function unmount(): void
    {
        // Clear the session when leaving the view page
        session()->forget('prescriptions_count');

        parent::unmount();
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('add_prescription')
                    ->label('Add Prescription')
                    ->icon('heroicon-o-plus-circle')
                    ->color(null)
                    ->modalHeading('Add Prescription for ' . $this->record->name)
                    ->modalWidth('3xl')
                    ->form(fn() => \App\Filament\Forms\PrescriptionForm::configure(\Filament\Schemas\Schema::make())->getComponents())
                    ->action(function (array $data) {
                        Prescription::create([
                            ...$data,
                            'patient_id' => $this->record->id,
                            'prescribed_by' => auth()->id(),
                        ]);

                        // Reload the record
                        $this->record->refresh();
                        $this->record->loadCount('prescriptions');
                        session([
                            'prescriptions_count' => $this->record->prescriptions_count
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Prescription Added')
                            ->body('Prescription has been successfully added.')
                            ->send();

                        // Refresh the page
                        $this->redirect(static::getResource()::getUrl('view', ['record' => $this->record]));
                    }),
                Action::make('print')
                    ->label('Print Patient Info')
                    ->color(null)
                    ->icon(Heroicon::Printer)
                    ->requiresConfirmation()
                    ->hidden(fn() => session('prescriptions_count') === 0)
                    ->modalHeading("Printing Patient Info")
                    ->modalDescription('Please select which date of prescription to include.')
                    ->schema([
                        Select::make('prescription_date')
                            ->hiddenLabel()
                            ->native(false)
                            ->options(
                                Prescription::query()
                                    ->where('patient_id', $this->record->id)
                                    ->orderBy('created_at', 'desc')
                                    ->get()
                                    ->pluck('created_at', 'id')
                                    ->map(function ($date) {
                                        return \Carbon\Carbon::parse($date)->format('F j, Y');
                                    })
                                    ->toArray(),
                            )
                            ->required(),
                    ])
                    ->action(function ($record, array $data): void {
                        $url = route('patient-info', [
                            'patient' => $record->id,
                            'prescription' => $data['prescription_date'],
                        ]);

                        $this->js("window.open('{$url}', '_blank');");
                    }),
                Action::make('Archive')
                    ->after(fn() => redirect()->to('/patients'))
                    ->action(function (Patient $record) {
                        $record->update([
                            'archived_at' => now(),
                        ]);
                        // ActivityLogActions::archiveProduct($record->name);
                        Notification::make()
                            ->title('Patient Archived')
                            ->body("{$record->name} has been successfully archived.")
                            ->success()
                            ->send();
                    })
                    ->color(null)
                    ->icon(Heroicon::ArchiveBox)
                    ->requiresConfirmation(),
                // EditAction::make()
                //     ->color('gray'),

                ActionGroup::make(UpdatePatientActions::all())
                    ->label('Update Information')
                    ->icon(Heroicon::EllipsisVertical)
                    ->color('gray'),
            ])->icon(Heroicon::EllipsisHorizontal)
            // ->dropdownPlacement('left')
            ,
        ];
    }
}
