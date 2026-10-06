<?php

namespace Database\Seeders;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Database\Seeder;

class InventoryMovementSeeder extends Seeder
{
    public function run(InventoryService $inventory): void
    {
        if (InventoryMovement::exists()) {
            return;
        }

        $admin = User::where('email', 'admin@inventario.test')->first();
        $user = User::where('email', 'usuario@inventario.test')->first();

        if (! $admin || ! $user) {
            return;
        }

        // [sku, tipo, cantidad, motivo, usuario, días atrás]
        $movements = [
            ['ELE-001', 'entrada', 20, 'Compra a proveedor', $admin, 12],
            ['ELE-001', 'salida', 5, 'Venta mostrador', $user, 10],
            ['ELE-002', 'salida', 4, 'Venta a cliente corporativo', $user, 9],
            ['PAP-001', 'entrada', 50, 'Reabastecimiento mensual', $admin, 8],
            ['PAP-002', 'salida', 6, 'Consumo interno', $user, 7],
            ['LIM-001', 'entrada', 30, 'Compra a proveedor', $admin, 6],
            ['LIM-002', 'entrada', 20, 'Compra a proveedor', $admin, 6],
            ['LIM-002', 'salida', 12, 'Venta mostrador', $user, 5],
            ['HER-002', 'ajuste', 5, 'Inventario físico: se contaron 5 piezas', $admin, 4],
            ['ALI-002', 'salida', 9, 'Venta mostrador', $user, 3],
            ['MOB-002', 'salida', 2, 'Venta a oficina', $user, 2],
            ['ELE-004', 'entrada', 15, 'Compra a proveedor', $admin, 1],
            ['PAP-003', 'ajuste', 25, 'Inventario físico sin diferencias', $admin, 0],
        ];

        foreach ($movements as [$sku, $type, $quantity, $reason, $who, $daysAgo]) {
            $product = Product::where('sku', $sku)->first();

            if (! $product) {
                continue;
            }

            $movement = $inventory->registerMovement($product->id, $type, $quantity, $who, $reason);

            // Fechas escalonadas para que el historial se vea realista
            $date = now()->subDays($daysAgo);
            $movement->forceFill(['created_at' => $date, 'updated_at' => $date])->save();
        }
    }
}