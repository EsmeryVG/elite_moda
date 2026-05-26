<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\AtributoValor;
use App\Services\ProductoVarianteService;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $service = new ProductoVarianteService();

        // ── Helpers ──────────────────────────────────
        $valor = fn (string $atributo, string $val) =>
            AtributoValor::whereHas('atributo', fn ($q) => $q->where('nombre', $atributo))
                ->where('valor', $val)
                ->value('id');

        $valores = fn (string $atributo, array $vals) =>
            AtributoValor::whereHas('atributo', fn ($q) => $q->where('nombre', $atributo))
                ->whereIn('valor', $vals)
                ->pluck('id')
                ->toArray();

        // ══════════════════════════════════════════════
        // ROPA DE MUJER (categoria_id: 1)
        // ══════════════════════════════════════════════

        // Producto 1: Blusa con variantes Talla × Color
        $blusa = $this->crearProducto('Blusa Casual Manga Corta', 'Zara', 1, true);
        $service->crearVariantesMasivo($blusa, [
            ['atributo_valor_ids' => [$valor('Talla','S'),  $valor('Color','Blanco')], 'precio_venta' => 850,  'costo' => 420],
            ['atributo_valor_ids' => [$valor('Talla','M'),  $valor('Color','Blanco')], 'precio_venta' => 850,  'costo' => 420],
            ['atributo_valor_ids' => [$valor('Talla','L'),  $valor('Color','Blanco')], 'precio_venta' => 850,  'costo' => 420],
            ['atributo_valor_ids' => [$valor('Talla','S'),  $valor('Color','Negro')],  'precio_venta' => 850,  'costo' => 420],
            ['atributo_valor_ids' => [$valor('Talla','M'),  $valor('Color','Negro')],  'precio_venta' => 850,  'costo' => 420],
            ['atributo_valor_ids' => [$valor('Talla','L'),  $valor('Color','Negro')],  'precio_venta' => 850,  'costo' => 420],
            ['atributo_valor_ids' => [$valor('Talla','S'),  $valor('Color','Rosado')], 'precio_venta' => 900,  'costo' => 440],
            ['atributo_valor_ids' => [$valor('Talla','M'),  $valor('Color','Rosado')], 'precio_venta' => 900,  'costo' => 440],
        ]);

        // Producto 2: Vestido formal
        $vestido = $this->crearProducto('Vestido Formal Elegante', 'H&M', 1, true);
        $service->crearVariantesMasivo($vestido, [
            ['atributo_valor_ids' => [$valor('Talla','S'), $valor('Color','Negro')],    'precio_venta' => 2200, 'costo' => 1100],
            ['atributo_valor_ids' => [$valor('Talla','M'), $valor('Color','Negro')],    'precio_venta' => 2200, 'costo' => 1100],
            ['atributo_valor_ids' => [$valor('Talla','L'), $valor('Color','Negro')],    'precio_venta' => 2200, 'costo' => 1100],
            ['atributo_valor_ids' => [$valor('Talla','S'), $valor('Color','Azul marino')], 'precio_venta' => 2400, 'costo' => 1200],
            ['atributo_valor_ids' => [$valor('Talla','M'), $valor('Color','Azul marino')], 'precio_venta' => 2400, 'costo' => 1200],
        ]);

        // ══════════════════════════════════════════════
        // ROPA DE HOMBRE (categoria_id: 2)
        // ══════════════════════════════════════════════

        // Producto 3: Camisa de vestir
        $camisa = $this->crearProducto('Camisa de Vestir Slim Fit', 'Calvin Klein', 2, true);
        $service->crearVariantesMasivo($camisa, [
            ['atributo_valor_ids' => [$valor('Talla','S'),  $valor('Color','Blanco')], 'precio_venta' => 1450, 'costo' => 720],
            ['atributo_valor_ids' => [$valor('Talla','M'),  $valor('Color','Blanco')], 'precio_venta' => 1450, 'costo' => 720],
            ['atributo_valor_ids' => [$valor('Talla','L'),  $valor('Color','Blanco')], 'precio_venta' => 1450, 'costo' => 720],
            ['atributo_valor_ids' => [$valor('Talla','XL'), $valor('Color','Blanco')], 'precio_venta' => 1550, 'costo' => 760],
            ['atributo_valor_ids' => [$valor('Talla','S'),  $valor('Color','Azul')],   'precio_venta' => 1450, 'costo' => 720],
            ['atributo_valor_ids' => [$valor('Talla','M'),  $valor('Color','Azul')],   'precio_venta' => 1450, 'costo' => 720],
            ['atributo_valor_ids' => [$valor('Talla','L'),  $valor('Color','Azul')],   'precio_venta' => 1450, 'costo' => 720],
            ['atributo_valor_ids' => [$valor('Talla','XL'), $valor('Color','Azul')],   'precio_venta' => 1550, 'costo' => 760],
        ]);

        // Producto 4: Pantalón casual (simple, sin variantes)
        $pantalon = $this->crearProducto('Pantalón Casual Chino', 'Tommy Hilfiger', 2, false);
        $service->crearVarianteDefault($pantalon, ['precio_venta' => 1800, 'costo' => 900]);

        // ══════════════════════════════════════════════
        // CALZADO DE MUJER (categoria_id: 4)
        // ══════════════════════════════════════════════

        $tacon = $this->crearProducto('Zapato de Tacón Stiletto', 'Steve Madden', 4, true);
        $service->crearVariantesMasivo($tacon, [
            ['atributo_valor_ids' => [$valor('Talla Calzado','36'), $valor('Color','Negro')],  'precio_venta' => 3200, 'costo' => 1600],
            ['atributo_valor_ids' => [$valor('Talla Calzado','37'), $valor('Color','Negro')],  'precio_venta' => 3200, 'costo' => 1600],
            ['atributo_valor_ids' => [$valor('Talla Calzado','38'), $valor('Color','Negro')],  'precio_venta' => 3200, 'costo' => 1600],
            ['atributo_valor_ids' => [$valor('Talla Calzado','39'), $valor('Color','Negro')],  'precio_venta' => 3200, 'costo' => 1600],
            ['atributo_valor_ids' => [$valor('Talla Calzado','36'), $valor('Color','Beige')],  'precio_venta' => 3400, 'costo' => 1700],
            ['atributo_valor_ids' => [$valor('Talla Calzado','37'), $valor('Color','Beige')],  'precio_venta' => 3400, 'costo' => 1700],
            ['atributo_valor_ids' => [$valor('Talla Calzado','38'), $valor('Color','Beige')],  'precio_venta' => 3400, 'costo' => 1700],
        ]);

        // ══════════════════════════════════════════════
        // CALZADO DE HOMBRE (categoria_id: 5)
        // ══════════════════════════════════════════════

        $mocasin = $this->crearProducto('Mocasín de Cuero', 'Clarks', 5, true);
        $service->crearVariantesMasivo($mocasin, [
            ['atributo_valor_ids' => [$valor('Talla Calzado','40'), $valor('Color','Café')],  'precio_venta' => 4500, 'costo' => 2200],
            ['atributo_valor_ids' => [$valor('Talla Calzado','41'), $valor('Color','Café')],  'precio_venta' => 4500, 'costo' => 2200],
            ['atributo_valor_ids' => [$valor('Talla Calzado','42'), $valor('Color','Café')],  'precio_venta' => 4500, 'costo' => 2200],
            ['atributo_valor_ids' => [$valor('Talla Calzado','43'), $valor('Color','Café')],  'precio_venta' => 4500, 'costo' => 2200],
            ['atributo_valor_ids' => [$valor('Talla Calzado','40'), $valor('Color','Negro')], 'precio_venta' => 4500, 'costo' => 2200],
            ['atributo_valor_ids' => [$valor('Talla Calzado','41'), $valor('Color','Negro')], 'precio_venta' => 4500, 'costo' => 2200],
            ['atributo_valor_ids' => [$valor('Talla Calzado','42'), $valor('Color','Negro')], 'precio_venta' => 4500, 'costo' => 2200],
        ]);

        // ══════════════════════════════════════════════
        // PERFUMERÍA (categoria_id: 6)
        // ══════════════════════════════════════════════

        $perfume1 = $this->crearProducto('Boss Bottled', 'Hugo Boss', 6, true);
        $service->crearVariantesMasivo($perfume1, [
            ['atributo_valor_ids' => [$valor('Volumen','50ml'),  $valor('Concentración','Eau de Toilette')], 'precio_venta' => 3800, 'costo' => 1900],
            ['atributo_valor_ids' => [$valor('Volumen','100ml'), $valor('Concentración','Eau de Toilette')], 'precio_venta' => 5500, 'costo' => 2750],
            ['atributo_valor_ids' => [$valor('Volumen','100ml'), $valor('Concentración','Eau de Parfum')],   'precio_venta' => 6200, 'costo' => 3100],
        ]);

        $perfume2 = $this->crearProducto('Chanel N°5', 'Chanel', 6, true);
        $service->crearVariantesMasivo($perfume2, [
            ['atributo_valor_ids' => [$valor('Volumen','35ml'), $valor('Concentración','Eau de Parfum')],  'precio_venta' => 7500,  'costo' => 3750],
            ['atributo_valor_ids' => [$valor('Volumen','50ml'), $valor('Concentración','Eau de Parfum')],  'precio_venta' => 10500, 'costo' => 5250],
            ['atributo_valor_ids' => [$valor('Volumen','100ml'),$valor('Concentración','Eau de Parfum')],  'precio_venta' => 15000, 'costo' => 7500],
        ]);

        // ══════════════════════════════════════════════
        // COSMÉTICOS (categoria_id: 7) — productos simples
        // ══════════════════════════════════════════════

        $crema = $this->crearProducto('Crema Hidratante Facial SPF50', "L'Oréal", 7, false);
        $service->crearVarianteDefault($crema, ['precio_venta' => 1200, 'costo' => 600]);

        $labial = $this->crearProducto('Labial Mate Larga Duración', 'MAC', 7, false);
        $service->crearVarianteDefault($labial, ['precio_venta' => 950, 'costo' => 475]);

        $mascara = $this->crearProducto('Máscara de Pestañas Volumizing', 'Maybelline', 7, false);
        $service->crearVarianteDefault($mascara, ['precio_venta' => 680, 'costo' => 340]);

        // ══════════════════════════════════════════════
        // ACCESORIOS (categoria_id: 8)
        // ══════════════════════════════════════════════

        $bolso = $this->crearProducto('Bolso Crossbody de Cuero', 'Michael Kors', 8, true);
        $service->crearVariantesMasivo($bolso, [
            ['atributo_valor_ids' => [$valor('Color','Negro')], 'precio_venta' => 6500, 'costo' => 3250],
            ['atributo_valor_ids' => [$valor('Color','Café')],  'precio_venta' => 6500, 'costo' => 3250],
            ['atributo_valor_ids' => [$valor('Color','Beige')], 'precio_venta' => 6800, 'costo' => 3400],
        ]);

        // ══════════════════════════════════════════════
        // ROPA DEPORTIVA (categoria_id: 9)
        // ══════════════════════════════════════════════

        $legging = $this->crearProducto('Legging Deportivo Compresión', 'Nike', 9, true);
        $service->crearVariantesMasivo($legging, [
            ['atributo_valor_ids' => [$valor('Talla','S'),  $valor('Color','Negro')],   'precio_venta' => 1350, 'costo' => 675],
            ['atributo_valor_ids' => [$valor('Talla','M'),  $valor('Color','Negro')],   'precio_venta' => 1350, 'costo' => 675],
            ['atributo_valor_ids' => [$valor('Talla','L'),  $valor('Color','Negro')],   'precio_venta' => 1350, 'costo' => 675],
            ['atributo_valor_ids' => [$valor('Talla','S'),  $valor('Color','Azul')],    'precio_venta' => 1350, 'costo' => 675],
            ['atributo_valor_ids' => [$valor('Talla','M'),  $valor('Color','Azul')],    'precio_venta' => 1350, 'costo' => 675],
            ['atributo_valor_ids' => [$valor('Talla','XL'), $valor('Color','Gris')],    'precio_venta' => 1450, 'costo' => 725],
        ]);
    }

    // ── Helper privado ───────────────────────────────
    private function crearProducto(
        string $nombre,
        string $marca,
        int $categoriaId,
        bool $tieneVariantes
    ): Producto {
        $producto = Producto::create([
            'categoria_id'    => $categoriaId,
            'nombre'          => $nombre,
            'marca'           => $marca,
            'tiene_variantes' => $tieneVariantes,
            'estado'          => true,
        ]);

        $producto->codigo = 'PROD-' . str_pad($producto->id, 3, '0', STR_PAD_LEFT);
        $producto->save();

        return $producto;
    }
}