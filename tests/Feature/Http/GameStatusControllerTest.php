<?php

use App\Enums\GameStatus;
use App\Models\Game;
use App\Models\User;
use App\Models\UserGameStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated users cannot set game status', function () {
    $game = Game::factory()->create();

    $response = $this->postJson("/games/{$game->id}/status", [
        'status' => GameStatus::PLAYING->value,
    ]);

    $response->assertUnauthorized();
});

test('backlog list is created automatically and only once', function () {
    $user = \App\Models\User::factory()->create();

    $this->actingAs($user)
        ->get('/my-games')
        ->assertOk();

    $this->assertDatabaseHas('game_lists', [
        'user_id' => $user->id,
        'type' => 'backlog',
        'name' => 'Backlog',
    ]);

    // Call again to ensure no duplicate backlog list
    $this->actingAs($user)
        ->get('/my-games')
        ->assertOk();

    $this->assertEquals(
        1,
        \App\Models\GameList::where('user_id', $user->id)->where('type', 'backlog')->count()
    );
});

test('authenticated users can set game status', function () {
    $user = User::factory()->create();
    $game = Game::factory()->create();

    $response = $this->actingAs($user)
        ->postJson("/games/{$game->id}/status", [
            'status' => GameStatus::PLAYING->value,
        ]);

    $response->assertSuccessful();
    $this->assertDatabaseHas('user_game_statuses', [
        'user_id' => $user->id,
        'game_id' => $game->id,
        'status' => GameStatus::PLAYING->value,
    ]);
});

test('authenticated users can update game status', function () {
    $user = User::factory()->create();
    $game = Game::factory()->create();

    // Create initial status
    UserGameStatus::factory()->create([
        'user_id' => $user->id,
        'game_id' => $game->id,
        'status' => GameStatus::PLAYING,
    ]);

    // Update status
    $response = $this->actingAs($user)
        ->postJson("/games/{$game->id}/status", [
            'status' => GameStatus::COMPLETED->value,
        ]);

    $response->assertSuccessful();
    $this->assertDatabaseHas('user_game_statuses', [
        'user_id' => $user->id,
        'game_id' => $game->id,
        'status' => GameStatus::COMPLETED->value,
    ]);

    // Ensure there's only one status record
    $this->assertCount(1, UserGameStatus::where('user_id', $user->id)->where('game_id', $game->id)->get());
});

test('authenticated users can remove game status', function () {
    $user = User::factory()->create();
    $game = Game::factory()->create();

    // Create initial status
    UserGameStatus::factory()->create([
        'user_id' => $user->id,
        'game_id' => $game->id,
        'status' => GameStatus::PLAYING,
    ]);

    // Remove status
    $response = $this->actingAs($user)
        ->deleteJson("/games/{$game->id}/status");

    $response->assertSuccessful();
    $this->assertDatabaseMissing('user_game_statuses', [
        'user_id' => $user->id,
        'game_id' => $game->id,
    ]);
});

test('status must be valid', function () {
    $user = User::factory()->create();
    $game = Game::factory()->create();

    $response = $this->actingAs($user)
        ->postJson("/games/{$game->id}/status", [
            'status' => 'invalid_status',
        ]);

    $response->assertUnprocessable();
    $this->assertDatabaseMissing('user_game_statuses', [
        'user_id' => $user->id,
        'game_id' => $game->id,
    ]);
});
