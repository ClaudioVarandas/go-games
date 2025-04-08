<?php

namespace Tests\Feature\Http;

use App\Enums\GameStatus;
use App\Models\Game;
use App\Models\User;
use App\Models\UserGameStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameStatusControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthenticated_users_cannot_set_game_status()
    {
        $game = Game::factory()->create();

        $response = $this->postJson("/games/{$game->id}/status", [
            'status' => GameStatus::PLAYING->value,
        ]);

        $response->assertUnauthorized();
    }

    /** @test */
    public function authenticated_users_can_set_game_status()
    {
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
    }

    /** @test */
    public function authenticated_users_can_update_game_status()
    {
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
    }

    /** @test */
    public function authenticated_users_can_remove_game_status()
    {
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
    }

    /** @test */
    public function status_must_be_valid()
    {
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
    }
}
