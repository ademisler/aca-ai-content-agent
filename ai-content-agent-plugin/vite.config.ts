import path from 'path';
import { defineConfig, loadEnv } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig(({ mode }) => {
    // Note: API keys should be handled at runtime via WordPress settings, not build time
    return {
      plugins: [react({
        babel: {
          parserOpts: {
            plugins: ['decorators-legacy']
          }
        }
      })],
      // Removed insecure API key definitions - keys should be loaded at runtime
      resolve: {
        alias: {
          '@': path.resolve(__dirname, '.'),
        }
      },
      build: {
        target: 'es2020',
        minify: false, // Disable minification to avoid hoisting issues
        rollupOptions: {
          output: {
            manualChunks: undefined,
            format: 'iife',
            name: 'ACAApp',
            inlineDynamicImports: true,
            entryFileNames: 'assets/[name]-[hash].js',
            chunkFileNames: 'assets/[name]-[hash].js',
            assetFileNames: 'assets/[name]-[hash].[ext]'
          }
        },
        sourcemap: false,
        outDir: 'dist',
        assetsDir: 'assets',
        emptyOutDir: true
      }
    };
});
