.DEFAULT_GOAL = help

.PHONY: cc
cc: ## Clear Symfony cache
	@docker compose exec php bin/console cache:clear

.PHONY: opcache
opcache: ## Clear opcache
	docker-compose exec php cachetool opcache:reset

.PHONY: build
build: ## Build containers
	@docker compose build

.PHONY: stop
stop: ## Stop containers
	@docker compose stop

.PHONY: down
down: ## Remove containers but keep volumes
	@docker compose down

.PHONY: remove
remove: ## Remove containers and volumes
	@docker compose down --remove-orphans -v

.PHONY: up
up: ## Run containers
	@docker compose up -d --remove-orphans

.PHONY: up-build
up-build: ## Build containers and run them
	@docker compose up -d --build --remove-orphans

.PHONY: upgrade
upgrade: ## Upgrade database after a WordPress upgrade
	@docker-compose exec -u 82:82 php bin/console wp core update-db
	@docker-compose exec -u 82:82 php bin/console wp wc update
	@docker-compose exec -u 82:82 php bin/console wp cron event run --due-now
	@docker-compose exec -u 82:82 php bin/console wp action-scheduler run

.PHONY: acl
acl: ## Reset project files and directories ACL
	@sudo chown -R $(whoami):$(whoami) .
	@sudo setfacl -dR -m u:$(whoami):rwX -m u:www-data:rwX -m u:82:rwX .
	@sudo setfacl -R -m u:$(whoami):rwX -m u:www-data:rwX -m u:82:rwX .

.PHONY: help
help: ## List all commands
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
