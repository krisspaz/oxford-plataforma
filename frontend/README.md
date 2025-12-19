# Frontend - Colegio Connect

Este es el frontend de la aplicación Colegio Connect, construido con **React**, **Vite**, y **Tailwind CSS**.

## Estructura del Proyecto

```
src/
├── components/     # Componentes reutilizables (UI, Layouts)
├── contexts/       # Contextos globales (AuthContext, ThemeContext)
├── pages/          # Vistas principales (Dashboard, Login, Módulos)
├── services/       # Comunicación con el Backend (Axios)
├── hooks/          # Custom hooks
└── config/         # Configuraciones (Menús por rol)
```

## Autenticación

El sistema utiliza **JWT (JSON Web Tokens)** almacenados en `localStorage` para mantener la sesión del usuario.
- El `AuthContext` gestiona el estado global del usuario y verifica la expiración del token.
- Los roles se extraen del payload del token JWT (`ROLE_ADMIN`, `ROLE_TEACHER`, etc.).

## Scripts Disponibles

- `npm run dev`: Inicia el servidor de desarrollo en `http://localhost:5173`.
- `npm run build`: Construye la aplicación para producción.
- `npm run preview`: Previsualiza la build de producción localmente.

## Configuración y Variables de Entorno

Crear un archivo `.env` basado en `.env.example` (si existe) o definir:

```
VITE_API_URL=http://localhost:8000/api
```

## Módulos Principales

1.  **Dashboard**: Vista personalizada por rol.
2.  **Inscripciones**: Gestión de alumnos y familias.
3.  **Finanzas**: Pagos, facturación, exoneraciones y reportes de corte.
4.  **Académico**: Gestión de materias, notas y reportes.
