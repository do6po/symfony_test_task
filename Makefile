
.SILENT:

php = php
web = web
db = db

d = docker
dc = docker-compose

####################################################
#Команды

exec = $(d) exec

ssh_command = $(exec) -it

build_command = $(dc) up -d --build --force-recreate

ssh_to_php_command = $(ssh_command) $(php) bash
ssh_to_web_command = $(ssh_command) $(web) bash
ssh_to_db_command = $(ssh_command) $(db) bash

console = ./bin/console
console_command = $(exec) $(php) ./bin/console

composer = composer
composer_install_command = $(exec) $(php) $(composer) --no-progress --prefer-dist install

create_database = $(console) doctrine:database:create --if-not-exists
create_database_command = $(exec) $(php) php $(create_database)

create_database_test = $(create_database) --env=test
create_database_test_command = $(exec) $(php) php $(create_database_test)

migrate = $(console) doctrine:migration:migrate --no-interaction
migrate_command = $(exec) $(php) php $(migrate)

migrate_test = $(migrate) --env=test
migrate_test_command = $(exec) $(php) php $(migrate_test)

phpunit = ./vendor/bin/simple-phpunit
run_tests_command = $(exec) $(php) $(phpunit)

####################################################
#Запуск контейнера
build:
	$(build_command)

####################################################
#Вход в контейнеры
ssh_to_php:
	$(ssh_to_php_command)

ssh_to_web:
	$(ssh_to_web_command)

ssh_to_db:
	$(ssh_to_db_command)

####################################################
#Работа с контейнерами
#Установка
install:
	$(build_command)
	$(composer_install_command)
	sleep 2
	$(create_database_command)
	$(create_database_test_command)
	$(migrate_command)
	$(migrate_test_command)
	$(console_command) cache:clear
	$(exec) $(php) chmod 777 -R /app/var/cache
	echo ""
	echo "go to http://symfony_project.test"
	echo ""

####################################################
#Работа с приложением


#console
console:
	$(console_command)

#Запуск миграций
migrate:
	$(migrate_command)

migrate_test:
	$(migrate_test_command)

#Запуск тестов
run_tests:
	$(run_tests_command) $(path)



