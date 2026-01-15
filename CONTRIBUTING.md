# Contributing to Oxford Platform

Thank you for your interest in contributing to the Oxford Platform project! We are committed to building a robust, secure, and scalable school management system.

## 🚀 Getting Started

1.  **Clone the repository**
    ```bash
    git clone https://github.com/krisspaz/oxford-plataforma.git
    cd oxford-plataforma
    ```

2.  **Environment Setup**
    - Copy `.env.example` to `.env` in root, backend, and frontend directories.
    - Ensure Docker Desktop is running.

3.  **Start the Stack**
    ```bash
    docker-compose up -d --build
    ```

## 🛠️ Development Guidelines

### 1. Code Style
- **Frontend**: We use ESLint + Prettier. Run `npm run lint` before committing.
- **Backend**: Follow PSR-12 standards. Run `php-cs-fixer` if available.
- **Naming**:
    - Variables: `camelCase` (JS), `camelCase` (PHP properties), `snake_case` (DB columns).
    - Components: `PascalCase` (e.g., `StudentList.jsx`).
    - Commits: Conventional Commits (e.g., `feat: add student attendance`, `fix: login error`).

### 2. Architecture Patterns
- **Frontend**: Feature-based folder structure (`src/features/student/...`).
    - Use `SkeletonLoader` for loading states.
    - Avoid hardcoded API URLs; use environment variables.
- **Backend**:
    - Slim Controllers, Fat Services.
    - Entities must be exposed via API Platform resources where possible.
    - Use DTOs for complex data transfer.

### 3. Testing Policy 🚨
- **Unit Tests**: Required for all critical business logic (calculations, auth).
- **Smoke Tests**: Run `php bin/phpunit` to verify backend integrity before push.
- **Snapshots**: Frontend visual changes should be verified manually until E2E is fully automated.

## 🔄 Workflow

1.  Create a branch: `git checkout -b feature/my-feature`
2.  Make changes & test locally.
3.  Commit: `git commit -m "feat: description"`
4.  Push: `git push origin feature/my-feature`
5.  Open a Pull Request (PR) for review.

## 🤝 Code of Conduct
Be respectful, constructive, and collaborative. We are building a tool to improve education.
