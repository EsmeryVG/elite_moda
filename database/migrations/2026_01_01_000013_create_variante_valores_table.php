<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variante_valores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variante_producto_id')->constrained('variante_productos')->cascadeOnDelete();
            $table->foreignId('atributo_valor_id')->constrained('atributo_valores')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['variante_producto_id', 'atributo_valor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variante_valores');
    }
};
