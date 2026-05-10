<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ajustes_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('almacen_id')->constrained('almacenes');
            $table->foreignId('usuario_id')->constrained('users');
            $table->enum('tipo', ['conteo_fisico', 'merma', 'daño', 'correccion', 'otro']);
            $table->string('motivo')->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->timestamp('fecha');
            $table->timestamps();
        });

        Schema::create('detalle_ajustes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ajuste_inventario_id')->constrained('ajustes_inventario')->cascadeOnDelete();
            $table->foreignId('variante_producto_id')->constrained('variante_productos');
            $table->integer('cantidad_sistema');    // Lo que dice el sistema
            $table->integer('cantidad_real');       // Lo que se contó
            $table->integer('diferencia');           // real - sistema
            $table->string('observacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_ajustes');
        Schema::dropIfExists('ajustes_inventario');
    }
};
