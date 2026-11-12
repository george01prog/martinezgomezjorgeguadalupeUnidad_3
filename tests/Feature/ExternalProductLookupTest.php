<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExternalProductLookupTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_product_form_offers_external_lookup(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'is_active' => true]);

        $this->actingAs($admin)
            ->get('/products/create')
            ->assertOk()
            ->assertSee('Buscar datos por código de barras')
            ->assertSee('Open Food Facts');
    }

    public function test_admin_can_lookup_a_product_by_barcode_from_open_food_facts(): void
    {
        Http::fake([
            'world.openfoodfacts.org/api/v2/product/7501031311309.json*' => Http::response([
                'status' => 1,
                'product' => [
                    'product_name' => 'Producto de prueba',
                    'generic_name' => 'Descripción de prueba',
                ],
            ]),
        ]);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'is_active' => true]);

        $this->actingAs($admin)
            ->getJson('/products/lookup/7501031311309')
            ->assertOk()
            ->assertExactJson([
                'name' => 'Producto de prueba',
                'description' => 'Descripción de prueba',
            ]);

        Http::assertSent(fn ($request) => $request->hasHeader('User-Agent'));
    }

    public function test_non_admin_cannot_use_the_external_product_lookup(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER, 'is_active' => true]);

        $this->actingAs($user)
            ->getJson('/products/lookup/7501031311309')
            ->assertForbidden();
    }

    public function test_lookup_reports_when_the_barcode_is_not_found(): void
    {
        Http::fake([
            'world.openfoodfacts.org/api/v2/product/7501031311309.json*' => Http::response([
                'status' => 0,
                'product' => null,
            ]),
        ]);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'is_active' => true]);

        $this->actingAs($admin)
            ->getJson('/products/lookup/7501031311309')
            ->assertNotFound()
            ->assertJson(['message' => 'No se encontró un producto para ese código.']);
    }
}
