# Sistema Oxford - API Documentation

## Base URL
```
http://localhost:8000/api
```

## Authentication

All protected endpoints require JWT authentication via HttpOnly cookies or Bearer token.

### Headers (Alternative to cookies)
```
Authorization: Bearer <jwt_token>
X-CSRF-Token: <csrf_token>
```

---

## Auth Endpoints

### POST /api/auth/login
Authenticate user and receive HttpOnly cookies.

**Request:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "twoFactorCode": "123456" // Optional, only if 2FA enabled
}
```

**Response (200):**
```json
{
  "success": true,
  "user": {
    "id": 1,
    "email": "user@example.com",
    "roles": ["ROLE_USER"],
    "isActive": true
  },
  "message": "Login exitoso"
}
```

**Cookies Set:**
- `access_token` (HttpOnly, Secure, SameSite=Strict)
- `refresh_token` (HttpOnly, Secure, SameSite=Strict)
- `csrf_token` (readable by JS for CSRF protection)

---

### POST /api/auth/refresh
Refresh access token using refresh token cookie.

**Response (200):**
```json
{
  "success": true,
  "user": { ... },
  "message": "Token refreshed"
}
```

---

### POST /api/auth/logout
Logout and revoke current session.

**Response (200):**
```json
{
  "success": true,
  "message": "Sesión cerrada correctamente"
}
```

---

### GET /api/auth/me
Get current authenticated user info.

**Response (200):**
```json
{
  "user": { ... },
  "sessions": [
    {
      "id": 1,
      "ipAddress": "127.0.0.1",
      "userAgent": "Mozilla/5.0...",
      "createdAt": "2024-12-16 10:30:00"
    }
  ]
}
```

---

## 2FA Endpoints

### GET /api/auth/2fa/status
Check if 2FA is enabled for current user.

### POST /api/auth/2fa/initialize
Initialize 2FA setup (generates QR code).

### POST /api/auth/2fa/enable
Enable 2FA after verification.

### POST /api/auth/2fa/disable
Disable 2FA (requires code verification).

---

## Task Endpoints

### GET /api/tasks
List tasks with optional filters.

**Query Parameters:**
- `teacherId` (int)
- `bimesterId` (int)
- `subjectId` (int)
- `gradeId` (int)
- `status` (string: active, completed, cancelled)

**Response (200):**
```json
[
  {
    "id": 1,
    "title": "Tarea de Matemáticas",
    "type": "tarea",
    "dueDate": "2024-12-20",
    "points": 100,
    "status": "active",
    "subject": { "id": 1, "name": "Matemáticas" },
    "teacher": { "id": 1, "name": "Prof. García" },
    "grades": [{ "gradeId": 1, "gradeName": "1ro Primaria" }],
    "submissionCount": 15,
    "pendingCount": 10
  }
]
```

---

### POST /api/tasks
Create a new task.

**Request:**
```json
{
  "title": "Examen Parcial",
  "description": "Examen del primer bimestre",
  "type": "examen",
  "dueDate": "2024-12-20",
  "points": 100,
  "teacherId": 1,
  "subjectId": 1,
  "bimesterId": 1,
  "cycleId": 1,
  "grades": [
    { "gradeId": 1, "sectionId": 1 },
    { "gradeId": 1, "sectionId": 2 }
  ]
}
```

---

### GET /api/tasks/{id}/submissions
Get all submissions for a task.

### POST /api/tasks/{id}/submit
Submit a task (for students).

### POST /api/tasks/submissions/{id}/grade
Grade a submission.

---

## Schedule Endpoints

### GET /api/schedule/my-schedule
Get current teacher's schedule.

### GET /api/schedule/weekly/{teacherId}
Get weekly schedule organized by days.

### GET /api/schedule/current-class/{teacherId}
Get current active class for a teacher.

---

## Attendance Endpoints

### GET /api/attendance/by-schedule/{scheduleId}
Get attendance for a class.

**Query Parameters:**
- `date` (string: YYYY-MM-DD, default: today)

### POST /api/attendance/batch
Save attendance for multiple students.

**Request:**
```json
{
  "scheduleId": 1,
  "date": "2024-12-16",
  "attendances": [
    { "studentId": 1, "status": "present" },
    { "studentId": 2, "status": "late", "notes": "10 minutos tarde" },
    { "studentId": 3, "status": "absent" }
  ]
}
```

### GET /api/attendance/report/{studentId}/bimester/{bimesterId}
Get attendance report for a student.

---

## Error Responses

All endpoints may return these errors:

### 400 Bad Request
```json
{
  "error": "Validation failed",
  "details": {
    "email": "Email is required",
    "password": "Password must be at least 6 characters"
  }
}
```

### 401 Unauthorized
```json
{
  "error": "Invalid or expired token"
}
```

### 403 Forbidden
```json
{
  "error": "Access denied"
}
```

### 404 Not Found
```json
{
  "error": "Resource not found"
}
```

### 429 Too Many Requests
```json
{
  "error": "Rate limit exceeded. Please slow down."
}
```

### 500 Internal Server Error
```json
{
  "error": "An unexpected error occurred"
}
```

---

## Rate Limits

| Endpoint | Limit |
|----------|-------|
| /api/auth/login | 5/minute |
| /api/auth/2fa/* | 5/minute |
| /api/* (general) | 100/minute |

---

## Status Codes Summary

| Code | Meaning |
|------|---------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 429 | Too Many Requests |
| 500 | Server Error |
