# API Quick Reference Guide

Quick reference for the most commonly used endpoints in your n8n Clone.

## 🔐 Authentication

### Register New User
```http
POST /api/v1/auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### Login
```http
POST /api/v1/auth/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}

Response:
{
  "token": "1|xxxxxxxxxxx",
  "user": {...}
}
```

### Get Current User
```http
GET /api/v1/auth/me
Authorization: Bearer YOUR_TOKEN
```

---

## 🔄 Workflows

### List All Workflows
```http
GET /api/v1/workflows
Authorization: Bearer YOUR_TOKEN
```

### Create Workflow
```http
POST /api/v1/workflows
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "name": "GitHub to Slack",
  "description": "Send GitHub events to Slack",
  "active": false,
  "nodes": [
    {
      "id": "node-1",
      "type": "webhook",
      "position": {"x": 100, "y": 100},
      "properties": {
        "path": "github-webhook",
        "method": "POST"
      }
    },
    {
      "id": "node-2",
      "type": "http-request",
      "position": {"x": 300, "y": 100},
      "properties": {
        "url": "https://hooks.slack.com/services/YOUR/WEBHOOK/URL",
        "method": "POST",
        "body": "{{ $node.node-1.data }}"
      }
    }
  ],
  "connections": [
    {
      "source": "node-1",
      "target": "node-2"
    }
  ]
}
```

### Get Workflow
```http
GET /api/v1/workflows/{id}
Authorization: Bearer YOUR_TOKEN
```

### Update Workflow
```http
PUT /api/v1/workflows/{id}
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "name": "Updated Name",
  "active": true
}
```

### Delete Workflow
```http
DELETE /api/v1/workflows/{id}
Authorization: Bearer YOUR_TOKEN
```

### Activate Workflow
```http
PATCH /api/v1/workflows/{id}/activate
Authorization: Bearer YOUR_TOKEN
```

### Execute Workflow
```http
POST /api/v1/workflows/{id}/execute
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "trigger_data": {
    "user_id": "123",
    "action": "test"
  }
}

Response:
{
  "execution_id": "uuid-here",
  "status": "running"
}
```

### Test Run (Without Saving)
```http
POST /api/v1/workflows/{id}/test-run
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "test_data": {
    "sample": "value"
  }
}
```

### Export Workflow
```http
GET /api/v1/workflows/{id}/export
Authorization: Bearer YOUR_TOKEN
```

### Import Workflow
```http
POST /api/v1/workflows/import
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "name": "Imported Workflow",
  "nodes": [...],
  "connections": [...]
}
```

---

## ▶️ Executions

### List Executions
```http
GET /api/v1/executions
Authorization: Bearer YOUR_TOKEN

Query Params:
  - status: success|error|running|waiting
  - workflow_id: filter by workflow
  - page: 1
  - per_page: 20
```

### Get Execution Details
```http
GET /api/v1/executions/{id}
Authorization: Bearer YOUR_TOKEN

Response:
{
  "id": "uuid",
  "workflow_id": "uuid",
  "status": "success",
  "started_at": "2024-01-15T10:00:00Z",
  "finished_at": "2024-01-15T10:00:05Z",
  "execution_time_ms": 5000,
  "node_executions_count": 3
}
```

### Get Execution Logs
```http
GET /api/v1/executions/{id}/logs
Authorization: Bearer YOUR_TOKEN
```

### Get Execution Timeline
```http
GET /api/v1/executions/{id}/timeline
Authorization: Bearer YOUR_TOKEN

Response:
[
  {
    "node_id": "node-1",
    "node_type": "webhook",
    "started_at": "2024-01-15T10:00:00Z",
    "finished_at": "2024-01-15T10:00:01Z",
    "duration_ms": 1000,
    "status": "success"
  }
]
```

### Stop Execution
```http
POST /api/v1/executions/{id}/stop
Authorization: Bearer YOUR_TOKEN
```

### Retry Failed Execution
```http
POST /api/v1/executions/{id}/retry
Authorization: Bearer YOUR_TOKEN
```

### Delete Execution
```http
DELETE /api/v1/executions/{id}
Authorization: Bearer YOUR_TOKEN
```

### Execution Stats
```http
GET /api/v1/executions/stats
Authorization: Bearer YOUR_TOKEN

Response:
{
  "total": 1000,
  "success": 950,
  "error": 30,
  "running": 15,
  "waiting": 5,
  "success_rate": 95.0,
  "avg_execution_time_ms": 3500
}
```

---

## 🔑 Credentials

### List Credentials
```http
GET /api/v1/credentials
Authorization: Bearer YOUR_TOKEN
```

### Create Credential
```http
POST /api/v1/credentials
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

# HTTP Bearer Token
{
  "name": "GitHub API",
  "type": "http_bearer",
  "data": {
    "token": "ghp_xxxxxxxxxxxxxxxxxxxx"
  }
}

# HTTP Basic Auth
{
  "name": "API Basic Auth",
  "type": "http_basic",
  "data": {
    "username": "user",
    "password": "pass"
  }
}

# SMTP Email
{
  "name": "Gmail SMTP",
  "type": "smtp",
  "data": {
    "host": "smtp.gmail.com",
    "port": 587,
    "username": "your-email@gmail.com",
    "password": "your-app-password",
    "encryption": "tls"
  }
}

# Database
{
  "name": "MySQL Production",
  "type": "database",
  "data": {
    "type": "mysql",
    "host": "localhost",
    "port": 3306,
    "database": "mydb",
    "username": "root",
    "password": "secret"
  }
}

# OAuth2
{
  "name": "Google OAuth",
  "type": "oauth2",
  "data": {
    "client_id": "your-client-id",
    "client_secret": "your-client-secret",
    "authorization_url": "https://accounts.google.com/o/oauth2/v2/auth",
    "token_url": "https://oauth2.googleapis.com/token",
    "scope": "email profile"
  }
}
```

### Get Credential Types
```http
GET /api/v1/credentials/types
Authorization: Bearer YOUR_TOKEN

Response:
[
  "http_basic",
  "http_bearer",
  "http_api_key",
  "oauth2",
  "smtp",
  "database",
  "aws",
  "gcp"
]
```

### Get Credential Type Schema
```http
GET /api/v1/credentials/types/http_bearer/schema
Authorization: Bearer YOUR_TOKEN
```

### Test Credential
```http
POST /api/v1/credentials/{id}/test
Authorization: Bearer YOUR_TOKEN
```

### OAuth Authorize
```http
GET /api/v1/credentials/{id}/oauth/authorize
Authorization: Bearer YOUR_TOKEN

Response:
{
  "authorization_url": "https://provider.com/oauth/authorize?..."
}
```

### OAuth Refresh Token
```http
POST /api/v1/credentials/{id}/oauth/refresh
Authorization: Bearer YOUR_TOKEN
```

---

## 🪝 Webhooks

### Create Webhook
```http
POST /api/v1/webhooks
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "workflow_id": "workflow-uuid",
  "path": "my-webhook",
  "method": "POST",
  "auth_type": "bearer",
  "auth_config": {
    "token": "my_secret_token_here"
  },
  "active": true
}

Response:
{
  "id": "webhook-uuid",
  "url": "http://localhost/api/webhook/workflow-uuid/my-webhook",
  "workflow_id": "workflow-uuid",
  "path": "my-webhook",
  "method": "POST",
  "auth_type": "bearer",
  "active": true
}
```

### Get Webhook
```http
GET /api/v1/webhooks/{id}
Authorization: Bearer YOUR_TOKEN
```

### Update Webhook
```http
PUT /api/v1/webhooks/{id}
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "active": true,
  "method": "POST"
}
```

### Test Webhook
```http
POST /api/v1/webhooks/{id}/test
Authorization: Bearer YOUR_TOKEN
```

### Get Webhook Logs
```http
GET /api/v1/webhooks/{id}/logs
Authorization: Bearer YOUR_TOKEN

Response:
[
  {
    "timestamp": "2024-01-15T10:00:00Z",
    "method": "POST",
    "ip": "192.168.1.1",
    "status": "success",
    "execution_id": "uuid"
  }
]
```

### Get Webhook Stats
```http
GET /api/v1/webhooks/{id}/stats
Authorization: Bearer YOUR_TOKEN

Response:
{
  "total_triggers": 1000,
  "successful": 950,
  "failed": 50,
  "success_rate": 95.0,
  "last_triggered_at": "2024-01-15T10:00:00Z"
}
```

### Regenerate Webhook Token
```http
POST /api/v1/webhooks/{id}/regenerate-token
Authorization: Bearer YOUR_TOKEN

Response:
{
  "token": "new_token_here"
}
```

### Update IP Whitelist
```http
PUT /api/v1/webhooks/{id}/ip-whitelist
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "ips": [
    "192.168.1.1",
    "10.0.0.0/8",
    "172.16.*.*"
  ]
}
```

### Trigger Webhook (Public - No Auth in this example)
```http
POST /api/webhook/{workflow_id}/{path}
Authorization: Bearer webhook_token_here
Content-Type: application/json

{
  "event": "user.created",
  "data": {
    "user_id": "123",
    "email": "user@example.com"
  }
}
```

---

## 🧩 Nodes

### List Available Nodes
```http
GET /api/v1/nodes
Authorization: Bearer YOUR_TOKEN

Response:
[
  {
    "type": "webhook",
    "name": "Webhook",
    "category": "trigger",
    "description": "Receive webhook requests"
  },
  {
    "type": "http-request",
    "name": "HTTP Request",
    "category": "action",
    "description": "Make HTTP requests"
  }
]
```

### Get Node Schema
```http
GET /api/v1/nodes/http-request/schema
Authorization: Bearer YOUR_TOKEN

Response:
{
  "type": "http-request",
  "properties": {
    "url": {
      "type": "string",
      "required": true,
      "description": "The URL to request"
    },
    "method": {
      "type": "enum",
      "values": ["GET", "POST", "PUT", "DELETE"],
      "default": "GET"
    }
  }
}
```

### Get Node Categories
```http
GET /api/v1/nodes/categories
Authorization: Bearer YOUR_TOKEN
```

### Get Node Usage Stats
```http
GET /api/v1/nodes/usage/stats
Authorization: Bearer YOUR_TOKEN
```

---

## 📊 Analytics

### Get Dashboard
```http
GET /api/v1/analytics/dashboard
Authorization: Bearer YOUR_TOKEN

Response:
{
  "overview": {
    "total_workflows": 50,
    "active_workflows": 35,
    "total_executions": 10000,
    "success_rate": 95.5
  },
  "recent_executions": [...],
  "top_workflows": [...],
  "error_rate": 4.5
}
```

### Workflow Performance
```http
GET /api/v1/analytics/workflows/performance
Authorization: Bearer YOUR_TOKEN

Response:
[
  {
    "workflow_id": "uuid",
    "workflow_name": "GitHub to Slack",
    "execution_count": 500,
    "avg_execution_time_ms": 1500,
    "success_rate": 98.5
  }
]
```

### Execution Timeline
```http
GET /api/v1/analytics/executions/timeline?days=30
Authorization: Bearer YOUR_TOKEN

Response:
[
  {
    "date": "2024-01-15",
    "success": 100,
    "error": 5,
    "total": 105
  }
]
```

### Node Usage
```http
GET /api/v1/analytics/nodes/usage
Authorization: Bearer YOUR_TOKEN

Response:
[
  {
    "node_type": "http-request",
    "usage_count": 500
  },
  {
    "node_type": "email",
    "usage_count": 250
  }
]
```

### Node Performance
```http
GET /api/v1/analytics/nodes/performance
Authorization: Bearer YOUR_TOKEN
```

---

## 🔧 Variables & Secrets

### List Variables
```http
GET /api/v1/variables
Authorization: Bearer YOUR_TOKEN
```

### Create Variable
```http
POST /api/v1/variables
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "name": "api_url",
  "value": "https://api.example.com",
  "type": "string"
}
```

### Create Secret (Encrypted)
```http
POST /api/v1/secrets
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "name": "api_secret_key",
  "value": "super_secret_value"
}
```

### Using Variables in Workflows
In node properties, use:
```
{{ $var.api_url }}
{{ $vars.api_url }}
```

---

## 🎯 Templates

### Browse Templates
```http
GET /api/v1/templates
Authorization: Bearer YOUR_TOKEN
```

### Search Templates
```http
GET /api/v1/templates/search?q=github&category=automation
Authorization: Bearer YOUR_TOKEN
```

### Use Template
```http
POST /api/v1/templates/{id}/use
Authorization: Bearer YOUR_TOKEN

Creates a new workflow from the template
```

---

## 🤖 AI Features

### Suggest Nodes
```http
POST /api/v1/ai/suggest-nodes
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "context": "I want to fetch data from GitHub API and send it to Slack",
  "current_nodes": ["start"]
}
```

### Generate Workflow from Prompt
```http
POST /api/v1/ai/generate-workflow
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "prompt": "Create a workflow that sends me an email every time a new issue is created in my GitHub repository"
}
```

### Explain Error
```http
POST /api/v1/ai/explain-error
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "error": "TypeError: Cannot read property 'data' of undefined",
  "context": {
    "node_type": "http-request",
    "execution_id": "uuid"
  }
}
```

---

## 🔧 Admin

### System Health
```http
GET /api/v1/admin/system/health
Authorization: Bearer YOUR_ADMIN_TOKEN

Response:
{
  "status": "healthy",
  "database": "connected",
  "redis": "connected",
  "queue": "running"
}
```

### System Metrics
```http
GET /api/v1/admin/system/metrics
Authorization: Bearer YOUR_ADMIN_TOKEN

Response:
{
  "cpu_usage": 45.5,
  "memory_usage": 60.2,
  "disk_usage": 35.0,
  "active_executions": 5
}
```

---

## 💡 Tips

### Using cURL
```bash
# Login
curl -X POST http://localhost/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password123"}'

# With token
TOKEN="1|xxxxxxx"
curl -X GET http://localhost/api/v1/workflows \
  -H "Authorization: Bearer $TOKEN"
```

### Rate Limits
- Default: 60 requests/minute per user
- Check headers: `X-RateLimit-Limit`, `X-RateLimit-Remaining`

### Pagination
All list endpoints support:
- `?page=1` - Page number
- `?per_page=20` - Items per page

### Filtering
- `?status=success` - Filter by status
- `?active=true` - Filter active items
- `?search=keyword` - Search by keyword

### Sorting
- `?sort=created_at` - Sort field
- `?order=desc` - Sort direction (asc/desc)

---

**Need more details?** Check the full Postman collection or `README.md`
