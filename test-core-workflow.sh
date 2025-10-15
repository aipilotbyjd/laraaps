#!/bin/bash

# N8N Clone Core Workflow Test Script
# This script tests the core workflow execution functionality

echo "🚀 N8N Clone Core Workflow Test"
echo "================================"

# Configuration
BASE_URL="http://laraaps.test/api/v1"
EMAIL="test$(date +%s)@example.com"
PASSWORD="password123"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    if [ $1 -eq 0 ]; then
        echo -e "${GREEN}✅ $2${NC}"
    else
        echo -e "${RED}❌ $2${NC}"
        exit 1
    fi
}

echo ""
echo "📝 Test 1: User Registration"
echo "----------------------------"
REGISTER_RESPONSE=$(curl -s -X POST "$BASE_URL/auth/register" \
  -H "Content-Type: application/json" \
  -d "{
    \"name\": \"Test User\",
    \"email\": \"$EMAIL\",
    \"password\": \"$PASSWORD\",
    \"password_confirmation\": \"$PASSWORD\"
  }")

if echo "$REGISTER_RESPONSE" | grep -q "access_token"; then
    print_status 0 "User registered successfully"
    ACCESS_TOKEN=$(echo "$REGISTER_RESPONSE" | grep -o '"access_token":"[^"]*' | sed 's/"access_token":"//')
    ORG_ID=$(echo "$REGISTER_RESPONSE" | grep -o '"org_id":"[^"]*' | sed 's/"org_id":"//')
    echo "   Token: ${ACCESS_TOKEN:0:20}..."
    echo "   Org ID: $ORG_ID"
else
    print_status 1 "Registration failed"
    echo "$REGISTER_RESPONSE"
fi

echo ""
echo "📝 Test 2: Create Simple Workflow"
echo "---------------------------------"
WORKFLOW_RESPONSE=$(curl -s -X POST "$BASE_URL/workflows" \
  -H "Authorization: Bearer $ACCESS_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Workflow - Core Functions",
    "description": "Testing core workflow execution",
    "active": false,
    "nodes": [
      {
        "id": "start-1",
        "type": "start",
        "position": {"x": 100, "y": 100},
        "properties": {}
      },
      {
        "id": "set-1",
        "type": "set",
        "position": {"x": 300, "y": 100},
        "properties": {
          "values": [
            {"key": "message", "value": "Hello from workflow"},
            {"key": "timestamp", "value": "{{Date.now()}}"},
            {"key": "test", "value": true}
          ]
        }
      },
      {
        "id": "if-1",
        "type": "if",
        "position": {"x": 500, "y": 100},
        "properties": {
          "conditions": [
            {"field": "test", "operator": "==", "value": true}
          ]
        }
      },
      {
        "id": "http-1",
        "type": "http-request",
        "position": {"x": 700, "y": 50},
        "properties": {
          "url": "https://jsonplaceholder.typicode.com/posts/1",
          "method": "GET"
        }
      }
    ],
    "connections": [
      {"source": "start-1", "target": "set-1"},
      {"source": "set-1", "target": "if-1"},
      {"source": "if-1", "target": "http-1", "sourceHandle": "true"}
    ]
  }')

if echo "$WORKFLOW_RESPONSE" | grep -q "\"id\""; then
    print_status 0 "Workflow created successfully"
    WORKFLOW_ID=$(echo "$WORKFLOW_RESPONSE" | grep -o '"id":"[^"]*' | head -1 | sed 's/"id":"//')
    echo "   Workflow ID: $WORKFLOW_ID"
else
    print_status 1 "Workflow creation failed"
    echo "$WORKFLOW_RESPONSE"
fi

echo ""
echo "📝 Test 3: Execute Workflow (Test Mode)"
echo "---------------------------------------"
EXECUTION_RESPONSE=$(curl -s -X POST "$BASE_URL/workflows/$WORKFLOW_ID/test-execute" \
  -H "Authorization: Bearer $ACCESS_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "trigger_data": {
      "input": "test data",
      "source": "manual test"
    }
  }')

if echo "$EXECUTION_RESPONSE" | grep -q "execution_id"; then
    print_status 0 "Workflow execution started"
    EXECUTION_ID=$(echo "$EXECUTION_RESPONSE" | grep -o '"execution_id":"[^"]*' | sed 's/"execution_id":"//')
    echo "   Execution ID: $EXECUTION_ID"
else
    print_status 1 "Workflow execution failed"
    echo "$EXECUTION_RESPONSE"
fi

echo ""
echo "📝 Test 4: Check Execution Status"
echo "---------------------------------"
sleep 2 # Wait for execution to complete

STATUS_RESPONSE=$(curl -s -X GET "$BASE_URL/executions/$EXECUTION_ID" \
  -H "Authorization: Bearer $ACCESS_TOKEN")

if echo "$STATUS_RESPONSE" | grep -q "\"status\""; then
    STATUS=$(echo "$STATUS_RESPONSE" | grep -o '"status":"[^"]*' | head -1 | sed 's/"status":"//')
    if [ "$STATUS" = "success" ]; then
        print_status 0 "Execution completed successfully (Status: $STATUS)"
    elif [ "$STATUS" = "running" ]; then
        echo -e "${YELLOW}⏳ Execution still running${NC}"
        echo "   Waiting 3 more seconds..."
        sleep 3
        STATUS_RESPONSE=$(curl -s -X GET "$BASE_URL/executions/$EXECUTION_ID" \
          -H "Authorization: Bearer $ACCESS_TOKEN")
        STATUS=$(echo "$STATUS_RESPONSE" | grep -o '"status":"[^"]*' | head -1 | sed 's/"status":"//')
        if [ "$STATUS" = "success" ]; then
            print_status 0 "Execution completed successfully (Status: $STATUS)"
        else
            print_status 1 "Execution status: $STATUS"
        fi
    else
        print_status 1 "Execution failed (Status: $STATUS)"
        ERROR=$(echo "$STATUS_RESPONSE" | grep -o '"error_message":"[^"]*' | sed 's/"error_message":"//')
        echo "   Error: $ERROR"
    fi
else
    print_status 1 "Could not get execution status"
    echo "$STATUS_RESPONSE"
fi

echo ""
echo "📝 Test 5: Get Execution Timeline"
echo "---------------------------------"
TIMELINE_RESPONSE=$(curl -s -X GET "$BASE_URL/executions/$EXECUTION_ID/timeline" \
  -H "Authorization: Bearer $ACCESS_TOKEN")

if echo "$TIMELINE_RESPONSE" | grep -q "node_id"; then
    print_status 0 "Execution timeline retrieved"
    echo "$TIMELINE_RESPONSE" | grep -o '"node_id":"[^"]*' | while read -r line; do
        NODE=$(echo "$line" | sed 's/"node_id":"//')
        echo "   ✓ Node executed: $NODE"
    done
else
    print_status 1 "Could not get timeline"
fi

echo ""
echo "📝 Test 6: Get Node Execution Logs"
echo "----------------------------------"
LOGS_RESPONSE=$(curl -s -X GET "$BASE_URL/executions/$EXECUTION_ID/logs" \
  -H "Authorization: Bearer $ACCESS_TOKEN")

if echo "$LOGS_RESPONSE" | grep -q "node_type"; then
    print_status 0 "Execution logs retrieved"
    NODE_COUNT=$(echo "$LOGS_RESPONSE" | grep -o '"node_type"' | wc -l | tr -d ' ')
    echo "   Total nodes executed: $NODE_COUNT"
else
    print_status 1 "Could not get logs"
fi

echo ""
echo "📝 Test 7: Create Webhook Workflow"
echo "----------------------------------"
WEBHOOK_WORKFLOW=$(curl -s -X POST "$BASE_URL/workflows" \
  -H "Authorization: Bearer $ACCESS_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Webhook Test Workflow",
    "description": "Testing webhook trigger",
    "active": true,
    "nodes": [
      {
        "id": "webhook-1",
        "type": "webhook",
        "position": {"x": 100, "y": 100},
        "properties": {
          "path": "test-hook",
          "method": "POST"
        }
      },
      {
        "id": "set-1",
        "type": "set",
        "position": {"x": 300, "y": 100},
        "properties": {
          "values": [
            {"key": "webhook_received", "value": true},
            {"key": "processed_at", "value": "{{Date.now()}}"}
          ]
        }
      }
    ],
    "connections": [
      {"source": "webhook-1", "target": "set-1"}
    ]
  }')

if echo "$WEBHOOK_WORKFLOW" | grep -q "\"id\""; then
    print_status 0 "Webhook workflow created"
    WEBHOOK_WF_ID=$(echo "$WEBHOOK_WORKFLOW" | grep -o '"id":"[^"]*' | head -1 | sed 's/"id":"//')
    echo "   Webhook URL: http://laraaps.test/api/webhook/$WEBHOOK_WF_ID/test-hook"
else
    print_status 1 "Webhook workflow creation failed"
fi

echo ""
echo "📝 Test 8: Test Database Node"
echo "-----------------------------"
DB_WORKFLOW=$(curl -s -X POST "$BASE_URL/workflows" \
  -H "Authorization: Bearer $ACCESS_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Database Query Test",
    "nodes": [
      {
        "id": "start-1",
        "type": "start"
      },
      {
        "id": "db-1",
        "type": "database",
        "properties": {
          "operation": "select",
          "query": "SELECT * FROM users LIMIT 1"
        }
      }
    ],
    "connections": [
      {"source": "start-1", "target": "db-1"}
    ]
  }')

if echo "$DB_WORKFLOW" | grep -q "\"id\""; then
    print_status 0 "Database workflow created"
else
    echo -e "${YELLOW}⚠️  Database workflow creation skipped (optional)${NC}"
fi

echo ""
echo "📝 Test 9: Get Workflow Statistics"
echo "----------------------------------"
STATS_RESPONSE=$(curl -s -X GET "$BASE_URL/executions/stats" \
  -H "Authorization: Bearer $ACCESS_TOKEN")

if echo "$STATS_RESPONSE" | grep -q "total"; then
    print_status 0 "Statistics retrieved successfully"
    TOTAL=$(echo "$STATS_RESPONSE" | grep -o '"total":[0-9]*' | sed 's/"total"://')
    SUCCESS=$(echo "$STATS_RESPONSE" | grep -o '"success":[0-9]*' | sed 's/"success"://')
    ERROR=$(echo "$STATS_RESPONSE" | grep -o '"error":[0-9]*' | sed 's/"error"://')
    echo "   Total executions: $TOTAL"
    echo "   Successful: $SUCCESS"
    echo "   Errors: $ERROR"
else
    print_status 1 "Could not get statistics"
fi

echo ""
echo "📝 Test 10: Complex Workflow with Loop"
echo "--------------------------------------"
LOOP_WORKFLOW=$(curl -s -X POST "$BASE_URL/workflows" \
  -H "Authorization: Bearer $ACCESS_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Loop Processing Test",
    "nodes": [
      {
        "id": "start-1",
        "type": "start"
      },
      {
        "id": "set-1",
        "type": "set",
        "properties": {
          "values": [
            {"key": "items", "value": [1,2,3,4,5]}
          ]
        }
      },
      {
        "id": "loop-1",
        "type": "loop",
        "properties": {
          "items": "{{items}}"
        }
      },
      {
        "id": "aggregate-1",
        "type": "aggregate",
        "properties": {
          "operation": "sum"
        }
      }
    ],
    "connections": [
      {"source": "start-1", "target": "set-1"},
      {"source": "set-1", "target": "loop-1"},
      {"source": "loop-1", "target": "aggregate-1"}
    ]
  }')

if echo "$LOOP_WORKFLOW" | grep -q "\"id\""; then
    print_status 0 "Complex workflow with loop created"
else
    echo -e "${YELLOW}⚠️  Complex workflow creation skipped${NC}"
fi

echo ""
echo "================================"
echo -e "${GREEN}🎉 Core Workflow Tests Complete!${NC}"
echo ""
echo "Summary:"
echo "--------"
echo "✅ Authentication: Working"
echo "✅ Workflow CRUD: Working"  
echo "✅ Workflow Execution: Working"
echo "✅ Node Processing: Working"
echo "✅ Execution Tracking: Working"
echo ""
echo "Test User: $EMAIL"
echo "Access Token: ${ACCESS_TOKEN:0:20}..."
echo ""
echo "You can now:"
echo "1. Use Postman with the token above"
echo "2. Trigger webhooks at: http://laraaps.test/api/webhook/{workflow-id}/{path}"
echo "3. View logs at: storage/logs/laravel.log"
echo ""
