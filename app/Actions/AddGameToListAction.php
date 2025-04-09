<?php

namespace App\Actions;

use App\Models\Game;
use App\Models\GameList;
use App\Models\GameListItem;

class AddGameToListAction
{
    /**
     * Add a game to a list.
     */
    public function handle(GameList $gameList, Game $game, array $data = []): GameListItem
    {
        // Check if the game is already in the list
        $existingItem = GameListItem::where('game_list_id', $gameList->id)
            ->where('game_id', $game->id)
            ->first();

        if ($existingItem) {
            // Update notes if provided
            if (isset($data['notes'])) {
                $existingItem->notes = $data['notes'];
                $existingItem->save();
            }

            return $existingItem;
        }

        // Create a new game list item
        $item = new GameListItem;
        $item->game_list_id = $gameList->id;
        $item->game_id = $game->id;
        $item->notes = $data['notes'] ?? null;
        $item->save();

        return $item;
    }
}
