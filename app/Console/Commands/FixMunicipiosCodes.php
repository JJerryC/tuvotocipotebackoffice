<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Municipio;
use App\Models\Departamento;
use Illuminate\Support\Facades\DB;

class FixMunicipiosCodes extends Command
{
    protected $signature = 'data:fix-municipios-codes';
    protected $description = 'Corregir códigos duplicados de municipios';

    public function handle()
    {
        $this->info('Corrigiendo códigos de municipios...');
        
        // Buscar duplicados
        $duplicates = DB::select('SELECT code, COUNT(*) as count FROM municipios GROUP BY code HAVING COUNT(*) > 1');
        
        if (!empty($duplicates)) {
            $this->error('Duplicados encontrados:');
            foreach ($duplicates as $duplicate) {
                $this->error("Código: {$duplicate->code}, Count: {$duplicate->count}");
            }
            
            // Eliminar todos los municipios para recrearlos con códigos únicos
            $this->info('Eliminando municipios duplicados...');
            Municipio::truncate();
            
            $this->info('Municipios eliminados. Serán recreados automáticamente en la próxima importación.');
        } else {
            $this->info('No hay duplicados.');
        }
        
        // Mostrar municipios restantes
        $municipios = Municipio::with('departamento')->get();
        $this->info("Municipios actuales: {$municipios->count()}");
        
        foreach ($municipios as $municipio) {
            $deptName = $municipio->departamento ? $municipio->departamento->name : 'Sin Dept';
            $this->line("Code: {$municipio->code}, Name: {$municipio->name}, Dept: {$deptName}");
        }
        
        return 0;
    }
}
