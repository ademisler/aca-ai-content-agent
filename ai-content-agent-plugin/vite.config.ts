import path from 'path';
import { defineConfig, loadEnv } from 'vite';
import react from '@vitejs/plugin-react';
import { visualizer } from 'rollup-plugin-visualizer';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, '.', '');
    return {
      plugins: [
        react({
          babel: {
            parserOpts: {
              plugins: ['decorators-legacy']
            }
          }
        }),
        // Add bundle analyzer for development
        mode === 'analyze' && visualizer({
          filename: 'dist/bundle-analysis.html',
          open: true,
          gzipSize: true,
          brotliSize: true
        })
      ].filter(Boolean),
      define: {
        'process.env.GEMINI_API_KEY': JSON.stringify(env.GEMINI_API_KEY)
      },
      resolve: {
        alias: {
          '@': path.resolve(__dirname, '.'),
        }
      },
      build: {
        target: 'es2020',
        minify: 'terser', // Enable minification with Terser for better optimization
        terserOptions: {
          compress: {
            drop_console: true, // Remove console.log in production
            drop_debugger: true,
            unused: true, // Remove unused code
            dead_code: true // Remove dead code
          },
          mangle: {
            safari10: true
          }
        },
        rollupOptions: {
          output: {
            format: 'iife',
            name: 'ACAApp',
            inlineDynamicImports: true, // Required for WordPress compatibility
            entryFileNames: 'assets/[name]-[hash].js',
            chunkFileNames: 'assets/[name]-[hash].js',
            assetFileNames: 'assets/[name]-[hash].[ext]'
          },
          treeshake: {
            moduleSideEffects: false
          }
        },
        sourcemap: false,
        outDir: 'dist',
        assetsDir: 'assets',
        emptyOutDir: true,
        chunkSizeWarningLimit: 300 // Warn if chunks exceed 300KB
      }
    };
});
