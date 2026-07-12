<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Configuracion;

class ConfiguracionSeeder extends Seeder
{
    public function run(): void
    {
        $configuraciones = [
            [
                'clave'       => 'itbis_porcentaje',
                'valor'       => '18',
                'descripcion' => 'Porcentaje de ITBIS aplicado en compras y ventas',
            ],
            [
                'clave'       => 'negocio_nombre',
                'valor'       => 'Elite Moda',
                'descripcion' => 'Nombre comercial del negocio',
            ],
            [
                'clave'       => 'negocio_rnc',
                'valor'       => '',
                'descripcion' => 'RNC del negocio (Registro Nacional del Contribuyente)',
            ],
            [
                'clave'       => 'negocio_email',
                'valor'       => '',
                'descripcion' => 'Correo electrónico del negocio',
            ],
            [
                'clave'       => 'factura_mensaje_pie',
                'valor'       => '¡Gracias por su compra!',
                'descripcion' => 'Mensaje al pie de la factura',
            ],
            [
                'clave'       => 'devolucion_dias_limite',
                'valor'       => '30',
                'descripcion' => 'Días límite para devolución sin autorización de administrador ni pérdida de ITBIS',
            ],
            
        ];

        foreach ($configuraciones as $config) {
            Configuracion::updateOrCreate(
                ['clave' => $config['clave']],
                [
                    'valor'       => $config['valor'],
                    'descripcion' => $config['descripcion'],
                ]
            );
        }
    }
}