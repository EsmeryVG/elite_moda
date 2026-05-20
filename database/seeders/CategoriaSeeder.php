<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            [
                'nombre'      => 'Ropa de Mujer',
                'descripcion' => 'Blusas, vestidos, faldas, pantalones y más.',
                'estado'      => true,
            ],
            [
                'nombre'      => 'Ropa de Hombre',
                'descripcion' => 'Camisas, pantalones, chaquetas y accesorios.',
                'estado'      => true,
            ],
            [
                'nombre'      => 'Ropa de Niños',
                'descripcion' => 'Ropa infantil para niños y niñas.',
                'estado'      => true,
            ],
            [
                'nombre'      => 'Calzado de Mujer',
                'descripcion' => 'Zapatos, sandalias, botas y tenis para dama.',
                'estado'      => true,
            ],
            [
                'nombre'      => 'Calzado de Hombre',
                'descripcion' => 'Zapatos formales, casuales y deportivos.',
                'estado'      => true,
            ],
            [
                'nombre'      => 'Perfumería',
                'descripcion' => 'Perfumes y fragancias para hombre y mujer.',
                'estado'      => true,
            ],
            [
                'nombre'      => 'Cosméticos',
                'descripcion' => 'Maquillaje, cremas, tratamientos y más.',
                'estado'      => true,
            ],
            [
                'nombre'      => 'Accesorios',
                'descripcion' => 'Bolsos, cinturones, joyería y bisutería.',
                'estado'      => true,
            ],
            [
                'nombre'      => 'Ropa Deportiva',
                'descripcion' => 'Ropa y calzado para actividad física.',
                'estado'      => false,
            ],
            [
                'nombre'      => 'Temporada',
                'descripcion' => 'Artículos de colecciones de temporada.',
                'estado'      => false,
            ],
        ];

        foreach ($categorias as $data) {
            $categoria = Categoria::create([
                'nombre'      => $data['nombre'],
                'descripcion' => $data['descripcion'],
                'estado'      => $data['estado'],
            ]);

            $categoria->codigo = 'CAT-' . str_pad($categoria->id, 3, '0', STR_PAD_LEFT);
            $categoria->save();
        }
    }
}