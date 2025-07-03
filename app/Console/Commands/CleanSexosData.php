<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sexo;
use Illuminate\Support\Facades\DB;

class CleanSexosData extends Command
{
    protected $signature = 'data:clean-sexos';
    protected $description = 'Limpiar datos duplicados de sexos';

    public function handle()
    {
        $this->info('Revisando tabla sexos...');
        
        // Mostrar todos los registros actuales
        $sexos = Sexo::all();
        $this->info("Registros actuales en sexos:");
        foreach ($sexos as $sexo) {
            $this->line("ID: {$sexo->id}, Code: {$sexo->code}, Description: {$sexo->description}");
        }
        
        // Buscar duplicados por código
        $duplicates = DB::select('SELECT code, COUNT(*) as count FROM sexos GROUP BY code HAVING COUNT(*) > 1');
        
        if (!empty($duplicates)) {
            $this->error('¡Duplicados encontrados!');
            foreach ($duplicates as $duplicate) {
                $this->error("Código: {$duplicate->code}, Count: {$duplicate->count}");
                
                // Eliminar duplicados, mantener solo el primero
                $sexosWithCode = Sexo::where('code', $duplicate->code)->get();
                $keep = $sexosWithCode->first();
                $toDelete = $sexosWithCode->skip(1);
                
                foreach ($toDelete as $sexo) {
                    $this->info("Eliminando duplicado: ID {$sexo->id}, Code: {$sexo->code}");
                    $sexo->delete();
                }
            }
        } else {
            $this->info('No hay duplicados en sexos.');
        }
        
        $this->info('Limpieza completada.');
        return 0;
    }
}
