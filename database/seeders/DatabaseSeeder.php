<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            CategoriaSeeder::class,
            AtributoSeeder::class,  
            ProductoSeeder::class,
            SucursalSeeder::class,
            AlmacenSeeder::class,
            GrupoClienteSeeder::class,
            ClienteSeeder::class,
            ProveedorSeeder::class,
            RolSeeder::class,
            UserSeeder::class,
            EmpleadoSeeder::class,
        ]);
    }
}
