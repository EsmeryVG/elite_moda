<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LimpiarDatosPrueba extends Command
{
    protected $signature = 'app:limpiar-datos-prueba {--confirmar}';
    protected $description = 'Borra datos transaccionales de prueba, conserva catálogo y configuración';

    // Tablas a vaciar por completo — orden pensado para respetar FKs con checks desactivados
    protected array $tablasABorrar = [
        // Ventas y derivados
        'pagos',
        'detalle_devoluciones',
        'notas_credito',
        'devoluciones',
        'detalle_ventas',
        'ventas',

        // Compras
        'detalle_recepciones',
        'recepciones_mercancia',
        'detalle_ordenes_compra',
        'ordenes_compra',

        // Caja
        'sesiones_caja',

        // Caja chica
        'movimientos_caja_chica',
        'caja_chica',

        // Gastos
        'gastos_fijos',
        'gastos',

        // Inventario
        'movimientos_inventario',
        'detalle_ajustes',
        'ajustes_inventario',

        // Crédito
        'pagos_credito',
        'cuentas_por_cobrar',

        // Nómina
        'comisiones',
        'detalle_nomina',
        'nominas',

        // Personas transaccionales
        'empleados',
        'clientes',
        'grupo_clientes',
        'proveedores',
    ];

    public function handle()
    {
        if (! $this->option('confirmar')) {
            $this->error('Este comando borra datos permanentemente.');
            $this->warn('Corre con --confirmar para ejecutar. Asegúrate de tener un backup (mysqldump) antes.');
            return 1;
        }

        $this->warn('Iniciando limpieza de datos de prueba...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($this->tablasABorrar as $tabla) {
            if (DB::getSchemaBuilder()->hasTable($tabla)) {
                DB::table($tabla)->truncate();
                $this->line("  ✓ {$tabla} vaciada");
            } else {
                $this->warn("  ⚠ {$tabla} no existe, se omite");
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info('Limpieza completada.');
        $this->comment('Recuerda: correr el seeder de stock si quieres reponer inventario base.');

        return 0;
    }
}