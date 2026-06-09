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
                'nombre'         => 'Regular',
                'descuento_base' => 0,
                'descripcion'    => 'Cliente regular sin descuento especial',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'nombre'         => 'VIP',
                'descuento_base' => 10,
                'descripcion'    => 'Cliente VIP con 10% de descuento',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'nombre'         => 'Mayorista',
                'descuento_base' => 20,
                'descripcion'    => 'Cliente mayorista con 20% de descuento',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ]);
    }
}