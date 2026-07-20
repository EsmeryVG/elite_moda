<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Almacen;
use App\Models\Sucursal;

class AlmacenSeeder extends Seeder
{
    public function run(): void
    {
        $sucursal = Sucursal::where('es_principal', true)->first();

        Almacen::create([
            'sucursal_id' => $sucursal?->id,
            'nombre'      => 'Almacén Principal',
            'tipo'        => 'secundario', 
            'direccion'   => 'Sucursal Elite Moda',
            'estado'      => true,
        ]);
    }
}