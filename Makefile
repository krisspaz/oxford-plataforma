# Oxford Plataforma - Development Commands
# ========================================
# Usage: make <command>

.PHONY: help install start stop test lint clean deploy

# Colors
GREEN  := $(shell tput -Txterm setaf 2)
YELLOW := $(shell tput -Txterm setaf 3)
RESET  := $(shell tput -Txterm sgr0)

## Help - Show available commands
help:
	@echo ''
	@echo 'Usage:'
	@echo '  ${YELLOW}make${RESET} ${GREEN}<target>${RESET}'
	@echo ''
	@echo 'Targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  ${YELLOW}%-15s${RESET} %s\n", $$1, $$2}' $(MAKEFILE_LIST)

## === SETUP ===

install: ## Install all dependencies
	@echo "📦 Installing frontend dependencies..."
	cd frontend && npm install
	@echo "📦 Installing backend dependencies..."
	cd backend/symfony && composer install
	@echo "✅ Dependencies installed!"

setup: install ## Full project setup
	@echo "🔑 Generating JWT keys..."
	cd backend/symfony && php bin/console lexik:jwt:generate-keypair --skip-if-exists
	@echo "🗄️ Setting up database..."
	cd backend/symfony && php bin/console doctrine:database:create --if-not-exists
	cd backend/symfony && php bin/console doctrine:migrations:migrate --no-interaction
	@echo "✅ Setup complete!"

## === DEVELOPMENT ===

start: ## Start all development servers
	@echo "🚀 Starting development servers..."
	@make -j3 start-frontend start-backend start-ai

start-frontend: ## Start frontend dev server
	cd frontend && npm run dev

start-backend: ## Start backend dev server
	cd backend/symfony && symfony server:start --no-tls

start-ai: ## Start AI service
	cd backend/ai_service && python main.py

## === TESTING ===

test: ## Run all tests
	@echo "🧪 Running tests..."
	@make test-backend
	@make test-frontend

test-backend: ## Run backend tests
	cd backend/symfony && php bin/phpunit

test-frontend: ## Run frontend tests
	cd frontend && npm run test -- --run

test-coverage: ## Run tests with coverage
	cd backend/symfony && php bin/phpunit --coverage-text
	cd frontend && npm run test -- --coverage

## === CODE QUALITY ===

lint: ## Run linters
	@echo "🔍 Linting code..."
	cd frontend && npm run lint
	cd backend/symfony && vendor/bin/php-cs-fixer fix --dry-run --diff

lint-fix: ## Fix lint issues
	cd frontend && npm run lint -- --fix
	cd backend/symfony && vendor/bin/php-cs-fixer fix

## === DATABASE ===

db-migrate: ## Run database migrations
	cd backend/symfony && php bin/console doctrine:migrations:migrate --no-interaction

db-diff: ## Generate new migration
	cd backend/symfony && php bin/console doctrine:migrations:diff

db-reset: ## Reset database (WARNING: destroys data)
	cd backend/symfony && php bin/console doctrine:database:drop --force --if-exists
	cd backend/symfony && php bin/console doctrine:database:create
	cd backend/symfony && php bin/console doctrine:migrations:migrate --no-interaction

db-seed: ## Load fixture data
	cd backend/symfony && php bin/console doctrine:fixtures:load --no-interaction

## === BUILD ===

build: ## Build for production
	@echo "🏗️ Building..."
	cd frontend && npm run build

## === CACHE ===

cache-clear: ## Clear all caches
	cd backend/symfony && php bin/console cache:clear
	@echo "✅ Cache cleared!"

## === CLEANUP ===

clean: ## Clean build artifacts
	rm -rf frontend/dist
	rm -rf backend/symfony/var/cache/*
	rm -rf backend/symfony/var/log/*
	@echo "🧹 Cleaned!"

## === DEPLOYMENT ===

deploy-staging: build ## Deploy to staging
	@echo "🚀 Deploying to staging..."
	# Add your staging deployment commands here

deploy-prod: build ## Deploy to production
	@echo "🚀 Deploying to production..."
	# Add your production deployment commands here

## === MONITORING ===
health-check: ## Verify all services are healthy
	@echo "🏥 Checking service health..."
	@curl -sf http://localhost:8000/health > /dev/null && echo "✅ Backend OK" || echo "❌ Backend DOWN"
	@curl -sf http://localhost:8001/health > /dev/null && echo "✅ AI Service OK" || echo "❌ AI Service DOWN"
	@docker-compose ps | grep -E "(Up|running)" || echo "❌ Some containers are down"

logs-tail: ## Tail all service logs
	docker-compose logs -f --tail=100

logs-backend: ## View backend logs
	docker-compose logs -f backend

logs-ai: ## View AI service logs
	docker-compose logs -f ai_service

## === BACKUP & RESTORE ===
db-backup: ## Backup database to /backups
	@./scripts/backup/backup_db.sh

db-restore: ## Restore database (usage: make db-restore FILE=backup.sql.gz)
	@if [ -z "$(FILE)" ]; then \
		echo "❌ Error: FILE parameter required"; \
		echo "Usage: make db-restore FILE=backup.sql.gz"; \
		exit 1; \
	fi
	@echo "⚠️  WARNING: This will OVERWRITE the current database!"
	@read -p "Type 'RESTORE' to continue: " confirm && [ "$$confirm" = "RESTORE" ]
	gunzip -c $(FILE) | docker exec -i postgres psql -U oxford_user oxford_db
	@echo "✅ Database restored from $(FILE)"

## === SECURITY ===

security-scan: ## Run comprehensive security audit
	@echo "🔒 Running security scans..."
	cd frontend && npm audit --audit-level=moderate
	cd backend/symfony && composer audit
	# docker run --rm -v $(PWD):/scan aquasec/trivy fs --severity HIGH,CRITICAL /scan

rotate-secrets: ## Rotate production secrets
	@echo "🔐 Rotating secrets..."
	@mkdir -p secrets
	@openssl rand -base64 32 > secrets/app_secret.txt
	@openssl rand -base64 24 > secrets/db_password.txt
	@echo "✅ New secrets generated in /secrets"
	@echo "⚠️  Action required:"
	@echo "   1. Update .env with new values"
	@echo "   2. Restart all services: docker-compose restart"

## === PERFORMANCE ===
load-test: ## Run k6 load tests
	@command -v k6 >/dev/null 2>&1 || { echo "❌ k6 not installed. Install: brew install k6"; exit 1; }
	k6 run infrastructure/load-tests/main.js

benchmark: ## Quick HTTP benchmark
	ab -n 1000 -c 10 -H "Authorization: Bearer test" http://localhost:8000/api/health

