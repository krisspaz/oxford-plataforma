#!/bin/bash
BASE_URL="http://localhost:8000"

echo "Verifying API Endpoints..."

check_endpoint() {
    url="$1"
    echo -n "Checking $url ... "
    status=$(curl -o /dev/null -s -w "%{http_code}\n" "$BASE_URL$url")
    if [ "$status" == "404" ]; then
        echo "❌ 404 NOT FOUND"
    elif [ "$status" == "500" ]; then
        echo "❌ 500 INTERNAL ERROR"
    else
        echo "✅ $status (Exists)"
    fi
}

check_endpoint "/api/contracts"
check_endpoint "/api/payments/overdue"
check_endpoint "/api/payments/totals"
check_endpoint "/api/teacher/summary?assignmentId=1"
check_endpoint "/api/bimesters/current"
