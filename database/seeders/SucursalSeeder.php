<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sucursal;

class SucursalSeeder extends Seeder
{
    public function run(): void
    {
        $sucursal = Sucursal::create([
            'nombre'       => 'Elite Moda', // ← ajustar con nombre real
            'direccion'    => 'Avenida José Horacio Rodríguez, La Vega', // ← confirmar dirección real
            'telefono'     => '8095732400', // ← confirmar teléfono real
            'es_principal' => true,
            'estado'       => true,
        ]);

        $sucursal->codigo = 'SUC-' . str_pad($sucursal->id, 3, '0', STR_PAD_LEFT);
        $sucursal->save();
    }
}