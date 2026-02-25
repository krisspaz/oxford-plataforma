#!/bin/bash
echo "🚀 Iniciando Entorno de Desarrollo Frontend..."

cd frontend

if [ ! -d "node_modules" ]; then
    echo "📦 Instalando dependencias..."
    npm install
fi

echo "✅ Iniciando servidor Vite..."
npm run dev
