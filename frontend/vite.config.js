import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import { visualizer } from "rollup-plugin-visualizer";

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    react(),
    visualizer({
      open: true,
      gzipSize: true,
      brotliSize: true,
    }),
  ],
  server: {
    port: 5173,
    proxy: {
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true,
        secure: false,
      },
      '/ai': {
        target: 'http://localhost:8001',
        changeOrigin: true,
        secure: false,
        rewrite: (path) => path.replace(/^\/ai/, ''),
      },
      '/ai-api': {
        target: 'http://127.0.0.1:8001',
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
    rollupOptions: {
      output: {
        manualChunks: {
          vendor: ['react', 'react-dom', 'react-router-dom'],
          // Separation of concerns: Dashboard heavy libs
          'pdf-worker': ['jspdf', 'jspdf-autotable'],
          'excel-worker': ['xlsx'],
          'calendar-vendor': ['@fullcalendar/core', '@fullcalendar/daygrid', '@fullcalendar/interaction', '@fullcalendar/react', '@fullcalendar/timegrid'],
          // UI Libs shared but isolated
          ui: ['framer-motion', 'sonner', 'lucide-react'],
        },
      },
    },
  },
})
