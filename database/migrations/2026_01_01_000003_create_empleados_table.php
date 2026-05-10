<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('codigo')->unique()->nullable();
            $table->string('cedula')->unique();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->string('cargo')->nullable();           // Cajero, Vendedor, etc.
            $table->decimal('salario_base', 10, 2)->default(0);
            $table->decimal('comision_porcentaje', 5, 2)->default(0);  // % de comisión por venta
            $table->date('fecha_ingreso')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
