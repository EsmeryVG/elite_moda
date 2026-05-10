<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuentas_por_cobrar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas');
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->string('codigo')->unique()->nullable();
            $table->decimal('monto_total', 12, 2);
            $table->decimal('monto_pagado', 12, 2)->default(0);
            $table->decimal('monto_pendiente', 12, 2);
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento');
            $table->enum('estado', ['pendiente', 'parcial', 'pagada', 'vencida', 'anulada'])->default('pendiente');
            $table->timestamps();
        });

        Schema::create('pagos_credito', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuenta_por_cobrar_id')->constrained('cuentas_por_cobrar')->cascadeOnDelete();
            $table->foreignId('tipo_pago_id')->constrained('tipos_pago');
            $table->foreignId('usuario_id')->constrained('users');
            $table->decimal('monto', 12, 2);
            $table->string('referencia')->nullable();
            $table->date('fecha');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos_credito');
        Schema::dropIfExists('cuentas_por_cobrar');
    }
};
