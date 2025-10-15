# N8N Clone - Testing Makefile
# Quick commands for testing the core workflow functionality

.PHONY: help test setup clean test-core test-api test-nodes test-all

help:
	@echo "N8N Clone Testing Commands:"
	@echo "  make setup       - Initial setup (migrations, passport, etc.)"
	@echo "  make test        - Run all PHPUnit tests"
	@echo "  make test-core   - Test core workflow execution via API"
	@echo "  make test-api    - Test all API endpoints"
	@echo "  make test-nodes  - Test individual node executors"
	@echo "  make test-all    - Run complete test suite"
	@echo "  make clean       - Clear caches and logs"
	@echo "  make queue       - Start queue worker for async execution"

setup:
	@echo "🔧 Setting up N8N Clone..."
	php artisan migrate
	php artisan passport:install
	php artisan key:generate
	php artisan cache:clear
	php artisan config:clear
	php artisan route:clear
	@echo "✅ Setup complete!"

test:
	@echo "🧪 Running PHPUnit tests..."
	php artisan test

test-core:
	@echo "🚀 Testing core workflow functionality..."
	@bash test-core-workflow.sh

test-api:
	@echo "📡 Testing API endpoints..."
	php artisan test tests/Feature/Api

test-nodes:
	@echo "🔌 Testing node executors..."
	php artisan test --filter=Node

test-all:
	@echo "🎯 Running complete test suite..."
	make setup
	make test
	make test-core
	@echo "✅ All tests complete!"

clean:
	@echo "🧹 Cleaning caches and logs..."
	php artisan cache:clear
	php artisan config:clear
	php artisan route:clear
	php artisan view:clear
	> storage/logs/laravel.log
	@echo "✅ Cleanup complete!"

queue:
	@echo "⚡ Starting queue worker..."
	php artisan queue:work --verbose

queue-listen:
	@echo "👂 Starting queue listener (auto-reload on code changes)..."
	php artisan queue:listen --verbose

serve:
	@echo "🌐 Starting Laravel development server..."
	php artisan serve

migrate-fresh:
	@echo "🔄 Fresh migration with seeders..."
	php artisan migrate:fresh --seed

tinker:
	@echo "🔮 Starting Tinker session..."
	php artisan tinker

logs:
	@echo "📋 Tailing Laravel logs..."
	tail -f storage/logs/laravel.log

status:
	@echo "📊 System Status Check..."
	@echo "PHP Version:"
	@php --version | head -1
	@echo ""
	@echo "Laravel Version:"
	@php artisan --version
	@echo ""
	@echo "Database Connection:"
	@php artisan migrate:status | head -5
	@echo ""
	@echo "Queue Status:"
	@php artisan queue:work --stop-when-empty
	@echo ""
	@echo "Routes Count:"
	@php artisan route:list | grep -c "api/v1" || true
