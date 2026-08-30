import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

// Force APP_URL to https to prevent Vercel from injecting http://
process.env.APP_URL = 'https://bimss.vercel.app';

export default defineConfig({
    base: '/',
    plugins: [
        tailwindcss(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
