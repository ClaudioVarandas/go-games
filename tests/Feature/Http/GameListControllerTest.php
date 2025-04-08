<?php

use App\Models\Game;
use App\Models\GameList;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('unauthenticated users cannot access game lists', function () {
    $response = $this->get('/game-lists');
    
    $response->assertRedirect('/login');
});

test('authenticated users can view their game lists', function () {
    $user = User::factory()->create();
    $gameList = GameList::factory()->create(['user_id' => $user->id]);
    
    $response = $this->actingAs($user)->get('/game-lists');
    
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('GameLists/Index')
        ->has('gameLists', 1)
        ->where('gameLists.0.id', $gameList->id)
    );
});

test('authenticated users can create a game list', function () {
    $user = User::factory()->create();
    $listData = [
        'name' => 'My Favorite Games',
        'description' => 'A collection of my favorite games',
        'is_public' => true,
    ];
    
    $response = $this->actingAs($user)->post('/game-lists', $listData);
    
    $response->assertRedirect();
    $this->assertDatabaseHas('game_lists', [
        'user_id' => $user->id,
        'name' => $listData['name'],
        'description' => $listData['description'],
        'is_public' => $listData['is_public'],
    ]);
});

test('authenticated users can view a specific game list', function () {
    $user = User::factory()->create();
    $gameList = GameList::factory()->create(['user_id' => $user->id]);
    $game = Game::factory()->create();
    $gameList->games()->attach($game->id);
    
    $response = $this->actingAs($user)->get("/game-lists/{$gameList->id}");
    
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('GameLists/Show')
        ->where('gameList.id', $gameList->id)
        ->has('gameList.games', 1)
    );
});

test('authenticated users can update their game list', function () {
    $user = User::factory()->create();
    $gameList = GameList::factory()->create(['user_id' => $user->id]);
    $updatedData = [
        'name' => 'Updated List Name',
        'description' => 'Updated description',
        'is_public' => false,
    ];
    
    $response = $this->actingAs($user)->put("/game-lists/{$gameList->id}", $updatedData);
    
    $response->assertRedirect();
    $this->assertDatabaseHas('game_lists', [
        'id' => $gameList->id,
        'name' => $updatedData['name'],
        'description' => $updatedData['description'],
        'is_public' => $updatedData['is_public'],
    ]);
});

test('authenticated users can delete their game list', function () {
    $user = User::factory()->create();
    $gameList = GameList::factory()->create(['user_id' => $user->id]);
    
    $response = $this->actingAs($user)->delete("/game-lists/{$gameList->id}");
    
    $response->assertRedirect();
    $this->assertDatabaseMissing('game_lists', ['id' => $gameList->id]);
});

test('users cannot access game lists of other users', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $gameList = GameList::factory()->create(['user_id' => $user1->id, 'is_public' => false]);
    
    $response = $this->actingAs($user2)->get("/game-lists/{$gameList->id}");
    
    $response->assertStatus(403);
});

test('users can view public game lists of other users', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $gameList = GameList::factory()->create(['user_id' => $user1->id, 'is_public' => true]);
    
    $response = $this->actingAs($user2)->get("/game-lists/{$gameList->id}");
    
    $response->assertStatus(200);
});

test('users cannot update game lists of other users', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $gameList = GameList::factory()->create(['user_id' => $user1->id]);
    
    $response = $this->actingAs($user2)->put("/game-lists/{$gameList->id}", [
        'name' => 'Hacked List',
    ]);
    
    $response->assertStatus(403);
});

test('users cannot delete game lists of other users', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $gameList = GameList::factory()->create(['user_id' => $user1->id]);
    
    $response = $this->actingAs($user2)->delete("/game-lists/{$gameList->id}");
    
    $response->assertStatus(403);
    $this->assertDatabaseHas('game_lists', ['id' => $gameList->id]);
});
