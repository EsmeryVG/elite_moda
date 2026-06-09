<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Models\GrupoCliente;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        // Cliente default — Consumidor Final
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

        $grupoRegular   = GrupoCliente::where('nombre', 'Regular')->first();
        $grupoVip       = GrupoCliente::where('nombre', 'VIP')->first();
        $grupoMayorista = GrupoCliente::where('nombre', 'Mayorista')->first();

        $clientes = [
            [
                'nombre'           => 'Ana',
                'apellido'         => 'García',
                'cedula'           => '00112345678',
                'telefono'         => '8091234567',
                'email'            => 'ana.garcia@email.com',
                'grupo_cliente_id' => $grupoRegular?->id,
                'credito_activo'   => false,
                'limite_credito'   => 0,
            ],
            [
                'nombre'           => 'Juan',
                'apellido'         => 'Martínez',
                'cedula'           => '00112345679',
                'telefono'         => '8091234568',
                'email'            => 'juan.martinez@email.com',
                'grupo_cliente_id' => $grupoVip?->id,
                'credito_activo'   => true,
                'limite_credito'   => 50000,
            ],
            [
                'nombre'           => 'Boutique',
                'apellido'         => 'Elegance',
                'rnc'              => '101234570',
                'telefono'         => '8091234569',
                'email'            => 'boutique@elegance.com',
                'grupo_cliente_id' => $grupoMayorista?->id,
                'credito_activo'   => true,
                'limite_credito'   => 200000,
            ],
        ];

        foreach ($clientes as $data) {
            $cliente = Cliente::create(array_merge($data, [
                'estado'         => true,
                'balance_credito' => 0,
            ]));
            $cliente->codigo = 'CLI-' . str_pad($cliente->id, 3, '0', STR_PAD_LEFT);
            $cliente->save();
        }
    }
}