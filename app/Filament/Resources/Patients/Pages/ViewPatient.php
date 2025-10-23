<?php

namespace App\Filament\Resources\Patients\Pages;

use App\Filament\Resources\Patients\PatientResource;
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

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('print')
                    ->label('Print Patient Info')
                    ->color('success')
                    ->icon(Heroicon::Printer)
                    ->requiresConfirmation()
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
                    ->color('info')
                    ->icon(Heroicon::ArchiveBox)
                    ->requiresConfirmation(),
                EditAction::make(),
            ]),
        ];
    }
}
