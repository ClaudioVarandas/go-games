<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GameList>
 */
class GameListFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->words(3, true).' Games',
            'description' => $this->faker->optional(0.7)->paragraph(),
            'is_public' => $this->faker->boolean(80), // 80% chance of being public
        ];
    }

    /**
     * Indicate that the game list is private.
     */
    public function private(): self
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => false,
        ]);
    }

    /**
     * Indicate that the game list is public.
     */
    public function public(): self
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => true,
        ]);
    }
}
