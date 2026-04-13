<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->string('codigo')->nullable()->after('id');
        });

        $productos = DB::table('productos')->orderBy('id')->get();

        foreach ($productos as $producto) {
            DB::table('productos')
                ->where('id', $producto->id)
                ->update([
                    'codigo' => 'PROD-' . str_pad($producto->id, 3, '0', STR_PAD_LEFT)
                ]);
        }

        Schema::table('productos', function (Blueprint $table) {
            $table->unique('codigo');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropUnique(['codigo']);
            $table->dropColumn('codigo');
        });
    }
};