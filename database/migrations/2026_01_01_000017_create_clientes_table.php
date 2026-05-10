<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupo_clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');                        // VIP, Regular, Mayorista
            $table->decimal('descuento_base', 5, 2)->default(0);  // % descuento del grupo
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_cliente_id')->nullable()->constrained('grupo_clientes')->nullOnDelete();
            $table->string('codigo')->unique()->nullable();
            $table->string('cedula')->unique()->nullable();
            $table->string('rnc')->unique()->nullable();         // Para clientes empresa
            $table->string('nombre');
            $table->string('apellido')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('direccion')->nullable();
            $table->decimal('limite_credito', 12, 2)->default(0);
            $table->decimal('balance_credito', 12, 2)->default(0);  // Deuda actual
            $table->boolean('credito_activo')->default(false);
            $table->boolean('es_default')->default(false);  // Cliente genérico "Consumidor Final"
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
        Schema::dropIfExists('grupo_clientes');
    }
};
