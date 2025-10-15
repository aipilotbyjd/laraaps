# n8n Clone Frontend Guide

## Overview
This is an n8n-inspired workflow automation frontend built with Vue 3, Vue Flow, and Inertia.js. It provides a visual workflow editor that communicates with your existing Laravel backend APIs.

## Features Implemented

### 1. **Workflow Management**
- List all workflows with search and filtering
- Create new workflows
- Edit existing workflows
- Duplicate workflows
- Activate/deactivate workflows
- Delete workflows

### 2. **Visual Workflow Editor (Vue Flow)**
- Drag-and-drop node interface
- Custom node types:
  - **Trigger Nodes** (purple) - Webhook, Schedule, Manual triggers
  - **Action Nodes** (blue) - HTTP requests, Database, Email, Code execution
  - **Condition Nodes** (orange) - IF conditions, Switch statements
- Connection management between nodes
- Node configuration panel
- Mini-map for navigation
- Zoom controls

### 3. **Node Palette**
- Categorized node listing
- Search functionality
- Drag nodes to canvas
- Node descriptions and icons

### 4. **Execution Panel**
- Real-time execution status
- Output viewer
- Execution logs
- Node-by-node execution details
- Error tracking

### 5. **API Integration**
- Complete API service layer (`resources/js/services/api.js`)
- Axios-based HTTP client
- CSRF token handling
- Bearer token authentication support
- All endpoints mapped:
  - Workflows API
  - Nodes API
  - Executions API
  - Credentials API
  - Templates API

### 6. **State Management (Pinia)**
- Centralized workflow store
- Execution state management
- Node and edge management
- Dirty state tracking for unsaved changes

## How to Run

### Development Mode
```bash
# Terminal 1 - Start Vite dev server
npm run dev

# Terminal 2 - Start Laravel server
php artisan serve

# Optional Terminal 3 - Start queue worker for background jobs
php artisan queue:work
```

### Production Build
```bash
# Build frontend assets
npm run build

# Start Laravel server
php artisan serve --env=production
```

## Accessing the Application

1. Navigate to `http://localhost:8000` (redirects to workflows)
2. Main sections:
   - **Workflows** (`/workflows`) - Manage automation workflows
   - **Executions** (`/executions`) - View execution history
   - **Credentials** (`/credentials`) - Manage API credentials
   - **Templates** (`/templates`) - Browse workflow templates

## Using the Workflow Editor

### Creating a Workflow
1. Click "Create Workflow" button
2. Drag nodes from the left sidebar onto the canvas
3. Connect nodes by dragging from output handle to input handle
4. Click on a node to configure it in the right panel
5. Save the workflow using the Save button

### Node Types and Configuration

#### Trigger Nodes
- **Webhook**: Configure HTTP method and path
- **Schedule**: Set cron expressions
- **Manual**: Triggered manually

#### Action Nodes
- **HTTP Request**: Configure URL, method, headers, and body
- **Database**: Set up queries
- **Email**: Configure recipients and content
- **Code**: Write custom JavaScript/Python code

#### Condition Nodes
- **IF**: Set field, operator, and value for conditions
- **Switch**: Multiple condition branches

### Executing Workflows
1. Save your workflow first
2. Click the "Execute" button in the toolbar
3. Monitor execution in the bottom panel
4. View output, logs, and node execution details

## API Communication

The frontend makes HTTP calls to your existing Laravel APIs:
- Authentication is handled via Bearer tokens stored in localStorage
- CSRF tokens are automatically included in requests
- Organization context is maintained through API calls

## File Structure

```
resources/js/
├── services/
│   └── api.js                 # API client and endpoints
├── stores/
│   └── workflow.js            # Pinia store for state management
├── components/
│   ├── MainLayout.vue         # Main app layout
│   ├── Workflow/
│   │   ├── NodePalette.vue    # Node sidebar
│   │   ├── NodeSettings.vue   # Node configuration panel
│   │   ├── ExecutionPanel.vue # Execution monitoring
│   │   └── ConnectionLine.vue # Custom connection rendering
│   └── Nodes/
│       ├── TriggerNode.vue    # Trigger node component
│       ├── ActionNode.vue     # Action node component
│       └── ConditionNode.vue  # Condition node component
└── Pages/
    └── Workflows/
        ├── Index.vue          # Workflow list page
        └── Edit.vue           # Workflow editor page
```

## Next Steps for Full Integration

1. **Authentication Integration**
   - Implement login/register pages
   - Store auth tokens properly
   - Add auth middleware to routes

2. **Real API Connection**
   - Update API endpoints if needed
   - Handle organization context
   - Implement proper error handling

3. **Additional Features**
   - Execution history page
   - Credentials management page
   - Template marketplace
   - Real-time updates via WebSockets

4. **Testing**
   - Add unit tests for components
   - Integration tests for API calls
   - E2E tests for workflows

## Important Notes

- The frontend is designed to work with your existing Laravel API structure
- All API calls go through the service layer in `api.js`
- Node configurations are stored as JSON in the workflow definition
- The visual editor uses Vue Flow for the canvas functionality
- Drag and drop is fully functional between the palette and canvas

## Development Tips

1. Check browser console for API errors
2. Ensure CORS is properly configured if API is on different domain
3. Use Vue DevTools for debugging component state
4. Monitor network tab for API requests/responses
5. The Pinia store can be inspected in Vue DevTools

## Troubleshooting

- **Nodes not appearing**: Check that the API is returning node data
- **Can't save workflows**: Verify auth token and CSRF token
- **Execution not working**: Ensure queue workers are running
- **Styles not loading**: Run `npm run build` or `npm run dev`
