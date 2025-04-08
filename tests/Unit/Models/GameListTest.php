<?php

use App\Models\Game;
use App\Models\GameList;
use App\Models\User;

test('game list belongs to a user', function () {
    $gameList = GameList::factory()->create();
    
    expect($gameList->user)->toBeInstanceOf(User::class);
});

test('game list can have many games', function () {
    $gameList = GameList::factory()->create();
    $game = Game::factory()->create();
    
    $gameList->games()->attach($game->id);
    
    expect($gameList->games)->toHaveCount(1);
    expect($gameList->games->first())->toBeInstanceOf(Game::class);
});

test('game list can have notes for games', function () {
    $gameList = GameList::factory()->create();
    $game = Game::factory()->create();
    $notes = 'This is a great game!';
    
    $gameList->games()->attach($game->id, ['notes' => $notes]);
    
    expect($gameList->games->first()->pivot->notes)->toBe($notes);
});

test('game list can be public or private', function () {
    $publicList = GameList::factory()->public()->create();
    $privateList = GameList::factory()->private()->create();
    
    expect($publicList->is_public)->toBeTrue();
    expect($privateList->is_public)->toBeFalse();
});
