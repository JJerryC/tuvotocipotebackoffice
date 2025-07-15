<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Role, Permission};

//Asignación de permisos a los roles 
$admin = Role::findByName('admin');
$admin->givePermissionTo([
    'manage candidates',
    'view confidential candidates',
    'export candidates',
    'import candidates'
]);

//Asignación de permiso a usuario
$user = $user::find(1); // o el usuario que quiera 
$user->givePermissionTo('view confidential candidates');


//Crear permisos una vez
Permission::create(['name' => 'manage candidates']);
Permission::create(['name' => 'view confidential candidates']);
Permission::create(['name' => 'export candidates']);
Permission::create(['name' => 'import candidates']);

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
