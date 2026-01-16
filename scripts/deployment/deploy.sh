#!/bin/bash
# Deploy script for Oxford Plataforma
# Usage: ./deploy.sh [staging|production]

set -e

ENVIRONMENT=${1:-staging}
PROJECT_NAME="oxford-plataforma"

echo "🚀 Deploying to $ENVIRONMENT..."

# Validate environment
if [[ "$ENVIRONMENT" != "staging" && "$ENVIRONMENT" != "production" ]]; then
    echo "❌ Invalid environment. Use 'staging' or 'production'"
    exit 1
fi

# Load environment variables
if [ -f ".env.$ENVIRONMENT" ]; then
    source ".env.$ENVIRONMENT"
fi

# ==========================================
# BUILD PHASE
# ==========================================

echo "📦 Building Docker images..."

# Build images with environment tag
docker build -t "$PROJECT_NAME-frontend:$ENVIRONMENT" ./frontend
docker build -t "$PROJECT_NAME-backend:$ENVIRONMENT" ./backend/symfony
docker build -t "$PROJECT_NAME-ai:$ENVIRONMENT" ./backend/ai_service

echo "✅ Build complete"

# ==========================================
# TEST PHASE
# ==========================================

echo "🧪 Running pre-deploy tests..."

# Frontend tests
cd frontend && npm test -- --passWithNoTests && cd ..

# Backend tests
cd backend/symfony && ./vendor/bin/phpunit --stop-on-failure && cd ../..

echo "✅ Tests passed"

# ==========================================
# DEPLOY PHASE
# ==========================================

if [ "$ENVIRONMENT" == "staging" ]; then
    echo "🌐 Deploying to staging..."
    
    docker-compose -f docker-compose.staging.yml down
    docker-compose -f docker-compose.staging.yml up -d
    
    # Run migrations
    docker-compose -f docker-compose.staging.yml exec backend \
        php bin/console doctrine:migrations:migrate --no-interaction
    
    # Clear cache
    docker-compose -f docker-compose.staging.yml exec backend \
        php bin/console cache:clear
        
elif [ "$ENVIRONMENT" == "production" ]; then
    echo "🌐 Deploying to production..."
    
    # Confirm production deploy
    read -p "⚠️  Are you sure you want to deploy to PRODUCTION? (y/N) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo "❌ Deployment cancelled"
        exit 1
    fi
    
    # Blue-green deployment
    docker-compose -f docker-compose.production.yml up -d --scale backend=2
    
    # Health check
    echo "🔍 Running health checks..."
    sleep 10
    
    HEALTH=$(curl -s http://localhost:8000/health | jq -r '.status')
    if [ "$HEALTH" != "healthy" ]; then
        echo "❌ Health check failed! Rolling back..."
        docker-compose -f docker-compose.production.yml down
        exit 1
    fi
    
    # Run migrations
    docker-compose -f docker-compose.production.yml exec backend \
        php bin/console doctrine:migrations:migrate --no-interaction
    
    # Clear cache
    docker-compose -f docker-compose.production.yml exec backend \
        php bin/console cache:clear --env=prod
fi

echo "✅ Deployment complete!"

# ==========================================
# POST-DEPLOY
# ==========================================

echo "📊 Post-deploy verification..."

# Health checks
echo "Checking services..."
curl -s http://localhost:8000/health | jq .
curl -s http://localhost:8001/health | jq .

echo ""
echo "🎉 Deployment to $ENVIRONMENT successful!"
echo "   Frontend: http://localhost:5173"
echo "   Backend:  http://localhost:8000"
echo "   AI:       http://localhost:8001/docs"
