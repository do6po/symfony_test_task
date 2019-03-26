
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
prod:
	$(d) network prune -f
	$(build_command)
	chmod +x console phpunit
	echo "####################################################"
	echo "go to http://symfony_project.test"
	echo "####################################################"

####################################################
#Работа с контейнерами
#Установка для разработки
dev:
	$(d) network prune -f
	CURRENT_UID=`id -u` CURRENT_GID=`id -g` CURRENT_USERNAME=`id -u -n` $(dc) -f docker-compose.yml -f docker-compose.dev.yml up -d --build --force-recreate
	chmod +x console phpunit
	echo "####################################################"
	echo "go to http://symfony_project.test"
	echo "####################################################"

####################################################
