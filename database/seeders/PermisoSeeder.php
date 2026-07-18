<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permiso;

class PermisoSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            // Productos
            ['clave' => 'productos.ver', 'nombre' => 'Ver productos', 'modulo' => 'Productos'],
            ['clave' => 'productos.gestionar', 'nombre' => 'Crear/editar/desactivar productos', 'modulo' => 'Productos'],

            // Inventario
            ['clave' => 'inventario.ver', 'nombre' => 'Ver stock y movimientos', 'modulo' => 'Inventario'],
            ['clave' => 'inventario.gestionar', 'nombre' => 'Hacer ajustes de inventario', 'modulo' => 'Inventario'],
            ['clave' => 'almacenes.ver', 'nombre' => 'Ver almacenes', 'modulo' => 'Inventario'],

            // Ventas
            ['clave' => 'ventas.vender', 'nombre' => 'Usar el POS / registrar ventas', 'modulo' => 'Ventas'],
            ['clave' => 'ventas.ver', 'nombre' => 'Ver historial de ventas', 'modulo' => 'Ventas'],
            ['clave' => 'ventas.anular', 'nombre' => 'Anular ventas', 'modulo' => 'Ventas'],
            ['clave' => 'devoluciones.gestionar', 'nombre' => 'Registrar devoluciones', 'modulo' => 'Ventas'],
            ['clave' => 'devoluciones.ver', 'nombre' => 'Ver devoluciones', 'modulo' => 'Ventas'],
            ['clave' => 'notas_credito.ver', 'nombre' => 'Ver notas de crédito', 'modulo' => 'Ventas'],

            // Clientes
            ['clave' => 'clientes.ver', 'nombre' => 'Ver clientes', 'modulo' => 'Clientes'],
            ['clave' => 'clientes.gestionar', 'nombre' => 'Crear/editar clientes', 'modulo' => 'Clientes'],
            ['clave' => 'cxc.ver', 'nombre' => 'Ver cuentas por cobrar', 'modulo' => 'Clientes'],
            ['clave' => 'cxc.abonar', 'nombre' => 'Registrar abonos', 'modulo' => 'Clientes'],

            // Compras
            ['clave' => 'compras.ver', 'nombre' => 'Ver órdenes/proveedores', 'modulo' => 'Compras'],
            ['clave' => 'compras.gestionar', 'nombre' => 'Crear órdenes de compra', 'modulo' => 'Compras'],

            // Caja
            ['clave' => 'caja.ver', 'nombre' => 'Ver sesiones/cuadre de caja', 'modulo' => 'Caja'],
            ['clave' => 'caja.abrir', 'nombre' => 'Abrir sesiones de caja', 'modulo' => 'Caja'],
            ['clave' => 'caja_chica.gestionar', 'nombre' => 'Registrar gastos de caja chica', 'modulo' => 'Caja'],
            ['clave' => 'caja_chica.ver', 'nombre' => 'Ver caja chica', 'modulo' => 'Caja'],
            ['clave' => 'gastos.gestionar', 'nombre' => 'Registrar gastos directos', 'modulo' => 'Caja'],
            ['clave' => 'gastos.ver', 'nombre' => 'Ver gastos', 'modulo' => 'Caja'],
            ['clave' => 'caja.autorizar_descuadre', 'nombre' => 'Autorizar/revisar descuadre de caja', 'modulo' => 'Caja'],

            // Personal
            ['clave' => 'empleados.ver', 'nombre' => 'Ver empleados', 'modulo' => 'Personal'],
            ['clave' => 'nomina.ver', 'nombre' => 'Ver nómina', 'modulo' => 'Personal'],
            ['clave' => 'nomina.gestionar', 'nombre' => 'Generar/pagar nómina', 'modulo' => 'Personal'],

            // Reportes
            ['clave' => 'reportes.ver', 'nombre' => 'Ver reportes', 'modulo' => 'Reportes'],

            // Sistema (solo admin en la práctica, pero queda flexible)
            ['clave' => 'configuracion.gestionar', 'nombre' => 'Editar configuración del sistema', 'modulo' => 'Sistema'],
            ['clave' => 'usuarios.gestionar', 'nombre' => 'Gestionar usuarios y roles', 'modulo' => 'Sistema'],
        ];

        foreach ($permisos as $p) {
            Permiso::updateOrCreate(['clave' => $p['clave']], $p);
        }
    }
}