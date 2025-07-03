<?php

namespace App\Imports;

use App\Models\Candidate;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CandidatesImport implements ToModel, WithHeadingRow, WithMultipleSheets
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Esta clase puede usarse como alternativa al procesamiento manual
        // Por ahora, el controlador maneja toda la lógica
        return null;
    }

    /**
     * Define qué hojas procesar
     */
    public function sheets(): array
    {
        return [
            // Procesar todas las hojas
        ];
    }
}
