<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e)
    {
        // Si la petición es API espera JSON
        if ($request->is('api/*') || $request->expectsJson()) {

            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'error'   => 'Unauthorized',
                    'message' => 'No autenticado'
                ], 401);
            }

            if ($e instanceof AuthorizationException) {
                return response()->json([
                    'error'   => 'Forbidden',
                    'message' => 'Sin permisos'
                ], 403);
            }

            if ($e instanceof ValidationException) {
                return response()->json([
                    'error'   => 'Validation Failed',
                    'message' => 'Datos inválidos',
                    'errors'  => $e->errors(),
                ], 422);
            }

            if ($e instanceof ModelNotFoundException) {
                return response()->json([
                    'error'   => 'Not Found',
                    'message' => 'Recurso no encontrado'
                ], 404);
            }

            if ($e instanceof NotFoundHttpException) {
                return response()->json([
                    'error'   => 'Not Found',
                    'message' => 'Endpoint no existe'
                ], 404);
            }

            if ($e instanceof MethodNotAllowedHttpException) {
                return response()->json([
                    'error'   => 'Method Not Allowed',
                    'message' => 'Método HTTP no permitido'
                ], 405);
            }

            // Cualquier otro error
            return response()->json([
                'error'   => 'Internal Server Error',
                'message' => $e->getMessage()
            ], 500);
        }

        return parent::render($request, $e);
    }
}
