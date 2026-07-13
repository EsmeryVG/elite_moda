<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropForeign(['caja_id']);
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->renameColumn('caja_id', 'sesion_caja_id');
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->foreign('sesion_caja_id')->references('id')->on('sesiones_caja')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropForeign(['sesion_caja_id']);
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->renameColumn('sesion_caja_id', 'caja_id');
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->foreign('caja_id')->references('id')->on('cajas')->nullOnDelete();
        });
    }
};