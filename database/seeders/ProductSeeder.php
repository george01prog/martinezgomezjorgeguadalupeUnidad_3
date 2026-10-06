<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // [sku, nombre, categoría, precio compra, precio venta, stock, stock mínimo]
        $products = [
            ['ELE-001', 'Mouse inalámbrico', 'Electrónica', 120, 199, 45, 10],
            ['ELE-002', 'Teclado mecánico', 'Electrónica', 480, 749, 8, 10],
            ['ELE-003', 'Audífonos Bluetooth', 'Electrónica', 350, 559, 0, 5],
            ['ELE-004', 'Cable HDMI 2 m', 'Electrónica', 45, 89, 60, 15],
            ['PAP-001', 'Cuaderno profesional', 'Papelería', 18, 35, 120, 30],
            ['PAP-002', 'Caja de bolígrafos (12 pzas)', 'Papelería', 40, 72, 9, 15],
            ['PAP-003', 'Resma de papel carta', 'Papelería', 85, 129, 25, 10],
            ['LIM-001', 'Desinfectante 1 L', 'Limpieza', 22, 42, 70, 20],
            ['LIM-002', 'Jabón líquido 500 ml', 'Limpieza', 18, 34, 0, 12],
            ['LIM-003', 'Trapeador industrial', 'Limpieza', 60, 105, 14, 5],
            ['HER-001', 'Martillo de acero', 'Herramientas', 75, 135, 18, 5],
            ['HER-002', 'Juego de desarmadores', 'Herramientas', 130, 229, 5, 8],
            ['HER-003', 'Taladro inalámbrico', 'Herramientas', 890, 1399, 12, 4],
            ['ALI-001', 'Café molido 500 g', 'Alimentos', 70, 115, 38, 12],
            ['ALI-002', 'Galletas surtidas', 'Alimentos', 15, 28, 11, 20],
            ['MOB-001', 'Silla ergonómica', 'Mobiliario', 1250, 1899, 7, 3],
            ['MOB-002', 'Escritorio de oficina', 'Mobiliario', 1800, 2699, 3, 3],
        ];

        foreach ($products as [$sku, $name, $categoryName, $purchase, $sale, $stock, $minStock]) {
            $category = Category::where('name', $categoryName)->first();

            if (! $category) {
                continue;
            }

            Product::withTrashed()->firstOrCreate(
                ['sku' => $sku],
                [
                    'category_id' => $category->id,
                    'name' => $name,
                    'description' => "{$name} para uso general.",
                    'purchase_price' => $purchase,
                    'sale_price' => $sale,
                    'stock' => $stock,
                    'min_stock' => $minStock,
                    'is_active' => true,
                ]
            );
        }
    }
}