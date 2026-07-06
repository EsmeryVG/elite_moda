<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoPago;

class TipoPagoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = ['Efectivo', 'Tarjeta', 'Transferencia', 'Cheque'];

        foreach ($tipos as $nombre) {
            TipoPago::create(['nombre' => $nombre, 'estado' => true]);
        }
    }
}