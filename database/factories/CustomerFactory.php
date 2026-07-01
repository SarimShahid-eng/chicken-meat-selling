<?php

namespace Database\Factories;

use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone_number' => fake()->phoneNumber(),
            'description' => fake()->text(),
            'region_id' => Region::factory(),
            'opening_balance' => fake()->randomFloat(2, 0, 99999999.99),
            'category' => fake()->randomElement(['hotel', 'customer']),
            'date' => now(),
        ];
    }
}
