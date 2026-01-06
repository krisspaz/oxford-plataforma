# ADR-001: Microservices Architecture

**Date:** 2026-01-05  
**Status:** Accepted  
**Context:** System architecture decision

## Decision
Adopt a microservices architecture with three main services:
1. Frontend (React)
2. Backend API (Symfony)
3. AI Service (FastAPI)

## Rationale
- **Scalability**: Each service can scale independently
- **Technology fit**: Best language for each domain
- **Team specialization**: Frontend and backend can work in parallel
- **Deployment flexibility**: Independent deployments

## Consequences
- Need for circuit breaker pattern for resilience
- API gateway for routing and rate limiting
- Increased operational complexity

---

# ADR-002: FastAPI for AI Service

**Date:** 2026-01-05  
**Status:** Implemented  
**Context:** Python web framework selection

## Decision
Migrate from Flask to FastAPI for the AI service.

## Rationale
- **Async support**: Native async/await for better performance
- **Auto-documentation**: OpenAPI/Swagger generated automatically
- **Type safety**: Pydantic for request/response validation
- **Performance**: Significantly faster than Flask

## Consequences
- Need to refactor existing endpoints
- Better developer experience with typed DTOs

---

# ADR-003: Circuit Breaker Pattern

**Date:** 2026-01-05  
**Status:** Implemented  
**Context:** Microservice resilience

## Decision
Implement circuit breaker pattern for inter-service communication.

## Rationale
- **Fail fast**: Prevent cascade failures
- **Graceful degradation**: Return fallbacks when service is down
- **Self-healing**: Automatic recovery after timeout

## States
- CLOSED: Normal operation
- OPEN: Fail fast after 5 failures
- HALF_OPEN: Test recovery after 30s

---

# ADR-004: PostgreSQL for Production

**Date:** 2026-01-05  
**Status:** Proposed  
**Context:** Database selection

## Decision
Use PostgreSQL for production instead of SQLite.

## Rationale
- **Concurrency**: Better multi-user support
- **Features**: Full-text search, JSON, arrays
- **Reliability**: ACID compliant with replication
- **Scalability**: Read replicas, partitioning

## Migration Path
1. Update DATABASE_URL in .env
2. Run doctrine:migrations:migrate
3. Configure read replicas for heavy reads

---

# ADR-005: JWT Authentication

**Date:** 2026-01-05  
**Status:** Implemented  
**Context:** Authentication mechanism

## Decision
Use JWT tokens with refresh token rotation.

## Token Lifetimes
- Access token: 15 minutes
- Refresh token: 7 days

## Security Measures
- Tokens stored in httpOnly cookies
- CSRF protection via SameSite=Strict
- Refresh token rotation on each use

---

# ADR-006: Feature-Based Folder Structure

**Date:** 2026-01-05  
**Status:** Proposed  
**Context:** Frontend code organization

## Decision
Reorganize from flat pages/ to feature-based structure.

## Current Structure
```
pages/
├── Dashboard.jsx
├── Students.jsx
├── ... (60+ files)
```

## Proposed Structure
```
features/
├── auth/
│   ├── LoginPage.jsx
│   ├── authSlice.js
│   └── authApi.js
├── students/
│   ├── StudentsPage.jsx
│   ├── StudentDetail.jsx
│   └── studentsApi.js
├── scheduling/
│   └── ...
```

## Benefits
- Better code colocation
- Easier to find related files
- Cleaner imports
