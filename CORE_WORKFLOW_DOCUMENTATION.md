# 📊 Core Workflow System - Complete Documentation

## 🏗️ Architecture Overview

Your Laravel application is a **visual workflow automation platform** (n8n clone) that enables users to create, execute, and manage automated workflows through a node-based system.

### **System Components**

```
┌─────────────────────────────────────────────────────────┐
│                    API Layer                             │
│  (Routes → Controllers → Middleware → Requests)         │
└────────────────────┬────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────┐
│                 Service Layer                            │
│  (Business Logic, Workflow Execution, Node Processing)  │
└────────────────────┬────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────┐
│                  Data Layer                              │
│  (Models, Database, Caching, Queue Jobs)                │
└─────────────────────────────────────────────────────────┘
```

## 🔄 Complete Workflow Execution Flow

### 1. **Workflow Triggers**

Workflows can be triggered through multiple entry points:

#### **A. Manual Execution**
```
POST /api/v1/workflows/{id}/execute
POST /api/v1/workflows/{id}/test-execute
```
- User initiates execution via API
- `ExecutionController::executeWorkflow()` handles request
- Creates execution job and queues it

#### **B. Webhook Triggers**
```
POST/GET/PUT/PATCH/DELETE /api/webhook/{workflowId}/{path}
```
- External systems send data to webhook endpoint
- `WebhookController::handleIncomingWebhook()` processes request
- Validates webhook authentication (Basic, Bearer, API Key, HMAC)
- Checks IP whitelist if configured
- Triggers workflow execution with webhook data

#### **C. Scheduled Execution**
```bash
php artisan workflows:run-scheduled
```
- Cron job runs every minute
- `RunScheduledWorkflows` command finds workflows with cron expressions
- Executes workflows whose schedule matches current time
- Supports timezone-aware scheduling

### 2. **Execution Pipeline**

```php
ExecutionService::runWorkflow()
    ↓
1. Create WorkflowExecution record (status: 'running')
    ↓
2. Build Graph from nodes and connections
    ↓
3. Find Start node
    ↓
4. Execute nodes recursively
    ↓
5. Update execution status (success/error)
    ↓
6. Calculate metrics (execution_time_ms, node_count)
```

### 3. **Node Execution Process**

Each node goes through this lifecycle:

```php
ExecutionService::executeNode()
    ↓
1. Create NodeExecution record
    ↓
2. Store input data in ExecutionData table
    ↓
3. Get NodeExecutor via Factory pattern
    ↓
4. Execute node-specific logic
    ↓
5. Store output data
    ↓
6. Update NodeExecution with timing/status
    ↓
7. Find successor nodes
    ↓
8. Pass output to next nodes
```

## 📦 Data Models & Relationships

### **Core Models**

#### 1. **Workflow**
```php
- id (UUID)
- org_id (organization context)
- user_id (owner)
- name, description
- nodes[] (array of node configurations)
- connections[] (array of node connections)
- active (boolean)
- cron_expression (for scheduling)
- trigger_config (webhook/manual/schedule)
- avg_execution_time_ms
- success_rate
```

#### 2. **WorkflowExecution**
```php
- id (UUID)
- workflow_id
- org_id
- user_id
- status (running/success/error/waiting)
- trigger_data (input data)
- mode (manual/webhook/schedule/test)
- started_at, finished_at
- execution_time_ms
- node_executions_count
- error_message, error_stack
```

#### 3. **NodeExecution**
```php
- id (UUID)
- execution_id
- workflow_id
- node_id
- node_type
- status (running/success/error)
- started_at, finished_at
- execution_time_ms
- input_data_id → ExecutionData
- output_data_id → ExecutionData
- error (if failed)
```

#### 4. **ExecutionData**
```php
- id (UUID)
- data (JSON - stores input/output for nodes)
```

## 🔧 Node Types & Executors

### **Available Node Types (20 Total)**

#### **Control Flow Nodes**
1. **Start** - Entry point for workflows
2. **If** - Conditional branching based on expressions
3. **Switch** - Multi-branch routing (like switch-case)
4. **Loop** - Iterate over arrays or repeat N times
5. **SubWorkflow** - Execute another workflow as a node
6. **Wait** - Pause execution (manual resume required)
7. **Merge** - Combine data from multiple branches

#### **Action Nodes**
8. **HttpRequest** - Make HTTP API calls
   - Supports GET, POST, PUT, DELETE, PATCH
   - Headers, query params, body
   - Credential integration for auth
9. **Email** - Send emails via SMTP
   - Dynamic from address
   - HTML/Text content
   - Attachments support
10. **Database** - Query databases
    - MySQL, PostgreSQL, MongoDB
    - SELECT, INSERT, UPDATE, DELETE operations

#### **Data Transformation Nodes**
11. **Set** - Set or modify data fields
12. **Filter** - Filter arrays based on conditions
13. **Split** - Split arrays into individual items
14. **Sort** - Sort arrays by field (asc/desc)
15. **Limit** - Pagination with offset/limit
16. **Aggregate** - Sum, avg, min, max, count operations

#### **Advanced Nodes**
17. **Code** - Execute custom PHP code (security risk noted)

### **Node Executor Factory**

```php
NodeExecutorFactory::make($node, $execution)
    ↓
Returns specific executor based on node type:
- StartNodeExecutor
- IfNodeExecutor
- HttpRequestNodeExecutor
- EmailNodeExecutor
- DatabaseNodeExecutor
- etc...
```

## 🔐 Security Features

### **1. Webhook Authentication**
- **Basic Auth**: Username/password validation
- **Bearer Token**: Token in Authorization header
- **API Key**: Key in header or query param
- **HMAC Signature**: Cryptographic signature validation
- **IP Whitelisting**: Single IPs, wildcards, CIDR notation

### **2. Expression Evaluation**
- Safe `ExpressionEvaluator` class (no eval())
- Supports: ==, !=, >, <, >=, <=, AND, OR, NOT
- Variable replacement from context

### **3. Credential Management**
- Encrypted storage using Laravel's Crypt facade
- `CredentialResolver` service for runtime decryption
- OAuth token refresh automation

### **4. Variable Resolution**
- `VariableResolver` with 5-minute cache
- Supports `{{ $var.name }}` syntax
- Encrypted secrets support

## 🚀 API Endpoints Structure

### **Authentication & User Management**
```
/api/v1/auth/
├── register         [POST]
├── login           [POST]
├── logout          [POST]
├── refresh         [POST]
├── me              [GET]
├── profile         [PUT]
├── oauth/{provider} [GET]
└── mfa/*           [Various]
```

### **Workflow Management**
```
/api/v1/workflows/
├── /               [GET, POST]     # List, Create
├── {id}            [GET, PUT, DELETE] # CRUD
├── {id}/execute    [POST]          # Run workflow
├── {id}/duplicate  [POST]          # Clone workflow
├── {id}/activate   [PATCH]         # Enable workflow
├── {id}/deactivate [PATCH]         # Disable workflow
├── {id}/versions   [GET, POST]     # Version control
├── {id}/shares     [GET, POST]     # Collaboration
├── {id}/validate   [POST]          # Validation
├── {id}/test-run   [POST]          # Test execution
└── import/export   [Various]       # Data portability
```

### **Execution Management**
```
/api/v1/executions/
├── /               [GET]           # List executions
├── {id}            [GET, DELETE]   # View, Delete
├── {id}/stop       [POST]          # Stop running
├── {id}/retry      [POST]          # Retry failed
├── {id}/logs       [GET]           # View logs
├── {id}/timeline   [GET]           # Execution timeline
├── stats/*         [Various]       # Analytics
└── queue/*         [Various]       # Queue management
```

### **Other Services**
- **Nodes**: `/api/v1/nodes/` - Node type management
- **Credentials**: `/api/v1/credentials/` - Auth credentials
- **Webhooks**: `/api/v1/webhooks/` - Webhook configuration
- **Analytics**: `/api/v1/analytics/` - Performance metrics
- **Templates**: `/api/v1/templates/` - Workflow templates
- **Organizations**: `/api/v1/organizations/` - Multi-tenancy

## 📊 Execution Example

### **Simple Workflow Example**

```json
{
  "name": "Fetch and Email Data",
  "nodes": [
    {
      "id": "start-1",
      "type": "start",
      "position": {"x": 0, "y": 0}
    },
    {
      "id": "http-1",
      "type": "http-request",
      "position": {"x": 200, "y": 0},
      "properties": {
        "url": "https://api.example.com/data",
        "method": "GET"
      }
    },
    {
      "id": "if-1",
      "type": "if",
      "position": {"x": 400, "y": 0},
      "properties": {
        "conditions": [
          {"field": "status", "operator": "==", "value": "200"}
        ]
      }
    },
    {
      "id": "email-1",
      "type": "email",
      "position": {"x": 600, "y": -50},
      "properties": {
        "to": "admin@example.com",
        "subject": "API Data Retrieved",
        "body": "Data: {{data}}"
      }
    }
  ],
  "connections": [
    {"source": "start-1", "target": "http-1"},
    {"source": "http-1", "target": "if-1"},
    {"source": "if-1", "target": "email-1", "sourceHandle": "true"}
  ]
}
```

### **Execution Flow**
1. **Start Node** → Initializes with trigger data
2. **HTTP Request** → Fetches data from API
3. **If Node** → Checks if status is 200
4. **Email Node** → Sends email if condition true

## 🔄 Background Jobs & Commands

### **Queue Jobs**
- `ExecuteWorkflowJob` - Async workflow execution
- `RefreshOAuthTokenJob` - Auto-refresh OAuth tokens

### **Artisan Commands**
```bash
# Run scheduled workflows (add to cron)
php artisan workflows:run-scheduled

# Clean old executions
php artisan workflows:cleanup-executions --days=30

# Refresh expiring OAuth tokens
php artisan oauth:refresh-tokens
```

## 📈 Analytics & Monitoring

### **Available Metrics**
- Workflow execution count
- Success/failure rates
- Average execution time
- Node performance stats
- Error rate by workflow/node
- Resource usage
- Daily/hourly statistics

### **Logging**
- Comprehensive logging at workflow and node level
- Execution timing for performance analysis
- Error stack traces for debugging
- Structured logs with context

## 🧪 Testing

### **Test Coverage**
- **CoreWorkflowTest** - Main integration tests
  - Basic workflow execution
  - Conditional workflows
  - HTTP requests
  - Loop nodes
  - Error handling
  - API endpoints
  - Statistics
  - Concurrent executions

### **Running Tests**
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=CoreWorkflowTest

# Run with coverage
php artisan test --coverage
```

## 🚦 Current Status

### **Completed Features (81%)**
✅ Core execution engine  
✅ 20 node types implemented  
✅ Webhook system with security  
✅ Scheduled execution  
✅ Expression engine  
✅ Credential management  
✅ Variable/environment support  
✅ OAuth token management  
✅ Analytics service  
✅ Multi-tenancy with organizations  

### **Missing Components**
❌ **Frontend UI** - No visual workflow builder
❌ Advanced node types (Transform, Spreadsheet, FTP, SSH)
❌ Real-time collaboration
❌ AI-powered features
❌ Template marketplace

## 🔧 Development Tips

### **Adding New Node Types**
1. Create executor in `app/Services/Node/Execution/`
2. Extend `NodeExecutor` base class
3. Implement `execute()` method
4. Register in `NodeExecutorFactory`

### **Database Queries**
```sql
-- Get recent executions
SELECT * FROM workflow_executions 
WHERE org_id = ? 
ORDER BY created_at DESC 
LIMIT 10;

-- Get workflow success rate
SELECT 
    workflow_id,
    COUNT(*) as total,
    SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as success,
    AVG(execution_time_ms) as avg_time
FROM workflow_executions
GROUP BY workflow_id;
```

## 📝 Configuration

### **Environment Variables**
```env
# Queue configuration
QUEUE_CONNECTION=database

# Mail configuration (for email nodes)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525

# OAuth providers
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
```

### **Key Files**
- `routes/api.php` - All API endpoints
- `app/Services/Execution/ExecutionService.php` - Core execution logic
- `app/Services/Node/Execution/NodeExecutorFactory.php` - Node factory
- `database/migrations/` - Database schema

## 🎯 Usage Examples

### **Create and Execute Workflow via API**

```bash
# 1. Authenticate
TOKEN=$(curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "password": "password"}' \
  | jq -r '.token')

# 2. Create workflow
WORKFLOW_ID=$(curl -X POST http://localhost:8000/api/v1/workflows \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Workflow",
    "nodes": [...],
    "connections": [...]
  }' | jq -r '.data.id')

# 3. Execute workflow
curl -X POST http://localhost:8000/api/v1/workflows/$WORKFLOW_ID/execute \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"input": "test data"}'
```

## 🔍 Troubleshooting

### **Common Issues**

1. **Workflow not executing**
   - Check queue worker is running: `php artisan queue:work`
   - Verify workflow is active
   - Check logs in `storage/logs/laravel.log`

2. **Webhook not triggering**
   - Verify webhook is active
   - Check IP whitelist settings
   - Validate authentication credentials

3. **Node execution fails**
   - Review node properties configuration
   - Check credential settings
   - Examine error in NodeExecution record

## 📚 Summary

Your Laravel application is a sophisticated workflow automation platform that:
- Executes visual workflows as directed graphs
- Supports 20+ node types for various operations
- Provides multiple trigger mechanisms (manual, webhook, schedule)
- Includes comprehensive security features
- Tracks detailed execution metrics
- Supports multi-tenancy via organizations
- Queues executions for scalability

The system is production-ready for backend operations but requires a frontend UI for visual workflow creation and management.
