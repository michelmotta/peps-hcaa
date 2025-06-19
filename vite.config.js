import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/web.scss', 
                'resources/css/theme.min.css', 
                'resources/sass/dashboard.scss', 
                'resources/js/app.js', 
            ],
            refresh: true,
        }),
    ],
});
