<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GameRecord>
 */
class GameRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'room_id' => \App\Models\Room::factory(),
            'user_id' => \App\Models\Member::factory(),
            'question_id' => \App\Models\Question::factory(),
            'user_answer' => $this->faker->sentence(),
            'is_correct' => $this->faker->boolean(70), // 70% 正確率
            'time_taken' => $this->faker->numberBetween(5, 60),
            'answered_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
