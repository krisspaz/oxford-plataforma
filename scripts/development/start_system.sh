#!/bin/bash

# Define colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}🔄 Initializing Oxford System...${NC}"

# Navigate to project root
cd "$(dirname "$0")/.." || exit 1
PROJECT_ROOT=$(pwd)

# 1. Cleanup existing processes
echo -e "${YELLOW}🧹 Cleaning up port 8000...${NC}"
# Kill process on port 8000 if exists
PID_8000=$(lsof -t -i:8000)
if [ -n "$PID_8000" ]; then
    kill -9 $PID_8000
    echo -e "${GREEN}✓ Killed process on port 8000 (PID: $PID_8000)${NC}"
fi

# Kill any running ngrok
echo -e "${YELLOW}🧹 Cleaning up ngrok...${NC}"
pkill ngrok && echo -e "${GREEN}✓ Cleaned up ngrok processes${NC}" || echo -e "${GREEN}✓ No ngrok processes found${NC}"

# 2. Start Backend
echo -e "${YELLOW}🚀 Starting Symfony Backend...${NC}"
cd "$PROJECT_ROOT/backend/symfony" || exit 1

# Check if symfony CLI is available, otherwise use php built-in server
if command -v symfony &> /dev/null; then
    echo "Using Symfony CLI..."
    symfony server:start -d --no-tls
    echo -e "${GREEN}✓ Symfony server started (daemon mode)${NC}"
else
    echo -e "${YELLOW}⚠️ Symfony CLI not found. Using PHP built-in server...${NC}"
    # Start PHP server in background
    php -S 127.0.0.1:8000 -t public > "$PROJECT_ROOT/backend.log" 2>&1 &
    echo -e "${GREEN}✓ PHP server started in background (PID: $!)${NC}"
fi

# 3. Health Check
echo -e "${YELLOW}❤️ Waiting for backend to be ready...${NC}"
MAX_RETRIES=30
COUNT=0
URL="http://127.0.0.1:8000"

while [ $COUNT -lt $MAX_RETRIES ]; do
    if curl -s --head "$URL" > /dev/null; then
        echo -e "${GREEN}✅ Backend is UP!${NC}"
        break
    fi
    sleep 1
    COUNT=$((COUNT+1))
    echo -n "."
done

if [ $COUNT -eq $MAX_RETRIES ]; then
    echo -e "${RED}❌ Backend failed to start. Check backend.log for details.${NC}"
    # tail "$PROJECT_ROOT/backend.log"
    exit 1
fi

# 4. Start Frontend (Optional but recommended)
echo -e "${YELLOW}🖥️  Starting Frontend...${NC}"
cd "$PROJECT_ROOT/frontend" || exit 1
# Check if node_modules exists
if [ ! -d "node_modules" ]; then
    echo -e "${YELLOW}📦 Installing frontend dependencies...${NC}"
    npm install
fi
# Start vite in background
npm run dev > "$PROJECT_ROOT/frontend.log" 2>&1 &
echo -e "${GREEN}✓ Frontend started on http://localhost:5173${NC}"

# 5. Start AI Service
echo -e "${YELLOW}🧠 Starting AI Service...${NC}"
cd "$PROJECT_ROOT/backend/ai_service" || exit 1
# Check if venv exists, if not create and install
if [ ! -d "venv" ]; then
    echo -e "${YELLOW}📦 Creating Python virtual environment...${NC}"
    python3 -m venv venv
    source venv/bin/activate
    pip install -r requirements.txt
else
    source venv/bin/activate
fi
# Start AI service in background
# Assuming main.py runs on port 8001 (based on vite config)
python main.py > "$PROJECT_ROOT/ai_service.log" 2>&1 &
echo -e "${GREEN}✓ AI Service started on http://localhost:8001${NC}"

# 6. Start Ngrok
echo -e "${YELLOW}🔗 Starting Ngrok...${NC}"
cd "$PROJECT_ROOT" || exit 1
./scripts/start_ngrok.sh

echo -e "\n${GREEN}✅ SYSTEM STARTED SUCCESSFULLY!${NC}"
echo -e " Backend:  http://localhost:8000 (Local) / <refer to ngrok window> (Public)"
echo -e " Frontend: http://localhost:5173"
echo -e " Logs:     $PROJECT_ROOT/backend.log, $PROJECT_ROOT/frontend.log"
