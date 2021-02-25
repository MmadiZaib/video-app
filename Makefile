.PHONY: help
.DEFAULT_GOAL = help

DOCKER_COMPOSE=@docker-compose
DOCKER_COMPOSE_EXEC=$(DOCKER_COMPOSE) exec
PHP_DOCKER_COMPOSE_EXEC=$(DOCKER_COMPOSE_EXEC) php-fpm
COMPOSER=$(PHP_DOCKER_COMPOSE_EXEC) composer
SYMFONY_CONSOLE=$(PHP_DOCKER_COMPOSE_EXEC) bin/console

## —— Docker 🐳  ———————————————————————————————————————————————————————————————
start:	## Lancer les containers docker
	$(DOCKER_COMPOSE) up -d

stop:	## Arréter les containers docker
	$(DOCKER_COMPOSE) stop

rm:	stop ## Supprimer les containers docker
	$(DOCKER_COMPOSE) rm -f

restart: rm start	## redémarrer les containers

ssh-php:	## Connexion au container php
	$(PHP_DOCKER_COMPOSE_EXEC) bash

## —— Symfony 🎶 ———————————————————————————————————————————————————————————————
vendor-install:	## Installation des vendors
	$(PHP_DOCKER_COMPOSE_EXEC) composer install

vendor-update:	## Mise à jour des vendors
	$(COMPOSER) update

composer:	## Composer
	$(PHP_DOCKER_COMPOSE_EXEC) composer $(filter-out $@,$(MAKECMDGOALS))

symfony:	## Composer
	$(SYMFONY_CONSOLE) $(filter-out $@,$(MAKECMDGOALS))

clean-vendor: cc-hard ## Suppression du répertoire vendor puis un réinstall
	$(PHP_DOCKER_COMPOSE_EXEC) rm -Rf vendor
	$(PHP_DOCKER_COMPOSE_EXEC) rm composer.lock
	$(COMPOSER) install

cc:	## Vider le cache
	$(SYMFONY_CONSOLE) c:c

cc-test:	## Vider le cache de l'environnement de test
	$(SYMFONY_CONSOLE) c:c --env=test

cc-hard: ## Supprimer le répertoire cache
	$(PHP_DOCKER_COMPOSE_EXEC) rm -fR var/cache/*

clean-db: ## Réinitialiser la base de donnée
	$(SYMFONY_CONSOLE) d:d:d --force --connection
	$(SYMFONY_CONSOLE) d:d:c
	$(SYMFONY_CONSOLE) d:m:m --no-interaction
	$(SYMFONY_CONSOLE) d:f:l --no-interaction

load-fixtures: cc ## load fixtures
	$(SYMFONY_CONSOLE) d:f:l -n

clean-db-test: cc-hard cc-test ## Réinitialiser la base de donnée en environnement de test
	- $(SYMFONY_CONSOLE) d:d:d --force --env=test
	$(SYMFONY_CONSOLE) d:d:c --env=test
	$(SYMFONY_CONSOLE) d:m:m --no-interaction --env=test
	$(SYMFONY_CONSOLE) d:f:l --no-interaction --env=test

test-unit: ## Lancement des tests unitaire
	$(PHP_DOCKER_COMPOSE_EXEC) bin/phpunit tests/Unit/

test-func: clean-db-test	## Lancement des tests fonctionnel
	$(PHP_DOCKER_COMPOSE_EXEC) bin/phpunit tests/Func/

tests: test-func test-unit	## Lancement de tous tests

cs: ## Lancement du php cs
	$(PHP_DOCKER_COMPOSE_EXEC) vendor/bin/phpcs -n

%:
    @:

## —— Others 🛠️️ ———————————————————————————————————————————————————————————————
help: ## Liste des commandes
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
