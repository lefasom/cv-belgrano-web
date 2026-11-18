import laravel from 'laravel-vite-plugin';
import { defineConfig, lazyPlugins } from 'vite-plus';

export default defineConfig({
    plugins: lazyPlugins(() => [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/css/layout.css',
                'resources/css/home.css',
                'resources/css/crear.css',
                'resources/css/landing/probar-online.css',
                'resources/css/landing/volver-arriba.css',
                'resources/css/auth.css',
                'resources/css/creador/seccion.css',
                'resources/css/creador/modal.css',
                'resources/css/creador/datos.css',
                'resources/css/creador/experiencia.css',
                'resources/css/creador/educacion.css',
                'resources/css/creador/habilidades.css',
            ],
            refresh: true,
        }),
    ]),
    server: {
        cors: true,
        watch: {
            ignored: [
                '**/.agents/**',
                '**/.claude/**',
                '**/.cursor/**',
                '**/.junie/**',
                '**/storage/framework/views/**',
                '**/vendor/**',
            ],
        },
    },
});
