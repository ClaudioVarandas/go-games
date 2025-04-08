<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'igdb_id',
        'name',
        'slug',
        'cover_url',
        'release_date',
        'summary',
        'rating',
        'genres',
        'platforms',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'release_date' => 'date',
        'rating' => 'float',
        'genres' => 'array',
        'platforms' => 'array',
    ];

    /**
     * Get the game lists that contain this game.
     */
    public function gameLists(): BelongsToMany
    {
        return $this->belongsToMany(GameList::class, 'game_list_items')
            ->withPivot('notes')
            ->withTimestamps();
    }
    
    /**
     * Get the user game statuses for this game.
     */
    public function userStatuses(): HasMany
    {
        return $this->hasMany(UserGameStatus::class);
    }
    
    /**
     * Get the status for a specific user.
     *
     * @param int $userId
     * @return \App\Models\UserGameStatus|null
     */
    public function statusForUser(int $userId)
    {
        return $this->userStatuses()->where('user_id', $userId)->first();
    }
}
