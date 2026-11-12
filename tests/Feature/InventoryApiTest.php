<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventory_api_rejects_missing_bearer_token(): void
    {
        config(['services.inventory_api.token' => 'inventory-test-token']);

        $this->getJson('/api/v1/products')
            ->assertUnauthorized()
            ->assertJson(['message' => 'Token de acceso no válido o ausente.']);
    }

    public function test_inventory_api_returns_paginated_products_with_valid_bearer_token(): void
    {
        config(['services.inventory_api.token' => 'inventory-test-token']);
        $product = Product::factory()->create(['sku' => 'API-0001', 'name' => 'Producto de prueba']);

        $this->withToken('inventory-test-token')
            ->getJson('/api/v1/products')
            ->assertOk()
            ->assertJsonPath('data.0.sku', $product->sku)
            ->assertJsonPath('data.0.category.id', $product->category_id)
            ->assertJsonStructure(['data', 'current_page', 'last_page', 'per_page', 'total']);
    }

    public function test_inventory_api_does_not_accept_requests_when_no_token_is_configured(): void
    {
        config(['services.inventory_api.token' => '']);

        $this->withToken('any-token')
            ->getJson('/api/v1/categories')
            ->assertUnauthorized();
    }

    public function test_inventory_api_rejects_an_incorrect_token(): void
    {
        config(['services.inventory_api.token' => 'inventory-test-token']);

        $this->withToken('incorrect-token')
            ->getJson('/api/v1/products')
            ->assertUnauthorized();
    }

    public function test_inventory_api_exposes_categories_and_movements(): void
    {
        config(['services.inventory_api.token' => 'inventory-test-token']);

        $this->withToken('inventory-test-token')
            ->getJson('/api/v1/categories')
            ->assertOk()
            ->assertJsonStructure(['data']);

        $this->getJson('/api/v1/movements')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }
}
