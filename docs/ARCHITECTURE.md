# 🏗️ Architecture Documentation

## System Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                        LOAD BALANCER                            │
│                      (nginx / Cloudflare)                       │
└──────────────────────────┬──────────────────────────────────────┘
                           │
          ┌────────────────┼────────────────┐
          ▼                ▼                ▼
┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐
│   Frontend      │ │   Backend API   │ │   AI Service    │
│  React + Vite   │ │  Symfony 7      │ │   FastAPI       │
│  Port: 5173     │ │  Port: 8000     │ │   Port: 8001    │
└────────┬────────┘ └────────┬────────┘ └────────┬────────┘
         │                   │                   │
         │                   ▼                   │
         │          ┌─────────────────┐          │
         │          │   PostgreSQL    │          │
         │          │   Primary DB    │◄─────────┘
         │          │   Port: 5432    │
         │          └────────┬────────┘
         │                   │
         │          ┌────────▼────────┐
         │          │     Redis       │
         └─────────►│  Cache/Queue    │
                    │   Port: 6379    │
                    └─────────────────┘
```

---

## Microservices Architecture

### Frontend (React)
- **Stack**: React 18, Vite, TailwindCSS
- **Responsibilities**: UI rendering, state management, routing
- **Communication**: REST API calls to Backend + AI Service

### Backend API (Symfony)
- **Stack**: PHP 8.2, Symfony 7, Doctrine ORM
- **Responsibilities**: 
  - Authentication (JWT)
  - Business logic
  - Data persistence
  - API Gateway

### AI Service (FastAPI)
- **Stack**: Python 3.11, FastAPI, Pydantic, scikit-learn
- **Responsibilities**:
  - NLP processing
  - Schedule optimization
  - Predictive analytics
  - Learning from feedback

---

## Data Flow

```
User Action → Frontend → API Gateway → Backend/AI → Database → Response
                              │
                              ▼
                    ┌──────────────────┐
                    │ Circuit Breaker  │
                    │ (Retry + Fallback)
                    └──────────────────┘
```

### Circuit Breaker Pattern

```python
# States: CLOSED → OPEN → HALF_OPEN
- CLOSED: Normal operation
- OPEN: Fail fast (after 5 failures)
- HALF_OPEN: Test if service recovered

Fallback Strategy:
1. Return cached data if available
2. Return degraded response
3. Queue for retry
```

---

## Database Schema (ERD)

```
┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│    User      │     │   Student    │     │   Teacher    │
├──────────────┤     ├──────────────┤     ├──────────────┤
│ id           │     │ id           │     │ id           │
│ email        │◄───►│ user_id (FK) │     │ user_id (FK) │
│ password     │     │ grade_id     │     │ speciality   │
│ roles[]      │     │ section_id   │     │ hire_date    │
│ two_factor   │     │ family_id    │     └──────────────┘
└──────────────┘     └──────────────┘

┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│    Grade     │     │   Subject    │     │ GradeRecord  │
├──────────────┤     ├──────────────┤     ├──────────────┤
│ id           │     │ id           │     │ id           │
│ name         │     │ name         │     │ student_id   │
│ level        │◄───►│ grade_id     │     │ subject_id   │
│ sections[]   │     │ teacher_id   │     │ grade        │
└──────────────┘     │ hours_week   │     │ period       │
                     └──────────────┘     └──────────────┘

┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│   Payment    │     │   AuditLog   │     │ Notification │
├──────────────┤     ├──────────────┤     ├──────────────┤
│ id           │     │ id           │     │ id           │
│ student_id   │     │ action       │     │ user_id      │
│ amount       │     │ entity_type  │     │ type         │
│ status       │     │ entity_id    │     │ message      │
│ due_date     │     │ user_id      │     │ read         │
│ paid_at      │     │ changes      │     │ created_at   │
└──────────────┘     │ ip_address   │     └──────────────┘
                     │ created_at   │
                     └──────────────┘
```

---

## Security Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    Security Layers                          │
├─────────────────────────────────────────────────────────────┤
│ 1. WAF (Web Application Firewall)                          │
│ 2. Rate Limiting (100 req/min per IP)                      │
│ 3. JWT Authentication + Refresh Tokens                     │
│ 4. Role-Based Access Control (RBAC)                        │
│ 5. 2FA (TOTP + Backup Codes)                               │
│ 6. Input Validation + Sanitization                         │
│ 7. SQL Injection Protection (Doctrine ORM)                 │
│ 8. XSS Protection (React escaping)                         │
│ 9. CSRF Protection (SameSite cookies)                      │
│ 10. Data Encryption (at-rest + in-transit)                 │
└─────────────────────────────────────────────────────────────┘

Security Headers:
- Content-Security-Policy
- Strict-Transport-Security
- X-Frame-Options: DENY
- X-Content-Type-Options: nosniff
```

---

## Deployment Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    Production Environment                    │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌─────────────┐   ┌─────────────┐   ┌─────────────┐       │
│  │ Frontend    │   │ Backend     │   │ AI Service  │       │
│  │ Container   │   │ Container   │   │ Container   │       │
│  │ (nginx)     │   │ (php-fpm)   │   │ (uvicorn)   │       │
│  └─────────────┘   └─────────────┘   └─────────────┘       │
│                                                             │
│  ┌─────────────┐   ┌─────────────┐   ┌─────────────┐       │
│  │ PostgreSQL  │   │ Redis       │   │ Prometheus  │       │
│  │ Primary     │   │ Cache       │   │ + Grafana   │       │
│  └─────────────┘   └─────────────┘   └─────────────┘       │
│                                                             │
│                    Kubernetes / Docker Swarm                │
└─────────────────────────────────────────────────────────────┘
```

---

## Scalability Strategy

### Horizontal Scaling
1. **Stateless services** - All services can be replicated
2. **Load balancing** - Round-robin or least-connections
3. **Database read replicas** - For read-heavy operations
4. **Redis clustering** - For cache distribution

### Vertical Scaling
1. **PostgreSQL optimization** - Indexes, partitioning
2. **Query optimization** - Eager loading, caching
3. **CDN for static assets** - Reduce server load

---

## Monitoring & Observability

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   Metrics    │    │    Logs      │    │   Traces     │
│  Prometheus  │    │  ELK Stack   │    │   Jaeger     │
└──────────────┘    └──────────────┘    └──────────────┘
       │                   │                   │
       └───────────────────┼───────────────────┘
                           ▼
                  ┌──────────────┐
                  │   Grafana    │
                  │  Dashboards  │
                  └──────────────┘
                           │
                           ▼
                  ┌──────────────┐
                  │   Alerting   │
                  │ PagerDuty/   │
                  │ Slack        │
                  └──────────────┘
```

---

## Technology Stack Summary

| Layer | Technology | Purpose |
|-------|------------|---------|
| Frontend | React 18, Vite | UI/UX |
| CSS | TailwindCSS | Styling |
| State | React Context + Query | State management |
| Backend | Symfony 7 | API + Business logic |
| ORM | Doctrine | Database abstraction |
| Auth | LexikJWT | JWT tokens |
| AI | FastAPI + Pydantic | ML/NLP services |
| Database | PostgreSQL | Primary storage |
| Cache | Redis | Session + cache |
| Queue | Redis/RabbitMQ | Async jobs |
| Container | Docker | Deployment |
| Orchestration | K8s/Swarm | Scaling |
| Monitoring | Prometheus/Grafana | Metrics |
| Logging | ELK Stack | Log aggregation |
