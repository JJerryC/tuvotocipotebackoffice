<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Crear permisos (si no existen)
        $permissions = [
            'manage candidates',
            'view confidential candidates',
            'export candidates',
            'import candidates'
        ];

        foreach ($permissions as $permName) {
            Permission::firstOrCreate(['name' => $permName]);
        }

        // Crear rol admin (si no existe)
        $admin = Role::firstOrCreate(['name' => 'admin']);

        // Asignar todos los permisos al rol admin
        $admin->syncPermissions($permissions);

        // Asignar permiso específico a usuario (ID 1 por ejemplo)
        $user = User::find(1);
        if ($user) {
            $user->givePermissionTo('view confidential candidates');
        }
    }
}
