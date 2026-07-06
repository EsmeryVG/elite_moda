<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GrupoCliente;

class GrupoClienteSeeder extends Seeder
{
    public function run(): void
    {
        GrupoCliente::insert([
            [
                'nombre'      => 'Regular',
                'descripcion' => 'Cliente regular sin descuento especial',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'nombre'      => 'VIP',
                'descripcion' => 'Cliente VIP con beneficios especiales',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'nombre'      => 'Mayorista',
                'descripcion' => 'Cliente mayorista',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}