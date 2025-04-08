<?php

namespace App\Actions;

use App\Models\GameList;
use App\Models\User;

class CreateGameListAction
{
    /**
     * Create a new game list.
     *
     * @param \App\Models\User $user
     * @param array $data
     * @return \App\Models\GameList
     */
    public function handle(User $user, array $data): GameList
    {
        $gameList = new GameList();
        $gameList->user_id = $user->id;
        $gameList->name = $data['name'];
        $gameList->description = $data['description'] ?? null;
        $gameList->is_public = $data['is_public'] ?? true;
        $gameList->save();

        return $gameList;
    }
}
