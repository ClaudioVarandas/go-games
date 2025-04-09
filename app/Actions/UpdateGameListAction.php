<?php

namespace App\Actions;

use App\Models\GameList;

class UpdateGameListAction
{
    /**
     * Update an existing game list.
     */
    public function handle(GameList $gameList, array $data): GameList
    {
        $gameList->name = $data['name'];
        $gameList->description = $data['description'] ?? null;
        $gameList->is_public = $data['is_public'] ?? $gameList->is_public;
        $gameList->save();

        return $gameList;
    }
}
