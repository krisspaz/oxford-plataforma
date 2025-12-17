# Contributing to Sistema Oxford

Thank you for your interest in contributing! This document provides guidelines and instructions.

## Development Setup

### Prerequisites
- Docker 24.0+
- Node.js 20+
- PHP 8.2+
- Git

### Quick Start
```bash
git clone https://github.com/krisspaz/oxford-plataforma.git
cd oxford-plataforma
cp .env.example .env
docker-compose up -d
```

---

## Code Style

### PHP (Backend)
- Follow PSR-12 coding standard
- Use PHP CS Fixer: `vendor/bin/php-cs-fixer fix`
- Run PHPStan: `vendor/bin/phpstan analyse src`

### JavaScript (Frontend)
- Use ESLint + Prettier
- Run: `npm run lint`
- Format: `npm run format`

### Python (AI Service)
- Follow PEP 8
- Use Black formatter: `black .`
- Run flake8: `flake8 .`

---

## Branch Naming

| Type | Pattern | Example |
|------|---------|---------|
| Feature | `feature/description` | `feature/add-2fa` |
| Bug Fix | `fix/description` | `fix/login-error` |
| Hotfix | `hotfix/description` | `hotfix/security-patch` |
| Docs | `docs/description` | `docs/api-endpoints` |

---

## Commit Messages

Follow Conventional Commits:

```
<type>(<scope>): <description>

[optional body]

[optional footer]
```

### Types
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation
- `style`: Formatting
- `refactor`: Code restructuring
- `test`: Adding tests
- `chore`: Maintenance

### Examples
```
feat(auth): add 2FA support with TOTP
fix(attendance): correct batch save validation
docs(api): add endpoint documentation
```

---

## Pull Request Process

1. **Fork** the repository
2. **Create** a feature branch
3. **Write** tests for new code
4. **Ensure** all tests pass
5. **Submit** PR with clear description

### PR Checklist
- [ ] Tests added/updated
- [ ] Documentation updated
- [ ] Code follows style guidelines
- [ ] No console errors
- [ ] Reviewed my own code

---

## Testing

### Backend
```bash
docker-compose exec backend vendor/bin/phpunit
```

### Frontend
```bash
cd frontend && npm test
```

### E2E
```bash
cd frontend && npx playwright test
```

### AI Service
```bash
cd backend/ai_service && pytest
```

---

## Project Structure

```
oxford-plataforma/
├── backend/
│   ├── symfony/           # PHP API
│   │   ├── src/
│   │   │   ├── Controller/
│   │   │   ├── Entity/
│   │   │   ├── Repository/
│   │   │   ├── Service/
│   │   │   └── DTO/
│   │   └── tests/
│   ├── ai_service/        # Python AI
│   └── nginx/             # Gateway
├── frontend/              # React
│   ├── src/
│   │   ├── components/
│   │   ├── pages/
│   │   ├── services/
│   │   └── hooks/
│   └── e2e/
├── docs/
└── scripts/
```

---

## Reporting Issues

### Bug Reports
Include:
- Steps to reproduce
- Expected behavior
- Actual behavior
- Environment (OS, browser)
- Screenshots if applicable

### Feature Requests
Include:
- Clear description
- Use case
- Proposed solution
- Alternatives considered

---

## Security

Report security vulnerabilities privately to: security@oxford.edu.gt

**Do NOT** create public issues for security bugs.

---

## Questions?

- Open a Discussion on GitHub
- Contact: dev@oxford.edu.gt

Thank you for contributing! 🎓
