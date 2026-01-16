#!/bin/bash
# Smoke Test Script
echo "Running Smoke Tests..."
curl -f http://localhost:80/health || exit 1
curl -f http://localhost:80/api/status || exit 1
echo "Smoke tests passed."
