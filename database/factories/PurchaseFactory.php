<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'supplier_id' => Supplier::factory(),
            'vehicle_no' => fake()->numberBetween(-10000, 10000),
            'crate_qty' => fake()->numberBetween(-10000, 10000),
            'total_weight' => fake()->numberBetween(-10000, 10000),
            'weight_cut' => fake()->numberBetween(-10000, 10000),
            'netweight' => fake()->numberBetween(-10000, 10000),
            'rate' => fake()->numberBetween(-10000, 10000),
            'rate_date' => fake()->date(),
            'total_amount' => fake()->numberBetween(-10000, 10000),
        ];
    }
}
