# API Documentation - Oxford Plataforma

## Base URLs

| Servicio | URL |
|----------|-----|
| Backend PHP | `http://localhost:8000/api` |
| AI Service | `http://localhost:8001` |

---

## Autenticación

### POST /api/login_check
Obtener token JWT.

**Request:**
```json
{
  "username": "admin@oxford.edu",
  "password": "oxford123"
}
```

**Response:**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
  "refresh_token": "..."
}
```

**Headers para requests autenticados:**
```
Authorization: Bearer <token>
```

---

## Estudiantes

### GET /api/students
Listar estudiantes.

**Query params:** `?page=1&limit=20&search=Juan`

### POST /api/students
Crear estudiante.

### GET /api/students/{id}
Ver detalle.

### PUT /api/students/{id}
Actualizar.

### DELETE /api/students/{id}
Eliminar.

---

## Docentes

### GET /api/teachers
### POST /api/teachers
### GET /api/teachers/{id}
### PUT /api/teachers/{id}

---

## Calificaciones

### GET /api/grades
Query: `?student_id=1&subject_id=2&period=1`

### POST /api/grades
```json
{
  "student_id": 1,
  "subject_id": 2,
  "grade": 85,
  "period": "1"
}
```

---

## Horarios

### GET /api/schedules
### POST /api/schedules/generate
Genera horario con IA.

---

## AI Service

### POST /process-command
Procesar comando de lenguaje natural.

```json
{
  "command": "¿Cuántos estudiantes hay en 3ero A?",
  "user_id": 1,
  "role": "teacher"
}
```

**Response:**
```json
{
  "response": "Hay 25 estudiantes en 3ero A.",
  "intent": "student_count",
  "confidence": 0.95,
  "actions": []
}
```

### GET /health
Estado del servicio.

```json
{
  "status": "healthy",
  "version": "2.0",
  "uptime": 3600
}
```

### POST /learn
Feedback para aprendizaje.

```json
{
  "query": "...",
  "response": "...",
  "feedback": "positive"
}
```

---

## Notificaciones

### GET /api/notifications
Obtener notificaciones del usuario.

### POST /api/notifications/{id}/read
Marcar como leída.

### POST /api/notifications/read-all
Marcar todas como leídas.

---

## Exportación

### GET /api/export/students?format=excel
### GET /api/export/grades?format=pdf&student_id=1

---

## Códigos de Error

| Código | Descripción |
|--------|-------------|
| 400 | Bad Request |
| 401 | No autenticado |
| 403 | Sin permisos |
| 404 | No encontrado |
| 422 | Validación fallida |
| 500 | Error interno |

---

## Rate Limiting

- 100 requests/minuto por IP
- 1000 requests/minuto por usuario autenticado
