<?php

namespace App\Filament\Resources\Patients\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PatientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('address')
                    ->required(),
                TextInput::make('contact_number')
                    ->required(),
                TextInput::make('age')
                    ->required(),
                TextInput::make('sex')
                    ->required(),
                TextInput::make('frame_type')
                    ->required()
                    ->default('N/A'),
                TextInput::make('color')
                    ->required()
                    ->default('N/A'),
                TextInput::make('lens_supply')
                    ->required()
                    ->default('N/A'),
                TextInput::make('diagnosis')
                    ->required()
                    ->default('N/A'),
                TextInput::make('special_instructions')
                    ->required()
                    ->default('N/A'),
                DateTimePicker::make('follow_up_on'),
                TextInput::make('amount')
                    ->numeric(),
                TextInput::make('deposit')
                    ->numeric(),
                TextInput::make('balance')
                    ->numeric(),
                TextInput::make('created_by')
                    ->numeric(),
                DateTimePicker::make('archived_at'),
            ]);
    }
}
