<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes_compra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->foreignId('almacen_id')->constrained('almacenes');
            $table->foreignId('usuario_id')->constrained('users');       // Quien creó la orden
            $table->string('codigo')->unique()->nullable();
            $table->date('fecha');
            $table->date('fecha_esperada')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('impuesto', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('estado', ['borrador', 'enviada', 'parcial', 'completada', 'cancelada'])->default('borrador');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('detalle_ordenes_compra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_compra_id')->constrained('ordenes_compra')->cascadeOnDelete();
            $table->foreignId('variante_producto_id')->nullable()->constrained('variante_productos');
            $table->json('caracteristicas_solicitadas')->nullable();  // Para compra por características
            $table->integer('cantidad_solicitada');
            $table->integer('cantidad_recibida')->default(0);
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });

        Schema::create('recepciones_mercancia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_compra_id')->constrained('ordenes_compra');
            $table->foreignId('usuario_id')->constrained('users');
            $table->string('codigo')->unique()->nullable();
            $table->date('fecha');
            $table->enum('tipo', ['completa', 'parcial', 'no_conforme'])->default('completa');
            $table->text('observaciones')->nullable();
            $table->string('motivo_rechazo')->nullable();
            $table->timestamps();
        });

        Schema::create('detalle_recepciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recepcion_id')->constrained('recepciones_mercancia')->cascadeOnDelete();
            $table->foreignId('detalle_orden_id')->constrained('detalle_ordenes_compra');
            $table->foreignId('variante_producto_id')->constrained('variante_productos');
            $table->integer('cantidad_recibida');
            $table->integer('cantidad_aceptada');
            $table->enum('estado_calidad', ['conforme', 'no_conforme'])->default('conforme');
            $table->string('observacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_recepciones');
        Schema::dropIfExists('recepciones_mercancia');
        Schema::dropIfExists('detalle_ordenes_compra');
        Schema::dropIfExists('ordenes_compra');
    }
};
