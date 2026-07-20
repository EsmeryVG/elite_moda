<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            CategoriaSeeder::class,
            AtributoSeeder::class,
            SucursalSeeder::class,
            AlmacenSeeder::class,
            ClienteSeeder::class,
            PermisoSeeder::class,
            RolSeeder::class,
            RolPermisoSeeder::class,
            UserSeeder::class,
            TipoPagoSeeder::class,
            ComprobanteFiscalSeeder::class,
            CategoriaGastoSeeder::class,
        ]);
    }
}