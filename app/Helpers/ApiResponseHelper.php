<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;

trait ApiResponseHelper
{
    public function findOrJson404(string $modelClass, int|string $id, array $with = [], array $columns = ['*']): \Illuminate\Database\Eloquent\Model|\Illuminate\Http\JsonResponse
    {
        $query = $modelClass::query();

        if (!empty($with)) {
            $query->with($with);
        }

        $item = $query->select($columns)->find($id);

        if (!$item) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        return $item;
    }
}