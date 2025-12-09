<?php

namespace App\Filament\Resources\Patients\Schemas;

use App\Filament\Resources\Patients\PatientResource;
use App\Models\Inventory;
use App\Models\InventoryOrder;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Illuminate\Support\Carbon;

class PatientInfolist
{
    public static function configure(Schema $schema): Schema
    {

        // var_dump(Inventory::where('id', InventoryOrder::where('order_id', Order::where('patient_id', 1)->value('id'))->skip(1)->value('inventory_id'))->value('name'));
        return $schema
            ->columns(4)
            ->components([

                Group::make()
                    ->columnSpan(3)
                    ->columnSpanFull()
                    ->schema([
                        Section::make()
                            ->description('Patient Details')
                            ->columns(2)
                            ->schema([
                                self::patient('name', 'Name'),
                                self::patient('contact_number', 'Contact Number'),
                                self::patient('age', 'Age'),
                                self::patient('sex', 'Sex'),
                                TextEntry::make('address')
                                    ->extraAttributes([
                                        'class' => 'block w-full rounded-lg border border-gray-300 bg-white px-3 py-1 text-lg text-gray-900 shadow-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100',
                                    ])
                                    ->columnSpanFull()
                            ]),
                        Section::make()
                            ->compact()
                            ->description('Clinical Assessment')
                            ->afterHeader(function ($record) {

                                $follow_up_on = $record->follow_up_on !== null
                                    ? \Carbon\Carbon::make($record->follow_up_on)->endOfDay()
                                    : null;

                                if (
                                    $record->follow_up_on === null
                                    || $follow_up_on < Carbon::now()->startOfDay()
                                ) {
                                    $date = 'Not scheduled';
                                    $bgClass = 'bg-gray-100 dark:bg-gray-800/50 shadow-sm ring-1 ring-gray-900/5 dark:shadow-lg dark:ring-gray-100/10';
                                    $textClass = 'text-gray-600 dark:text-gray-400';
                                    $iconColor = 'text-gray-400 dark:text-gray-500';
                                } else {
                                    $date = \Carbon\Carbon::parse($record->follow_up_on)->format('F d, Y');
                                    $bgClass = 'bg-blue-50 dark:bg-blue-900/20 shadow-sm ring-1 ring-blue-900/5 dark:shadow-lg dark:ring-blue-100/10';
                                    $textClass = 'text-blue-700 dark:text-blue-300';
                                    $iconColor = 'text-blue-500 dark:text-blue-400';
                                }

                                return new \Illuminate\Support\HtmlString(
                                    '<div class="inline-flex items-center gap-2 px-3 py-2 text-sm rounded-lg mb-3 ' . $bgClass . ' ' . $textClass . '">
            <svg class="w-4 h-4 ' . $iconColor . '" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
            </svg>
            <span class="font-medium">Follow up On : </span>
            <span class="font-semibold">' . $date . '</span>
        </div>'
                                );
                            })
                            ->columns(2)
                            ->schema([

                                TextEntry::make('diagnosis')
                                    ->placeholder('N/A')
                                    ->extraAttributes([
                                        'class' => 'block w-full min-h-[80px] rounded-lg border border-gray-300 bg-white px-3 py-2 text-lg text-gray-900 shadow-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100',
                                    ]),
                                TextEntry::make('special_instructions')
                                    ->placeholder('N/A')
                                    ->extraAttributes([
                                        'class' => 'block w-full min-h-[80px] rounded-lg border border-gray-300 bg-white px-3 py-2 text-lg text-gray-900 shadow-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100',
                                    ]),
                                self::patient('creator.name', 'Added By', 'sm')->prefix('Dr. '),
                                self::patient('created_at', 'Date Added', 'sm')->date(),
                            ]),

                        Section::make('Medical History')
                            ->compact()
                            ->description(fn() => 'Prescription/s Total: ' . session('prescriptions_count'))
                            // Use the eager-loaded count instead of querying
                            ->schema([
                                View::make('components.prescription')
                                    ->visible(fn() => (session('prescriptions_count')) !== 0),
                            ]),
                    ]),

                Group::make()
                    ->inlineLabel()
                    ->columnSpan(1)
                    ->schema([
                        Section::make()
                            ->description(
                                fn($record) => 'Optical Specifications'
                                // . Inventory::where('id', InventoryOrder::where('order_id', Order::where('patient_id', $record->id)->value('id'))->skip(1)->value('inventory_id'))->value('name')
                            )
                            ->schema([
                                TextEntry::make('frame_type')
                                    ->size(TextSize::Large)
                                    ->beforeLabel('Frame Type')
                                    ->default(fn($record) => Inventory::where('id', InventoryOrder::where('order_id', Order::where('patient_id', $record->id)->value('id'))->value('inventory_id'))->value('name'))
                                    ->hiddenLabel()
                                    ->placeholder('N/A'),


                                TextEntry::make('color')
                                    ->beforeContent(fn($record) => Inventory::where('id', InventoryOrder::where('order_id', Order::where('patient_id', $record->id)->value('id'))->skip(1)->value('inventory_id'))->value('name'))
                                    ->beforeLabel('Color')
                                    // ->placeholder('N/A')
                                    ->hiddenLabel(),

                                TextEntry::make('lens_supply')
                                    ->size(TextSize::Large)
                                    ->beforeLabel('Lens Supply')
                                    ->default(fn($record) => Inventory::where('id', InventoryOrder::where('order_id', Order::where('patient_id', $record->id)->value('id'))->skip(2)->value('inventory_id'))->value('name'))
                                    ->hiddenLabel()
                                    ->placeholder('N/A'),
                            ]),

                        Section::make()
                            ->description('Billing Information')
                            ->schema([
                                TextEntry::make('amount')
                                    ->size(TextSize::Large)
                                    ->beforeLabel('Amount')
                                    ->default(fn($record) => Order::where('patient_id', $record->id)->value('amount'))
                                    ->hiddenLabel()
                                    ->money('php')
                                    ->color('info')
                                    ->placeholder('N/A'),

                                TextEntry::make('deposit')
                                    ->size(TextSize::Large)
                                    ->beforeLabel('Deposit')
                                    ->default(fn($record) => Order::where('patient_id', $record->id)->value('deposit'))
                                    ->hiddenLabel()
                                    ->money('php')
                                    ->color('success')
                                    ->placeholder('N/A'),

                                TextEntry::make('balance')
                                    ->size(TextSize::Large)
                                    ->beforeLabel('Balance')
                                    ->default(fn($record) => Order::where('patient_id', $record->id)->value('balance'))
                                    ->hiddenLabel()
                                    ->money('php')
                                    ->color('danger')
                                    ->placeholder('N/A'),

                                Actions::make([
                                    Action::make('Return')
                                        ->extraAttributes(['class' => 'w-full'])
                                        ->url(PatientResource::getUrl('index')),
                                ])
                            ])
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

    public static function patient($column, $label, $size = 'lg')
    {
        return TextEntry::make($column)
            ->inlineLabel()
            ->label($label . ' : ')
            ->placeholder('N/A')
            ->extraAttributes([
                'class' => 'block w-full rounded-lg border border-gray-300 bg-white px-3 py-1 text-' . $size . ' text-gray-900 shadow-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100',
            ]);
    }
}