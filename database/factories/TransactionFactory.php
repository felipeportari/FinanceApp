<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'category_id' => Category::inRandomOrder()->first()?->id ?? 1,
            'type'        => $this->faker->randomElement(['expense', 'income']),
            'amount'      => $this->faker->randomFloat(2, 10, 5000),
            'description' => $this->faker->sentence(4),
            'date'        => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
        ];
    }

    public function expense(): static
    {
        return $this->state(fn () => ['type' => 'expense']);
    }

    public function income(): static
    {
        return $this->state(fn () => ['type' => 'income']);
    }
}
