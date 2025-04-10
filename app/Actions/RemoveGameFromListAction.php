<?php

namespace App\Actions;

use App\Models\Game;
use App\Models\GameList;
use App\Models\GameListItem;

class RemoveGameFromListAction
{
    /**
     * Remove a game from a list.
     */
    public function handle(GameList $gameList, Game $game): bool
    {
        return GameListItem::where('game_list_id', $gameList->id)
            ->where('game_id', $game->id)
            ->delete();
    }
}
