#!/bin/bash
# Reconnect Ngrok Script

# Kill any existing ngrok
pkill ngrok

# Auth Token (Hardcoded for reliability based on troubleshooting)
export NGROK_AUTHTOKEN=371yItn8iaQfq1SrFe0QBJWK7ik_7oxUsG5MefDWE1dT172mL

# Start Ngrok in background with log redirect
echo "🚀 Restarting Ngrok Tunnel..."
nohup ngrok start --config=./ngrok.yml oxford-system > ngrok.log 2>&1 &

# Wait for startup
sleep 3

# Check status
if pgrep ngrok > /dev/null; then
    echo "✅ Ngrok is running (PID: $(pgrep ngrok))"
    # Get URL
    URL=$(curl -s http://localhost:4040/api/tunnels | grep -o 'https://[^"]*ngrok-free.dev')
    echo "🔗 Public URL: $URL"
else
    echo "❌ Ngrok failed to start. Check ngrok.log"
    cat ngrok.log
fi
