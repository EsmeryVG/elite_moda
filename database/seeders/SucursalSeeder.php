<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sucursal;

class SucursalSeeder extends Seeder
{
    public function run(): void
    {
        $sucursal = Sucursal::create([
            'nombre'       => 'Sucursal Principal',
            'direccion'    => 'Av. Winston Churchill, Santo Domingo',
            'telefono'     => '8095550001',
            'es_principal' => true,
            'estado'       => true,
        ]);

        $sucursal->codigo = 'SUC-' . str_pad($sucursal->id, 3, '0', STR_PAD_LEFT);
        $sucursal->save();

        $sucursal2 = Sucursal::create([
            'nombre'       => 'Sucursal Norte',
            'direccion'    => 'Av. Hermanas Mirabal, Santiago',
            'telefono'     => '8095550002',
            'es_principal' => false,
            'estado'       => true,
        ]);

        $sucursal2->codigo = 'SUC-' . str_pad($sucursal2->id, 3, '0', STR_PAD_LEFT);
        $sucursal2->save();
    }
}