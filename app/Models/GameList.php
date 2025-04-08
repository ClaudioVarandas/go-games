<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameList extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Get the user that owns the game list.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the games in this list.
     */
    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_list_items')
            ->withPivot('notes')
            ->withTimestamps();
    }

    /**
     * Get the game list items for this list.
     */
    public function items(): HasMany
    {
        return $this->hasMany(GameListItem::class);
    }
}
