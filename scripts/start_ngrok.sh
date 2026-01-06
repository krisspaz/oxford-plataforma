#!/bin/bash

# Check if ngrok is installed
if ! command -v ngrok &> /dev/null; then
    echo "❌ ngrok is not installed. Please install it first."
    exit 1
fi

# Check for NGROK_AUTHTOKEN in .env if not already set
if [ -z "$NGROK_AUTHTOKEN" ]; then
    if [ -f .env ]; then
        export NGROK_AUTHTOKEN=$(grep NGROK_AUTHTOKEN .env | cut -d '=' -f2)
    fi
fi

# Prompt for token if still missing
if [ -z "$NGROK_AUTHTOKEN" ] || [ "$NGROK_AUTHTOKEN" == "your_token_here" ]; then
    echo "⚠️ NGROK_AUTHTOKEN is missing."
    echo "Please paste your Ngrok Authtoken (from dashboard.ngrok.com):"
    read -r USER_TOKEN
    
    if [ -n "$USER_TOKEN" ]; then
        # Save to .env
        if [ -f .env ]; then
            # Remove existing line if present to avoid duplicates
            sed -i.bak '/NGROK_AUTHTOKEN/d' .env
            rm .env.bak
        fi
        echo "NGROK_AUTHTOKEN=$USER_TOKEN" >> .env
        export NGROK_AUTHTOKEN=$USER_TOKEN
        echo "✅ Token saved to .env"
    else
        echo "❌ No token provided. Exiting."
        exit 1
    fi
fi

echo "🚀 Starting ngrok tunnel for Oxford System (Port 8000)..."
ngrok start --config=./ngrok.yml oxford-system
