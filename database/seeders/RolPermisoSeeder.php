<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rol;
use App\Models\Permiso;

class RolPermisoSeeder extends Seeder
{
    public function run(): void
    {
        $cajero = Rol::where('nombre', 'Cajero')->first();
        $contable = Rol::where('nombre', 'Contable')->first();

        if ($cajero) {
            $cajero->permisos()->sync(Permiso::whereIn('clave', [
                'productos.ver', 'inventario.ver', 'almacenes.ver',
                'ventas.vender', 'ventas.ver',
                'devoluciones.gestionar', 'devoluciones.ver', 'notas_credito.ver',
                'clientes.ver', 'clientes.gestionar',
                'caja.ver', 'caja.abrir',
                'caja_chica.gestionar', 'caja_chica.ver',
            ])->pluck('id'));
        }

        if ($contable) {
            $contable->permisos()->sync(Permiso::whereIn('clave', [
                'productos.ver', 'inventario.ver', 'almacenes.ver',
                'ventas.ver', 'devoluciones.ver', 'notas_credito.ver',
                'clientes.ver', 'cxc.ver', 'cxc.abonar',
                'compras.ver',
                'caja.ver', 'caja.autorizar_descuadre',
                'caja_chica.ver', 'caja_chica.gestionar',
                'gastos.ver', 'gastos.gestionar',
                'empleados.ver', 'nomina.ver', 'nomina.gestionar',
                'reportes.ver',
            ])->pluck('id'));
        }
    }
}