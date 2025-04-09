<?php

use App\Models\Game;
use App\Models\GameList;
use App\Models\User;

test('unauthenticated users cannot add games to lists', function () {
    $gameList = GameList::factory()->create();
    $game = Game::factory()->create();

    $response = $this->post("/game-lists/{$gameList->id}/games", [
        'game_id' => $game->id,
    ]);

    $response->assertRedirect('/login');
});

test('authenticated users can add games to their lists', function () {
    $user = User::factory()->create();
    $gameList = GameList::factory()->create(['user_id' => $user->id]);
    $game = Game::factory()->create();

    $response = $this->actingAs($user)->post("/game-lists/{$gameList->id}/games", [
        'game_id' => $game->id,
        'notes' => 'Great game!',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('game_list_items', [
        'game_list_id' => $gameList->id,
        'game_id' => $game->id,
        'notes' => 'Great game!',
    ]);
});

test('authenticated users can remove games from their lists', function () {
    $user = User::factory()->create();
    $gameList = GameList::factory()->create(['user_id' => $user->id]);
    $game = Game::factory()->create();
    $gameList->games()->attach($game->id);

    $response = $this->actingAs($user)->delete("/game-lists/{$gameList->id}/games/{$game->id}");

    $response->assertRedirect();
    $this->assertDatabaseMissing('game_list_items', [
        'game_list_id' => $gameList->id,
        'game_id' => $game->id,
    ]);
});

test('users cannot add games to lists of other users', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $gameList = GameList::factory()->create(['user_id' => $user1->id]);
    $game = Game::factory()->create();

    $response = $this->actingAs($user2)->post("/game-lists/{$gameList->id}/games", [
        'game_id' => $game->id,
    ]);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('game_list_items', [
        'game_list_id' => $gameList->id,
        'game_id' => $game->id,
    ]);
});

test('users cannot remove games from lists of other users', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $gameList = GameList::factory()->create(['user_id' => $user1->id]);
    $game = Game::factory()->create();
    $gameList->games()->attach($game->id);

    $response = $this->actingAs($user2)->delete("/game-lists/{$gameList->id}/games/{$game->id}");

    $response->assertStatus(403);
    $this->assertDatabaseHas('game_list_items', [
        'game_list_id' => $gameList->id,
        'game_id' => $game->id,
    ]);
});

test('adding a game that is already in the list updates the notes', function () {
    $user = User::factory()->create();
    $gameList = GameList::factory()->create(['user_id' => $user->id]);
    $game = Game::factory()->create();
    $gameList->games()->attach($game->id, ['notes' => 'Original notes']);

    $response = $this->actingAs($user)->post("/game-lists/{$gameList->id}/games", [
        'game_id' => $game->id,
        'notes' => 'Updated notes',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('game_list_items', [
        'game_list_id' => $gameList->id,
        'game_id' => $game->id,
        'notes' => 'Updated notes',
    ]);
});
