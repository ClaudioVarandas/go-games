<?php

namespace App\Actions;

use App\Models\GameList;

class DeleteGameListAction
{
    /**
     * Delete a game list.
     *
     * @param \App\Models\GameList $gameList
     * @return bool
     */
    public function handle(GameList $gameList): bool
    {
        return $gameList->delete();
    }
}
