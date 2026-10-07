<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $weekStart = now()->subDays(6)->startOfDay();

        // Tarjetas estadísticas
        $stats = [
            'products' => Product::count(),
            'categories' => Category::count(),
            'available' => Product::available()->count(),
            'low' => Product::lowStock()->count(),
            'out' => Product::outOfStock()->count(),
            'recent' => InventoryMovement::where('created_at', '>=', $weekStart)->count(),
        ];

        // Productos que necesitan reabastecimiento (stock bajo y agotados)
        $lowStockProducts = Product::with('category')
            ->whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock')
            ->orderBy('name')
            ->limit(8)
            ->get();

        // Últimos movimientos
        $latestMovements = InventoryMovement::with(['product', 'user'])
            ->latest()
            ->latest('id')
            ->limit(8)
            ->get();

        // Gráfico de barras: entradas y salidas (unidades) de los últimos 7 días
        $days = collect(range(6, 0))->map(fn ($i) => now()->subDays($i)->startOfDay());

        $rows = InventoryMovement::query()
            ->selectRaw('DATE(created_at) as day, type, SUM(quantity) as total')
            ->where('created_at', '>=', $weekStart)
            ->whereIn('type', [InventoryMovement::TYPE_IN, InventoryMovement::TYPE_OUT])
            ->groupBy('day', 'type')
            ->get();

        $seriesFor = fn (string $type) => $days->map(function ($day) use ($rows, $type) {
            $row = $rows->first(fn ($r) => $r->type === $type && $r->day === $day->toDateString());

            return (int) ($row->total ?? 0);
        })->values()->all();

        $chart = [
            'labels' => $days->map(fn ($d) => $d->format('d/m'))->values()->all(),
            'entradas' => $seriesFor(InventoryMovement::TYPE_IN),
            'salidas' => $seriesFor(InventoryMovement::TYPE_OUT),
            'stock' => [
                Product::normalStock()->count(),
                $stats['low'],
                $stats['out'],
            ],
        ];

        return view('dashboard.index', compact('stats', 'lowStockProducts', 'latestMovements', 'chart'));
    }
}