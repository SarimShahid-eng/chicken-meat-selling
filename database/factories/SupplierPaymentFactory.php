<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierPaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'amount' => fake()->numberBetween(-10000, 10000),
            'description' => fake()->text(),
            'date' => fake()->date(),
            'type' => fake()->randomElement(["bank","cash"]),
        ];
    }
}
