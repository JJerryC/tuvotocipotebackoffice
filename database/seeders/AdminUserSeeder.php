<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        /* 1️⃣ Rol “admin” (o el nombre que uses) */
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        /* 2️⃣ Usuario administrador por defecto */
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Administrador',
                'password' => Hash::make('Admin123*'), // ← cámbialo luego
            ]
        );

        /* 3️⃣ Asignar rol */
        if (! $user->hasRole($adminRole)) {
            $user->assignRole($adminRole);
        }
    }
}
