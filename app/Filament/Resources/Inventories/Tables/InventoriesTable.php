<?php

namespace App\Filament\Resources\Inventories\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Table;
// use Filament\Tables\Actions\Action;

class InventoriesTable
{
    // EXAMPLE 1: Basic Card Layout (Like your image)
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultGroup('category.name')
            ->paginated(false)
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                Split::make([
                    // Header with image
                    Stack::make([
                        ImageColumn::make('image')
                            ->height(100)
                            ->width('100%')
                            ->extraImgAttributes(['class' => 'rounded-lg']),
                        ColorColumn::make('name')
                            ->extraAttributes(['class' => 'scale-365 items-center justify-center font-bold text-lg'])
                        ,
                    ])->space(10)
                        ->extraAttributes(['class' => 'p-0']),

                    // Main content
                    Stack::make([
                        TextColumn::make('name')
                            ->weight('bold')
                            ->size('lg')
                            ->searchable(),

                        TextColumn::make('category.name')
                            ->icon('heroicon-s-folder-open')
                            ->color('primary')
                            ->size('sm'),

                        // Divider
                        TextColumn::make('divider')
                            ->label('')
                            ->formatStateUsing(fn() => '')
                            ->extraAttributes(['class' => 'border-t border-gray-200 dark:border-gray-700']),

                        // Stock and price grid
                        Grid::make(4)
                            ->schema([

                                TextColumn::make('stock')
                                    ->weight('bold')
                                    ->badge(fn($record) => $record->stock <= 5)
                                    ->color(fn($record) => $record->stock <= 5 ? 'danger' : null)
                                    ->size('lg'),

                                TextColumn::make('cost_per_unit')
                                    ->money('php')
                                    ->columnSpan(3)
                                    ->weight('bold')
                                    ->size('md')
                                    ->color('success'),
                            ]),
                    ])->space(2)
                        ->extraAttributes(['class' => 'p-0']),
                ])
                // ->space(10)
                // ->extraAttributes(['class' => 'bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow']),
            ])
            ->recordActions([

                Action::make('restock')
                    ->icon(fn($record) => $record->stock <= 5 ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-shopping-cart')
                    ->extraAttributes(['class' => 'flex-[3] w-full'])
                    ->button()
                    ->color(fn($record) => $record->stock <= 5 ? 'danger' : 'success')
                ,

                ActionGroup::make([
                    Action::make('edit')
                        ->icon('heroicon-o-pencil')
                ])->dropdownPlacement('bottom-center')
                    ->extraAttributes(['class' => 'flex-[1]',])
                    ->icon('heroicon-o-ellipsis-vertical')
                   ->extraAttributes(['class' => 'text-black dark:text-white'])
            ]);
    }
}