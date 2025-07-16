<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckApiKey
{
    public function handle(Request $request, Closure $next)
    {
        $providedKey = $request->header('X-API-KEY');
        $validKey    = config('api.candidates_api_key');

        if (! $providedKey || $providedKey !== $validKey) {
            return response()->json([
                'message' => 'Unauthorized: API Key inválida'
            ], 401);
        }

        return $next($request);
    }
}
