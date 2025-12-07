<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                ImageEntry::make('banner')
                    ->state(asset('images/image.png'))
                    ->hiddenLabel()
                    ->extraImgAttributes([
                        'style' => 'border-radius: 12px;',
                    ])
                    // ->size(240)
                    ->height(230)
                    ->width(320),

                Group::make()->schema([
                    TextInput::make('name')
                        ->required(),
                    TextInput::make('email')
                        ->label('Email address')
                        ->email()
                        ->required(),
                    TextInput::make('password')
                        ->revealable()
                        ->password()
                        ->required(),

                ]),
            ])->columns(2);
    }
}
