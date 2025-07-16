<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\{Candidate, Party, Nomina, Entidad, Departamento, Municipio, Cargo, Sexo};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CandidateImportController extends Controller
{
    /**
     * Mostrar la página de importación
     */
    public function index()
    {
        // Debug: verificar que el método se está ejecutando
        Log::info('CandidateImportController@index ejecutándose');
        
        // Verificar si la vista existe
        if (!view()->exists('candidates.import')) {
            return response('La vista candidates.import no existe', 404);
        }
        
        // Obtener datos de previsualización si existen en la sesión
        $previewData = session('preview_data', []);
        $previewErrors = session('preview_errors', []);
        
        return view('candidates.import', compact('previewData', 'previewErrors'));
    }

    /**
     * Procesar y previsualizar el archivo Excel (AJAX)
     */
    public function preview(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('excel_file');
            $data = Excel::toArray([], $file);
            
            // Procesar todas las hojas del Excel
            $previewData = [];
            $errors = [];
            $totalPreview = 0;
            
            foreach ($data as $sheetIndex => $sheet) {
                $sheetName = "Hoja " . ($sheetIndex + 1);
                $sheetData = $this->processSheet($sheet, $sheetName, 100); // Solo 100 registros para preview
                
                if (!empty($sheetData['candidates'])) {
                    $previewData[$sheetName] = $sheetData;
                    $totalPreview += count($sheetData['candidates']);
                }
                
                if (!empty($sheetData['errors'])) {
                    $errors[$sheetName] = $sheetData['errors'];
                }
            }

            // Guardar los datos completos en la sesión para la importación final
            $completeData = [];
            foreach ($data as $sheetIndex => $sheet) {
                $sheetName = "Hoja " . ($sheetIndex + 1);
                $completeData[$sheetName] = $this->processSheet($sheet, $sheetName); // Todos los registros
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

    /**
     * Procesar una hoja del Excel
     */
    private function processSheet($sheet, $sheetName, $limit = null)
    {
        $candidates = [];
        $errors = [];
        
        // Buscar la fila de encabezados (que empieza en B2 según el usuario)
        $headerRow = null;
        $dataStartRow = null;
        
        foreach ($sheet as $index => $row) {
            if (isset($row[1]) && $row[1] === 'PARTIDO') { // Columna B (índice 1)
                $headerRow = $row;
                $dataStartRow = $index + 1;
                break;
            }
        }
        
        if (!$headerRow) {
            $errors[] = "No se encontraron encabezados válidos en {$sheetName}";
            return ['candidates' => [], 'errors' => $errors];
        }
        
        // Determinar el límite de filas a procesar
        $endRow = count($sheet);
        if ($limit && $dataStartRow + $limit < $endRow) {
            $endRow = $dataStartRow + $limit;
        }
        
        // Procesar las filas de datos
        for ($i = $dataStartRow; $i < $endRow; $i++) {
            $row = $sheet[$i];
            
            // Saltar filas vacías
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

    /**
     * Normalizar los datos de una fila
     */
    private function normalizeRowData($row, $rowNumber)
    {
        // Mapeo de columnas según el formato proporcionado
        $data = [
            'partido' => $this->cleanText($row[1] ?? ''),           // B
            'entidad' => $this->cleanText($row[2] ?? ''),           // C  
            'nomina' => $this->cleanText($row[3] ?? ''),            // D
            'departamento' => $this->cleanText($row[4] ?? ''),      // E
            'municipio' => $this->cleanText($row[5] ?? ''),         // F
            'cargo' => $this->cleanText($row[6] ?? ''),             // G
            'posicion' => (int)($row[7] ?? 0),                      // H
            'numero_identidad' => $this->cleanText($row[8] ?? ''),  // I
            'primer_nombre' => $this->cleanText($row[9] ?? ''),     // J
            'segundo_nombre' => $this->cleanText($row[10] ?? ''),   // K
            'primer_apellido' => $this->cleanText($row[11] ?? ''),  // L
            'segundo_apellido' => $this->cleanText($row[12] ?? ''), // M
            'sexo' => $this->cleanText($row[13] ?? ''),             // N
            'row_number' => $rowNumber
        ];

        // Validaciones básicas
        if (empty($data['numero_identidad']) || empty($data['primer_nombre'])) {
            throw new \Exception("Datos incompletos: falta número de identidad o nombre");
        }

        return $data;
    }

    /**
     * Limpiar texto
     */
    private function cleanText($text)
    {
        return trim(preg_replace('/\s+/', ' ', $text));
    }

    /**
     * Confirmar e importar los datos
     */
    public function import(Request $request)
    {
        $completeData = session('complete_data'); // Usar datos completos, no solo preview
        
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
                    if ($result) {
                        $importedCount++;
                    } else {
                        $skippedCount++;
                    }
                }
            }
            
            DB::commit();
            
            // Limpiar la sesión
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

    /**
     * Importar un candidato individual
     */
    private function importCandidate($data)
    {
        // Buscar o crear las entidades relacionadas
        $party = Party::firstOrCreate(['name' => $data['partido']]);
        
        $entidad = Entidad::firstOrCreate(['name' => $data['entidad']]);
        
        $nomina = Nomina::firstOrCreate([
            'entidad_id' => $entidad->id,
            'name' => $data['nomina']
        ]);
        
        $departamento = null;
        $municipio = null;
        
        if (!empty($data['departamento'])) {
            $departamento = Departamento::firstOrCreate([
                'name' => $data['departamento'],
                'code' => $this->extractDepartmentCode($data['departamento'])
            ]);
            
            if (!empty($data['municipio'])) {
                // Extraer código del municipio
                $municipioCode = $this->extractMunicipalityCode($data['municipio']);
                
                // Crear código único combinando departamento + municipio
                $uniqueCode = $departamento->code . $municipioCode;
                
                $municipio = Municipio::firstOrCreate([
                    'departamento_id' => $departamento->id,
                    'name' => $data['municipio']
                ], [
                    'code' => $uniqueCode // Código único por departamento
                ]);
            } else {
                // Crear municipio "Sin asignación" para el departamento específico
                // Usar un código único basado en el departamento
                $municipioCode = $departamento->code . '00'; // Ej: 0100, 0200, etc.
                $municipio = Municipio::firstOrCreate([
                    'departamento_id' => $departamento->id,
                    'name' => 'Sin asignación'
                ], [
                    'code' => $municipioCode
                ]);
            }
        } else {
            // Crear departamento y municipio "Sin asignación" para candidatos nacionales
            $departamento = Departamento::firstOrCreate([
                'name' => 'Sin asignación',
                'code' => '00'
            ]);
            
            $municipio = Municipio::firstOrCreate([
                'departamento_id' => $departamento->id,
                'name' => 'Sin asignación'
            ], [
                'code' => '0000' // Código especial para nacional
            ]);
        }
        
        $cargo = Cargo::firstOrCreate(['name' => $data['cargo']]);
        
        // Obtener sexo con mapeo inteligente
        $sexo = $this->getSexo($data['sexo']);

        // Verificar si el candidato ya existe
        $existingCandidate = Candidate::where('numero_identidad', $data['numero_identidad'])->first();
        
        if ($existingCandidate) {
            return false; // Ya existe, omitir
        }

        // Crear el candidato
        Candidate::create([
            'party_id' => $party->id,
            'nomina_id' => $nomina->id,
            'municipio_id' => $municipio->id, // Siempre tendrá un valor ahora
            'cargo_id' => $cargo->id,
            'sexo_id' => $sexo->id,
            'posicion' => $data['posicion'],
            'numero_identidad' => $data['numero_identidad'],
            'primer_nombre' => $data['primer_nombre'],
            'segundo_nombre' => $data['segundo_nombre'],
            'primer_apellido' => $data['primer_apellido'],
            'segundo_apellido' => $data['segundo_apellido'],
        ]);

        return true;
    }

    /**
     * Obtener o crear sexo con mapeo inteligente
     */
    private function getSexo($sexoText)
    {
        $sexoText = strtoupper(trim($sexoText));
        
        // Mapeo de términos comunes a códigos específicos (estándar internacional)
        $sexoMap = [
            'MASCULINO' => ['code' => 'M', 'description' => 'Masculino'],
            'HOMBRE' => ['code' => 'M', 'description' => 'Masculino'], // Mismo código que masculino
            'FEMENINO' => ['code' => 'F', 'description' => 'Femenino'],
            'MUJER' => ['code' => 'F', 'description' => 'Femenino'], // Mismo código que femenino
            'OTRO' => ['code' => 'O', 'description' => 'Otro'],
            'NO BINARIO' => ['code' => 'O', 'description' => 'Otro'], // Agrupado en "Otro"
        ];
        
        // Buscar coincidencia exacta
        if (isset($sexoMap[$sexoText])) {
            return Sexo::firstOrCreate([
                'code' => $sexoMap[$sexoText]['code']
            ], [
                'description' => $sexoMap[$sexoText]['description']
            ]);
        }
        
        // Si no hay coincidencia, usar lógica de primera letra pero con descripción original
        $code = substr($sexoText, 0, 1);
        
        // Verificar si ya existe ese código
        $existing = Sexo::where('code', $code)->first();
        if ($existing) {
            // Si existe, usar un código modificado
            $code = $code . '1';
        }
        
        return Sexo::firstOrCreate([
            'code' => $code,
            'description' => ucfirst(strtolower($sexoText))
        ]);
    }

    /**
     * Extraer código de departamento del nombre
     */
    private function extractDepartmentCode($name)
    {
        // Buscar patrón como "01 ATLANTIDA"
        if (preg_match('/^(\d{2})\s+/', $name, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Extraer código de municipio del nombre
     */
    private function extractMunicipalityCode($name)
    {
        if (empty($name)) {
            return '00'; // Código por defecto
        }
        
        // Limpiar el texto
        $name = trim($name);
        
        // Buscar patrón: código numérico al inicio seguido de espacio y nombre
        // Ejemplos: "01 LA CEIBA", "02 TELA", "15 DISTRITO CENTRAL"
        if (preg_match('/^(\d{1,3})\s+/', $name, $matches)) {
            return str_pad($matches[1], 2, '0', STR_PAD_LEFT); // Asegurar 2 dígitos mínimo
        }
        
        // Si no encuentra patrón numérico, generar código basado en hash del nombre
        // Esto asegura consistencia para el mismo nombre
        $hash = substr(md5($name), 0, 2);
        return strtoupper($hash);
    }

    /**
     * Iniciar importación con progreso
     */
    public function startImport(Request $request)
    {
        $data = session('complete_data'); // Cambiar de 'normalized_data' a 'complete_data'
        if (!$data) {
            return response()->json(['error' => 'No hay datos para importar'], 400);
        }

        $sessionId = uniqid('import_', true);
        $totalRecords = count($data);
        $batchSize = 50; // Procesar 50 registros por lote

        // Guardar información de la importación en sesión
        session([
            "import_{$sessionId}" => [
                'data' => $data,
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
            'batch_size' => $batchSize
        ]);
    }

    /**
     * Obtener progreso de importación
     */
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

    /**
     * Importar un lote de registros
     */
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
        
        // Obtener el lote de datos a procesar
        $batchData = array_slice($progress['data'], $startIndex, $batchSize);
        
        $successCount = 0;
        $errors = $progress['errors'];

        // Procesar cada registro del lote
        foreach ($batchData as $index => $data) {
            try {
                if ($this->createCandidate($data)) {
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

        // Actualizar progreso
        $progress['processed'] += count($batchData);
        $progress['success'] += $successCount;
        $progress['current_batch']++;
        $progress['errors'] = $errors;
        
        // Verificar si terminamos
        if ($progress['processed'] >= $progress['total']) {
            $progress['status'] = 'completed';
        } else {
            $progress['status'] = 'processing';
        }

        // Guardar progreso actualizado
        session(["import_{$sessionId}" => $progress]);

        return response()->json([
            'status' => $progress['status'],
            'processed' => $progress['processed'],
            'success' => $progress['success'],
            'errors' => count($progress['errors']),
            'percentage' => round(($progress['processed'] / $progress['total']) * 100, 2)
        ]);
    }

    /**
     * Limpiar tablas de la base de datos
     */
    public function clearDatabase(Request $request)
    {
        try {
            DB::beginTransaction();
            
            // Obtener conteos antes de limpiar
            $counts = [
                'candidates' => Candidate::count(),
                'parties' => Party::count(),
                'nominas' => Nomina::count(), 
                'entidades' => Entidad::count(),
                'departamentos' => Departamento::count(),
                'municipios' => Municipio::count(),
                'cargos' => Cargo::count()
            ];
            
            // Deshabilitar verificación de claves foráneas temporalmente
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // Limpiar tablas en orden específico
            // Primero la tabla principal que depende de otras
            DB::table('candidates')->delete();
            
            // Luego las tablas de catálogo
            DB::table('parties')->delete();
            DB::table('nominas')->delete();
            DB::table('entidades')->delete();
            DB::table('cargos')->delete();
            DB::table('municipios')->delete();
            DB::table('departamentos')->delete();
            
            // Reestablecer AUTO_INCREMENT a 1 para cada tabla
            DB::statement('ALTER TABLE candidates AUTO_INCREMENT = 1;');
            DB::statement('ALTER TABLE parties AUTO_INCREMENT = 1;');
            DB::statement('ALTER TABLE nominas AUTO_INCREMENT = 1;');
            DB::statement('ALTER TABLE entidades AUTO_INCREMENT = 1;');
            DB::statement('ALTER TABLE cargos AUTO_INCREMENT = 1;');
            DB::statement('ALTER TABLE municipios AUTO_INCREMENT = 1;');
            DB::statement('ALTER TABLE departamentos AUTO_INCREMENT = 1;');
            
            // Rehabilitar verificación de claves foráneas
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            // Recrear datos básicos
            $this->createDefaultData();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Base de datos limpiada exitosamente',
                'cleared_counts' => $counts
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Asegurar que las claves foráneas estén habilitadas aunque haya error
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            } catch (\Exception $fkError) {
                // Ignorar error al reestablecer FK
            }
            
            Log::error('Error limpiando base de datos: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar la base de datos: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Crear datos por defecto después de limpiar
     */
    private function createDefaultData()
    {
        // Crear departamento "Sin asignación"
        $defaultDept = Departamento::create([
            'name' => 'Sin asignación',
            'code' => '00'
        ]);
        
        // Crear municipio "Sin asignación"
        Municipio::create([
            'departamento_id' => $defaultDept->id,
            'name' => 'Sin asignación',
            'code' => '0000'
        ]);
    }
}
