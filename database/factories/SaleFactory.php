<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'customer_id' => Customer::factory(),
            'crate_qty' => fake()->numberBetween(-10000, 10000),
            'total_weight' => fake()->numberBetween(-10000, 10000),
            'weight_cut' => fake()->numberBetween(-10000, 10000),
            'netweight' => fake()->numberBetween(-10000, 10000),
            'rate' => fake()->numberBetween(-10000, 10000),
            'total_amount' => fake()->numberBetween(-10000, 10000),
        ];
    }
}
