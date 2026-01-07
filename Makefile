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
