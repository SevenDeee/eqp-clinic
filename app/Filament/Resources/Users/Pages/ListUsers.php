<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth(Width::ThreeExtraLarge)
                ->createAnother(false),
        ];
    }

    // protected function getTableActions(): array
    // {
    //     return [
    //         ViewAction::make()
    //             ->modalHeading('Product Details')
    //             ->modalWidth('xs'),
    //     ];
    // }
}
