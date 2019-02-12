#!/usr/bin/env bash

#== Import script args ==

github_token=$(echo "$1")

#== Bash helpers ==

function info {
  echo " "
  echo "--> $1"
  echo " "
}

#== Provision script ==

info "Provision-script user: `whoami`"

info "Configure composer"
composer config --global github-oauth.github.com ${github_token}
echo "Done!"

info "Install project dependencies"
cd /app
composer --no-progress --prefer-dist install

info "Create bash-alias 'app' for vagrant user"
echo 'alias app="cd /app"' >> /home/vagrant/.bash_aliases
echo 'alias console="./bin/console"' >> /home/vagrant/.bash_aliases
echo 'alias phpunit="./vendor/bin/simple-phpunit"' >> /home/vagrant/.bash_aliases

info "Enabling colorized prompt for guest console"
sed -i "s/#force_color_prompt=yes/force_color_prompt=yes/" /home/vagrant/.bashrc

source /home/vagrant/.bash_aliases

cd /app

console doctrine:database:create
console doctrine:database:create --env=test

console doctrine:migration:migrate --no-interaction
console doctrine:migration:migrate --no-interaction --env=test