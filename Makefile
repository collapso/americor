.PHONY: help
.DEFAULT_GOAL := help
SHELL ?= /bin/bash
MAKEFILE_PATH := $(abspath $(lastword $(MAKEFILE_LIST)))
COMPOSE_EXEC ?= docker-compose exec

## Start shell session in the yii2-php:8.1-apache container
bash:
	docker exec -it americor-app bash

## Build docker
build: docker-compose.yml
	docker-compose up -d --build --remove-orphans

## Run composer install
composer-install: composer.json
	$(COMPOSE_EXEC) php composer install

## Execute database migrations
db-migrate:
	$(COMPOSE_EXEC) php yii migrate

up: build composer-install db-migrate

help:
	@echo "Available targets:"
	@awk '/^[a-zA-Z0-9_-]+:/ { \
	        helpMessage = match(lastLine, /^## (.*)/); \
	        if (helpMessage) { \
	            sub(/^## /, "", lastLine); \
	            printf "  %-30s %s\n", $$1, lastLine; \
	        } \
	    } \
	    { lastLine = $$0 }' $(MAKEFILE_LIST) | sort
