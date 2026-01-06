# API Authentication Guide

## Overview
The system uses JWT (JSON Web Tokens) for secure stateless authentication.

## Flow
1. **Login**: Client sends POST `/api/login_check` with `{username, password}`.
2. **Token Issue**: Server validats credentials and returns `{token, refresh_token}`.
3. **Authenticated Requests**: Client sends `Authorization: Bearer <token>` header.
4. **Token Refresh**: Client sends POST `/api/token/refresh` with `{refresh_token}` when access token expires.

## Endpoints

### POST /api/login_check
**Request:**
```json
{
  "username": "admin@oxford.edu",
  "password": "password"
}
```

**Response (200 OK):**
```json
{
  "token": "eyJhbGciOi...",
  "refresh_token": "def50200..."
}
```

## Security Measures
- **Rate Limiting**: 5 attempts per minute.
- **Expiration**: Access tokens expire in 1 hour. Refresh tokens in 30 days.
- **HTTPS**: Required for all auth traffic.
