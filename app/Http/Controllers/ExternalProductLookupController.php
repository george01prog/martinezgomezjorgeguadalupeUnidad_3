<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class ExternalProductLookupController extends Controller
{
    public function __invoke(string $barcode): JsonResponse
    {
        try {
            $response = Http::acceptJson()
                ->withUserAgent(config('app.name').' inventory lookup')
                ->timeout(5)
                ->get("https://world.openfoodfacts.org/api/v2/product/{$barcode}.json", [
                    'fields' => 'product_name,generic_name',
                ]);
        } catch (ConnectionException $exception) {
            Log::warning('Open Food Facts is not reachable.', ['exception' => $exception->getMessage()]);

            return response()->json(['message' => 'El servicio externo no está disponible por el momento.'], 503);
        }

        if (! $response->successful()) {
            Log::warning('Open Food Facts returned an unsuccessful response.', [
                'status' => $response->status(),
            ]);

            return response()->json(['message' => 'No fue posible consultar el servicio externo.'], 502);
        }

        $payload = $response->json();

        if (! is_array($payload) || ! array_key_exists('status', $payload)) {
            Log::warning('Open Food Facts returned an invalid response.');

            return response()->json(['message' => 'El servicio externo devolvió una respuesta inválida.'], 502);
        }

        if ((int) $payload['status'] !== 1) {
            return response()->json(['message' => 'No se encontró un producto para ese código.'], 404);
        }

        $product = $payload['product'] ?? null;

        if (! is_array($product) || ! is_string($product['product_name'] ?? null)
            || trim($product['product_name']) === '') {
            Log::warning('Open Food Facts returned product data without a name.');

            return response()->json(['message' => 'El servicio externo devolvió una respuesta inválida.'], 502);
        }

        return response()->json([
            'name' => mb_substr(trim($product['product_name']), 0, 150),
            'description' => is_string($product['generic_name'] ?? null)
                ? mb_substr(trim($product['generic_name']), 0, 1000)
                : '',
        ]);
    }
}
