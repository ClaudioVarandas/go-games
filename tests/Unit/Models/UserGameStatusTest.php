<?php

namespace Tests\Unit\Models;

use App\Enums\GameStatus;
use App\Models\Game;
use App\Models\User;
use App\Models\UserGameStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserGameStatusTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $game = Game::factory()->create();
        
        $status = UserGameStatus::factory()->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'status' => GameStatus::PLAYING,
        ]);
        
        $this->assertInstanceOf(User::class, $status->user);
        $this->assertEquals($user->id, $status->user->id);
    }
    
    /** @test */
    public function it_belongs_to_a_game()
    {
        $user = User::factory()->create();
        $game = Game::factory()->create();
        
        $status = UserGameStatus::factory()->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'status' => GameStatus::COMPLETED,
        ]);
        
        $this->assertInstanceOf(Game::class, $status->game);
        $this->assertEquals($game->id, $status->game->id);
    }
    
    /** @test */
    public function it_casts_status_to_enum()
    {
        $user = User::factory()->create();
        $game = Game::factory()->create();
        
        $status = UserGameStatus::factory()->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'status' => GameStatus::BEATEN,
        ]);
        
        $this->assertInstanceOf(GameStatus::class, $status->status);
        $this->assertEquals(GameStatus::BEATEN, $status->status);
    }
    
    /** @test */
    public function a_user_can_have_only_one_status_per_game()
    {
        $user = User::factory()->create();
        $game = Game::factory()->create();
        
        // Create first status
        UserGameStatus::factory()->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'status' => GameStatus::PLAYING,
        ]);
        
        // Try to create another status for the same user and game
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        UserGameStatus::factory()->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'status' => GameStatus::COMPLETED,
        ]);
    }
    
    /** @test */
    public function a_game_can_have_status_for_user()
    {
        $user = User::factory()->create();
        $game = Game::factory()->create();
        
        UserGameStatus::factory()->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'status' => GameStatus::ON_HOLD,
        ]);
        
        $status = $game->statusForUser($user->id);
        
        $this->assertInstanceOf(UserGameStatus::class, $status);
        $this->assertEquals(GameStatus::ON_HOLD, $status->status);
    }
}
