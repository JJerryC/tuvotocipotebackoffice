<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\{Candidate, Party, Nomina, Entidad, Departamento, Municipio, Cargo, Sexo};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CandidateImportController extends Controller
{
    public function index()
    {
        Log::info('CandidateImportController@index ejecutándose');

        if (!view()->exists('candidates.import')) {
            return response('La vista candidates.import no existe', 404);
        }

        $previewData = session('preview_data', []);
        $previewErrors = session('preview_errors', []);

        return view('candidates.import', compact('previewData', 'previewErrors'));
    }

    public function preview(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        try {
            $file = $request->file('excel_file');
            $data = Excel::toArray([], $file);

            $previewData = [];
            $errors = [];
            $totalPreview = 0;

            foreach ($data as $sheetIndex => $sheet) {
                $sheetName = "Hoja " . ($sheetIndex + 1);
                $sheetData = $this->processSheet($sheet, $sheetName, 100);

                if (!empty($sheetData['candidates'])) {
                    $previewData[$sheetName] = $sheetData;
                    $totalPreview += count($sheetData['candidates']);
                }

                if (!empty($sheetData['errors'])) {
                    $errors[$sheetName] = $sheetData['errors'];
                }
            }

            $completeData = [];
            foreach ($data as $sheetIndex => $sheet) {
                $sheetName = "Hoja " . ($sheetIndex + 1);
                $completeData[$sheetName] = $this->processSheet($sheet, $sheetName);
            }

            session(['preview_data' => $previewData]);
            session(['complete_data' => $completeData]);
            session(['preview_errors' => $errors]);

            return response()->json([
                'success' => true,
                'previewData' => $previewData,
                'errors' => $errors,
                'totalPreview' => $totalPreview,
                'message' => "Se procesaron {$totalPreview} registros para previsualización"
            ]);

        } catch (\Exception $e) {
            Log::error('Error al procesar el archivo Excel: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el archivo: ' . $e->getMessage()
            ], 500);
        }
    }

    private function processSheet($sheet, $sheetName, $limit = null)
    {
        $candidates = [];
        $errors = [];

        $headerRow = null;
        $dataStartRow = null;

        foreach ($sheet as $index => $row) {
            if (isset($row[1]) && strtoupper(trim($row[1])) === 'PARTIDO') {
                $headerRow = $row;
                $dataStartRow = $index + 1;
                break;
            }
        }

        if (!$headerRow) {
            $errors[] = "No se encontraron encabezados válidos en {$sheetName}";
            return ['candidates' => [], 'errors' => $errors];
        }

        $endRow = count($sheet);
        if ($limit && $dataStartRow + $limit < $endRow) {
            $endRow = $dataStartRow + $limit;
        }

        for ($i = $dataStartRow; $i < $endRow; $i++) {
            $row = $sheet[$i];
            if (empty(array_filter($row))) {
                continue;
            }
            try {
                $candidate = $this->normalizeRowData($row, $i + 1);
                if ($candidate) {
                    $candidates[] = $candidate;
                }
            } catch (\Exception $e) {
                $errors[] = "Error en fila " . ($i + 1) . ": " . $e->getMessage();
            }
        }

        return [
            'candidates' => $candidates,
            'errors' => $errors,
            'total' => count($candidates),
            'limited' => $limit !== null && count($sheet) > ($dataStartRow + $limit)
        ];
    }

    private function normalizeRowData($row, $rowNumber)
    {
        $data = [
            'partido' => $this->cleanText($row[1] ?? ''),
            'entidad' => $this->cleanText($row[2] ?? ''),
            'nomina' => $this->cleanText($row[3] ?? ''),
            'departamento' => $this->cleanText($row[4] ?? ''),
            'municipio' => $this->cleanText($row[5] ?? ''),
            'cargo' => $this->cleanText($row[6] ?? ''),
            'posicion' => (int)($row[7] ?? 0),
            'numero_identidad' => $this->cleanText($row[8] ?? ''),
            'primer_nombre' => $this->cleanText($row[9] ?? ''),
            'segundo_nombre' => $this->cleanText($row[10] ?? ''),
            'primer_apellido' => $this->cleanText($row[11] ?? ''),
            'segundo_apellido' => $this->cleanText($row[12] ?? ''),
            'sexo' => $this->cleanText($row[13] ?? ''),
            'row_number' => $rowNumber
        ];

        if (empty($data['numero_identidad']) || empty($data['primer_nombre'])) {
            throw new \Exception("Datos incompletos: falta número de identidad o nombre");
        }

        return $data;
    }

    private function cleanText($text)
    {
        return trim(preg_replace('/\s+/', ' ', strval($text ?? '')));
    }

    public function import(Request $request)
    {
        $completeData = session('complete_data');

        if (!$completeData) {
            return response()->json([
                'success' => false,
                'message' => 'No hay datos para importar. Debe subir un archivo primero.'
            ], 400);
        }

        try {
            DB::beginTransaction();
            $importedCount = 0;
            $skippedCount = 0;

            foreach ($completeData as $sheetName => $sheetData) {
                foreach ($sheetData['candidates'] as $candidateData) {
                    $result = $this->importCandidate($candidateData);
                    $result ? $importedCount++ : $skippedCount++;
                }
            }

            DB::commit();
            session()->forget(['preview_data', 'complete_data', 'preview_errors']);

            return response()->json([
                'success' => true,
                'message' => "Importación completada: {$importedCount} candidatos importados, {$skippedCount} omitidos",
                'imported' => $importedCount,
                'skipped' => $skippedCount
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error durante la importación: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error durante la importación: ' . $e->getMessage()
            ], 500);
        }
    }

    private function importCandidate($data)
    {
        try {
            if (empty($data['numero_identidad']) || empty($data['primer_nombre']) || empty($data['primer_apellido'])) {
                Log::warning("Datos insuficientes para crear candidato: " . json_encode($data));
                return false;
            }

            if (Candidate::where('numero_identidad', $data['numero_identidad'])->exists()) {
                Log::info("Candidato duplicado, se omite: {$data['numero_identidad']}");
                return false;
            }

            $party = Party::firstOrCreate(['name' => $data['partido']]);
            $entidad = Entidad::firstOrCreate(['name' => $data['entidad']]);
            $nomina = Nomina::firstOrCreate(['name' => $data['nomina']]);
            $cargo = Cargo::firstOrCreate(['name' => $data['cargo']]);

            // Limpieza de departamento y municipio para búsqueda
            $depName = preg_replace('/^\d+\s*/', '', $data['departamento']);
            $depName = trim($depName);
            $departamento = Departamento::where('name', 'LIKE', '%' . $depName . '%')->first();

            $munName = preg_replace('/^\d+\s*/', '', $data['municipio']);
            $munName = trim($munName);
            $municipio = Municipio::where('departamento_id', $departamento->id ?? null)
                ->where('name', 'LIKE', '%' . $munName . '%')
                ->first();

            $sexo = $this->getSexo($data['sexo']) ?? Sexo::where('code', 'U')->first();

            if (!$party || !$entidad || !$nomina || !$cargo || !$departamento || !$municipio || !$sexo) {
                Log::warning("Faltan datos relacionados para candidato: " . json_encode($data));
                return false;
            }

            Candidate::create([
                'party_id' => $party->id,
                'entidad_id' => $entidad->id,
                'nomina_id' => $nomina->id,
                'departamento_id' => $departamento->id,
                'municipio_id' => $municipio->id,
                'cargo_id' => $cargo->id,
                'sexo_id' => $sexo->id,
                'posicion' => intval($data['posicion']),
                'numero_identidad' => $data['numero_identidad'],
                'primer_nombre' => $data['primer_nombre'],
                'segundo_nombre' => $data['segundo_nombre'] ?? null,
                'primer_apellido' => $data['primer_apellido'],
                'segundo_apellido' => $data['segundo_apellido'] ?? null,
                'fotografia' => null,
                'reeleccion' => false,
                'propuestas' => null,
                'independiente' => false,
                'fotografia_original' => null,
            ]);

            Log::info("Candidato creado: " . $data['numero_identidad']);
            return true;
        } catch (\Exception $e) {
            Log::error("Error creando candidato ({$data['numero_identidad']}): " . $e->getMessage());
            return false;
        }
    }

    private function getSexo($sexoText)
    {
        $sexoText = strtoupper(trim($sexoText));

        $map = [
            'HOMBRE' => 'H',
            'MUJER' => 'M',
            'OTRO' => 'O',
        ];

        $code = $map[$sexoText] ?? 'U';

        return Sexo::firstOrCreate(
            ['code' => $code],
            ['description' => $this->getSexoDescripcion($code)]
        );
    }

    private function getSexoDescripcion($code)
    {
        return [
            'H' => 'HOMBRE',
            'M' => 'MUJER',
            'O' => 'Otro',
            'U' => 'Indefinido'
        ][$code] ?? 'Indefinido';
    }

    public function startImport(Request $request)
    {
        $data = session('complete_data');

        if (!$data) {
            return response()->json(['error' => 'No hay datos para importar'], 400);
        }

        $flatCandidates = [];
        foreach ($data as $sheetName => $sheetData) {
            if (isset($sheetData['candidates']) && is_array($sheetData['candidates'])) {
                $flatCandidates = array_merge($flatCandidates, $sheetData['candidates']);
            }
        }

        $totalRecords = count($flatCandidates);
        $batchSize = 50;
        $sessionId = uniqid('import_', true);

        session([
            "import_{$sessionId}" => [
                'data' => $flatCandidates,
                'total' => $totalRecords,
                'processed' => 0,
                'batch_size' => $batchSize,
                'current_batch' => 0,
                'success' => 0,
                'errors' => [],
                'status' => 'starting'
            ]
        ]);

        return response()->json([
            'session_id' => $sessionId,
            'total' => $totalRecords,
            'batch_size' => $batchSize,
        ]);
    }

    // **Este es el método que te faltaba, agregado completo:**
    public function importBatch($sessionId)
    {
        $progress = session("import_{$sessionId}");

        if (!$progress) {
            return response()->json(['error' => 'Sesión no encontrada'], 404);
        }

        if ($progress['status'] === 'completed') {
            return response()->json(['status' => 'completed']);
        }

        $batchSize = $progress['batch_size'];
        $currentBatch = $progress['current_batch'];
        $startIndex = $currentBatch * $batchSize;

        $batchData = array_slice($progress['data'], $startIndex, $batchSize);

        $successCount = 0;
        $errors = $progress['errors'];

        foreach ($batchData as $index => $data) {
            try {
                if ($this->importCandidate($data)) {
                    $successCount++;
                }
            } catch (\Exception $e) {
                $errors[] = [
                    'row' => $startIndex + $index + 1,
                    'data' => $data,
                    'error' => $e->getMessage()
                ];
            }
        }

        $progress['processed'] += count($batchData);
        $progress['success'] += $successCount;
        $progress['current_batch']++;
        $progress['errors'] = $errors;

        $progress['status'] = $progress['processed'] >= $progress['total'] ? 'completed' : 'processing';

        session(["import_{$sessionId}" => $progress]);

        return response()->json([
            'status' => $progress['status'],
            'processed' => $progress['processed'],
            'success' => $progress['success'],
            'errors' => count($progress['errors']),
            'percentage' => round(($progress['processed'] / $progress['total']) * 100, 2)
        ]);
    }

    public function getImportProgress($sessionId)
    {
        $progress = session("import_{$sessionId}");

        if (!$progress) {
            return response()->json(['error' => 'Sesión no encontrada'], 404);
        }

        return response()->json([
            'total' => $progress['total'],
            'processed' => $progress['processed'],
            'success' => $progress['success'],
            'errors' => count($progress['errors']),
            'status' => $progress['status'],
            'percentage' => $progress['total'] > 0 ? round(($progress['processed'] / $progress['total']) * 100, 2) : 0
        ]);
    }

    public function clearDatabase(Request $request)
    {
        try {
            $countCandidates = Candidate::count();

            Candidate::truncate();

            return response()->json([
                'success' => true,
                'message' => "Se eliminaron {$countCandidates} candidatos de la base de datos.",
            ]);

        } catch (\Exception $e) {
            Log::error('Error al limpiar candidatos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar candidatos: ' . $e->getMessage()
            ], 500);
        }
    }

    private function createDefaultData()
    {
        Party::firstOrCreate(['name' => 'Sin asignación']);
        Entidad::firstOrCreate(['name' => 'Sin asignación']);
        Nomina::firstOrCreate(['name' => 'Sin asignación']);
        Departamento::firstOrCreate(['name' => 'Sin asignación', 'code' => '00']);
        Municipio::firstOrCreate(['name' => 'Sin asignación']);
        Cargo::firstOrCreate(['name' => 'Sin asignación']);
        Sexo::firstOrCreate(['code' => 'U', 'description' => 'Indefinido']);
    }
}