<?php

namespace App\Filament\Resources\Inventories\Pages;

use App\Filament\Resources\Inventories\InventoryResource;
use App\Models\Category;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;

class ListInventories extends ListRecords
{
    protected static string $resource = InventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
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
}
