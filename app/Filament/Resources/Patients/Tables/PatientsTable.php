<?php

namespace App\Filament\Resources\Patients\Tables;

use App\Models\Patient;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PatientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // ->modifyQueryUsing(fn(Builder $query) => $query->whereNull('archived_at'))
            ->columns([
                TextColumn::make('name')->size(TextSize::Large)->searchable(),
                TextColumn::make('age')->searchable(),
                TextColumn::make('sex'),
                TextColumn::make('contact_number')->searchable(),
                TextColumn::make('address')->limit(20)->tooltip(fn(TextColumn $column): ?string => $column->getState()),
                TextColumn::make('balance')->money('php'),
                TextColumn::make('follow_up_on')->placeholder('N/A')
                    ->date()
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([
                ActionGroup::make([
                    Action::make('Archive')
                        ->hidden(fn($record) => !is_null($record->archived_at))
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
                    Action::make('Restore')
                        ->hidden(fn($record) => is_null($record->archived_at))
                        ->after(fn() => redirect()->to('/patients'))
                        ->action(function (Patient $record) {
                            $record->update([
                                'archived_at' => null,
                            ]);
                            // ActivityLogActions::archiveProduct($record->name);
                            Notification::make()
                                ->title('Patient Restored')
                                ->body("{$record->name} has been successfully restored.")
                                ->success()
                                ->send();
                        })
                        ->color('info')
                        ->icon(Heroicon::ArchiveBox)
                        ->requiresConfirmation(),
                    ViewAction::make(),
                    EditAction::make(),
                ])
            ])
            ->toolbarActions([]);
    }
}
