# ☁️ Guía de Despliegue en Render.com (Gratis)

Esta guía usa **Render Blueprints**, que lee el archivo `render.yaml` de tu repositorio y configura **automáticamente** los 3 servicios (Frontend, Backend, AI) y las 2 bases de datos (Postgres, Redis).

## ✅ Prerrequisitos
1.  Cuenta en [Render.com](https://render.com) (entra con GitHub).
2.  Tener tu código subido a GitHub (repositorio `oxford-plataforma`).

---

## 🚀 Paso Único: Crear Blueprint

1.  Ve al [Dashboard de Render](https://dashboard.render.com/).
2.  Click en el botón **"New +"** y selecciona **"Blueprint"**.
3.  Conecta tu repositorio `oxford-plataforma`.
4.  Render detectará el archivo `render.yaml`.
5.  Te mostrará una lista de los servicios que va a crear:
    *   `oxford-frontend` (Web)
    *   `oxford-backend` (Web)
    *   `oxford-ai` (Web)
    *   `oxford-db` (Database)
    *   `oxford-redis` (Redis)
6.  Click en **"Apply"** (o "Create Blueprint").

¡Y listo! Render empezará a construir todo. ☕

---

## 🛠️ Detalles Importantes (Plan Gratis)

### 1. "Spin Down" (Dormir)
En el plan gratuito, los servicios web se "duermen" después de 15 minutos de inactividad.
- **Consecuencia**: La primera vez que entres después de un rato, la página tardará unos 30-50 segundos en cargar mientras el servidor "despierta".
- **Solución**: Para una demo, está bien. Para producción real, cuesta $7/mes (Plan "Starter") para evitar esto.

### 2. Base de Datos
- La base de datos Postgres gratuita dura **90 días**. Después de eso se borra (a menos que actualices).
- Ideal para demos y pruebas.

### 3. Persistencia de Archivos
- Igual que en Railway, el disco es efímero. Si subes fotos de perfil, se borrarán cuando Render reinicie el servicio.
- **Solución**: Usar AWS S3 (el código ya está listo para eso, solo faltaría añadir las variables de entorno de AWS).

---

## 📋 Variables de Entorno
El archivo `render.yaml` ya configura casi todo automáticamente, enlazando los servicios entre sí:
- El Frontend recibe automáticamente la URL del Backend.
- El Backend recibe automáticamente la conexión a la BD y Redis.

Si necesitas cambiar algo manual, puedes ir al Dashboard -> Servicio -> Environment.

## 🆘 Troubleshooting

- **Error de Build**: Mira los logs en el dashboard.
- **Error 502/504 (Timeout)**: Si el servicio está "dormido", puede dar timeout la primera vez. Recarga la página.
- **Health Check**:
    - Backend: `/health` -> Debe devolver 200 OK.
    - AI: `/health` -> Debe devolver `{ "status": "ok" }`.
