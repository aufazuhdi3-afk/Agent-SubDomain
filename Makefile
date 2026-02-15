.PHONY: help install build serve test clean db-seed queue-work queue-failed format lint watch

help:
	@echo "Available commands:"
	@echo "  make install      - Install dependencies (Composer + npm)"
	@echo "  make build        - Build assets (Vite)"
	@echo "  make serve        - Start development server"
	@echo "  make test         - Run all tests"
	@echo "  make test-e2e     - Run E2E provisioning test only"
	@echo "  make clean        - Clear caches and logs"
	@echo "  make db-seed      - Seed admin user"
	@echo "  make queue-work   - Run queue worker once"
	@echo "  make queue-failed - Show failed jobs"
	@echo "  make format       - Format PHP code"
	@echo "  make lint         - Lint PHP code"
	@echo "  make watch       - Watch assets for changes"
	@echo "  make dev          - Build & serve (development)"

install:
	composer install
	npm ci

build:
	npm run build

serve:
	php artisan serve --host=0.0.0.0 --port=8000

test:
	./vendor/bin/phpunit --colors=always

test-e2e:
	./vendor/bin/phpunit tests/Feature/DomainProvisioningE2ETest.php --colors=always

clean:
	php artisan config:clear
	php artisan cache:clear
	php artisan route:cache
	php artisan view:clear
	rm -f storage/logs/laravel.log

db-seed:
	php artisan migrate:refresh --seed

queue-work:
	php artisan queue:work --once --timeout=60 --tries=3

queue-failed:
	php artisan queue:failed

format:
	./vendor/bin/pint

lint:
	./vendor/bin/phpstan analyse app

watch:
	npm run dev

dev: build serve
