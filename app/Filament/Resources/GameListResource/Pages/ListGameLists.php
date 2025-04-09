<?php

namespace App\Filament\Resources\GameListResource\Pages;

use App\Filament\Resources\GameListResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGameLists extends ListRecords
{
    protected static string $resource = GameListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
