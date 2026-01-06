import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vite.dev/config/
export default defineConfig({
  plugins: [react()],
  server: {
    host: '0.0.0.0',
    port: 5173,
    allowedHosts: [
      'vexatiously-dextrocular-esteban.ngrok-free.dev',
      '.ngrok-free.dev',
      'localhost',
      '192.168.1.70'
    ],
    proxy: {
      '/api': {
        target: 'http://127.0.0.1:8000',
        changeOrigin: true,
        secure: false,
      },
      '/ai-api': {
        target: 'http://127.0.0.1:8001',
        changeOrigin: true,
        secure: false,
        rewrite: (path) => path.replace(/^\/ai-api/, '')
      }
    }
  },
  build: {
    rollupOptions: {
      output: {
        manualChunks(id) {
          if (id.includes('node_modules')) return 'vendor';
          if (id.includes('src/pages/')) {
            if (id.match(/(Inscripciones|Matriculacion|Students|Familias|Contracts)/)) return 'secretaria';
            if (id.match(/(Financial|Pagos|EstadoCuenta|Comprobantes|Paquetes|Costos)/)) return 'finanzas';
            if (id.match(/(Academic|Grados|Materias|Docentes|Horarios|Bimestres)/)) return 'academico';
          }
        }
      }
    }
  },
  test: {
    globals: true,
    environment: 'jsdom',
    setupFiles: './src/test/setup.js',
    css: true,
  },
})
