<?php

namespace App\Services;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    /**
     * Registra un movimiento y actualiza el stock del producto de forma atómica.
     *
     * @throws ValidationException si la operación no es válida
     */
    public function registerMovement(
        int $productId,
        string $type,
        int $quantity,
        User $user,
        ?string $reason = null
    ): InventoryMovement {
        return DB::transaction(function () use ($productId, $type, $quantity, $user, $reason) {
            // Bloquea la fila del producto hasta terminar la transacción
            $product = Product::whereKey($productId)->lockForUpdate()->first();

            if (! $product) {
                throw ValidationException::withMessages([
                    'product_id' => 'El producto seleccionado no existe.',
                ]);
            }

            if (! $product->is_active) {
                throw ValidationException::withMessages([
                    'product_id' => 'No se pueden registrar movimientos de un producto inactivo.',
                ]);
            }

            $before = $product->stock;

            $after = match ($type) {
                InventoryMovement::TYPE_IN => $before + $quantity,
                InventoryMovement::TYPE_OUT => $before - $quantity,
                InventoryMovement::TYPE_ADJUST => $quantity,
                default => throw ValidationException::withMessages([
                    'type' => 'El tipo de movimiento no es válido.',
                ]),
            };

            if ($type === InventoryMovement::TYPE_OUT && $after < 0) {
                throw ValidationException::withMessages([
                    'quantity' => "La salida ({$quantity}) supera el stock disponible ({$before}).",
                ]);
            }

            $product->update(['stock' => $after]);

            return InventoryMovement::create([
                'product_id' => $product->id,
                'user_id' => $user->id,
                'type' => $type,
                'quantity' => $quantity,
                'stock_before' => $before,
                'stock_after' => $after,
                'reason' => $reason,
            ]);
        });
    }
}