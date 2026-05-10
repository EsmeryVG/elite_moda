<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variante_producto_id')->constrained('variante_productos')->cascadeOnDelete();
            $table->foreignId('almacen_id')->constrained('almacenes')->cascadeOnDelete();
            $table->integer('cantidad_disponible')->default(0);
            $table->integer('cantidad_vendida')->default(0);
            $table->integer('cantidad_devuelta')->default(0);
            $table->integer('cantidad_mermada')->default(0);
            $table->integer('stock_minimo')->default(5);    // Alerta de bajo stock
            $table->timestamps();

            $table->unique(['variante_producto_id', 'almacen_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
