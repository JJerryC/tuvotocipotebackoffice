<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class MantenimientosPermisosSeeder extends Seeder
{
    public function run(): void
    {
        $mantenimientos = [
            'users',
            'cargos',
            'mominas',
            'sexos',
            'candidates',
            'partidos',
            'entidades',
            'departamentos',
            'municipios',
        ];

        foreach ($mantenimientos as $modulo) {
            Permission::firstOrCreate(['name' => "view $modulo"]);
            Permission::firstOrCreate(['name' => "edit $modulo"]);
        }
    }
}