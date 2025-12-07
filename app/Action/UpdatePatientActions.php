<?php

namespace App\Action;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Enums\Width;

class UpdatePatientActions
{
    public static function personalDetails(): Action
    {
        return self::saveAction(
            Action::make('edit_personal')
                ->label('Personal Details')
                ->modalHeading('Update Personal Details')
                ->icon('heroicon-s-user')
                ->fillForm(fn($record) => [
                    'name' => $record->name,
                    'address' => $record->address,
                    'contact_number' => $record->contact_number,
                    'age' => $record->age,
                    'sex' => $record->sex,
                ])
                ->form([
                    Wizard::make([
                        Step::make('Personal Details')
                            ->icon('heroicon-s-user')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        TextInput::make('name')
                                            ->required(),
                                        TextInput::make('contact_number')
                                            ->prefix('+63')
                                            ->mask('9999999999')
                                            ->required(),
                                        TextInput::make('age')
                                            ->numeric()
                                            ->required(),
                                        Select::make('sex')
                                            ->options(['Male' => 'Male', 'Female' => 'Female', 'Other' => 'Other'])
                                            ->native(false)
                                            ->required(),
                                        Textarea::make('address')
                                            ->required()
                                            ->columnSpanFull()
                                            ->rows(3)
                                            ->autosize(),
                                    ])->columns(2),
                            ]),
                    ])->columnSpanFull()
                        ->contained(false),
                ]),
            'Personal Details Updated',
            'Patient personal information has been updated successfully.'
        );
    }

    public static function followUpSchedule(): Action
    {
        return self::saveAction(
            Action::make('edit_schedule')
                ->label('Follow-up Schedule')
                ->modalHeading('Update Follow-up Schedule')
                ->modalWidth(Width::ExtraLarge)
                ->hidden(function ($record) {
                    if ($record->follow_up_on === null) {
                        return false; // Hide if null
                    }

                    $follow_up_on = Carbon::parse($record->follow_up_on)->startOfDay();

                    return $follow_up_on->isFuture(); // Hide if future
                })
                ->icon('heroicon-s-calendar')
                ->fillForm(fn($record) => [
                    'follow_up_on' => $record->follow_up_on,
                ])
                ->form([
                    Wizard::make([
                        Step::make('Follow-up Schedule')
                            ->icon('heroicon-s-calendar')
                            ->schema([
                                DatePicker::make('follow_up_on')
                                    ->displayFormat('F d, Y')
                                    ->closeOnDateSelection()
                                    ->minDate(now())
                                    ->required()
                                    ->hiddenLabel()
                                    ->disabledDates(function () {
                                        $disabledDates = [];
                                        $start = now()->startOfYear();
                                        $end = now()->addYears(2)->endOfYear();

                                        for ($date = $start; $date <= $end; $date->addDay()) {
                                            if ($date->isSunday()) {
                                                $disabledDates[] = $date->format('Y-m-d');
                                            }
                                        }

                                        // dd($disabledDates);
                                        return $disabledDates;
                                    })
                                    ->suffixIcon('heroicon-s-calendar')
                                    ->native(false),
                            ]),
                    ])->columnSpanFull()
                        ->contained(false),
                ]),
            'Billing Information Updated',
            'Patient billing information has been updated successfully.'
        );
    }

    public static function clinicalAssessment(): Action
    {
        return self::saveAction(
            Action::make('edit_clinical')
                ->label('Clinical Assessment')
                ->modalHeading('Update Clinical Assessment')
                ->icon('heroicon-s-document-text')
                ->fillForm(fn($record) => [
                    'diagnosis' => $record->diagnosis,
                    'special_instructions' => $record->special_instructions,
                    'follow_up_on' => $record->follow_up_on,
                ])
                ->form([
                    Wizard::make([
                        Step::make('Clinical Assessment')
                            ->icon('heroicon-s-document-text')
                            ->schema([
                                Textarea::make('diagnosis')
                                    ->label('Diagnosis')
                                    ->rows(3)
                                    ->autosize()
                                    ->maxLength(500),
                                Textarea::make('special_instructions')
                                    ->label('Special Instructions')
                                    ->rows(3)
                                    ->autosize()
                                    ->maxLength(500),
                                // DatePicker::make('follow_up_on')
                                //     ->label('Follow-up Date')
                                //     ->native(false),
                            ]),
                    ])->columnSpanFull()
                        ->contained(false),
                ]),
            'Clinical Assessment Updated',
            'Patient clinical assessment has been updated successfully.'
        );
    }

    public static function opticalSpecs(): Action
    {
        return self::saveAction(
            Action::make('edit_optical')
                ->label('Specifications & Billing')
                ->modalHeading('Update Optical Specs & Billing Info')
                ->modalWidth(Width::FiveExtraLarge)
                ->icon('heroicon-s-eye')
                ->fillForm(fn($record) => [
                    'frame_type' => $record->frame_type,
                    'color' => $record->color,
                    'lens_supply' => $record->lens_supply,
                    'amount' => $record->amount,
                    'deposit' => $record->deposit,
                    'balance' => $record->balance,
                ])
                ->form([

                    Wizard::make([
                        Step::make('Optical Specifications & Billing Information')
                            ->icon('heroicon-s-eye')
                            ->columns(3)
                            ->schema([

                                Section::make()
                                    ->columnSpan(2)
                                    ->schema([
                                        Select::make('frame_type')
                                            ->label('Frame Type')
                                            ->options([
                                                'Metal' => 'Metal',
                                                'Plastic' => 'Plastic',
                                                'Rimless' => 'Rimless',
                                            ])
                                            ->native(false),
                                        TextInput::make('color')
                                            ->label('Frame Color'),
                                        TextInput::make('lens_supply')
                                            ->label('Lens Supply')
                                            ->maxLength(255),
                                        // Select::make('lens_coating')
                                        //     ->label('Lens Coating')
                                        //     ->options([
                                        //         'Metal' => 'Metal',
                                        //         'Plastic' => 'Plastic',
                                        //         'Rimless' => 'Rimless',
                                        //     ])
                                        //     ->native(false),
                                    ]),


                                Section::make()
                                    ->inlineLabel()
                                    ->schema([
                                        TextInput::make('amount')
                                            ->hiddenLabel()
                                            ->beforeLabel('Total Amount')
                                            ->numeric()
                                            ->prefix('₱')
                                            ->required(),
                                        TextInput::make('deposit')
                                            ->hiddenLabel()
                                            ->beforeLabel('Deposit')
                                            ->numeric()
                                            ->prefix('₱')
                                            ->required(),
                                        TextInput::make('balance')
                                            ->hiddenLabel()
                                            ->beforeLabel('Balance')
                                            ->numeric()
                                            ->prefix('₱')
                                            ->disabled()
                                            ->dehydrated(false)
                                    ]),

                            ]),
                    ])->columnSpanFull()
                        ->contained(false),
                ]),
            'Optical Specifications Updated',
            'Patient optical specifications have been updated successfully.'
        );
    }


    protected static function saveAction(
        Action $action,
        string $notificationTitle = 'Updated Successfully',
        string $notificationBody = 'The record has been updated successfully.'
    ): Action {
        return $action
            ->modalSubmitActionLabel('Save Changes')
            ->action(function (array $data, $record) use ($notificationTitle, $notificationBody) {

                // dd($data);
                $record->update($data);

                Notification::make()
                    ->success()
                    ->title($notificationTitle)
                    ->body($notificationBody)
                    ->send();
            });
    }

    /**
     * Get all update actions as an array
     */
    public static function all(): array
    {
        return [
            self::personalDetails(),
            self::followUpSchedule(),
            self::clinicalAssessment(),
            self::opticalSpecs(),
        ];
    }
}
