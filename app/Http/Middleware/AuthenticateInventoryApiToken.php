<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateInventoryApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $configuredToken = (string) config('services.inventory_api.token', '');
        $providedToken = (string) $request->bearerToken();

        if ($configuredToken === '' || $providedToken === '' || ! hash_equals($configuredToken, $providedToken)) {
            return response()->json(['message' => 'Token de acceso no válido o ausente.'], 401)
                ->header('WWW-Authenticate', 'Bearer');
        }

        return $next($request);
    }
}
