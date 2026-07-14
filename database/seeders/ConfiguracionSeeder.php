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
            [
                'clave'       => 'caja_monto_minimo_apertura',
                'valor'       => '500',
                'descripcion' => 'Monto mínimo de fondo de caja requerido para abrir una sesión',
            ],
            [
                'clave'       => 'horario_apertura',
                'valor'       => '09:00',
                'descripcion' => 'Hora de apertura de la tienda (informativo)',
            ],
            [
                'clave'       => 'horario_cierre',
                'valor'       => '19:00',
                'descripcion' => 'Hora de cierre de la tienda (usada para recordatorio de cierre de caja en el TPV)',
            ],
            [
            'clave'       => 'caja_chica_monto_base',
            'valor'       => '2000',
            'descripcion' => 'Monto al que se repone la caja chica en una reposición normal',
            ],
          [
            'clave'       => 'caja_chica_dias_reposicion',
            'valor'       => '1',
            'descripcion' => 'Cada cuántos días se permite una reposición normal de caja chica',
            ],
            [
    'clave'       => 'credito_dias_vencimiento',
    'valor'       => '30',
    'descripcion' => 'Días de plazo por defecto para el vencimiento de cuentas por cobrar',
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