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
        if ($request->is('api/*')) {
            // No autenticado
            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'error'   => 'Unauthorized',
                    'message' => 'No autenticado'
                ], 401);
            }

            // Sin permisos
            if ($e instanceof AuthorizationException) {
                return response()->json([
                    'error'   => 'Forbidden',
                    'message' => 'No tienes permiso'
                ], 403);
            }

            // Validación de datos
            if ($e instanceof ValidationException) {
                return response()->json([
                    'error'   => 'Validation Failed',
                    'message' => 'Datos inválidos',
                    'errors'  => $e->errors(),
                ], 422);
            }

            // Modelo no encontrado
            if ($e instanceof ModelNotFoundException) {
                return response()->json([
                    'error'   => 'Not Found',
                    'message' => 'Recurso no encontrado',
                ], 404);
            }

            // Ruta no definida
            if ($e instanceof NotFoundHttpException) {
                return response()->json([
                    'error'   => 'Not Found',
                    'message' => 'Endpoint no existe',
                ], 404);
            }

            // Verbo HTTP incorrecto
            if ($e instanceof MethodNotAllowedHttpException) {
                return response()->json([
                    'error'   => 'Method Not Allowed',
                    'message' => 'Método no permitido',
                ], 405);
            }

            // Otros errores
            return response()->json([
                'error'   => 'Internal Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }

        return parent::render($request, $e);
    }
}
