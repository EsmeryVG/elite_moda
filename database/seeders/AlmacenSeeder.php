<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Almacen;
use App\Models\Sucursal;

class AlmacenSeeder extends Seeder
{
    public function run(): void
    {
        $sucPrincipal = Sucursal::where('es_principal', true)->first();
        $sucNorte     = Sucursal::where('nombre', 'Sucursal Norte')->first();

        Almacen::create([
            'sucursal_id' => $sucPrincipal?->id,
            'nombre'      => 'Almacén Principal',
            'tipo'        => 'principal',
            'direccion'   => 'Planta baja, Sucursal Principal',
            'estado'      => true,
        ]);

        Almacen::create([
            'sucursal_id' => $sucPrincipal?->id,
            'nombre'      => 'Almacén Secundario',
            'tipo'        => 'secundario',
            'direccion'   => 'Segundo piso, Sucursal Principal',
            'estado'      => true,
        ]);

        Almacen::create([
            'sucursal_id' => $sucNorte?->id,
            'nombre'      => 'Almacén Norte',
            'tipo'        => 'principal',
            'direccion'   => 'Planta baja, Sucursal Norte',
            'estado'      => true,
        ]);
    }
}