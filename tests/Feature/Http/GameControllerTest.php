<?php

use App\Models\Game;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use MarcReichel\IGDBLaravel\Models\Game as IGDBGame;

uses(RefreshDatabase::class);

test('index page displays upcoming games', function () {
    // Create a mock for the IGDBGame class
    $mock = Mockery::mock('overload:MarcReichel\IGDBLaravel\Models\Game');
    $mock->shouldReceive('whereBetween')->andReturnSelf();
    $mock->shouldReceive('with')->andReturnSelf();
    $mock->shouldReceive('where')->andReturnSelf();
    $mock->shouldReceive('orderBy')->andReturnSelf();
    $mock->shouldReceive('limit')->andReturnSelf();
    $mock->shouldReceive('get')->andReturn(collect([]));
    
    // Visit the index page
    $response = $this->get(route('home'));
    
    // Assert the response is successful and renders the correct Inertia page
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Games/Index')
        ->has('nextWeekGames')
        ->has('thisMonthGames')
    );
});

test('search page returns empty results when no query is provided', function () {
    $response = $this->get(route('games.search'));
    
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Games/Search')
        ->where('games', [])
        ->where('query', '')
    );
});

test('search page returns games when query is provided', function () {
    // Create a mock game result
    $mockGame = (object)[
        'id' => 12345,
        'name' => 'Test Game',
        'slug' => 'test-game',
        'summary' => 'This is a test game',
        'cover' => (object)[
            'url' => '//images.igdb.com/igdb/image/upload/t_thumb/co1234.jpg'
        ],
        'genres' => [
            (object)['name' => 'Action'],
            (object)['name' => 'Adventure']
        ],
        'platforms' => [
            (object)['name' => 'PC'],
            (object)['name' => 'PlayStation 5']
        ],
        'first_release_date' => Carbon::now()->timestamp
    ];
    
    // Create a mock for the IGDBGame class
    $mock = Mockery::mock('overload:MarcReichel\IGDBLaravel\Models\Game');
    $mock->shouldReceive('search')->with('test')->andReturnSelf();
    $mock->shouldReceive('with')->andReturnSelf();
    $mock->shouldReceive('where')->andReturnSelf();
    $mock->shouldReceive('limit')->andReturnSelf();
    $mock->shouldReceive('get')->andReturn(collect([$mockGame]));
    
    // Visit the search page with a query
    $response = $this->get(route('games.search', ['query' => 'test']));
    
    // Assert the response is successful and renders the correct Inertia page
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Games/Search')
        ->has('games')
        ->where('query', 'test')
    );
});

test('search page handles API errors gracefully', function () {
    // Create a mock for the IGDBGame class
    $mock = Mockery::mock('overload:MarcReichel\IGDBLaravel\Models\Game');
    $mock->shouldReceive('search')->with('test')->andThrow(new \Exception('API Error'));
    
    // Visit the search page with a query
    $response = $this->get(route('games.search', ['query' => 'test']));
    
    // Assert the response is successful and renders the correct Inertia page with an error
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Games/Search')
        ->where('games', [])
        ->where('query', 'test')
        ->has('error')
    );
});

test('show page displays game from database if it exists', function () {
    // Create a game in the database
    $game = Game::factory()->create([
        'slug' => 'existing-game',
    ]);
    
    // Visit the show page for the existing game
    $response = $this->get(route('games.show', ['slug' => 'existing-game']));
    
    // Assert the response is successful and renders the correct Inertia page
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Games/Show')
        ->has('game')
        ->where('game.id', $game->id)
        ->where('game.slug', 'existing-game')
    );
});

test('show page fetches game from IGDB if not in database', function () {
    // Create a mock game result
    $mockGame = (object)[
        'id' => 12345,
        'name' => 'New Game',
        'slug' => 'new-game',
        'summary' => 'This is a new game',
        'cover' => (object)[
            'url' => '//images.igdb.com/igdb/image/upload/t_thumb/co1234.jpg'
        ],
        'genres' => [
            (object)['name' => 'Action'],
            (object)['name' => 'Adventure']
        ],
        'platforms' => [
            (object)['name' => 'PC'],
            (object)['name' => 'PlayStation 5']
        ],
        'first_release_date' => Carbon::now()->timestamp
    ];
    
    // Create a mock for the IGDBGame class
    $mock = Mockery::mock('overload:MarcReichel\IGDBLaravel\Models\Game');
    $mock->shouldReceive('where')->with('slug', 'new-game')->andReturnSelf();
    $mock->shouldReceive('with')->andReturnSelf();
    $mock->shouldReceive('first')->andReturn($mockGame);
    
    // Visit the show page for a game not in the database
    $response = $this->get(route('games.show', ['slug' => 'new-game']));
    
    // Assert the response is successful and renders the correct Inertia page
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Games/Show')
        ->has('game')
        ->where('game.slug', 'new-game')
    );
    
    // Assert the game was saved to the database
    $this->assertDatabaseHas('games', [
        'igdb_id' => 12345,
        'slug' => 'new-game',
    ]);
});

test('show page returns 404 if game not found in database or IGDB', function () {
    // Create a mock for the IGDBGame class
    $mock = Mockery::mock('overload:MarcReichel\IGDBLaravel\Models\Game');
    
    // Set up the mock to return null for the first() method
    $mock->shouldReceive('where')->with('slug', 'non-existent-game')->andReturnSelf();
    $mock->shouldReceive('with')->andReturnSelf();
    $mock->shouldReceive('first')->andReturn(null);
    
    // Visit the show page for a non-existent game
    $response = $this->get(route('games.show', ['slug' => 'non-existent-game']));
    
    // Assert the response is a 404
    $response->assertStatus(404);
    $response->assertJson(['error' => 'Game not found']);
});

test('show page handles API errors gracefully', function () {
    // Create a mock for the IGDBGame class
    $mock = Mockery::mock('overload:MarcReichel\IGDBLaravel\Models\Game');
    $mock->shouldReceive('where')->with('slug', 'error-game')->andThrow(new \Exception('API Error'));
    
    // Visit the show page that will trigger an API error
    $response = $this->get(route('games.show', ['slug' => 'error-game']));
    
    // Assert the response is a 500
    $response->assertStatus(500);
});
