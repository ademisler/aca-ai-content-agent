import path from 'path';
import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig(() => {
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
        // Minification disabled to prevent Temporal Dead Zone (TDZ) issues
        // WordPress environment can cause variable hoisting problems with minified code
        // Trade-off: ~600KB unminified vs ~95KB minified (but stable vs unstable)
        minify: false,
        rollupOptions: {
          output: {
            manualChunks: undefined,
            format: 'iife', // Immediately Invoked Function Expression for better isolation
            name: 'ACAApp',
            inlineDynamicImports: true,
            entryFileNames: 'assets/[name]-[hash].js', // Cache busting with hash
            chunkFileNames: 'assets/[name]-[hash].js',
            assetFileNames: 'assets/[name]-[hash].[ext]'
          }
        },
        sourcemap: false, // Disabled for production to reduce bundle size
        outDir: 'dist',
        assetsDir: 'assets',
        emptyOutDir: true,
        // Chunk size warnings disabled since we prioritize stability over size
        chunkSizeWarningLimit: 1000
      }
    };
});
