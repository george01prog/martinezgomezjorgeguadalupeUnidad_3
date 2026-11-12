<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use Illuminate\Http\JsonResponse;

class InventoryMovementController extends Controller
{
    public function index(): JsonResponse
    {
        $movements = InventoryMovement::query()
            ->with(['product:id,sku,name', 'user:id,name'])
            ->latest()
            ->paginate(20);

        return response()->json($movements);
    }
}
