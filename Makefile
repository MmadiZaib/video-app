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

restart: stop start	## redémarrer les containers

ssh-php:	## Connexion au container php
	$(PHP_DOCKER_COMPOSE_EXEC) bash

## —— Symfony 🎶 ———————————————————————————————————————————————————————————————
vendor-install:	## Installation des vendors
	$(PHP_DOCKER_COMPOSE_EXEC) composer install

vendor-update:	## Mise à jour des vendors
	$(COMPOSER) update

composer:	## Composer
	$(PHP_DOCKER_COMPOSE_EXEC) composer $(filter-out $@,$(MAKECMDGOALS))

console:	## Composer
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
	$(SYMFONY_CONSOLE) d:d:d --force --connection --if-exists
	$(SYMFONY_CONSOLE) d:d:c
	$(SYMFONY_CONSOLE) d:m:m --no-interaction
	$(SYMFONY_CONSOLE) d:s:u --force
	$(SYMFONY_CONSOLE) d:f:l --no-interaction

load-fixtures: cc ## load fixtures
	$(SYMFONY_CONSOLE) d:f:l -n

clean-db-test: cc-hard cc-test ## Réinitialiser la base de donnée en environnement de test
	$(SYMFONY_CONSOLE) d:d:d --force --env=test --if-exists
	$(SYMFONY_CONSOLE) d:d:c --env=test
	$(SYMFONY_CONSOLE) d:m:m --no-interaction --env=test
	$(SYMFONY_CONSOLE) d:s:u --force --env=test
	$(SYMFONY_CONSOLE) d:f:l --no-interaction --env=test

test-unit: ## Lancement des tests unitaire
	$(PHP_DOCKER_COMPOSE_EXEC) bin/phpunit tests/unit/

test-unit-coverage:
	$(PHP_DOCKER_COMPOSE_EXEC) bin/phpunit tests/unit/ --testdox --coverage-html=test-coverage --testsuite=unit

test-single-coverage:
	$(PHP_DOCKER_COMPOSE_EXEC) bin/phpunit tests/unit/ --testdox --coverage-text $(test)

test-func: clean-db-test	## Lancement des tests fonctionnel
	$(PHP_DOCKER_COMPOSE_EXEC) bin/phpunit tests/functional/

test-single: ## Lancement test sur un fichier unique
	$(PHP_DOCKER_COMPOSE_EXEC) bin/phpunit --testdox $(test)

tests: test-func test-unit	## Lancement de tous tests

cs-fix: ## Lancement du php-cs-fixer
	$(PHP_DOCKER_COMPOSE_EXEC) vendor/bin/php-cs-fixer fix src

%:
    @:

## —— Others 🛠️️ ———————————————————————————————————————————————————————————————
help: ## Liste des commandes
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
