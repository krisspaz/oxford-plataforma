#!/bin/bash
# ============================================
# Oxford AI Service - Production Startup Script
# ============================================
# This script starts the Flask AI microservice
# Required: Python 3.8+, dependencies installed via pip

cd "$(dirname "$0")"

# Check if already running
if lsof -Pi :8001 -sTCP:LISTEN -t >/dev/null ; then
    echo "⚠️  AI Service already running on port 8001"
    exit 0
fi

# Create virtual environment if not exists
if [ ! -d "venv" ]; then
    echo "📦 Creating Python virtual environment..."
    python3 -m venv venv
fi

# Activate and install dependencies
source venv/bin/activate
pip install -q flask flask-cors python-jose werkzeug

# Start the service
echo "🚀 Starting Oxford AI Service on port 8001..."
nohup python main.py > ai_service.log 2>&1 &

sleep 2

if lsof -Pi :8001 -sTCP:LISTEN -t >/dev/null ; then
    echo "✅ AI Service started successfully!"
    echo "   Logs: $(pwd)/ai_service.log"
else
    echo "❌ Failed to start AI Service. Check ai_service.log"
    tail -20 ai_service.log
    exit 1
fi
