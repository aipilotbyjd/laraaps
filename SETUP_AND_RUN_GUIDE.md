# 🚀 Complete Setup & Run Guide for Workflow System

## 📋 Prerequisites Check

### 1. **Environment Setup**
```bash
# Check PHP version (need 8.1+)
php -v

# Check Composer
composer --version

# Check MySQL is running
mysql --version

# Check if Laravel Herd is running (if using Herd)
# Your site should be accessible at http://laraaps.test
```

## 🔧 Initial Setup (One-Time)

### 1. **Install Dependencies**
```bash
# Install PHP dependencies
composer install

# Install Node dependencies (if frontend exists)
npm install
```

### 2. **Configure Environment**
```bash
# Copy .env.example to .env if not exists
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. **Database Setup**
```bash
# Create database in MySQL
mysql -u root -p
CREATE DATABASE laraaps;
EXIT;

# Run migrations
php artisan migrate

# Seed database (if seeders exist)
php artisan db:seed
```

### 4. **Authentication Setup**
```bash
# Install Passport (for API authentication)
php artisan passport:install

# This will output:
# Personal access client created successfully.
# Client ID: 1
# Client secret: xxxxxxxxxxxxx
# Password grant client created successfully.
# Client ID: 2
# Client secret: xxxxxxxxxxxxx
```

## 🏃‍♂️ Running the Application

### **IMPORTANT: You need 3-4 terminal windows/tabs running simultaneously**

### Terminal 1: **Web Server**
```bash
# Option A: Using Laravel's built-in server
php artisan serve
# Application will be available at http://localhost:8000

# Option B: Using Laravel Herd (if installed)
# Application automatically available at http://laraaps.test
```

### Terminal 2: **Queue Worker** (CRITICAL!)
```bash
# This processes workflow executions
php artisan queue:work --tries=3 --timeout=300

# For development with auto-restart on code changes:
php artisan queue:listen

# To process failed jobs:
php artisan queue:retry all
```

### Terminal 3: **Scheduler** (For Scheduled Workflows)
```bash
# Run manually every minute for testing
while true; do php artisan schedule:run; sleep 60; done

# OR for production, add to crontab:
crontab -e
# Add this line:
* * * * * cd /Users/jaydeepdhrangiya/Herd/laraaps && php artisan schedule:run >> /dev/null 2>&1
```

### Terminal 4: **Queue Monitor** (Optional)
```bash
# Monitor queue status
php artisan queue:monitor

# OR watch the logs
tail -f storage/logs/laravel.log
```

## 📊 Verify Everything is Working

### 1. **Check Application Health**
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Check routes are loaded
php artisan route:list | grep workflow

# Check queue connection
php artisan queue:work --stop-when-empty
```

### 2. **Test Basic Workflow Execution**
```bash
# Run the test suite
php artisan test tests/Feature/CoreWorkflowTest.php

# OR test manually via API:

# Step 1: Create a test user
curl -X POST http://laraaps.test/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'

# Step 2: Login to get token
TOKEN=$(curl -X POST http://laraaps.test/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }' | grep -o '"access_token":"[^"]*' | grep -o '[^"]*$')

echo "Token: $TOKEN"

# Step 3: Create a simple workflow
WORKFLOW_ID=$(curl -X POST http://laraaps.test/api/v1/workflows \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Workflow",
    "description": "Simple test workflow",
    "nodes": [
      {"id": "start-1", "type": "start", "position": {"x": 0, "y": 0}},
      {"id": "set-1", "type": "set", "position": {"x": 200, "y": 0}, 
       "properties": {"values": [{"key": "message", "value": "Hello World"}]}}
    ],
    "connections": [
      {"source": "start-1", "target": "set-1"}
    ]
  }' | grep -o '"id":"[^"]*' | head -1 | grep -o '[^"]*$')

echo "Workflow ID: $WORKFLOW_ID"

# Step 4: Execute the workflow
curl -X POST http://laraaps.test/api/v1/workflows/$WORKFLOW_ID/execute \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"trigger_data": {"test": true}}'

# Step 5: Check execution status
curl -X GET http://laraaps.test/api/v1/executions \
  -H "Authorization: Bearer $TOKEN" | python -m json.tool
```

## 🔄 Scheduled Tasks Setup

### **Tasks that run automatically:**

1. **Scheduled Workflows** (every minute)
```bash
php artisan workflows:run-scheduled
```

2. **OAuth Token Refresh** (every hour)
```bash
php artisan oauth:refresh-tokens
```

3. **Cleanup Old Executions** (daily)
```bash
php artisan workflows:cleanup-executions --days=30
```

### **Add to Laravel Scheduler** (`app/Console/Kernel.php`):
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('workflows:run-scheduled')->everyMinute();
    $schedule->command('oauth:refresh-tokens')->hourly();
    $schedule->command('workflows:cleanup-executions --days=30')->daily();
}
```

## 🐛 Troubleshooting

### **Issue: Workflows not executing**
```bash
# Check if queue worker is running
ps aux | grep queue:work

# Check for failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear and restart queue
php artisan queue:clear
php artisan queue:restart
php artisan queue:work
```

### **Issue: "Class not found" errors**
```bash
# Regenerate autoload files
composer dump-autoload

# Clear compiled classes
php artisan clear-compiled
php artisan optimize:clear
```

### **Issue: Database connection errors**
```bash
# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check .env database settings
cat .env | grep DB_
```

### **Issue: Permission errors**
```bash
# Fix storage permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## 📦 Queue Management Commands

```bash
# View queue statistics
php artisan queue:monitor

# Clear all jobs from queue
php artisan queue:clear

# Restart queue workers
php artisan queue:restart

# List failed jobs
php artisan queue:failed

# Retry specific failed job
php artisan queue:retry {job-id}

# Delete failed job
php artisan queue:forget {job-id}

# Flush all failed jobs
php artisan queue:flush
```

## 🔍 Monitoring & Logs

### **Check Application Logs**
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Filter for workflow execution
tail -f storage/logs/laravel.log | grep "workflow"

# Filter for errors only
tail -f storage/logs/laravel.log | grep "ERROR"
```

### **Database Monitoring**
```sql
-- Check recent executions
SELECT * FROM workflow_executions 
ORDER BY created_at DESC 
LIMIT 10;

-- Check running workflows
SELECT * FROM workflow_executions 
WHERE status = 'running';

-- Check failed executions
SELECT * FROM workflow_executions 
WHERE status = 'error' 
ORDER BY created_at DESC;

-- Check queue jobs
SELECT * FROM jobs;

-- Check failed queue jobs
SELECT * FROM failed_jobs;
```

## 🚦 Development Workflow

### **For active development:**

1. **Start all services:**
```bash
# Terminal 1
php artisan serve

# Terminal 2
php artisan queue:listen  # Auto-restarts on code changes

# Terminal 3
php artisan schedule:work  # Runs scheduler every minute

# Terminal 4 (optional)
npm run dev  # If you have frontend assets
```

2. **Watch logs in real-time:**
```bash
tail -f storage/logs/laravel.log
```

3. **Test changes:**
```bash
php artisan test --filter=CoreWorkflowTest
```

## 🎯 Quick Test Checklist

✅ **Step 1:** Laravel server running  
✅ **Step 2:** Queue worker running (`queue:work` or `queue:listen`)  
✅ **Step 3:** Database connected and migrated  
✅ **Step 4:** Passport keys generated  
✅ **Step 5:** Create test user  
✅ **Step 6:** Create test workflow  
✅ **Step 7:** Execute workflow  
✅ **Step 8:** Check execution in database  

## 📝 Environment Variables (.env)

### **Essential Settings:**
```env
APP_NAME=LaraAPS
APP_ENV=local
APP_DEBUG=true
APP_URL=http://laraaps.test

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laraaps
DB_USERNAME=root
DB_PASSWORD=

# Queue (IMPORTANT!)
QUEUE_CONNECTION=database
# Can also use: redis, sync (for testing only)

# Mail (for email nodes)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=

# Cache
CACHE_DRIVER=file
# Can also use: redis, memcached

# Session
SESSION_DRIVER=file
```

## 🔴 IMPORTANT NOTES

1. **Queue Worker is MANDATORY** - Without it, workflows won't execute
2. **Use `database` queue driver** for development (easier to debug)
3. **Keep queue worker running** while testing
4. **Check logs** if something doesn't work
5. **Run tests** to verify setup: `php artisan test`

## 💡 Pro Tips

1. **Use queue:listen for development** - Auto-reloads on code changes
2. **Use queue:work for production** - More efficient
3. **Set timeout for long-running workflows** - `--timeout=300`
4. **Monitor failed jobs table** - Contains error details
5. **Use Laravel Telescope** (if installed) for debugging

---

## 🎉 You're Ready!

With all services running, you can:
- Create workflows via API
- Trigger executions manually
- Set up webhooks
- Schedule automated workflows
- Monitor execution status
- View analytics

Remember: **Always keep the queue worker running!** 🚀
