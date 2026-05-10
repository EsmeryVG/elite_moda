<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devoluciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas');
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('usuario_id')->constrained('users');    // Quien procesa
            $table->string('codigo')->unique()->nullable();
            $table->date('fecha');
            $table->decimal('total', 12, 2)->default(0);
            $table->string('motivo')->nullable();
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
            $table->timestamps();
        });

        Schema::create('detalle_devoluciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devolucion_id')->constrained('devoluciones')->cascadeOnDelete();
            $table->foreignId('variante_producto_id')->constrained('variante_productos');
            $table->foreignId('detalle_venta_id')->constrained('detalle_ventas');  // Qué línea de la venta
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });

        Schema::create('notas_credito', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devolucion_id')->nullable()->constrained('devoluciones');
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->string('codigo')->unique()->nullable();
            $table->string('ncf')->nullable();
            $table->decimal('monto_original', 12, 2);
            $table->decimal('monto_disponible', 12, 2);    // Saldo restante para usar
            $table->date('fecha');
            $table->date('fecha_vencimiento')->nullable();
            $table->enum('estado', ['activa', 'agotada', 'vencida', 'anulada'])->default('activa');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas_credito');
        Schema::dropIfExists('detalle_devoluciones');
        Schema::dropIfExists('devoluciones');
    }
};
