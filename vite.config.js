import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import fs from 'fs';
import path from 'path';

function getJsEntries(dir) {
  return fs.readdirSync(path.resolve(__dirname, dir))
    .filter(f => f.endsWith('.js'))
    .map(f => `${dir}/${f}`);
}

export default defineConfig({
    server: {
        port: 5173,
    },
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                ...getJsEntries('resources/js'),
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '~coreui': path.resolve(__dirname, 'node_modules/@coreui/coreui'),
        }
    },
});
