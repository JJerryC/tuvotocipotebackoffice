<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /* (opcional) usuarios dummy */
        // User::factory(10)->create();

        /* Usuario de prueba sencillo */
        User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ]);

        /* EJECUTAR seeders oficiales en orden */
        $this->call([
            RolesAndPermissionsSeeder::class, // crea roles & permisos
            AdminUserSeeder::class,           // crea admin@example.com y le asigna rol admin
            BasicDataSeeder::class,
            DefaultDataSeeder::class,
        ]);
    }
}
