<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sexo;
use Illuminate\Support\Facades\DB;

class StandardizeSexos extends Command
{
    protected $signature = 'data:standardize-sexos';
    protected $description = 'Estandarizar códigos de sexos a F/M/O';

    public function handle()
    {
        $this->info('Estandarizando códigos de sexos...');
        
        // Actualizar todos los masculinos a 'M'
        $masculinos = Sexo::whereIn('code', ['H'])->get();
        foreach ($masculinos as $sexo) {
            if ($sexo->code === 'H') {
                // Verificar si ya existe un registro con código 'M'
                $existing = Sexo::where('code', 'M')->where('id', '!=', $sexo->id)->first();
                if ($existing) {
                    // Si existe, eliminar el duplicado
                    $this->info("Eliminando duplicado: {$sexo->description} (ID: {$sexo->id})");
                    $sexo->delete();
                } else {
                    // Si no existe, actualizar el código
                    $sexo->update(['code' => 'M', 'description' => 'Masculino']);
                    $this->info("Actualizado: {$sexo->description} -> código M");
                }
            }
        }
        
        // Mostrar estado final
        $this->info("\nEstado final de sexos:");
        $sexos = Sexo::all();
        foreach ($sexos as $sexo) {
            $this->line("ID: {$sexo->id}, Code: {$sexo->code}, Description: {$sexo->description}");
        }
        
        $this->info('Estandarización completada.');
        return 0;
    }
}
