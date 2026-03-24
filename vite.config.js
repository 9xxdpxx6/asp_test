import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/sass/admin-sidebar.scss',
                'resources/js/app.js', // Используем app.js для обеих точек входа
            ],
            refresh: true,
        }),
        vue(),
    ],
    server: {
        // Force IPv4 to avoid [::1] hot URL issues on Windows browsers.
        host: '127.0.0.1',
        port: 5173,
        strictPort: true,
        hmr: {
            host: '127.0.0.1',
            port: 5173,
        },
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
    css: {
        preprocessorOptions: {
            scss: {
                quietDeps: true,
                silenceDeprecations: ['legacy-js-api', 'color-functions', 'mixed-decls', 'import'],
                api: 'modern-compiler',
                logger: {
                    warn: function(message) {
                        // Подавляем предупреждения о mixed-decls
                        if (message.includes('mixed-decls deprecation is obsolete')) {
                            return;
                        }
                        console.warn(message);
                    }
                }
            }
        }
    }
});
