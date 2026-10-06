<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMovementRequest;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryMovementController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->query('type');
        $productId = $request->query('product');
        $from = $request->query('from');
        $to = $request->query('to');

        $movements = InventoryMovement::query()
            ->with(['product', 'user'])
            ->when(array_key_exists((string) $type, InventoryMovement::TYPES),
                fn ($q) => $q->where('type', $type))
            ->when($productId, fn ($q) => $q->where('product_id', $productId))
            ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $products = Product::orderBy('name')->get(['id', 'sku', 'name']);

        return view('movements.index', compact('movements', 'products', 'type', 'productId', 'from', 'to'));
    }

    public function create(Request $request): View
    {
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'sku', 'name', 'stock']);

        return view('movements.create', [
            'products' => $products,
            'selectedProduct' => $request->query('product'),
        ]);
    }

    public function store(StoreMovementRequest $request, InventoryService $inventory): RedirectResponse
    {
        $data = $request->validated();

        $movement = $inventory->registerMovement(
            productId: (int) $data['product_id'],
            type: $data['type'],
            quantity: (int) $data['quantity'],
            user: $request->user(),
            reason: $data['reason'] ?? null,
        );

        return redirect()->route('movements.index')
            ->with('success', "Movimiento registrado: {$movement->type_label} de {$movement->quantity} unidad(es). Stock actual: {$movement->stock_after}.");
    }

    public function show(InventoryMovement $movement): View
    {
        $movement->load(['product.category', 'user']);

        return view('movements.show', compact('movement'));
    }
}