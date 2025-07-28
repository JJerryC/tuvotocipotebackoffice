<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class CheckApiKey
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // ✅ Validación de API Key
            $providedKey = $request->header('X-API-KEY');
            $validKey    = config('api.candidates_api_key');

            if (! $providedKey || $providedKey !== $validKey) {
                return response()->json([
                    'error'   => 'Unauthorized',
                    'message' => 'Unauthorized: API Key inválida',
                    'status'  => 401
                ], 401);
            }

            // ✅ Ejecutar la petición
            return $next($request);

        } catch (ModelNotFoundException $e) {
            // ✅ Manejo de recursos no encontrados
            return response()->json([
                'error'   => 'Not Found',
                'message' => 'El recurso solicitado no existe',
                'status'  => 404
            ], 404);

        } catch (Throwable $e) {
            // ✅ Manejo de errores inesperados
            return response()->json([
                'error'   => 'Internal Server Error',
                'message' => $e->getMessage(),
                'status'  => 500
            ], 500);
        }
    }
}
