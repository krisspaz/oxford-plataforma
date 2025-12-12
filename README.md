# Corpo Oxford - Sistema de Gestión Educativa

## 🏗 Arquitectura del Sistema

El sistema utiliza una arquitectura de microservicios contenerizada:

```mermaid
graph TD
    Client[Cliente Web (React)] -->|HTTP/JSON| Nginx[Nginx Reverse Proxy]
    Nginx -->|/api| Backend[Backend (Symfony 7)]
    Nginx -->|/ai| AI[Servicio IA (FastAPI)]
    Nginx -->|/*| Frontend[Frontend Static (Vite Build)]
    
    Backend -->|SQL| DB[(PostgreSQL)]
    Backend -->|JSON| AI
    AI -->|Data| DB
```

### Tecnologías Principales
- **Backend:** Symfony 7, API Platform, Doctrine ORM.
- **Frontend:** React 18, Vite, TailwindCSS, Axios.
- **Inteligencia Artificial:** Python 3.10, FastAPI, Scikit-learn, Pandas.
- **Base de Datos:** PostgreSQL 16.
- **Infraestructura:** Docker, Docker Compose, Nginx.

## 🚀 Guía de Instalación (Development)

### Prerrequisitos
- Docker y Docker Compose
- Node.js 18+ (opcional, si se ejecuta local sin docker)
- PHP 8.2+ (opcional)

### Paso 1: Clonar y Configurar
```bash
git clone <repo_url>
cd corpo-oxford-sistema
cp .env.example .env
```

### Paso 2: Levantar Contenedores
```bash
docker-compose up -d --build
```

Esto levantará:
- **Frontend:** http://localhost (Puerto 80)
- **Backend API:** http://localhost/api
- **API Docs:** http://localhost/api/docs
- **Adminer (DB):** http://localhost:8080

### Paso 3: Inicializar Base de Datos
```bash
docker-compose exec backend php bin/console doctrine:migrations:migrate
docker-compose exec backend php bin/console doctrine:fixtures:load
```

## 🔒 Seguridad
- Autenticación JWT con Refresh Tokens (HttpOnly Cookies).
- RBAC (Role-Based Access Control) estricto.
- Validaciones de entrada en Backend y Frontend.

## 🤖 Servicios de IA
1. **Análisis de Riesgo:** Endpoint `/ai/predict-risk`
2. **Generador de Horarios:** Endpoint `/ai/generate-schedule`
