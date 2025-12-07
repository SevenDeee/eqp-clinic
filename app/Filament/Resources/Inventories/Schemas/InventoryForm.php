<?php

namespace App\Filament\Resources\Inventories\Schemas;

use App\Models\Category;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class InventoryForm
{
    public static function configure(Schema $schema): Schema
    {
        // Helper function to determine if the "image" section should be hidden
        $shouldHideImage = function ($get) {
            $categoryId = $get('category_name');

            if (!$categoryId) {
                return true;
            }

            // $categoryName = Str::title(Category::where('id', $categoryId)->value('name') ?? '');

            return Str::contains($categoryId ?? '', 'Color');
        };

        // $shouldHideImage = fn($get) => (Str::contains($get('category_name') ?? '', 'Color'));

        return $schema
            ->columns(2)
            ->components([
                Section::make()
                    ->columnSpan(fn($get) => $shouldHideImage($get) ? 2 : 1)
                    ->schema([
                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name') // specify display column
                            // ->options(Category::pluck('name', 'id'))
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                $categoryName = Str::title(Category::where('id', $state)->value('name') ?? '');
                                $set('category_name', $categoryName); // store computed name in form state
                            })
                            ->required(),

                        TextInput::make('name')
                            ->required(),

                        // TextInput::make('stock')
                        //     ->suffix('pc/s')
                        //     ->required()
                        //     ->numeric()
                        //     ->default(0),

                        TextInput::make('cost_per_unit')
                            ->prefix('₱')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),

                Section::make()
                    ->hidden($shouldHideImage)
                    ->schema([
                        FileUpload::make('image')
                            ->hidden($shouldHideImage)
                            ->image()
                            ->imageEditor()
                            ->hiddenLabel()
                            ->imageEditorViewportWidth('600')
                            ->imageEditorViewportHeight('600')
                            ->panelAspectRatio('1:0.65')
                            ->panelLayout('integrated')
                            ->required(),
                    ]),
            ]);
    }
}
