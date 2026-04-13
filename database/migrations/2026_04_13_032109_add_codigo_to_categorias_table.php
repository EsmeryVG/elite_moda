<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->string('codigo')->nullable()->unique()->after('id');
        });

            $categorias = DB::table('categorias')->orderBy('id')->get();
    
            foreach ($categorias as $categoria) {
                DB::table('categorias')
                    ->where('id', $categoria->id)
                    ->update([
                        'codigo' => 'CAT-' . str_pad($categoria->id, 3, '0', STR_PAD_LEFT)
                    ]);
            }
    }

    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn('codigo');
        });
    }
};