# Oxford Plataforma - Sistema de Gestión Escolar

<p align="center">
  <img src="https://img.shields.io/badge/version-2.0-blue" alt="Version">
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4" alt="PHP">
  <img src="https://img.shields.io/badge/React-18-61DAFB" alt="React">
  <img src="https://img.shields.io/badge/Python-3.11-3776AB" alt="Python">
</p>

## 🎯 Descripción

Plataforma integral de gestión escolar con IA empresarial para el Colegio Oxford. Incluye gestión académica, financiera, de horarios y asistentes inteligentes.

## 🏗️ Arquitectura

```
├── frontend/          # React 18 + Vite + TailwindCSS
├── backend/
│   ├── symfony/       # PHP 8.2 + Symfony 7
│   └── ai_service/    # Python 3.11 + Flask + NLP
└── docker-compose.yml
```

## 🚀 Instalación Rápida

### Requisitos
- Docker & Docker Compose
- Node.js 18+
- PHP 8.2+ con Composer
- Python 3.11+

### 1. Clonar repositorio
```bash
git clone https://github.com/krisspaz/oxford-plataforma.git
cd oxford-plataforma
```

### 2. Configurar variables de entorno
```bash
cp .env.example .env
# Editar .env con tus valores
```

### 3. Iniciar con Docker
```bash
docker-compose up -d
```

### 4. O iniciar manualmente

**Backend Symfony:**
```bash
cd backend/symfony
composer install
php bin/console doctrine:migrations:migrate
symfony serve
```

**Frontend React:**
```bash
cd frontend
npm install
npm run dev
```

**AI Service:**
```bash
cd backend/ai_service
pip install -r requirements.txt
python main.py
```

## 👥 Usuarios de Prueba

| Email | Contraseña | Rol |
|-------|------------|-----|
| admin@oxford.edu | oxford123 | Admin |
| director@oxford.edu | oxford123 | Director |
| teacher@oxford.edu | oxford123 | Docente |
| student@oxford.edu | oxford123 | Estudiante |
| parent@oxford.edu | oxford123 | Padre |

## 📦 Módulos Principales

### Académico
- Gestión de cursos, secciones, materias
- Carga y consulta de notas
- Horarios inteligentes con IA
- Reportes de rendimiento

### Financiero
- Gestión de pagos y cuotas
- Estados de cuenta
- Facturación electrónica
- Reportes financieros

### IA Empresarial
- 🤖 Asistente personal por rol
- 📊 Predicción de riesgos
- 🧠 Auto-aprendizaje continuo
- 🚨 Modo crisis
- 💰 Dashboard económico

### Comunicación
- 🔔 Centro de notificaciones
- 📅 Calendario escolar
- 📤 Exportación PDF/Excel

## 🔧 API Endpoints

### Autenticación
```
POST /api/login_check   # Login con JWT
POST /api/register      # Registro
```

### Estudiantes
```
GET  /api/students      # Listar
POST /api/students      # Crear
GET  /api/students/{id} # Ver detalle
```

### IA
```
POST /ai-api/process-command  # Procesar comando IA
GET  /ai-api/health           # Estado del servicio
```

## 🛡️ Seguridad

- Autenticación JWT
- MFA (Two-Factor Auth)
- Roles y permisos granulares
- Detección de anomalías
- Logs de auditoría

## 📊 Tecnologías

| Área | Stack |
|------|-------|
| Frontend | React 18, Vite, TailwindCSS, FullCalendar |
| Backend | Symfony 7, Doctrine ORM, LexikJWT |
| IA | Python, Flask, scikit-learn, NLP |
| BD | MySQL 8 / PostgreSQL |
| Cache | Redis |

## 📝 Licencia

Propiedad de Corporación Oxford Guatemala. Todos los derechos reservados.

---

Desarrollado con ❤️ por el equipo de Oxford
