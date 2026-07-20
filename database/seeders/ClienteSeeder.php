<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $default = Cliente::create([
            'nombre'           => 'Consumidor',
            'apellido'         => 'Final',
            'es_default'       => true,
            'estado'           => true,
            'credito_activo'   => false,
            'limite_credito'   => 0,
            'balance_credito'  => 0,
        ]);
        $default->codigo = 'CLI-' . str_pad($default->id, 3, '0', STR_PAD_LEFT);
        $default->save();
    }
}