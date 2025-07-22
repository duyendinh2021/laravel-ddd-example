.PHONY: help build up down logs shell test

# Default target
help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

# Development commands
dev-build: ## Build development containers
	docker compose build

dev-up: ## Start development environment
	docker compose up -d

dev-down: ## Stop development environment
	docker compose down

dev-logs: ## Show development logs
	docker compose logs -f

dev-shell: ## Access development container shell
	docker compose exec app bash

dev-install: ## Install dependencies in development
	docker compose exec app composer install

dev-migrate: ## Run migrations in development
	docker compose exec app php artisan migrate:fresh --seed

dev-key: ## Generate application key in development
	docker compose exec app php artisan key:generate

dev-test: ## Run tests in development
	docker compose exec app php artisan test

# Production commands
prod-build: ## Build production containers
	docker compose -f docker-compose.prod.yml build

prod-up: ## Start production environment
	docker compose -f docker-compose.prod.yml up -d

prod-down: ## Stop production environment
	docker compose -f docker-compose.prod.yml down

prod-logs: ## Show production logs
	docker compose -f docker-compose.prod.yml logs -f

prod-shell: ## Access production container shell
	docker compose -f docker-compose.prod.yml exec app bash

# Utility commands
clean: ## Clean up Docker resources
	docker system prune -f
	docker volume prune -f

restart-dev: dev-down dev-up ## Restart development environment

restart-prod: prod-down prod-up ## Restart production environment