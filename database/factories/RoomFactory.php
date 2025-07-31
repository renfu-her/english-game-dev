<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'code' => strtoupper($this->faker->bothify('??????')),
            'host_id' => \App\Models\Member::factory(),
            'max_players' => $this->faker->numberBetween(4, 8),
            'current_players' => $this->faker->numberBetween(0, 4),
            'status' => $this->faker->randomElement(['waiting', 'playing', 'finished']),
            'settings' => [
                'categories' => ['daily-conversation', 'travel-transport'],
                'question_count' => $this->faker->numberBetween(10, 20),
                'difficulty' => $this->faker->randomElement(['easy', 'medium', 'hard', 'mixed']),
                'time_limit' => $this->faker->numberBetween(20, 45),
                'allow_skip' => true,
                'show_explanation' => true,
                'auto_start' => false,
            ],
        ];
    }
}
