<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PrescriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Rx:')
                ->schema([
                    self::headerRow(),

                    self::visionRow('FAR', 'od', 'FAR - OD'),
                    self::visionRow('FAR', 'os', 'FAR - OS'),

                    self::visionRow('NEAR', 'od', 'NEAR - OD'),
                    self::visionRow('NEAR', 'os', 'NEAR - OS'),

                    self::remarksRow(),
                ])
                ->columns(1),
        ]);
    }

    /** ---------------------------------------
     *  HEADER ROW
     * --------------------------------------*/
    protected static function headerRow(): Grid
    {
        $headers = [
            '',
            'SPHERE',
            'CYLINDER',
            'AXIS',
            'MonoPD',
        ];

        return Grid::make(5)->schema(
            array_map(function ($label) {
                return Placeholder::make('header_' . strtolower($label ?: 'eye'))
                    ->hiddenLabel()
                    ->content($label)
                    ->extraAttributes(['class' => 'font-bold text-center']);
            }, $headers)
        );
    }

    /** ---------------------------------------
     *  FAR / NEAR rows
     * --------------------------------------*/
    protected static function visionRow(string $type, string $eye, string $label): Grid
    {
        $prefix = strtolower($type) . '.' . strtolower($eye);

        return Grid::make(5)->schema([
            Placeholder::make("{$type}_{$eye}_label")
                ->hiddenLabel()
                ->content($label)
                ->extraAttributes(['class' => 'font-semibold flex items-center']),

            TextInput::make("$prefix.sphere")->hiddenLabel()->nullable(),
            TextInput::make("$prefix.cylinder")->hiddenLabel()->nullable(),
            TextInput::make("$prefix.axis")->hiddenLabel()->nullable()->maxValue(180),
            TextInput::make("$prefix.monopd")->hiddenLabel()->nullable(),
        ]);
    }

    /** ---------------------------------------
     *  REMARKS ROW
     * --------------------------------------*/
    protected static function remarksRow(): Grid
    {
        return Grid::make(5)->schema([
            Placeholder::make('remarks_label')
                ->hiddenLabel()
                ->content('Remark(s):')
                ->extraAttributes(['class' => 'font-semibold flex items-center']),

            Textarea::make('remarks')
                ->hiddenLabel()
                ->placeholder('Follow up needed')
                ->maxLength(255)
                ->autosize()
                ->columnSpan(4)
                ->nullable(),
        ]);
    }
}
