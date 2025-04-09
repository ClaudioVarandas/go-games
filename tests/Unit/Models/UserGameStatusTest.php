<?php

use App\Enums\GameStatus;
use App\Models\Game;
use App\Models\User;
use App\Models\UserGameStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it belongs to a user', function () {
    $user = User::factory()->create();
    $game = Game::factory()->create();

    $status = UserGameStatus::factory()->create([
        'user_id' => $user->id,
        'game_id' => $game->id,
        'status' => GameStatus::PLAYING,
    ]);

    expect($status->user)->toBeInstanceOf(User::class)
        ->and($status->user->id)->toEqual($user->id);
});

test('it belongs to a game', function () {
    $user = User::factory()->create();
    $game = Game::factory()->create();

    $status = UserGameStatus::factory()->create([
        'user_id' => $user->id,
        'game_id' => $game->id,
        'status' => GameStatus::COMPLETED,
    ]);

    expect($status->game)->toBeInstanceOf(Game::class)
        ->and($status->game->id)->toEqual($game->id);
});

test('it casts status to enum', function () {
    $user = User::factory()->create();
    $game = Game::factory()->create();

    $status = UserGameStatus::factory()->create([
        'user_id' => $user->id,
        'game_id' => $game->id,
        'status' => GameStatus::BEATEN,
    ]);

    expect($status->status)->toBeInstanceOf(GameStatus::class)
        ->and($status->status)->toEqual(GameStatus::BEATEN);
});

test('a user can have only one status per game', function () {
    $user = User::factory()->create();
    $game = Game::factory()->create();

    // Create first status
    UserGameStatus::factory()->create([
        'user_id' => $user->id,
        'game_id' => $game->id,
        'status' => GameStatus::PLAYING,
    ]);

    // Try to create another status for the same user and game
    expect(fn () => UserGameStatus::factory()->create([
        'user_id' => $user->id,
        'game_id' => $game->id,
        'status' => GameStatus::COMPLETED,
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});

test('a game can have status for user', function () {
    $user = User::factory()->create();
    $game = Game::factory()->create();

    UserGameStatus::factory()->create([
        'user_id' => $user->id,
        'game_id' => $game->id,
        'status' => GameStatus::ON_HOLD,
    ]);

    $status = $game->statusForUser($user->id);

    expect($status)->toBeInstanceOf(UserGameStatus::class)
        ->and($status->status)->toEqual(GameStatus::ON_HOLD);
});
