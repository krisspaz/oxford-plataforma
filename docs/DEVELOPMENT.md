# Oxford Plataforma - Development Guide

## Quick Start (< 5 minutes)

### Prerequisites
- Node.js 18+
- PHP 8.2+
- Composer 2.x
- Python 3.11+ (for AI service)

### 1. Clone and Install

```bash
# Clone repository
git clone [repo-url] oxford-plataforma
cd oxford-plataforma

# Install frontend dependencies
cd frontend
npm install

# Install backend dependencies
cd ../backend/symfony
composer install
```

### 2. Configure Environment

```bash
# Backend - copy and edit env file
cp .env.example .env
# Edit .env with your database and JWT settings

# Generate JWT keys
php bin/console lexik:jwt:generate-keypair
```

### 3. Database Setup

```bash
# Create database schema
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Load seed data (optional)
php bin/console doctrine:fixtures:load
```

### 4. Start Development Servers

```bash
# Terminal 1: Frontend
cd frontend
npm run dev

# Terminal 2: Backend
cd backend/symfony
symfony server:start
# or: php -S localhost:8000 -t public
```

**Frontend**: http://localhost:5173  
**Backend API**: http://localhost:8000/api

---

## Project Structure

```
oxford-plataforma/
├── frontend/                 # React application
│   ├── src/
│   │   ├── components/
│   │   │   └── shared/       # ← Reusable components (EntityManager, etc.)
│   │   ├── contexts/
│   │   ├── features/
│   │   ├── pages/
│   │   ├── services/
│   │   └── routes/
│   └── package.json
├── backend/
│   ├── symfony/              # PHP API
│   │   ├── src/
│   │   │   ├── Controller/
│   │   │   ├── Entity/
│   │   │   ├── Service/
│   │   │   └── Repository/
│   │   └── config/
│   └── ai_service/           # Python AI
└── docs/                     # Documentation
```

---

## Common Commands

### Frontend

```bash
npm run dev          # Start dev server
npm run build        # Production build
npm run lint         # Run ESLint
npm run test         # Run tests
```

### Backend

```bash
php bin/console cache:clear           # Clear cache
php bin/console debug:router          # List all routes
php bin/console doctrine:migrations:diff  # Generate migration
php bin/console doctrine:migrations:migrate  # Run migrations
```

---

## Coding Standards

### React/JavaScript
- Use functional components with hooks
- Use `useTheme()` for dark mode support
- API calls through `services/*.js` files
- Component naming: `PascalCase.jsx`

### PHP/Symfony
- PSR-12 coding standard
- Services for business logic (not controllers)
- Annotations for API Platform resources
- PHPDoc for all public methods

---

## Default Test Users

| Email | Password | Role |
|-------|----------|------|
| admin@oxford.edu | admin123 | ROLE_ADMIN |
| director@oxford.edu | director123 | ROLE_DIRECTOR |
| teacher@oxford.edu | teacher123 | ROLE_TEACHER |
| student@oxford.edu | student123 | ROLE_STUDENT |

---

## Troubleshooting

### JWT Token Issues
```bash
# Regenerate keys
php bin/console lexik:jwt:generate-keypair --overwrite

# Clear cache
php bin/console cache:clear
```

### CORS Errors
Check `config/packages/nelmio_cors.yaml` - ensure frontend URL is allowed.

### Database Connection
Verify `.env` DATABASE_URL is correct and database exists.

---

## Git Workflow

1. Create feature branch: `git checkout -b feature/my-feature`
2. Make changes and commit: `git commit -m "feat: description"`
3. Push and create PR: `git push origin feature/my-feature`

### Commit Message Format
- `feat:` New feature
- `fix:` Bug fix
- `docs:` Documentation
- `refactor:` Code refactoring
- `test:` Adding tests
