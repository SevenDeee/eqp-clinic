<?php

namespace App\Filament\Resources\Patients\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PatientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            // ->columns(4)
            ->components([

                // Group::make()
                //     ->columnSpan(3)
                //     ->schema([


                Wizard::make([
                    Step::make('Personal Details')
                        ->icon(Heroicon::UserPlus)
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
                    ->contained(false)
                //         Section::make()
                //             ->compact()
                //             ->description('Clinical Assessment')
                //             ->columnSpanFull()
                //             ->schema([
                //                 Textarea::make('diagnosis')
                //                     ->rows(6)
                //                     ->autosize(),
                //                 Textarea::make('special_instructions')
                //                     ->rows(6)
                //                     ->autosize(),
                //                 // DatePicker::make('follow_up_on')
                //                 //     ->prefixIcon(Heroicon::CalendarDateRange)
                //                 //     ->native(false),
                //             ])->columns(2),
                //     ]),

                // Group::make()
                //     ->schema([

                //         Section::make()
                //             ->description('Optical Specifications')
                //             ->columnSpanFull()
                //             ->schema([
                //                 TextInput::make('frame_type'),
                //                 TextInput::make('color'),
                //                 TextInput::make('lens_supply'),
                //             ]),

                //         Section::make()
                //             ->description('Billing Information')
                //             // ->columnStart(4)
                //             ->schema([
                //                 TextInput::make('amount')
                //                     ->prefix('₱')
                //                     ->numeric(),
                //                 TextInput::make('deposit')
                //                     ->prefix('₱')
                //                     ->numeric(),
                //                 TextInput::make('balance')
                //                     ->prefix('₱')
                //                     ->numeric(),
                //                 TextInput::make('created_by')
                //                     ->default(fn() => auth()->id())
                //                     ->dehydrated()
                //                     ->visible(false),
                //             ])->columns(1),


                //     ]),
            ]);
    }
}
