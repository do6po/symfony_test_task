#!/usr/bin/env bash

chmod +x bin/console

composer install --no-progress --prefer-dist --working-dir=/app

sleep 2

php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:database:create --if-not-exists --env=test
php bin/console doctrine:migration:migrate --no-interaction
php bin/console doctrine:migration:migrate --no-interaction --env=test

php bin/console cache:clear

sudo -s

exec php-fpm --nodaemonize