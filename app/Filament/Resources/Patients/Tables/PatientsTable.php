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
            ->modifyQueryUsing(function (Builder $query) {
                // Eager load prescriptions relationship with count to avoid N+1 queries
                $query->withCount('prescriptions');
            })
            ->columns([
                TextColumn::make('name')->size(TextSize::Large)->searchable(),
                TextColumn::make('age')->searchable(),
                TextColumn::make('sex'),
                TextColumn::make('contact_number')->searchable(),
                TextColumn::make('address')
                    ->limit(20)
                    ->tooltip(fn(TextColumn $column): ?string => $column->getState()),
                // Use the eager-loaded count instead of querying on each row
                TextColumn::make('prescriptions_count')
                    ->label('Prescription/s')
                    ->counts('prescriptions'),
                TextColumn::make('follow_up_on')
                    ->placeholder('N/A')
                    ->date()
                    ->color(function ($state) {

                        $date = \Carbon\Carbon::parse($state);

                        if ($date->isToday())
                            return 'success';
                        if ($date->isPast())
                            return 'danger';

                        return null; // Future dates
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Date Added')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->datetime('M d, Y h:i A')
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([
                ActionGroup::make([
                    Action::make('Archive')
                        ->hidden(fn($record) => !is_null($record->archived_at))
                        ->action(function (Patient $record) {
                            $record->update([
                                'archived_at' => now(),
                            ]);

                            Notification::make()
                                ->title('Patient Archived')
                                ->body("{$record->name} has been successfully archived.")
                                ->success()
                                ->send();
                        })
                        ->color('info')
                        ->icon(Heroicon::ArchiveBox)
                        ->requiresConfirmation()
                        ->successRedirectUrl('/patients'),
                    Action::make('Restore')
                        ->hidden(fn($record) => is_null($record->archived_at))
                        ->action(function (Patient $record) {
                            $record->update([
                                'archived_at' => null,
                            ]);

                            Notification::make()
                                ->title('Patient Restored')
                                ->body("{$record->name} has been successfully restored.")
                                ->success()
                                ->send();
                        })
                        ->color('info')
                        ->icon(Heroicon::ArchiveBox)
                        ->requiresConfirmation()
                        ->successRedirectUrl('/patients'),
                    ViewAction::make(),
                    // EditAction::make(),
                ])
            ])
            ->toolbarActions([]);
    }
}