<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proveedor;

class ProveedorSeeder extends Seeder
{
    public function run(): void
    {
        $proveedores = [
            [
                'nombre'          => 'Distribuidora Fashion RD',
                'rnc'             => '101234567',
                'telefono'        => '8095550010',
                'email'           => 'ventas@fashionrd.com',
                'direccion'       => 'Zona Industrial, Santo Domingo',
                'contacto_nombre' => 'María Pérez',
            ],
            [
                'nombre'          => 'Importadora Textil Nacional',
                'rnc'             => '101234568',
                'telefono'        => '8095550011',
                'email'           => 'contacto@textilnacional.com',
                'direccion'       => 'Av. Duarte, Santo Domingo',
                'contacto_nombre' => 'Carlos Rodríguez',
            ],
            [
                'nombre'          => 'Calzados y Accesorios del Caribe',
                'rnc'             => '101234569',
                'telefono'        => '8095550012',
                'email'           => 'info@calzadoscaribe.com',
                'direccion'       => 'Calle El Conde, Santo Domingo',
                'contacto_nombre' => 'Ana Martínez',
            ],
        ];

        foreach ($proveedores as $data) {
            $proveedor = Proveedor::create(array_merge($data, ['estado' => true]));
            $proveedor->codigo = 'PROV-' . str_pad($proveedor->id, 3, '0', STR_PAD_LEFT);
            $proveedor->save();
        }
    }
}