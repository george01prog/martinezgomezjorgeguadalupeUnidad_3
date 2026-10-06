<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Product::class);

        $search = trim((string) $request->query('search', ''));
        $categoryId = $request->query('category');
        $status = $request->query('status');
        $stock = $request->query('stock');

        $products = Product::query()
            ->with('category')
            ->search($search)
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when(in_array($status, ['active', 'inactive'], true),
                fn ($q) => $q->where('is_active', $status === 'active'))
            ->when($stock, fn ($q) => match ($stock) {
                'out' => $q->outOfStock(),
                'low' => $q->lowStock(),
                'normal' => $q->normalStock(),
                default => $q,
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get(['id', 'name']);

        return view('products.index', compact(
            'products', 'categories', 'search', 'categoryId', 'status', 'stock'
        ));
    }

    public function create(): View
    {
        Gate::authorize('create', Product::class);

        return view('products.create', [
            'product' => new Product(['is_active' => true, 'stock' => 0, 'min_stock' => 5]),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        Gate::authorize('create', Product::class);

        Product::create($request->validated());

        return redirect()->route('products.index')
            ->with('success', 'Producto registrado correctamente.');
    }

    public function show(Product $product): View
    {
        Gate::authorize('view', $product);

        $product->load('category');

        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        Gate::authorize('update', $product);

        // Categorías activas + la categoría actual del producto (aunque esté inactiva)
        $categories = Category::where('is_active', true)
            ->orWhere('id', $product->category_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        Gate::authorize('update', $product);

        $product->update($request->validated());

        return redirect()->route('products.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        Gate::authorize('delete', $product);

        $product->delete(); // soft delete: se conserva el historial de movimientos

        return redirect()->route('products.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}