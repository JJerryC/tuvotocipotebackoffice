<?php
namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    // …

    public function render($request, Throwable $e)
    {
        
        if ($request->is('api/*')) {
            
            if ($e instanceof ModelNotFoundException) {
                return response()->json([
                    'error'   => 'Not Found',
                    'message' => 'Recurso no encontrado',
                ], 404);
            }

            if ($e instanceof NotFoundHttpException) {
                return response()->json([
                    'error'   => 'Not Found',
                    'message' => 'Endpoint no existe',
                ], 404);
            }

            if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                return response()->json([
                    'error'   => 'Forbidden',
                    'message' => 'No tienes permiso para acceder a este recurso',
                ], 403);
            }
            return response()->json([
                'error'   => 'Internal Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }

        return parent::render($request, $e);
    }
}
