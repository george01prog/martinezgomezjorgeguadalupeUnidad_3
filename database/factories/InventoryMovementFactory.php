<?php

namespace Database\Factories;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryMovementFactory extends Factory
{
    public function definition(): array
    {
        $before = fake()->numberBetween(0, 100);
        $quantity = fake()->numberBetween(1, 20);

        return [
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
            'type' => InventoryMovement::TYPE_IN,
            'quantity' => $quantity,
            'stock_before' => $before,
            'stock_after' => $before + $quantity,
            'reason' => fake()->sentence(),
        ];
    }
}