PHP     = php
ARTISAN = $(PHP) artisan
COMPOSER = composer

.PHONY: help serve dev build install update migrate migrate-status seed fresh rollback \
        cache-clear config-cache queue-work queue-restart tinker test lint lint-check

help: ## Show available commands
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'

# ── Dev server ────────────────────────────────────────────────────────────────

serve: ## Start local dev server at http://localhost:8000
	$(ARTISAN) serve
	npm run dev

dev: ## Start Vite asset dev server (run alongside make serve)
	npm run dev

build: ## Build and bundle assets for production
	npm run build

# ── Composer ──────────────────────────────────────────────────────────────────

install: ## composer install
	$(COMPOSER) install

update: ## composer update --with-all-dependencies
	$(COMPOSER) update --with-all-dependencies

# ── Artisan ───────────────────────────────────────────────────────────────────

migrate: ## Run pending migrations
	$(ARTISAN) migrate

migrate-status: ## Show migration status
	$(ARTISAN) migrate:status

seed: ## Run database seeders
	$(ARTISAN) db:seed

fresh: ## Drop all tables and re-run migrations + seeders
	$(ARTISAN) migrate:fresh --seed

rollback: ## Rollback the last migration batch
	$(ARTISAN) migrate:rollback

cache-clear: ## Clear all application caches
	$(ARTISAN) cache:clear
	$(ARTISAN) config:clear
	$(ARTISAN) route:clear
	$(ARTISAN) view:clear

config-cache: ## Cache config, routes and views for production
	$(ARTISAN) config:cache
	$(ARTISAN) route:cache
	$(ARTISAN) view:cache

queue-work: ## Start a queue worker
	$(ARTISAN) queue:work --verbose --tries=3 --timeout=90

queue-restart: ## Restart queue workers gracefully
	$(ARTISAN) queue:restart

tinker: ## Open Laravel Tinker REPL
	$(ARTISAN) tinker

# ── Quality ───────────────────────────────────────────────────────────────────

test: ## Run PHPUnit test suite
	$(ARTISAN) test

lint: ## Run Laravel Pint code formatter
	./vendor/bin/pint

lint-check: ## Check formatting without writing changes
	./vendor/bin/pint --test
