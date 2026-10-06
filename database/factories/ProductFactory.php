<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $purchase = fake()->randomFloat(2, 10, 500);

        return [
            'category_id' => Category::factory(),
            'sku' => strtoupper(fake()->unique()->bothify('???-####')),
            'name' => fake()->unique()->words(3, true),
            'description' => fake()->sentence(),
            'purchase_price' => $purchase,
            'sale_price' => round($purchase * 1.3, 2),
            'stock' => fake()->numberBetween(0, 100),
            'min_stock' => fake()->numberBetween(5, 15),
            'is_active' => true,
        ];
    }
}