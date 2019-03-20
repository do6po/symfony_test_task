#!/usr/bin/env bash

cd /app

chmod +x bin/console

composer install --no-progress --prefer-dist --working-dir=/app

sleep 2

php bin/console doctrine:database:create --if-not-exists

php bin/console doctrine:migration:migrate --no-interaction


php bin/console cache:clear

chmod 777 -R /app/var

exec php-fpm --nodaemonize