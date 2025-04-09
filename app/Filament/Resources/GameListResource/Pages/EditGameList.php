<?php

namespace App\Filament\Resources\GameListResource\Pages;

use App\Filament\Resources\GameListResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGameList extends EditRecord
{
    protected static string $resource = GameListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
