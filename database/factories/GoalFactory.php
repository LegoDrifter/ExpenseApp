<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Goal>
 */
class GoalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

            return [
                'user_id' => 1,
                'title' => $this->faker->sentence,
                'description' => $this->faker->paragraph,
                'start_date' => $this->faker->date(),
                'price' => $this->faker->randomFloat(2, 100, 5000),
                'initial_budget' => $this->faker->randomFloat(2, 100, 5000),
                'status' => $this->faker->numberBetween(0, 1),
            ];

    }
}
