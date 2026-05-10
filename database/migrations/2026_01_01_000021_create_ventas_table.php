<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('empleado_id')->nullable()->constrained('empleados')->nullOnDelete();
            $table->foreignId('caja_id')->nullable()->constrained('cajas')->nullOnDelete();
            $table->foreignId('usuario_id')->constrained('users');     // Cajero que registra
            $table->string('codigo')->unique()->nullable();
            $table->datetime('fecha');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('descuento_total', 12, 2)->default(0);
            $table->decimal('impuesto', 12, 2)->default(0);           // ITBIS 18%
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('estado', ['pendiente', 'completada', 'anulada'])->default('pendiente');
            $table->string('ncf')->nullable();                         // Número de comprobante fiscal
            $table->foreignId('comprobante_fiscal_id')->nullable()->constrained('comprobantes_fiscales')->nullOnDelete();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->foreignId('variante_producto_id')->constrained('variante_productos');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);       // Precio al momento de vender
            $table->decimal('costo_unitario', 10, 2);        // Costo al momento (para margen)
            $table->decimal('descuento_aplicado', 10, 2)->default(0);
            $table->decimal('subtotal', 12, 2);
            $table->boolean('itbis_aplicado')->default(true);
            $table->timestamps();
        });

        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->foreignId('tipo_pago_id')->constrained('tipos_pago');
            $table->decimal('monto', 12, 2);
            $table->string('referencia')->nullable();        // Nro. cheque, nro. transferencia, etc.
            $table->string('banco')->nullable();             // Para cheques/transferencias
            $table->datetime('fecha');
            $table->enum('estado', ['pendiente', 'confirmado', 'rechazado'])->default('confirmado');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
        Schema::dropIfExists('detalle_ventas');
        Schema::dropIfExists('ventas');
    }
};
