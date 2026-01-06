# ☁️ Guía de Despliegue en la Nube (Railway)

Esta guía te permitirá desplegar **Oxford Plataforma** en internet para que funcione **24/7**, sin depender de tu computadora local ni de túneles como ngrok.

## ✅ Prerrequisitos
1.  Cuenta en [GitHub](https://github.com) (donde está tu código).
2.  Cuenta en [Railway.app](https://railway.app) (puedes entrar con GitHub).

---

## 🚀 Paso 1: Crear Proyecto en Railway

1.  Entra a [Railway Dashboard](https://railway.app/dashboard).
2.  Click en **"New Project"** -> **"Deploy from GitHub repo"**.
3.  Selecciona tu repositorio: `oxford-plataforma`.
4.  Railway intentará detectar el proyecto. **NO despliegues todavía.** Click en **"Add Variables"** primero si te deja, o deja que falle la primera vez (es normal en monorepos).

---

## 🛠️ Paso 2: Configurar Servicios (Monorepo)

Como tenemos Frontend, Backend y AI en un solo repo, necesitamos crear **3 Servicios** dentro del mismo proyecto de Railway.

### Servicio 1: Base de Datos (PostgreSQL)
1.  En tu proyecto Railway, click **New** -> **Database** -> **Add PostgreSQL**.
2.  Espera a que se cree.
3.  Click en la tarjeta de Postgres -> Pestaña **Connect**.
4.  Copia la `DATABASE_URL` (la usaremos en el backend).

### Servicio 2: Base de Datos (Redis)
1.  Click **New** -> **Database** -> **Add Redis**.
2.  Copia la `REDIS_URL`.

### Servicio 3: Backend (Symfony)
1.  Click **New** -> **GitHub Repo** -> `oxford-plataforma`.
2.  Ve a **Settings** del servicio -> **General** -> **Root Directory**.
3.  Cambia `/` por `/backend/symfony`.
4.  Ve a **Settings** -> **Build** -> **DockerfilePath**.
5.  Escribe: `Dockerfile.railway`.
6.  Ve a **Variables**:
    - `APP_ENV`: `prod`
    - `APP_SECRET`: (Genera una clave aleatoria larga)
    - `DATABASE_URL`: (Pega la URL de Postgres del Paso 1)
    - `REDIS_URL`: (Pega la URL de Redis del Paso 2)
    - `JWT_SECRET_KEY`: (Railway no soporta archivos fácilmente, usa base64 o genera on startup. Nuestro script lo genera al inicio si falta).
    - `CORS_ALLOW_ORIGIN`: `*` (o la URL de tu frontend cuando la tengas).
    - `AI_SERVICE_URL`: (URL del servicio AI que crearemos después).
7.  Railway desplegará automáticamente.

### Servicio 4: AI Service (Python)
1.  Click **New** -> **GitHub Repo** -> `oxford-plataforma`.
2.  **Root Directory**: `/backend/ai_service`.
3.  **Variables**:
    - `REDIS_URL`: (Pega la URL de Redis).
    - `SECRET_KEY`: (Crea una clave).
4.  Railway desplegará. Ve a **Settings** -> **Networking** y genera un **Domain** (ej: `oxford-ai.up.railway.app`).
5.  **IMPORTANTE**: Copia este dominio y actualiza la variable `AI_SERVICE_URL` en el **Servicio Backend**.

### Servicio 5: Frontend (React)
1.  Click **New** -> **GitHub Repo** -> `oxford-plataforma`.
2.  **Root Directory**: `/frontend`.
3.  **DockerfilePath**: `Dockerfile.railway`.
4.  **Variables**:
    - `VITE_API_URL`: (URL de tu backend, ej: `https://oxford-backend.up.railway.app`).
    - `VITE_AI_URL`: (URL de tu AI service).
5.  Genera un **Domain** en Networking (ej: `oxford-plataforma.up.railway.app`).
6.  **IMPORTANTE**: Actualiza `CORS_ALLOW_ORIGIN` en el Backend con este dominio.

---

## 🔄 Paso 3: Verificar Health Checks

- **Frontend**: Entra a tu dominio `https://oxford-plataforma...`.
- **Backend**: Entra a `https://oxford-backend.../health`. Debería decir "healthy".
- **AI**: Entra a `https://oxford-ai.../health`.

---

## 📝 Notas Importantes

1.  **Persistencia**: Los archivos subidos (fotos) en Railway **se borran** en cada despliegue si usas el sistema de archivos local.
    - **Solución Enterprise**: Configurar AWS S3 para subidas (soportado por el código, solo añade `AWS_S3_BUCKET` en variables).
    - Por ahora, funcionará, pero las fotos se pierden al actualizar.
    
2.  **Costos**: Railway cobra por uso (CPU/RAM).
    - Backend PHP + Postgres + Redis + Frontend puede costar ~$5-10/mes dependiendo del tráfico.
    - Tienen un "Trial Plan" de $5 gratis.

3.  **Dormir servicios**: Si no pagas, Railway puede "dormir" los servicios. Para producción real, actualiza al plan "Hobby" o "Pro".

---

## 🆘 Troubleshooting

- **Error 500 en Backend**: Revisa los logs en Railway (`Click servicio -> Logs`).
- **Error de Conexión BD**: Verifica que `DATABASE_URL` sea correcta.
- **CORS Error**: Verifica que `CORS_ALLOW_ORIGIN` en backend coincida con la URL del frontend (sin slash al final).
