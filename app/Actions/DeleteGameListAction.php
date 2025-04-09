<?php

namespace App\Actions;

use App\Models\GameList;

class DeleteGameListAction
{
    /**
     * Delete a game list.
     */
    public function handle(GameList $gameList): bool
    {
        return $gameList->delete();
    }
}
