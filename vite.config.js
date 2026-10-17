import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { google } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                google('Plus Jakarta Sans', {
                    weights: [400, 500, 600, 700, 800],
                    preload: [{ weight: 400 }, { weight: 800 }],
                    optimizedFallbacks: false,
                }),
                google('Plus Jakarta Sans', {
                    alias: 'plus-jakarta-sans-italic',
                    weights: [400],
                    styles: ['italic'],
                    preload: false,
                    optimizedFallbacks: false,
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        host: '127.0.0.1',
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
