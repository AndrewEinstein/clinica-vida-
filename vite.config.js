import { resolve } from 'node:path';
import { defineConfig } from 'vite';

const previewPages = [
    'index',
    'login',
    'dashboard',
    'appointments',
    'patients',
    'medical-record',
    'settings',
];

export default defineConfig({
    root: 'preview',
    build: {
        outDir: '../dist',
        emptyOutDir: true,
        rollupOptions: {
            input: Object.fromEntries(
                previewPages.map((page) => [
                    page,
                    resolve(__dirname, 'preview', `${page}.html`),
                ]),
            ),
        },
    },
});
