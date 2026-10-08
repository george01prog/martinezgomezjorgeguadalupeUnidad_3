<?php

namespace App\Services;

use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ReportService
{
    public const REPORTS = [
        'products' => 'Productos',
        'low_stock' => 'Stock bajo',
        'out_of_stock' => 'Agotados',
        'movements' => 'Movimientos',
        'entries' => 'Entradas',
        'exits' => 'Salidas',
    ];

    public const TITLES = [
        'products' => 'Reporte de productos',
        'low_stock' => 'Reporte de productos con stock bajo',
        'out_of_stock' => 'Reporte de productos agotados',
        'movements' => 'Reporte de movimientos de inventario',
        'entries' => 'Reporte de entradas de inventario',
        'exits' => 'Reporte de salidas de inventario',
    ];

    public function isMovementReport(string $report): bool
    {
        return in_array($report, ['movements', 'entries', 'exits'], true);
    }

    public function query(string $report, array $filters): Builder
    {
        return $this->isMovementReport($report)
            ? $this->movementQuery($report, $filters)
            : $this->productQuery($report, $filters);
    }

    private function productQuery(string $report, array $f): Builder
    {
        return Product::query()
            ->with('category')
            ->when($f['category'] ?? null, fn ($q, $v) => $q->where('category_id', $v))
            ->when($f['product'] ?? null, fn ($q, $v) => $q->whereKey($v))
            ->when($f['from'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($f['to'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->when($report === 'low_stock', fn ($q) => $q->lowStock())
            ->when($report === 'out_of_stock', fn ($q) => $q->outOfStock())
            ->orderBy('name');
    }

    private function movementQuery(string $report, array $f): Builder
    {
        $type = match ($report) {
            'entries' => InventoryMovement::TYPE_IN,
            'exits' => InventoryMovement::TYPE_OUT,
            default => $f['type'] ?? null,
        };

        return InventoryMovement::query()
            ->with(['product.category', 'user'])
            ->when($type, fn ($q, $v) => $q->where('type', $v))
            ->when($f['product'] ?? null, fn ($q, $v) => $q->where('product_id', $v))
            ->when($f['category'] ?? null, fn ($q, $v) => $q->whereHas('product', fn ($p) => $p->where('category_id', $v)))
            ->when($f['from'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($f['to'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->latest()
            ->latest('id');
    }

    /** Totales que se muestran sobre el reporte: [título, valor, icono, color]. */
    public function summary(string $report, Builder $query): array
    {
        $count = (clone $query)->reorder()->count();

        if (! $this->isMovementReport($report)) {
            $units = (int) (clone $query)->reorder()->sum('stock');
            $value = (float) (clone $query)->reorder()
                ->selectRaw('COALESCE(SUM(stock * purchase_price), 0) as total')
                ->value('total');

            return [
                ['Registros', number_format($count), 'cube', 'indigo'],
                ['Unidades en stock', number_format($units), 'chart', 'blue'],
                ['Valor de inventario', '$' . number_format($value, 2), 'check-circle', 'green'],
            ];
        }

        if ($report === 'movements') {
            $in = (int) (clone $query)->reorder()->where('type', InventoryMovement::TYPE_IN)->sum('quantity');
            $out = (int) (clone $query)->reorder()->where('type', InventoryMovement::TYPE_OUT)->sum('quantity');

            return [
                ['Registros', number_format($count), 'arrows', 'indigo'],
                ['Unidades de entrada', number_format($in), 'arrow-down', 'blue'],
                ['Unidades de salida', number_format($out), 'arrow-up', 'red'],
            ];
        }

        $units = (int) (clone $query)->reorder()->sum('quantity');

        return [
            ['Registros', number_format($count), 'arrows', 'indigo'],
            [$report === 'entries' ? 'Unidades ingresadas' : 'Unidades retiradas', number_format($units),
                $report === 'entries' ? 'arrow-down' : 'arrow-up', $report === 'entries' ? 'blue' : 'red'],
        ];
    }

    public function headers(string $report): array
    {
        return $this->isMovementReport($report)
            ? ['Fecha', 'SKU', 'Producto', 'Categoría', 'Tipo', 'Cantidad', 'Stock antes', 'Stock después', 'Usuario', 'Motivo']
            : ['SKU', 'Producto', 'Categoría', 'Precio compra', 'Precio venta', 'Stock', 'Stock mínimo', 'Nivel de stock', 'Estado', 'Registrado'];
    }

    public function row(string $report, Model $model): array
    {
        if ($this->isMovementReport($report)) {
            return [
                $model->created_at->format('d/m/Y H:i'),
                $model->product->sku,
                $model->product->name,
                $model->product->category->name ?? '',
                $model->type_label,
                $model->quantity,
                $model->stock_before,
                $model->stock_after,
                $model->user->name,
                $model->reason ?? '',
            ];
        }

        return [
            $model->sku,
            $model->name,
            $model->category->name,
            number_format((float) $model->purchase_price, 2, '.', ''),
            number_format((float) $model->sale_price, 2, '.', ''),
            $model->stock,
            $model->min_stock,
            $model->stock_label,
            $model->is_active ? 'Activo' : 'Inactivo',
            $model->created_at->format('d/m/Y H:i'),
        ];
    }
}