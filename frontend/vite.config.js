import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import { visualizer } from "rollup-plugin-visualizer";
import path from 'path';

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    react(),
    visualizer({
      open: false, // Don't auto-open in production
      gzipSize: true,
      brotliSize: true,
      filename: 'stats.html'
    }),
  ],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
  server: {
    port: 5173,
    proxy: {
      '/api': {
        target: 'http://nginx:80',
        changeOrigin: true,
        secure: false,
      },
      '/ai': {
        target: 'http://ai_service:8001',
        changeOrigin: true,
        secure: false,
        rewrite: (path) => path.replace(/^\/ai/, ''),
      },
      '/ai-api': {
        target: 'http://ai_service:8001',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/ai-api/, ''),
      },
    },
    // Allow any host to fix white screen on ngrok
    allowedHosts: true,
    host: true,
    cors: true,
    hmr: {
      clientPort: 443, // Forces the client to connect via standard HTTPS port (handled by Ngrok)
      protocol: 'wss', // Use secure websocket
      host: process.env.VITE_NGROK_HOST || undefined, // Use env var or dynamic
    },

  },
  test: {
    globals: true,
    environment: 'jsdom',
    setupFiles: './src/__tests__/setup.js',
    css: true,
    coverage: {
      provider: 'v8',
      reporter: ['text', 'json', 'html'],
      exclude: [
        'node_modules/',
        'src/__tests__/',
      ],
    },
  },
  build: {
    target: 'esnext',
    minify: 'esbuild',
    sourcemap: false, // Disable source maps in production
    rollupOptions: {
      output: {
        manualChunks: (id) => {
          // Core React - always needed
          if (id.includes('node_modules/react/') ||
            id.includes('node_modules/react-dom/')) {
            return 'react-core';
          }

          // Router - always needed
          if (id.includes('react-router')) {
            return 'router';
          }

          // React Query - frequently used
          if (id.includes('@tanstack')) {
            return 'query-libs';
          }

          // PDF libraries - lazy loaded via dynamic import
          if (id.includes('jspdf') || id.includes('jspdf-autotable')) {
            return 'pdf-libs';
          }

          // Excel library - lazy loaded via dynamic import
          if (id.includes('xlsx')) {
            return 'excel-libs';
          }

          // Calendar - route-level split
          if (id.includes('@fullcalendar')) {
            return 'calendar-libs';
          }

          // HTML to canvas - lazy loaded
          if (id.includes('html2canvas')) {
            return 'screenshot-libs';
          }

          // Charts - keep separate
          if (id.includes('recharts')) {
            return 'chart-libs';
          }

          // Animation libs
          if (id.includes('framer-motion')) {
            return 'animation-libs';
          }

          // i18n
          if (id.includes('i18next')) {
            return 'i18n-libs';
          }

          // UI icons - frequently used
          if (id.includes('lucide-react')) {
            return 'icons';
          }

          // Form validation
          if (id.includes('react-hook-form') || id.includes('zod') || id.includes('@hookform')) {
            return 'form-libs';
          }

          // Drag and drop
          if (id.includes('react-dnd') || id.includes('dnd-core')) {
            return 'dnd-libs';
          }
        },
      },
    },
  },
})
