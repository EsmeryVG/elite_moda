<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variante_producto_id')->constrained('variante_productos');
            $table->foreignId('almacen_id')->constrained('almacenes');
            $table->enum('tipo', [
                'entrada_compra',
                'salida_venta',
                'entrada_devolucion',
                'ajuste_positivo',
                'ajuste_negativo',
                'transferencia_entrada',
                'transferencia_salida',
            ]);
            $table->integer('cantidad');
            $table->string('referencia_tipo')->nullable();   // venta, orden_compra, ajuste, devolucion
            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->string('motivo')->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('fecha');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
