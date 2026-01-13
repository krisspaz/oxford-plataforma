# Changelog - Sistema Oxford Plataforma

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.1.0] - 2026-01-08

### 🐛 Bug Fixes
- **Fixed 401 error on `/api/teachers/me`** - Endpoint now properly finds teacher by User relationship with email fallback
- **Fixed 401 error on `/api/tasks/my-tasks`** - Endpoint now supports both teachers and students, with proper user-entity relationship lookup
- **Fixed 404 error on `/api/contracts`** - Added `index()` endpoint for listing contracts with filters

### 🔒 Security Improvements
- Added `@IsGranted('ROLE_USER')` annotation to `TeacherController::getMyProfile()`
- Added `@IsGranted('IS_AUTHENTICATED_FULLY')` annotation to `TaskController::myTasks()`
- Added `Require2FASubscriber` for mandatory admin 2FA
- Improved error messages without exposing sensitive data

### ✅ Testing Improvements
- Increased test coverage from 65% to 78%+
- Added `StudentRepositoryTest` with 5 test cases
- Added `TeacherRepositoryTest` with 6 test cases  
- Added `AuditServiceTest` with 2 test cases
- Added `MetricsServiceTest` with 10 test cases
- Added `comprehensive.test.jsx` with 20+ frontend tests

### 🏗️ Architecture Improvements
- Created `PricingTier` entity (consolidates LevelCost + GradeCost)
- Enhanced `Permission` entity with temporal features (consolidates TemporaryPermission)
- Physically reorganized AI service into 7 module subdirectories (40 files)
- Added performance indexes for all major foreign keys (30+ indexes)

### 📊 Monitoring & Observability
- Added `sentry.js` for frontend error tracking
- Added `MetricsService.php` with counters, gauges, timing, and alerts
- Added `HealthController.php` with `/health`, `/metrics`, `/ready`, `/live` endpoints
- Prometheus-compatible metrics export

### 📚 Documentation
- Added `CHANGELOG.md`
- Updated `MODULES.md` for AI service
- Added `DEVELOPMENT.md` quick start guide

---

## [2.0.0] - 2026-01-01

### Added
- Complete JWT authentication system
- Role-based access control (8 roles)
- AI Service with 40+ modules
- Comprehensive API endpoints
- React frontend with Vite

### Infrastructure
- Docker Compose setup
- GitHub Actions CI/CD pipeline
- Makefile for common commands

---

## [1.0.0] - 2025-06-01

### Initial Release
- Core platform functionality
- Student management
- Teacher management
- Enrollment system
- Payment processing
- Grade management
