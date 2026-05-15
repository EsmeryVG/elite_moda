<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variante_productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->string('codigo')->unique()->nullable();
            $table->string('codigo_barras', 100)->unique()->nullable();
            $table->string('descripcion')->nullable();
            $table->decimal('costo', 10, 2)->nullable();
            $table->decimal('precio_venta', 10, 2);
            $table->boolean('es_default')->default(false);            // true = variante de producto simple
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variante_productos');
    }
};
