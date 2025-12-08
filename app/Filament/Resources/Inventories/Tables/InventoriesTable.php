<?php

namespace App\Filament\Resources\Inventories\Tables;

use App\Models\Inventory;
use App\Models\Supplier;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
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
                    ->modalWidth(Width::Medium)
                    ->color(fn($record) => $record->stock <= 5 ? 'danger' : 'success')
                    ->schema([

                        Section::make('Supplier/s to Contact')
                            ->schema([
                                Placeholder::make('supplier_list')
                                    ->content(function ($record) {
                                        $categoryName = $record->category->name;
                                        // dd($categoryName);
                                        $suppliers = Supplier::select('name', 'contact_number', 'notes')->get()
                                            ->filter(function ($supplier) use ($categoryName) {
                                            return str_contains($supplier->notes ?? '', $categoryName);
                                        });

                                        if ($suppliers->isEmpty()) {
                                            return new HtmlString(
                                                '<p class="text-sm text-gray-500 dark:text-gray-400 text-center italic">No suppliers found.</p>'
                                            );
                                        }

                                        $html = '<div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 bg-gray-50 dark:bg-gray-800 max-h-64 overflow-y-auto">';
                                        $html .= '<ul class="space-y-1 m-0 pl-4">';

                                        foreach ($suppliers as $supplier) {
                                            $name = e($supplier->name);
                                            $contact_number = e($supplier->contact_number);
                                            $notes = e($supplier->notes);
                                            $html .= <<<HTML
<li class="text-lg text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 last:border-b-0 pb-1 pt-1">
    <div class="flex justify-between items-center">
        <span>{$name}</span>
        <span class="underline">0{$contact_number}
        </span>
    </div>
    <!-- <p class="text-xs">0{contact_number}</p> -->
</li>
HTML;
                                        }

                                        $html .= '</ul></div>';

                                        return new HtmlString($html);
                                    })
                                    ->disableLabel(),
                            ])->compact(),
                        Section::make()
                            ->schema([
                                TextInput::make('stock')
                                    ->label('Amount to Restock')
                                    ->integer()
                                    ->suffix('pc/s')
                                    ->minValue(1)
                                    ->required(),
                            ]),

                    ])
                    ->action(function (array $data, $record) {

                        // dd($data, $record);
            
                        $record->increment('stock', $data['stock']);
                        // ->update($data);
            
                        Notification::make()
                            ->success()
                            ->title('Restock Completed')
                            ->body($record->name . ' Restocked Successfully.')
                            ->send();
                    })
                ,

                ActionGroup::make([
                    EditAction::make()
                        ->disabled()
                    ,
                    Action::make('Archive')
                        ->hidden(fn($record) => !is_null($record->archived_at))
                        ->action(function (Inventory $record) {
                            $record->update([
                                'archived_at' => now(),
                            ]);

                            Notification::make()
                                ->title('Item Archived')
                                ->body("{$record->name} has been successfully archived.")
                                ->success()
                                ->send();
                        })
                        ->color('info')
                        ->icon(Heroicon::ArchiveBox)
                        ->requiresConfirmation()
                        ->successRedirectUrl('/inventories'),
                    Action::make('Restore')
                        ->hidden(fn($record) => is_null($record->archived_at))
                        ->action(function (Inventory $record) {
                            $record->update([
                                'archived_at' => null,
                            ]);

                            Notification::make()
                                ->title('Item Restored')
                                ->body("{$record->name} has been successfully restored.")
                                ->success()
                                ->send();
                        })
                        ->color('info')
                        ->icon(Heroicon::ArchiveBox)
                        ->requiresConfirmation()
                        ->successRedirectUrl('/inventories'),
                ])->dropdownPlacement('bottom-center')
                    ->extraAttributes(['class' => 'flex-[1]',])
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->extraAttributes(['class' => 'text-black dark:text-white']),
            ])
            // ->filters([
            //     Filter::make('Archived')
            //         ->query(fn(Builder $query): Builder => $query->whereNotNull('archived_at'))
            //     // ...
            // ])
        ;
    }
}