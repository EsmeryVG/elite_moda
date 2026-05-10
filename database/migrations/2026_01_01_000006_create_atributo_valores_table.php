<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atributo_valores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atributo_id')->constrained('atributos')->cascadeOnDelete();
            $table->string('valor');               // S, M, L, XL, Rojo, Azul, etc.
            $table->integer('orden')->default(0);   // Para ordenar en dropdowns
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->unique(['atributo_id', 'valor']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atributo_valores');
    }
};
