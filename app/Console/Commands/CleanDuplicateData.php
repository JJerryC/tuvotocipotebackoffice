<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Municipio;
use App\Models\Departamento;

class CleanDuplicateData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:clean-duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpiar datos duplicados y corregir códigos de municipios';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Limpiando datos duplicados...');

        // Buscar todos los municipios con código '00'
        $duplicateMunicipios = Municipio::where('code', '00')->get();
        
        if ($duplicateMunicipios->count() > 1) {
            // Mantener solo el primero y eliminar el resto
            $keep = $duplicateMunicipios->first();
            $toDelete = $duplicateMunicipios->skip(1);
            
            foreach ($toDelete as $municipio) {
                $this->info("Eliminando municipio duplicado: {$municipio->name} (ID: {$municipio->id})");
                $municipio->delete();
            }
            
            // Actualizar el que se mantuvo con código único
            $keep->update(['code' => '0000']);
            $this->info("Actualizado código del municipio 'Sin asignación' a '0000'");
        } else {
            // Solo actualizar el código si hay uno
            $municipio = $duplicateMunicipios->first();
            if ($municipio) {
                $municipio->update(['code' => '0000']);
                $this->info("Actualizado código del municipio 'Sin asignación' a '0000'");
            }
        }

        $this->info('Limpieza completada exitosamente.');
        return 0;
    }
}
