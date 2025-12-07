<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Actions\Action;
use Filament\Actions\ButtonAction;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
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
                    ->height(200)
                    ->width(280),

                Group::make()->schema([

                    Section::make()->schema([

                        Group::make()->schema([

                            TextEntry::make('name'),
                            TextEntry::make('email')
                                ->label('Email @'),
                            TextEntry::make('created_at')
                                ->datetime('M d, Y h:i A'),
                        ])->inlineLabel(),
                    ]),

                    Action::make('promote')
                        ->label('Promote to Admin')
                        ->button()
                        ->color('primary')
                        ->action(function () {
                            dd('test');
                        })
                        ->extraAttributes(['class' => 'w-full']),
                ])

            ])->columns(2);
    }
}
