import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/css/dashboard.css',  
                'resources/js/dashboard.js',    
            ],
            refresh: true,
        }),
    ],
});