-include .env
export

ifeq ($(ARG), selenium)
arg = --scale selenium=0
endif

DB_PORT = $$DATABASE_PORT
DB_USER = $$DATABASE_USER
DB_PASSWORD = $$DATABASE_PASSWORD
DB_NAME = $$DATABASE_NAME

default: install

install:
	docker-compose up -d --build --force-recreate $(arg)
	docker-compose exec php gzip -dkf data/sql/bayard_test.sql.gz
	docker-compose exec -T mysql mysql -h 127.0.0.1 -P $(DB_PORT) -u $(DB_USER) -p$(DB_PASSWORD) -D $(DB_NAME) < data/sql/bayard_test.sql
	rm data/sql/bayard_test.sql
	docker-compose exec php composer install

start:
	docker-compose up -d

recreate:
	docker-compose up -d --build --force-recreate

stop:
	@docker-compose down

down:
	@docker-compose down -v --remove-orphans

clear:
	@docker-compose down --rmi -v --remove-orphans

php:
	@docker-compose exec php $(ARG)

httpd:
	@docker-compose exec httpd $(ARG)

mysql:
	@docker-compose exec mysql $(ARG)

selenium:
	@docker-compose exec selenium $(ARG)
