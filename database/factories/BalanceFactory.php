<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Balance>
 */
class BalanceFactory extends Factory
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
            'category_id' => 1,
            'wage' => $this->faker->randomFloat(2, 100, 5000),
            'recurring' => $this->faker->randomElement([0, 1]),
            'description' => $this->faker->sentence,
            'date' => $this->faker->date(),
            'cycle' => $this->faker->randomNumber(),
            'status' => $this->faker->numberBetween(0, 1),
            'type' => $this->faker->randomElement([0, 1]),
        ];
    }
}
