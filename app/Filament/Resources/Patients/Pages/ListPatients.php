<?php

namespace App\Filament\Resources\Patients\Pages;

use App\Filament\Resources\Patients\PatientResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListPatients extends ListRecords
{
    protected static string $resource = PatientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->createAnother(false),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Active' => Tab::make()->query(fn($query) => $query->whereNull('archived_at')->whereNull('deleted_at')),
            'Archived' => Tab::make()->query(fn($query) => $query->whereNotNull('archived_at')->whereNull('deleted_at')),
        ];
    }
}
