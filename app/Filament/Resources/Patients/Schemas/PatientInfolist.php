<?php

namespace App\Filament\Resources\Patients\Schemas;

use App\Filament\Resources\Patients\PatientResource;
use Filament\Actions\Action;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;

class PatientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(4)
            ->components([

                Section::make()
                    ->columnSpan(3)
                    ->columns(2)
                    ->schema([

                        self::infoEntry('name', 'Name'),
                        self::infoEntry('address', 'Address'),
                        self::infoEntry('contact_number', 'Contact Number'),
                        self::infoEntry('age', 'Age'),
                        self::infoEntry('sex', 'Sex'),
                        self::infoEntry('diagnosis', 'Diagnosis'),
                        self::infoEntry('special_instructions', 'Special Instructions'),
                        self::infoEntry('follow_up_on', 'Follow up on')->date(),
                        self::infoEntry('creator.name', 'Created By')->prefix('Dr. '),
                        self::infoEntry('created_at', 'created_at')->date(),
                    ]),


                Group::make()
                    ->inlineLabel()
                    ->columnSpan(1)
                    ->schema([

                        Section::make()
                            ->schema([
                                self::textEntry('frame_type', 'Frame Type'),
                                ColorEntry::make('color')->beforeContent(fn($record) => $record->color)
                                    ->beforeLabel('Color')
                                    ->hiddenLabel(),
                                self::textEntry('lens_supply', 'Lens Supply'),
                            ]),

                        Section::make()
                            ->schema([
                                self::textEntry('amount', 'Amount')->money('php')->color('info'),
                                self::textEntry('deposit', 'Deposit')->money('php')->color('success'),
                                self::textEntry('balance', 'Balance')->money('php')->color('danger'),

                                Actions::make([
                                    Action::make('Back')
                                        ->extraAttributes(['class' => 'w-full'])
                                        ->url(PatientResource::getUrl('index')),
                                ])
                            ])
                    ]),

                Section::make('Prescriptions')
                    ->columnSpan(3)
                    ->schema([
                        View::make('components.prescription'),

                    ]),
            ]);
    }

    public static function textEntry($column, $label)
    {
        return TextEntry::make($column)
            ->size(TextSize::Large)
            ->beforeLabel($label)
            ->hiddenLabel()
            ->placeholder('N/A');
    }
    public static function infoEntry($column, $label)
    {
        return TextEntry::make($column)
            ->size(TextSize::Large)
            ->hiddenLabel()
            ->aboveContent($label)
            ->placeholder('N/A');
    }
}
