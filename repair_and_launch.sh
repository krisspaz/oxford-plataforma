#!/bin/bash
echo "🌟 INICIANDO REPARACIÓN TOTAL DEL SISTEMA OXFORD 🌟"

# 1. Detener Contenedores y Limpiar Volúmenes (CRÍTICO)
echo "🛑 Deteniendo sistema y limpiando datos corruptos..."
docker-compose down -v

# 2. Reconstruir Imágenes (para asegurar cambios en Dockerfile.dev)
echo "🏗️ Reconstruyendo entorno (Frontend Hot-Reload + Backend)..."
docker-compose build

# 3. Iniciar Sistema
echo "🚀 Levantando servicios..."
docker-compose up -d

echo "⏳ Esperando a que la Base de Datos inicialice (20s)..."
sleep 20

# 4. Verificación de Salud
echo "🏥 Verificando estado del sistema..."
if docker-compose ps | grep "Exit"; then
    echo "⚠️ ALERTA: Algunos contenedores fallaron. Revisa 'docker-compose logs'."
else
    echo "✅ Sistema Operativo y Saludable."
    echo "   - Frontend (Dev): http://localhost:5173"
    echo "   - Backend (API):   http://localhost:8000"
    echo "   - Credenciales:    admin@oxford.edu / oxford123"
fi
