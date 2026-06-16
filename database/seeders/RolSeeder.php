<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rol;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'nombre'      => 'Administrador',
                'descripcion' => 'Acceso total al sistema. Puede gestionar usuarios, configuración y todas las operaciones.',
            ],
            [
                'nombre'      => 'Cajero',
                'descripcion' => 'Acceso al módulo de ventas, caja y consulta de inventario.',
            ],
            [
                'nombre'      => 'Contable',
                'descripcion' => 'Acceso a reportes fiscales, cuentas por cobrar y cuadre diario.',
            ],
        ];

        foreach ($roles as $data) {
            Rol::create($data);
        }
    }
}