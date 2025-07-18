<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Role, Permission};
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        /* -----------------------------------------------------------------
         | Lista global de permisos
         |  – Añadido el que faltaba:  manage candidates
         |  – Incluidos también delete / import por si los usas ya o pronto
         |-----------------------------------------------------------------*/
        $permissions = [

            // ───── CANDIDATOS
            'view candidates',
            'create candidates',
            'edit candidates',
            'delete candidates',
            'import candidates',
            'manage candidates',
            'view confidential candidates',

            // ───── USUARIOS
            'view users',
            'create users',
            'edit users',
            'delete users',

            // ───── ROLES
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'manage roles',

            // ───── REPORTERÍA
            'view reports',

            // ───── CATÁLOGOS (MANTENIMIENTO)
            'view maintenance',
            'create maintenance',
            'edit maintenance',
            'delete maintenance',
            'manage maintenance',
        ];

        /* -----------------------------------------------------------------
         | 1) Crea cada permiso si aún no existe
         |-----------------------------------------------------------------*/
        foreach ($permissions as $permName) {
            Permission::firstOrCreate([
                'name'       => $permName,
                'guard_name' => 'web',   // asegura mismo guard que tu app
            ]);
        }

        /* -----------------------------------------------------------------
         | 2) Asegura el rol admin y le asigna TODOS los permisos
         |-----------------------------------------------------------------*/
        $admin = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web']
        );

        $admin->syncPermissions($permissions);

        /* -----------------------------------------------------------------
         | 3) Asigna rol admin al usuario #1 si aún no lo tiene
         |-----------------------------------------------------------------*/
        $user = User::find(1);

        if ($user && ! $user->hasRole('admin')) {
            $user->assignRole($admin);
        }
    }
}
