<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nominas', function (Blueprint $table) {
            $table->id();
            $table->date('periodo_inicio');
            $table->date('periodo_fin');
            $table->date('fecha_pago');
            $table->decimal('total_general', 12, 2)->default(0);
            $table->enum('estado', ['pendiente', 'pagada'])->default('pendiente');
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamps();

            $table->unique(['periodo_inicio', 'periodo_fin']);
        });

        Schema::create('detalle_nomina', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomina_id')->constrained('nominas')->cascadeOnDelete();
            $table->foreignId('empleado_id')->constrained('empleados');
            $table->decimal('salario_base', 10, 2);
            $table->decimal('total_comisiones', 10, 2)->default(0);
            $table->decimal('total_pagar', 10, 2);
            $table->timestamps();
        });

        Schema::table('comisiones', function (Blueprint $table) {
            $table->foreignId('detalle_nomina_id')->nullable()->after('estado')
                  ->constrained('detalle_nomina')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('comisiones', function (Blueprint $table) {
            $table->dropConstrainedForeignId('detalle_nomina_id');
        });

        Schema::dropIfExists('detalle_nomina');
        Schema::dropIfExists('nominas');
    }
};