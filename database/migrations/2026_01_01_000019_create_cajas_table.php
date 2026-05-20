<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->string('nombre');                               // Caja 1, Caja 2
            $table->decimal('monto_apertura', 12, 2)->default(0);
            $table->datetime('fecha_apertura')->nullable();
            $table->datetime('fecha_cierre')->nullable();
            $table->decimal('monto_cierre_esperado', 12, 2)->default(0);
            $table->decimal('monto_cierre_real', 12, 2)->default(0);
            $table->decimal('diferencia', 12, 2)->default(0);
            $table->foreignId('usuario_apertura_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('usuario_cierre_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('estado', ['abierta', 'cerrada'])->default('cerrada');
            $table->timestamps();
        });

        Schema::create('caja_chica', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->string('nombre')->default('Caja Chica');
            $table->decimal('monto_asignado', 12, 2)->default(0);
            $table->decimal('monto_disponible', 12, 2)->default(0);
            $table->datetime('fecha_apertura')->nullable();
            $table->datetime('fecha_cierre')->nullable();
            $table->enum('estado', ['abierta', 'cerrada'])->default('cerrada');
            $table->timestamps();
        });

        Schema::create('movimientos_caja_chica', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_chica_id')->constrained('caja_chica')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('users');
            $table->enum('tipo', ['ingreso', 'egreso']);
            $table->decimal('monto', 12, 2);
            $table->string('concepto');
            $table->timestamp('fecha');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_caja_chica');
        Schema::dropIfExists('caja_chica');
        Schema::dropIfExists('cajas');
    }
};
