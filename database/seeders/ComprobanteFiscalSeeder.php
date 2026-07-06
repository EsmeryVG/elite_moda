<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ComprobanteFiscal;

class ComprobanteFiscalSeeder extends Seeder
{
    public function run(): void
    {
        ComprobanteFiscal::create([
            'tipo_comprobante'  => 'Consumidor Final',
            'prefijo_ncf'       => 'B02',
            'rango_inicio'      => 1,
            'rango_fin'         => 5000,
            'numero_actual'     => 1,
            'fecha_vencimiento' => now()->addYear(),
            'estado'            => true,
        ]);

        ComprobanteFiscal::create([
            'tipo_comprobante'  => 'Crédito Fiscal',
            'prefijo_ncf'       => 'B01',
            'rango_inicio'      => 1,
            'rango_fin'         => 1000,
            'numero_actual'     => 1,
            'fecha_vencimiento' => now()->addYear(),
            'estado'            => true,
        ]);
    }
}