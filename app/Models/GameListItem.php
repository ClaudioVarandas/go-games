<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameListItem extends Model
{
    use HasFactory;

    /**
     * Get the game list that owns the item.
     */
    public function gameList(): BelongsTo
    {
        return $this->belongsTo(GameList::class);
    }

    /**
     * Get the game that belongs to the item.
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
