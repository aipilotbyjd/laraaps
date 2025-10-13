# n8n Clone - Postman Collection

Complete API documentation and testing collection for the n8n Clone workflow automation platform.

## 📦 Files

- **`n8n-clone-collection.json`** - Complete Postman collection with 150+ endpoints
- **`n8n-clone-environment.json`** - Environment variables for local development

## 🚀 Quick Start

### 1. Import into Postman

1. Open Postman
2. Click **Import** button (top left)
3. Drag and drop both JSON files or browse to select them
4. Collections will appear in your workspace

### 2. Setup Environment

1. Select the **"n8n Clone - Local Environment"** from environment dropdown (top right)
2. Update the `base_url` if your app runs on different port:
   - Default: `http://localhost/api/v1`
   - Laravel Valet: `http://laraaps.test/api/v1`
   - Custom: `http://your-domain.com/api/v1`

### 3. Authentication Flow

The collection includes automatic token management:

1. **Register or Login**:
   - Go to `1. Authentication` → `Register` or `Login`
   - Execute the request
   - Token automatically saves to `{{access_token}}` variable

2. **All subsequent requests** use the saved token automatically via Bearer auth

## 📚 Collection Structure

### 1. Authentication (8 endpoints)
- Register, Login, Logout
- Password change/reset
- MFA setup
- OAuth & SAML
- API Keys management

### 2. Workflows (13 endpoints)
- CRUD operations
- Activate/Deactivate
- Import/Export
- Duplicate
- Test run & Execute
- Version control

### 3. Executions (13 endpoints)
- List & view executions
- Stop, Retry, Resume
- Logs, Timeline, Errors
- Stats & Analytics
- Queue management

### 4. Credentials (10 endpoints)
- CRUD operations
- Test credentials
- OAuth flows
- Credential types & schemas
- Share management

### 5. Webhooks (12 endpoints)
- CRUD operations
- Test webhooks
- Logs & Stats
- IP whitelist
- Token regeneration
- **Public webhook trigger** (no auth)

### 6. Nodes (8 endpoints)
- List available nodes
- Categories & Tags
- Node schemas
- Test configurations
- Usage statistics

### 7. Templates (7 endpoints)
- Browse marketplace
- Featured & Trending
- Search templates
- Use/Clone templates
- Publish your own

### 8. Analytics (17 endpoints)
- Dashboard overview
- Workflow performance
- Execution metrics
- Node analytics
- Reports & Export

### 9. Variables & Environment (9 endpoints)
- Variable management
- Environment switching
- Secrets (encrypted)

### 10. Notifications (5 endpoints)
- List notifications
- Mark as read
- Settings management

### 11. Organizations (6 endpoints)
- Organization management
- Member management
- Teams
- Usage tracking

### 12. AI Features (6 endpoints)
- Node suggestions
- Workflow generation
- Error explanations
- Expression generation
- AI chat assistant

### 13. Admin (8 endpoints)
- System health
- User management
- Audit logs
- System config
- Backups

### 14. Storage (4 endpoints)
- File upload/download
- Multipart uploads
- File management

### 15. Collaboration (5 endpoints)
- Real-time presence
- Workflow locking
- Concurrent editing

## 🔐 Authentication Methods

### 1. Bearer Token (Default)
```
Authorization: Bearer {{access_token}}
```
Automatically applied after Login/Register.

### 2. API Keys
Create via `Authentication` → `Create API Key`, then use:
```
Authorization: Bearer YOUR_API_KEY
```

### 3. Webhook Authentication
Public webhooks support multiple auth types:
- **None** - Open endpoint
- **Basic Auth** - Username/password
- **Bearer Token** - Token-based
- **API Key** - Header or query parameter
- **HMAC** - Signature validation

## 🎯 Common Workflows

### Creating Your First Workflow

1. **Login**
   ```
   POST /auth/login
   ```

2. **Create Workflow**
   ```
   POST /workflows
   Body: {
     "name": "My First Workflow",
     "nodes": [...],
     "connections": [...]
   }
   ```
   → Saves `workflow_id` to variables

3. **Execute Workflow**
   ```
   POST /workflows/{{workflow_id}}/execute
   ```
   → Saves `execution_id` to variables

4. **Check Execution Status**
   ```
   GET /executions/{{execution_id}}
   ```

### Setting Up a Webhook Trigger

1. **Create Webhook**
   ```
   POST /webhooks
   Body: {
     "workflow_id": "{{workflow_id}}",
     "path": "my-webhook",
     "method": "POST",
     "auth_type": "bearer"
   }
   ```

2. **Get Webhook URL**
   ```
   GET /webhooks/{{webhook_id}}/test-url
   ```

3. **Trigger Webhook** (External)
   ```
   POST http://localhost/api/webhook/{{workflow_id}}/my-webhook
   Authorization: Bearer YOUR_TOKEN
   ```

### Viewing Analytics

1. **Dashboard Overview**
   ```
   GET /analytics/dashboard
   ```

2. **Workflow Performance**
   ```
   GET /analytics/workflows/performance
   ```

3. **Execution Timeline**
   ```
   GET /analytics/executions/timeline?days=30
   ```

## 🧪 Testing Tips

### Auto-Save Variables
Key requests automatically save IDs to variables:
- Login/Register → `access_token`, `org_id`
- Create Workflow → `workflow_id`
- Execute Workflow → `execution_id`
- Create Credential → `credential_id`
- Create Webhook → `webhook_id`

### Test Scripts
Many endpoints include test scripts that:
- Validate response status
- Extract and save important values
- Set environment variables automatically

### Request Examples
All endpoints include:
- Sample request bodies
- Query parameters
- Expected responses

## 🔧 Environment Variables

### Required
- `base_url` - API base URL
- `webhook_base_url` - Webhook endpoint URL

### Auto-populated
- `access_token` - Bearer token (from login)
- `org_id` - Organization ID
- `workflow_id` - Current workflow
- `execution_id` - Current execution
- `credential_id` - Current credential
- `webhook_id` - Current webhook

### Custom Environments

Create additional environments for different setups:

**Development**
```json
{
  "base_url": "http://localhost/api/v1",
  "webhook_base_url": "http://localhost/api/webhook"
}
```

**Staging**
```json
{
  "base_url": "https://staging.yourdomain.com/api/v1",
  "webhook_base_url": "https://staging.yourdomain.com/api/webhook"
}
```

**Production**
```json
{
  "base_url": "https://api.yourdomain.com/v1",
  "webhook_base_url": "https://webhooks.yourdomain.com"
}
```

## 📖 API Documentation

### Response Formats

**Success (200/201)**
```json
{
  "success": true,
  "data": {...},
  "message": "Operation successful"
}
```

**Error (4xx/5xx)**
```json
{
  "success": false,
  "message": "Error description",
  "errors": {...}
}
```

### Pagination
List endpoints support pagination:
```
GET /workflows?page=1&per_page=20
```

Response includes:
```json
{
  "data": [...],
  "meta": {
    "current_page": 1,
    "total": 100,
    "per_page": 20
  }
}
```

### Filtering & Sorting
```
GET /executions?status=success&sort=created_at&order=desc
```

## 🐛 Troubleshooting

### Token Expired
Re-login to get a new token:
```
POST /auth/login
```

### 404 Not Found
- Verify `base_url` in environment
- Check Laravel routes with `php artisan route:list`

### 401 Unauthorized
- Ensure token is set in environment
- Check if endpoint requires auth

### CORS Issues
Add to Laravel config/cors.php:
```php
'paths' => ['api/*', 'webhook/*'],
'allowed_origins' => ['*'],
```

## 🚦 Rate Limiting

Default Laravel rate limits apply:
- **API**: 60 requests/minute per user
- **Webhooks**: Unlimited (configure per webhook)

## 📝 Notes

1. **IDs are UUIDs** - Use the auto-saved variables instead of manual IDs
2. **Timestamps** - All in ISO 8601 format (UTC)
3. **File Uploads** - Use form-data for file endpoints
4. **OAuth Flows** - Requires browser redirect (see docs)
5. **Webhook Testing** - Use ngrok/tunnels for local testing with external services

## 🤝 Contributing

Found an issue or want to add more examples?
1. Update the collection JSON
2. Test all requests
3. Update this README
4. Submit your changes

## 📄 License

This collection is part of the n8n Clone project.

---

**Total Endpoints**: 150+  
**Last Updated**: 2024  
**Version**: 1.0.0

Happy Testing! 🎉
