<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ComprobanteFiscal;

class ComprobanteFiscalSeeder extends Seeder
{
    public function run(): void
    {
        ComprobanteFiscal::updateOrCreate(
            ['prefijo_ncf' => 'B02'],
            [
                'tipo_comprobante'  => 'Consumidor Final',
                'rango_inicio'      => 1,
                'rango_fin'         => 5000,
                'numero_actual'     => 1,
                'fecha_vencimiento' => now()->addYear(),
                'estado'            => true,
            ]
        );

        ComprobanteFiscal::updateOrCreate(
            ['prefijo_ncf' => 'B01'],
            [
                'tipo_comprobante'  => 'Crédito Fiscal',
                'rango_inicio'      => 1,
                'rango_fin'         => 1000,
                'numero_actual'     => 1,
                'fecha_vencimiento' => now()->addYear(),
                'estado'            => true,
            ]
        );

        ComprobanteFiscal::updateOrCreate(
            ['prefijo_ncf' => 'B04'],
            [
                'tipo_comprobante'  => 'Nota de Crédito',
                'rango_inicio'      => 1,
                'rango_fin'         => 1000,
                'numero_actual'     => 1,
                'fecha_vencimiento' => now()->addYear(),
                'estado'            => true,
            ]
        );
    }
}