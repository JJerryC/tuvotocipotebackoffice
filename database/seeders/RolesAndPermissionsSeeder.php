<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Role, Permission};

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        /* 1️⃣ Permiso para módulo candidatos */
        $manageCandidates = Permission::firstOrCreate(['name' => 'manage candidates']);

        /* 2️⃣ Rol Admin (si no existe) */
        $admin = Role::firstOrCreate(['name' => 'admin']);

        /* 3️⃣ Conecta permiso ↔ rol */
        $admin->givePermissionTo($manageCandidates);
        // (opcional) agrega más permisos:
        // $admin->givePermissionTo(['manage users', 'manage roles']);
    }
}
