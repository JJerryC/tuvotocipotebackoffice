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
    $permissions = [
        // CANDIDATOS
        'view candidates',
        'create candidates',
        'edit candidates',
        'view confidential candidates',

        // USUARIOS
        'view users',
        'create users',
        'edit users',

        // ROLES
        'view roles',
        'create roles',
        'edit roles',

        // REPORTERÍA
        'view reports',

        // CATÁLOGOS (MANTENIMIENTO)
        'view maintenance',
        'create maintenance',
        'edit maintenance',
    ];

    foreach ($permissions as $permName) {
        Permission::firstOrCreate(['name' => $permName]);
    }

    // Crear rol admin (si no existe)
    $admin = Role::firstOrCreate(['name' => 'admin']);
    $admin->syncPermissions($permissions); // Asignar todos los permisos al admin

    // Asignar admin al usuario 1 (si no lo tiene)
    $user = \App\Models\User::find(1);
    if ($user && !$user->hasRole('admin')) {
        $user->assignRole($admin);
    }
}
}
