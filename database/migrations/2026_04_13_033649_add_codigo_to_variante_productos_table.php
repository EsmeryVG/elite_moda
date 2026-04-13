<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('variante_productos', function (Blueprint $table) {
            $table->string('codigo')->nullable()->after('id');
        });

        $variantes = DB::table('variante_productos')->orderBy('id')->get();

        foreach ($variantes as $variante) {
            DB::table('variante_productos')
                ->where('id', $variante->id)
                ->update([
                    'codigo' => 'VAR-' . str_pad($variante->id, 3, '0', STR_PAD_LEFT)
                ]);
        }

        Schema::table('variante_productos', function (Blueprint $table) {
            $table->unique('codigo');
        });
    }

    public function down(): void
    {
        Schema::table('variante_productos', function (Blueprint $table) {
            $table->dropUnique(['codigo']);
            $table->dropColumn('codigo');
        });
    }
};