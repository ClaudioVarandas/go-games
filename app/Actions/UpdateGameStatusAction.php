<?php

namespace App\Actions;

use App\Models\Game;
use App\Models\User;
use App\Models\UserGameStatus;

class UpdateGameStatusAction
{
    /**
     * Update or create a game status for a user.
     */
    public function handle(User $user, Game $game, array $data): ?UserGameStatus
    {
        // If status is null, remove the status
        if (! isset($data['status']) || $data['status'] === null) {
            UserGameStatus::where('user_id', $user->id)
                ->where('game_id', $game->id)
                ->delete();

            return null;
        }

        // Find existing status or create a new one
        $status = UserGameStatus::firstOrNew([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);

        // Update status
        $status->status = $data['status'];

        // Update notes if provided
        if (isset($data['notes'])) {
            $status->notes = $data['notes'];
        }

        $status->save();

        return $status;
    }
}
