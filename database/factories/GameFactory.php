<?php

namespace Database\Factories;

use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Game::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'igdb_id' => $this->faker->unique()->randomNumber(6),
            'name' => $this->faker->words(3, true),
            'slug' => $this->faker->unique()->slug(3),
            'cover_url' => $this->faker->imageUrl(264, 374, 'games'),
            'release_date' => $this->faker->dateTimeBetween('-2 years', '+1 year'),
            'summary' => $this->faker->paragraph(),
            'rating' => $this->faker->randomFloat(1, 1, 10),
            'genres' => $this->faker->randomElements(['Action', 'Adventure', 'RPG', 'Strategy', 'Simulation', 'Sports'], $this->faker->numberBetween(1, 3)),
            'platforms' => $this->faker->randomElements(['PC', 'PlayStation 5', 'Xbox Series X', 'Nintendo Switch'], $this->faker->numberBetween(1, 3)),
        ];
    }
}
