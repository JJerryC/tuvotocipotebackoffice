<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Departamento, Municipio, Sexo};

class DefaultDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear departamento "Sin asignación"
        $departamentoSinAsignacion = Departamento::firstOrCreate([
            'code' => '00',
            'name' => 'Sin asignación'
        ]);

        // Crear municipio "Sin asignación" nacional
        Municipio::firstOrCreate([
            'departamento_id' => $departamentoSinAsignacion->id,
            'name' => 'Sin asignación'
        ], [
            'code' => '0000' // Código especial para nacional
        ]);

        // Crear registros de sexo básicos
        Sexo::firstOrCreate([
            'code' => 'H',
            'description' => 'HOMBRE'
        ]);

        Sexo::firstOrCreate([
            'code' => 'M',
            'description' => 'MUJER'
        ]);

        $this->command->info('Datos por defecto creados exitosamente.');
    }
}
