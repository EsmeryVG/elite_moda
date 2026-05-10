<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_pago', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');       // Efectivo, Tarjeta, Cheque, Transferencia, Crédito, Nota de Crédito
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        Schema::create('comprobantes_fiscales', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_comprobante');     // Consumidor Final, Crédito Fiscal, Nota de Crédito, etc.
            $table->string('prefijo_ncf');          // B01, B02, B04, B14, B15
            $table->integer('rango_inicio');
            $table->integer('rango_fin');
            $table->integer('numero_actual');
            $table->date('fecha_vencimiento');
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        Schema::create('descuentos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('tipo');                     // porcentaje, monto_fijo
            $table->decimal('valor', 10, 2);           // 10 = 10% o $10 según tipo
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->boolean('requiere_autorizacion')->default(false);  // Regla negocio #5
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('descuentos');
        Schema::dropIfExists('comprobantes_fiscales');
        Schema::dropIfExists('tipos_pago');
    }
};
