<?php

use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;
use MarcReichel\IGDBLaravel\Models\Game as IGDBGame;

uses(RefreshDatabase::class);

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
});
