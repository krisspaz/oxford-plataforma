# 🔐 Credenciales de Acceso - Oxford Plataforma

## Contraseña Universal
**Password:** `oxford123`

---

## Usuarios del Sistema

| # | Email | Rol | Permisos |
|---|-------|-----|----------|
| 1 | `admin@oxford.edu` | Super Admin | Acceso total |
| 2 | `director@oxford.edu` | Dirección | Reportes, aprobaciones |
| 3 | `secretary@oxford.edu` | Secretaría | Inscripciones, documentos |
| 4 | `accountant@oxford.edu` | Contabilidad | Pagos, finanzas |
| 5 | `coordination@oxford.edu` | Coordinación | Horarios, docentes |
| 6 | `informatics@oxford.edu` | Informática | Sistema, soporte |
| 7 | `teacher@oxford.edu` | Docente | Notas, tareas, asistencia |
| 8 | `student@oxford.edu` | Estudiante | Ver notas, tareas, pagos |
| 9 | `parent@oxford.edu` | Padre | Ver info de hijos |

---

## URLs de Acceso

### Web
- **Local:** http://localhost:5173
- **Producción:** https://oxford-frontend.onrender.com

### API
- **Local:** http://localhost:8000/api
- **Producción:** https://oxford-gateway.onrender.com/api

### Apps Móviles
- **iOS:** Xcode → Run
- **Android:** Android Studio → Run

---

## Login Endpoint
```
POST /api/login_check
{
  "email": "student@oxford.edu",
  "password": "oxford123"
}
```

---

*Generado: Enero 2026*
