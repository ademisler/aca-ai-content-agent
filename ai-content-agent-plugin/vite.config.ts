import path from 'path';
import { defineConfig, loadEnv } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, '.', '');
    return {
      plugins: [react({
        babel: {
          parserOpts: {
            plugins: ['decorators-legacy']
          }
        }
      })],
      define: {
        'process.env.API_KEY': JSON.stringify(env.GEMINI_API_KEY),
        'process.env.GEMINI_API_KEY': JSON.stringify(env.GEMINI_API_KEY)
      },
      resolve: {
        alias: {
          '@': path.resolve(__dirname, '.'),
        }
      },
      build: {
        target: 'es2015',
        minify: 'esbuild',
        sourcemap: true, // Enable source maps for debugging
        rollupOptions: {
          output: {
            manualChunks: undefined,
            format: 'iife',
            inlineDynamicImports: true
          }
        },
        commonjsOptions: {
          include: [/node_modules/]
        }
      },
      esbuild: {
        keepNames: true,
        legalComments: 'none'
      }
    };
});
