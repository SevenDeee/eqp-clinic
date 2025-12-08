<?php

namespace App\Filament\Resources\Inventories\Pages;

use App\Filament\Resources\Inventories\InventoryResource;
use App\Models\Category;
use App\Models\Supplier;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;

class ListInventories extends ListRecords
{
    protected static string $resource = InventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('add_supplier')
                ->modalHeading('Add Supplier')
                ->modalWidth(Width::FiveExtraLarge)
                ->icon(Heroicon::UserPlus)
                ->schema([
                    Grid::make()->columns(2)
                        ->schema([
                            Section::make('_')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Supplier Name')
                                        ->unique(Supplier::class, 'name')
                                        ->required(),
                                    TextInput::make('contact_number')
                                        ->unique(Supplier::class, 'contact_number')
                                        ->prefix('+63')
                                        ->mask('9999999999')
                                        ->required(),
                                    Select::make('notes')
                                        ->options(Category::pluck('name', 'name'))
                                        ->multiple()
                                        ->label('Supply Category')
                                        ->native(false)
                                        ->columnSpanFull()
                                        ->required()
                                        ->dehydrateStateUsing(fn($state) => is_array($state) ? implode(', ', $state) : $state)
                                        ->formatStateUsing(fn($state) => is_string($state) ? explode(', ', $state) : $state),
                                ]),
                            Section::make('Existing Supplier')
                                ->schema([
                                    Placeholder::make('supplier_list')
                                        ->content(function () {
                                            $suppliers = Supplier::select('name', 'contact_number', 'notes')->get();

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
        <span class="text-xs italic text-gray-500 dark:text-gray-400">
            {$notes}
        </span>
    </div>
    <p class="text-xs">0{$contact_number}</p>
</li>
HTML;
                                            }

                                            $html .= '</ul></div>';

                                            return new HtmlString($html);
                                        })
                                        ->disableLabel(),
                                ])->compact(),
                        ])
                ])
                ->action(function (array $data) {

                    // dd($data);
        
                    Supplier::create([
                        'name' => $data['name'],
                        'contact_number' => $data['contact_number'],
                        'notes' => $data['notes'],
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Supplier Added')
                        ->body('Supplier Added Successfully.')
                        ->send();
                })
            // ->label('New Item')
            ,
            ActionGroup::make([
                CreateAction::make()
                    ->modalHeading('Create Item')
                    ->modalWidth(Width::FourExtraLarge)
                    ->createAnother(false)
                    ->icon(Heroicon::Plus)
                    ->label('New Item'),
                Action::make('New Category')
                    ->icon(Heroicon::Plus)
                    ->form([

                        Grid::make()->columns(2)
                            ->schema([

                                Section::make()
                                    ->schema([
                                        TextInput::make('category_name')
                                            ->unique(Category::class, 'name')
                                            ->required(),
                                    ]),
                                Section::make('Existing Categories')
                                    ->schema([
                                        Placeholder::make('category_list')
                                            ->content(function () {
                                                $categories = Category::pluck('name');

                                                if ($categories->isEmpty()) {
                                                    return new HtmlString(
                                                        '<p class="text-sm text-gray-500 dark:text-gray-400 text-center italic">No categories found.</p>'
                                                    );
                                                }

                                                $html = '<div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 bg-gray-50 dark:bg-gray-800 max-h-64 overflow-y-auto">';
                                                $html .= '<ul class="space-y-1 m-0 pl-4 text-center">';

                                                foreach ($categories as $category) {
                                                    $escaped = e($category);
                                                    $html .= <<<HTML
<li class="text-sm text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 last:border-b-0 pb-1 pt-1">{$escaped}</li>
HTML;
                                                }

                                                $html .= '</ul></div>';

                                                return new HtmlString($html);
                                            })
                                            ->disableLabel(),
                                    ])->compact(),
                            ]),

                    ])
                    ->action(function (array $data) {

                        // dd($data);
            
                        Category::create([
                            'name' => $data['category_name'],
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Category Added')
                            ->body('Category Added Successfully.')
                            ->send();
                    }),
            ])
                ->label('Add')
                ->button()
                ->icon(Heroicon::PlusCircle)
                ->dropdownPlacement('left')
                ->color('success'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Active' => Tab::make()->query(fn($query) => $query->whereNull('archived_at')),
            'Archived' => Tab::make()->query(fn($query) => $query->whereNotNull('archived_at')),
        ];
    }
}
