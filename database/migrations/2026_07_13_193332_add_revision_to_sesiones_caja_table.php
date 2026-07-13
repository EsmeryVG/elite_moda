<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sesiones_caja', function (Blueprint $table) {
            $table->foreignId('revisada_por')->nullable()->after('observacion_diferencia')
                  ->constrained('users')->nullOnDelete();
            $table->datetime('revisada_en')->nullable()->after('revisada_por');
        });
    }

    public function down(): void
    {
        Schema::table('sesiones_caja', function (Blueprint $table) {
            $table->dropConstrainedForeignId('revisada_por');
            $table->dropColumn('revisada_en');
        });
    }
};