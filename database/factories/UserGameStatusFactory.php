<?php

namespace Database\Factories;

use App\Enums\GameStatus;
use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserGameStatus>
 */
class UserGameStatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = [
            GameStatus::PLAYING,
            GameStatus::BEATEN,
            GameStatus::COMPLETED,
            GameStatus::ON_HOLD,
            GameStatus::ABANDONED,
        ];

        return [
            'user_id' => User::factory(),
            'game_id' => Game::factory(),
            'status' => $this->faker->randomElement($statuses),
            'notes' => $this->faker->optional(0.7)->paragraph(),
        ];
    }
}
