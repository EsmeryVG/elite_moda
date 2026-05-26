<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Atributo;

class AtributoSeeder extends Seeder
{
    public function run(): void
    {
        $atributos = [
            'Talla' => [
                ['valor' => 'XS',   'orden' => 1],
                ['valor' => 'S',    'orden' => 2],
                ['valor' => 'M',    'orden' => 3],
                ['valor' => 'L',    'orden' => 4],
                ['valor' => 'XL',   'orden' => 5],
                ['valor' => 'XXL',  'orden' => 6],
                ['valor' => 'XXXL', 'orden' => 7],
            ],
            'Talla Calzado' => [
                ['valor' => '35', 'orden' => 1],
                ['valor' => '36', 'orden' => 2],
                ['valor' => '37', 'orden' => 3],
                ['valor' => '38', 'orden' => 4],
                ['valor' => '39', 'orden' => 5],
                ['valor' => '40', 'orden' => 6],
                ['valor' => '41', 'orden' => 7],
                ['valor' => '42', 'orden' => 8],
                ['valor' => '43', 'orden' => 9],
                ['valor' => '44', 'orden' => 10],
            ],
            'Color' => [
                ['valor' => 'Blanco',   'orden' => 1],
                ['valor' => 'Negro',    'orden' => 2],
                ['valor' => 'Gris',     'orden' => 3],
                ['valor' => 'Rojo',     'orden' => 4],
                ['valor' => 'Azul',     'orden' => 5],
                ['valor' => 'Azul marino', 'orden' => 6],
                ['valor' => 'Verde',    'orden' => 7],
                ['valor' => 'Amarillo', 'orden' => 8],
                ['valor' => 'Rosado',   'orden' => 9],
                ['valor' => 'Beige',    'orden' => 10],
                ['valor' => 'Café',     'orden' => 11],
            ],
            'Material' => [
                ['valor' => 'Algodón',    'orden' => 1],
                ['valor' => 'Poliéster',  'orden' => 2],
                ['valor' => 'Lino',       'orden' => 3],
                ['valor' => 'Cuero',      'orden' => 4],
                ['valor' => 'Denim',      'orden' => 5],
                ['valor' => 'Lycra',      'orden' => 6],
            ],
            'Aroma' => [
                ['valor' => 'Floral',     'orden' => 1],
                ['valor' => 'Amaderado',  'orden' => 2],
                ['valor' => 'Cítrico',    'orden' => 3],
                ['valor' => 'Oriental',   'orden' => 4],
                ['valor' => 'Fresco',     'orden' => 5],
                ['valor' => 'Dulce',      'orden' => 6],
            ],
            'Volumen' => [
                ['valor' => '30ml',  'orden' => 1],
                ['valor' => '50ml',  'orden' => 2],
                ['valor' => '75ml',  'orden' => 3],
                ['valor' => '100ml', 'orden' => 4],
                ['valor' => '150ml', 'orden' => 5],
                ['valor' => '200ml', 'orden' => 6],
            ],
            'Concentración' => [
                ['valor' => 'Eau de Cologne',  'orden' => 1],
                ['valor' => 'Eau de Toilette', 'orden' => 2],
                ['valor' => 'Eau de Parfum',   'orden' => 3],
                ['valor' => 'Parfum',          'orden' => 4],
            ],
        ];

        foreach ($atributos as $nombre => $valores) {
            $atributo = Atributo::create([
                'nombre' => $nombre,
                'estado' => true,
            ]);

            foreach ($valores as $v) {
                $atributo->valores()->create([
                    'valor'  => $v['valor'],
                    'orden'  => $v['orden'],
                    'estado' => true,
                ]);
            }
        }
    }
}