<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Ajustar caja_chica: ya no es por sesiones, es saldo corriente ──
        Schema::table('caja_chica', function (Blueprint $table) {
            $table->dropColumn(['fecha_apertura', 'fecha_cierre']);
            $table->renameColumn('monto_asignado', 'monto_base');
            $table->date('fecha_ultima_reposicion')->nullable()->after('monto_disponible');
        });

        Schema::table('caja_chica', function (Blueprint $table) {
            $table->enum('estado', ['activa', 'inactiva'])->default('activa')->change();
        });

        // ── Categorías de gasto ──
        Schema::create('categorias_gasto', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        // ── Gastos ──
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_gasto_id')->constrained('categorias_gasto');
            $table->enum('origen', ['caja_chica', 'directo']);
            $table->string('nombre');
            $table->decimal('monto', 12, 2);
            $table->foreignId('usuario_id')->constrained('users');
            $table->datetime('fecha');
            $table->string('metodo_pago')->nullable(); // solo si origen = directo
            $table->string('referencia')->nullable();  // nro. cheque, etc. — solo si origen = directo
            $table->boolean('es_reposicion_extraordinaria')->default(false);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gastos');
        Schema::dropIfExists('categorias_gasto');

        Schema::table('caja_chica', function (Blueprint $table) {
            $table->dropColumn('fecha_ultima_reposicion');
            $table->renameColumn('monto_base', 'monto_asignado');
            $table->datetime('fecha_apertura')->nullable();
            $table->datetime('fecha_cierre')->nullable();
        });

        Schema::table('caja_chica', function (Blueprint $table) {
            $table->enum('estado', ['abierta', 'cerrada'])->default('cerrada')->change();
        });
    }
};