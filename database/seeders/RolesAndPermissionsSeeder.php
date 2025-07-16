<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Role, Permission};

//Asignación de permisos a los roles 
$admin = Role::findByName('admin');
$admin = Role::findOrCreate('admin');
$admin->givePermissionTo([
    'manage candidates',
    'view confidential candidates',
    'export candidates',
    'import candidates'
]);
$admin->givePermissionTo(['view candidates', 'create candidates', 'edit candidates']);
$admin = Role::findOrCreate('admin');
$admin->givePermissionTo(['view users', 'create users', 'edit users']);
//Mantenimeinto de permisos a los roles
$admin = Role::findOrCreate('admin');

$admin->givePermissionTo([
    'viww users', 'edit users',
    'view cargos', 'edit cargos',
    'view nominas', 'edit nominas',
    'view sexos', 'edit sexos',
    'view candidates', 'edit candidates',
    'view partidos', 'edit partidos',
    'view entidades', 'edit entidades',
    'view departamentos', 'edit departamentos',
    'view municipios', 'edit municipios',
]);

//Asignación de permiso a usuario

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view users',
            'create users',
            'edit users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}

$user = $user::find(1); // o el usuario que quiera 
$user->givePermissionTo('view candidates');
$user->givePermissionTo('view users');

// Asignar un rol al usuario
$user = \App\Models\User::find(1);
$user->assignRole('admin');



//Crear permisos una vez
Permission::create(['name' => 'manage candidates']);
Permission::create(['name' => 'view confidential candidates']);
Permission::create(['name' => 'export candidates']);
Permission::create(['name' => 'import candidates']);

//Permisos crear ver y editar candidatos 
Permission::create(['name' => 'view candidates']);
Permission::create(['name' => 'create candidates']);
Permission::create(['name' => 'edit candidates']);

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
