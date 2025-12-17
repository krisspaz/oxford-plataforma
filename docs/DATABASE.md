# Sistema Oxford - Entity Relationship Diagram

## Database Schema Overview

```mermaid
erDiagram
    %% Core Entities
    USER ||--o{ REFRESH_TOKEN : has
    USER ||--o{ AUDIT_LOG : creates
    USER {
        int id PK
        string email UK
        string password
        json roles
        boolean is_active
        datetime created_at
    }

    REFRESH_TOKEN {
        int id PK
        string token UK
        int user_id FK
        datetime expires_at
        string ip_address
        boolean is_revoked
    }

    %% Academic Structure
    ACADEMIC_LEVEL ||--o{ GRADE : contains
    ACADEMIC_LEVEL {
        int id PK
        string name
        string code
        int sort_order
    }

    GRADE ||--o{ SECTION : has
    GRADE ||--o{ ENROLLMENT : receives
    GRADE {
        int id PK
        string name
        string code
        int level_id FK
        int sort_order
    }

    SECTION {
        int id PK
        string name
        int grade_id FK
        int capacity
    }

    %% People
    STUDENT ||--o{ ENROLLMENT : has
    STUDENT ||--o{ ATTENDANCE : has
    STUDENT ||--o{ TASK_SUBMISSION : submits
    STUDENT {
        int id PK
        string student_code UK
        string first_name
        string last_name
        date birth_date
        boolean is_active
    }

    TEACHER ||--o{ SUBJECT_ASSIGNMENT : teaches
    TEACHER ||--o{ SCHEDULE : has
    TEACHER ||--o{ TASK : creates
    TEACHER {
        int id PK
        string employee_code UK
        string first_name
        string last_name
        string email
        string specialization
        boolean is_active
    }

    FAMILY ||--o{ FAMILY_STUDENT : has
    FAMILY_STUDENT }o--|| STUDENT : contains
    FAMILY {
        int id PK
        string name
        string phone
        string email
    }

    %% Academic Operations
    SCHOOL_CYCLE ||--o{ ENROLLMENT : for
    SCHOOL_CYCLE ||--o{ BIMESTER : contains
    SCHOOL_CYCLE {
        int id PK
        string name
        date start_date
        date end_date
        boolean is_active
    }

    BIMESTER ||--o{ GRADE_RECORD : for
    BIMESTER ||--o{ TASK : for
    BIMESTER {
        int id PK
        string name
        int number
        date start_date
        date end_date
    }

    SUBJECT ||--o{ SUBJECT_ASSIGNMENT : assigned
    SUBJECT ||--o{ TASK : for
    SUBJECT {
        int id PK
        string name
        string code
        boolean is_active
    }

    SUBJECT_ASSIGNMENT {
        int id PK
        int teacher_id FK
        int subject_id FK
        int grade_id FK
        int section_id FK
        int hours_per_week
    }

    ENROLLMENT {
        int id PK
        int student_id FK
        int grade_id FK
        int section_id FK
        int cycle_id FK
        string status
        date enrollment_date
    }

    %% Scheduling
    SCHEDULE {
        int id PK
        int teacher_id FK
        int subject_id FK
        int grade_id FK
        int section_id FK
        int day_of_week
        int period
        time start_time
        time end_time
        string classroom
    }

    ATTENDANCE }o--|| SCHEDULE : for
    ATTENDANCE }o--|| STUDENT : for
    ATTENDANCE {
        int id PK
        int student_id FK
        int schedule_id FK
        date date
        string status
        text notes
    }

    %% Tasks & Grades
    TASK ||--o{ TASK_GRADE : assigned_to
    TASK ||--o{ TASK_SUBMISSION : receives
    TASK {
        int id PK
        string title
        string type
        date due_date
        int points
        int teacher_id FK
        int subject_id FK
        int bimester_id FK
    }

    TASK_GRADE {
        int id PK
        int task_id FK
        int grade_id FK
        int section_id FK
    }

    TASK_SUBMISSION {
        int id PK
        int task_id FK
        int student_id FK
        datetime submitted_at
        decimal score
        text feedback
    }

    GRADE_RECORD {
        int id PK
        int student_id FK
        int subject_id FK
        int bimester_id FK
        decimal score
        string letter_grade
    }

    %% Financial
    STUDENT ||--o{ INVOICE : has
    INVOICE ||--o{ PAYMENT : receives
    INVOICE {
        int id PK
        int student_id FK
        decimal amount
        string status
        date issue_date
        date due_date
    }

    PAYMENT {
        int id PK
        int invoice_id FK
        decimal amount
        string method
        datetime created_at
    }

    %% Audit
    AUDIT_LOG {
        int id PK
        string action
        string entity_type
        int entity_id
        int user_id FK
        json changes
        string ip_address
        datetime created_at
    }
```

## Table Summary

| Category | Tables | Description |
|----------|--------|-------------|
| Auth | user, refresh_token | Authentication and sessions |
| Academic | grade, section, subject, enrollment | School structure |
| People | student, teacher, family | Actors |
| Schedule | schedule, attendance | Time management |
| Tasks | task, task_grade, task_submission | Assignments |
| Grades | grade_record, bimester | Academic records |
| Financial | invoice, payment | Billing |
| Audit | audit_log | Activity tracking |

## Key Relationships

1. **Student → Enrollment → Grade/Section**: Students enroll in specific grade and section
2. **Teacher → Subject Assignment → Subject/Grade**: Teachers assigned to teach subjects
3. **Schedule → Teacher + Subject + Grade**: Class schedule entries
4. **Task → Task Grade**: Tasks assigned to multiple grades
5. **Task → Task Submission**: Student submissions and grading
