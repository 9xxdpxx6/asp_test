import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    base: '/',

    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js', // Используем app.js для обеих точек входа
            ],
            refresh: true,
        }),
        vue(),
    ],
    resolve: {
        alias: {
            '~': path.resolve(__dirname, './resources/js'),
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
});
