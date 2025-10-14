.PHONY: help build up down shell composer install test psalm rector rector-fix clean

# Цвета для вывода
BLUE := \033[0;34m
NC := \033[0m # No Color

help: ## Показать эту справку
	@echo "$(BLUE)Доступные команды:$(NC)"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(BLUE)%-15s$(NC) %s\n", $$1, $$2}'

build: ## Собрать Docker образ
	docker compose build

up: ## Запустить контейнер
	docker compose up -d

down: ## Остановить контейнер
	docker compose down

shell: ## Войти в контейнер (bash)
	docker compose exec php sh

composer: ## Запустить composer (использование: make composer ARGS="install")
	docker compose run --rm php composer $(ARGS)

install: ## Установить зависимости
	docker compose run --rm php composer install

update: ## Обновить зависимости
	docker compose run --rm php composer update

test: ## Запустить тесты
	docker compose run --rm -e XDEBUG_MODE=off php vendor/bin/phpunit --no-coverage

test-coverage: ## Запустить тесты с coverage
	docker compose run --rm php vendor/bin/phpunit --coverage-html coverage

test-coverage-text: ## Запустить тесты с текстовым coverage
	docker compose run --rm php vendor/bin/phpunit --coverage-text

psalm: ## Проверить код с Psalm
	docker compose run --rm -e XDEBUG_MODE=off php vendor/bin/psalm

psalm-info: ## Показать информацию о типах
	docker compose run --rm -e XDEBUG_MODE=off php vendor/bin/psalm --show-info=true

rector: ## Проверить код с Rector (dry-run)
	docker compose run --rm -e XDEBUG_MODE=off php vendor/bin/rector process --dry-run

rector-fix: ## Применить исправления Rector
	docker compose run --rm -e XDEBUG_MODE=off php vendor/bin/rector process

clean: ## Очистить кеши и временные файлы
	rm -rf vendor coverage .phpunit.cache
	docker compose down -v

init: build install ## Инициализация проекта (build + install)
	@echo "$(BLUE)Проект готов к работе!$(NC)"

check: psalm rector test ## Полная проверка кода (psalm + rector + tests)
	@echo "$(BLUE)Все проверки пройдены!$(NC)"
