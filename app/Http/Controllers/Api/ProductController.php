<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::query()
            ->with('category:id,name')
            ->orderBy('name')
            ->paginate(20);

        return response()->json($products);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($product->load('category:id,name'));
    }
}
