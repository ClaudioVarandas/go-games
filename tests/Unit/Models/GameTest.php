<?php

use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('game has correct fillable attributes', function () {
    $game = new Game;

    expect($game->getFillable())->toEqual([
        'igdb_id',
        'name',
        'slug',
        'cover_url',
        'release_date',
        'summary',
        'rating',
        'genres',
        'platforms',
    ]);
});

test('game has correct casts', function () {
    $game = new Game;

    expect($game->getCasts())->toEqual([
        'id' => 'int',
        'release_date' => 'date',
        'rating' => 'float',
        'genres' => 'array',
        'platforms' => 'array',
    ]);
});

test('can create game using factory', function () {
    $game = Game::factory()->create();

    expect($game)->toBeInstanceOf(Game::class)
        ->and($game->exists)->toBeTrue()
        ->and($game->igdb_id)->toBeInt()
        ->and($game->name)->toBeString()
        ->and($game->slug)->toBeString()
        ->and($game->genres)->toBeArray()
        ->and($game->platforms)->toBeArray();
});

test('can create game with specific attributes', function () {
    $attributes = [
        'igdb_id' => 12345,
        'name' => 'Test Game',
        'slug' => 'test-game',
        'cover_url' => 'https://example.com/cover.jpg',
        'release_date' => '2025-01-01',
        'summary' => 'This is a test game',
        'rating' => 8.5,
        'genres' => ['Action', 'Adventure'],
        'platforms' => ['PC', 'PlayStation 5'],
    ];

    $game = Game::create($attributes);

    expect($game->refresh())
        ->igdb_id->toBe(12345)
        ->name->toBe('Test Game')
        ->slug->toBe('test-game')
        ->cover_url->toBe('https://example.com/cover.jpg')
        ->release_date->toDateString()->toBe('2025-01-01')
        ->summary->toBe('This is a test game')
        ->rating->toBe(8.5)
        ->genres->toBe(['Action', 'Adventure'])
        ->platforms->toBe(['PC', 'PlayStation 5']);
});

test('release_date is cast to date', function () {
    $game = Game::factory()->create([
        'release_date' => '2025-01-01',
    ]);

    expect($game->release_date)->toBeInstanceOf(\Carbon\Carbon::class);
});

test('genres and platforms are cast to arrays', function () {
    $game = Game::factory()->create([
        'genres' => ['Action', 'Adventure'],
        'platforms' => ['PC', 'PlayStation 5'],
    ]);

    expect($game->genres)->toBeArray()
        ->and($game->genres)->toBe(['Action', 'Adventure'])
        ->and($game->platforms)->toBeArray()
        ->and($game->platforms)->toBe(['PC', 'PlayStation 5']);
});
