<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gastos_fijos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('categoria_gasto_id')->constrained('categorias_gasto');
            $table->decimal('monto_sugerido', 12, 2)->default(0);
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        Schema::table('gastos', function (Blueprint $table) {
            $table->foreignId('gasto_fijo_id')->nullable()->after('categoria_gasto_id')
                  ->constrained('gastos_fijos')->nullOnDelete();
            $table->string('periodo')->nullable()->after('fecha');
        });
    }

    public function down(): void
    {
        Schema::table('gastos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('gasto_fijo_id');
            $table->dropColumn('periodo');
        });

        Schema::dropIfExists('gastos_fijos');
    }
};