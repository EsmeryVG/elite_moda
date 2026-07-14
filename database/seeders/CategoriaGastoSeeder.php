<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoriaGasto;

class CategoriaGastoSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = ['Transporte', 'Suministros', 'Mantenimiento', 'Mensajería', 'Servicios públicos', 'Alimentación', 'General', 'Otro'];

        foreach ($categorias as $nombre) {
            CategoriaGasto::updateOrCreate(['nombre' => $nombre], ['estado' => true]);
        }
    }
}