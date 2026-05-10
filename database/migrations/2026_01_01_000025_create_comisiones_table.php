<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comisiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('empleados');
            $table->foreignId('venta_id')->constrained('ventas');
            $table->decimal('monto_venta', 12, 2);
            $table->decimal('porcentaje_comision', 5, 2);
            $table->decimal('monto_comision', 12, 2);
            $table->date('fecha');
            $table->enum('estado', ['pendiente', 'pagada'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comisiones');
    }
};
