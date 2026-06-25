import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        minify: 'esbuild',
        cssMinify: true,
        sourcemap: false,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        if (id.includes('alpinejs')) {
                            return 'vendor-alpine';
                        }
                        if (id.includes('axios')) {
                            return 'vendor-axios';
                        }
                        return 'vendor-core';
                    }
                }
            }
        }
    }
});
