import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/css/dashboard.css',
                'resources/css/categorias.css',
                'resources/css/atributos.css',
                'resources/css/productos.css',
                'resources/js/app.js',
                'resources/js/dashboard.js',
                'resources/js/categorias.js',
                'resources/js/atributos.js',
                'resources/js/productos.js',
                'resources/js/sucursales.js',
                'resources/js/almacenes.js',
                'resources/js/proveedores.js',
                'resources/js/clientes.js',
                'resources/js/ordenes_compra.js'
            ],
            refresh: true,
        }),
    ],
});