import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  plugins: [vue()],
  root: 'resources',
  server: {
    origin: 'http://localhost:5174', // important
    cors: true,
  },
  build: {
    outDir: '../public',
    emptyOutDir: true,
    rollupOptions: {
      input: '/js/main.js',
      output: {
        entryFileNames: 'assets/main.js',
        chunkFileNames: 'assets/[name].js',
        assetFileNames: 'assets/[name].[ext]',
      },
    },
  },
});

