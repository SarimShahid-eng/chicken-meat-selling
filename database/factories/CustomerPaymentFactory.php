<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerPaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'amount' => fake()->numberBetween(-10000, 10000),
            'description' => fake()->text(),
            'type' => fake()->randomElement(["bank","cash"]),
            'date' => fake()->date(),
        ];
    }
}
