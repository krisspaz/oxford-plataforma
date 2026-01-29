# 🗺️ Mapa Arquitectónico de Corpo Oxford

Este documento sirve como la fuente de verdad técnica para la arquitectura de la plataforma **Corpo Oxford**. Describe la infraestructura, el modelo de datos y los flujos operativos.

## 1. Stack Tecnológico

| Capa | Tecnología | Versión | Rol |
| :--- | :--- | :--- | :--- |
| **Frontend** | React + Vite + Tailwind | 18+ | Interfaz de Usuario (SPA) |
| **Backend** | Symfony (PHP) | 6.4/7.0 | API REST, Lógica de Negocio |
| **AI Layer** | Python (FastAPI/LangChain) | 3.10+ | Procesamiento Lenguaje Natural (Rhema) |
| **Database** | MySQL | 8.0 | Almacenamiento Relacional (Strict Mode) |
| **Cache/Queue**| Redis | 7.x | Cache, Sesiones, Mensajería Asíncrona |
| **Storage** | MinIO (S3 Compatible) | Latest | Almacenamiento de Archivos (Fotos, Docs) |
| **Gateway** | Nginx | 1.25 | Reverse Proxy, SSL Termination |

---

## 2. Infraestructura (Contenedores)

El sistema se despliega mediante **Docker Compose**. Todos los servicios se comunican dentro de la red interna `app_net`.

```mermaid
graph TD
    %% INTERNET
    User((Usuario)) -->|HTTPS| Nginx[GATEWAY: Nginx :8000]

    %% GATEWAY ROUTING
    Nginx -->|/api| Backend[API CORE: Symfony :9000]
    Nginx -->|/ai| AIService[AI BRAIN: Python :8001]
    Nginx -->|/| Frontend[UI: React :80]

    %% BACKEND CONNECTIONS
    Backend -->|Data| Database[(MySQL :3306)]
    Backend -->|Cache/Queue| Redis[(Redis :6379)]
    Backend -->|Files| MinIO[(MinIO :9000)]
    Backend -->|Async Job| Worker(Worker PHP)

    %% AI CONNECTIONS
    AIService -->|Logs/Learning| Database
    AIService -->|RAG Documents| MinIO

    %% WORKER CONNECTIONS
    Worker -->|Consume| Redis
    Worker -->|Persist| Database

    classDef container fill:#e1f5fe,stroke:#01579b,stroke-width:2px;
    classDef db fill:#fff3e0,stroke:#ff6f00,stroke-width:2px,stroke-dasharray: 5 5;
    class Backend,Frontend,AIService,Nginx,Worker container;
    class Database,Redis,MinIO db;
```

---

## 3. Modelo de Datos (Esquema Multi-Tenant)

El núcleo del sistema es la **Arquitectura Multi-Tenant** donde una sola base de datos (`oxford_db`) aloja a múltiples colegios (`Tenant`), pero los datos están estrictamente segregados lógicamente.

### Diagrama de Entidades (ERD Maestro)

```mermaid
classDiagram
    %% --- GLOBAL CONTEXT ---
    class Tenant {
        +int id
        +string name ("Colegio Oxford")
        +string slug ("oxford")
        +settings JSON
    }

    class SchoolCycle {
        +int id
        +string name ("2026")
        +date start_date
        +date end_date
        +bool is_active
        +tenant_id FK
    }

    %% --- ACTORES Y SEGURIDAD ---
    class User {
        +int id
        +string email
        +string password_hash
        +array roles
        +tenant_id FK
    }

    class Person {
        +int id
        +string first_name
        +string last_name
        +string dpi
        +string phone
        +tenant_id FK
        <<Abstract>>
    }

    class Teacher {
        +string employee_code
        +string specialization
    }

    class Student {
        +string carnet
        +float academic_risk_score
    }

    class Guardian {
        +string billing_info
    }

    %% --- ESTRUCTURA ACADÉMICA ---
    class AcademicLevel {
        +string name (Primaria, Basicos)
        +tenant_id FK
    }

    class Grade {
        +string name (1ro Primaria)
        +tenant_id FK
    }

    class Section {
        +string name (A, B)
        +tenant_id FK
    }

    class Subject {
        +string name (Matemáticas)
        +string code (MAT101)
        +tenant_id FK
    }

    %% --- OPERACIONES ---
    class Enrollment {
        +date enrolled_at
        +string status (INSCRITO, RETIRADO)
        +tenant_id FK
    }

    class SubjectAssignment {
        +int hours_per_week
        +tenant_id FK
    }

    class GradeRecord {
        +float score
        +tenant_id FK
    }

    class Attendance {
        +date date
        +string status (P, A, T)
        +tenant_id FK
    }

    %% --- RELACIONES ---
    %% Tenant Isolation
    Tenant "1" -- "*" User : Owns
    Tenant "1" -- "*" SchoolCycle : Configures
    
    %% User Identity
    User "1" -- "1" Person : Profile
    Person <|-- Teacher : Extends
    Person <|-- Student : Extends
    Person <|-- Guardian : Extends

    %% Hierarchy
    AcademicLevel "1" *-- "*" Grade
    Grade "1" *-- "*" Section

    %% Core Operations
    SchoolCycle "1" -- "*" Enrollment : Scope
    Student "1" -- "*" Enrollment : Has history
    Enrollment "*" -- "1" Grade : Placed in
    Enrollment "*" -- "1" Section : Assigned to

    %% Teaching
    Teacher "1" -- "*" SubjectAssignment : Teaches
    SubjectAssignment "*" -- "1" Subject : The Course
    SubjectAssignment "*" -- "1" Grade : Target Grade
    SubjectAssignment "*" -- "1" SchoolCycle : Period

    %% Grading
    SubjectAssignment "1" -- "*" GradeRecord : Generates
    Student "1" -- "*" GradeRecord : Earns
    GradeRecord "*" -- "1" Bimester : Period
```

---

## 4. Flujos Críticos

### A. Autenticación y Resolución de Tenant
1. **Login**: Usuario envía credenciales a `/api/login_check`.
2. **JWT**: Backend valida y emite un Token JWT que incluye `{"tenant_id": 1, "slug": "oxford"}`.
3. **Guard**: Para peticiones subsiguientes (`Bearer Token`), el `TenantListener` intercepta la petición.
4. **Filtro**: Se activa `TenantFilter` de Doctrine, inyectando `AND tenant_id = 1` en **todas** las consultas SQL automáticamente.

### B. Inscripción (Enrollment)
1. **Secretaria** selecciona un Estudiante existente o crea uno nuevo.
2. Selecciona el **Ciclo Escolar** activo y el **Grado** destino.
3. Se crea registro en tabla `enrollment`.
4. El sistema valida cupos (futuro) y genera cargos financieros (si aplica).

### C. Calificaciones (Grading)
1. **Docente** ve sus cursos (`SubjectAssignment`).
2. Selecciona un curso (ej. Matemáticas).
3. Backend consulta `enrollment` filtrando por el Grado del curso.
4. Docente ingresa notas para el Bimestre activo.
5. Se guardan en `grade_record`.

---

## 5. Módulo de IA (Rhema)
El servicio de IA (`ai_service`) corre independiente pero comparte la base de datos MySQL.

- **Memoria**: Lee/Escribe en tabla `ai_interactions` y `ai_memory` en MySQL.
- **Contexto**: Al recibir una pregunta, consulta (RAG) documentos en MinIO y reglas en MySQL (`institutional_rules`).
- **Feedback**: El sistema aprende de correcciones explícitas guardadas en `ai_feedback`.
