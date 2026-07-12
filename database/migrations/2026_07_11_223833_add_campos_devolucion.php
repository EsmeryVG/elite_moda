<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devoluciones', function (Blueprint $table) {
            $table->foreignId('empleado_id')->nullable()->after('usuario_id')->constrained('empleados');
            $table->boolean('requiere_autorizacion')->default(false)->after('estado');
            $table->foreignId('autorizado_por')->nullable()->after('requiere_autorizacion')->constrained('users');
            $table->unsignedInteger('dias_desde_venta')->nullable()->after('autorizado_por');
            $table->boolean('incluye_itbis')->default(true)->after('dias_desde_venta');
        });

        Schema::table('detalle_devoluciones', function (Blueprint $table) {
            $table->enum('motivo', [
                'talla_incorrecta',
                'no_satisfaccion',
                'defecto_fabrica',
                'producto_danado',
                'error_facturacion',
                'otro',
            ])->after('subtotal');

            $table->enum('condicion_inspeccion', ['pendiente', 'conforme', 'no_conforme'])
                ->default('pendiente')->after('motivo');

            $table->decimal('itbis_linea', 10, 2)->default(0)->after('condicion_inspeccion');
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->boolean('permite_devolucion')->default(true)->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('devoluciones', function (Blueprint $table) {
            $table->dropConstrainedForeignId('empleado_id');
            $table->dropConstrainedForeignId('autorizado_por');
            $table->dropColumn(['requiere_autorizacion', 'dias_desde_venta', 'incluye_itbis']);
        });

        Schema::table('detalle_devoluciones', function (Blueprint $table) {
            $table->dropColumn(['motivo', 'condicion_inspeccion', 'itbis_linea']);
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('permite_devolucion');
        });
    }
};