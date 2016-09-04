#!/usr/bin/env bash

set -e

export DEBIAN_FRONTEND=noninteractive

# Enable parametization of via env variables
[ -z $MYSQL_PASS ] && MYSQL_PASS=vagrant
[ -z $NGINX_CONFIG_FILE ] && NGINX_CONFIG_FILE=/vagrant/provision/default.nginx
[ -z $PHPUNIT_EXECUTABLE ] && PHPUNIT_EXECUTABLE=/usr/local/bin/phpunit
[ -z $COMPOSER_EXECUTABLE ] && COMPOSER_EXECUTABLE=/usr/local/bin/composer

echo Updating apt package repos
sudo apt-get update -qq

echo Installing MySQL
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password ${MYSQL_PASS} ${MYSQL_PASS}"
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password_again ${MYSQL_PASS} ${MYSQL_PASS}"
sudo apt-get -y -qq install mysql-server mysql-client

echo Installing Required Packages
sudo apt-get install -y -qq \
  nginx \
  php5-fpm \
  php5-mysql \
  php5 \
  php5-cli \
  php5-mcrypt \
  curl


echo  Backing up Nginx Config
sudo cp /etc/nginx/sites-available/default{,_old}

echo Installing Nginx custom config
sudo cp $NGINX_CONFIG_FILE /etc/nginx/sites-available/default

_php_fpm_configured() { grep -q 'cgi.fix_pathinfo=0' /etc/php5/fpm/php.ini ;}

if ! _php_fpm_configured; then
  echo Configuring php-fpm
  echo 'cgi.fix_pathinfo=0' | sudo tee --append /etc/php5/fpm/php.ini
fi

if ! php -m | grep -q mcrypt; then
  echo Enabling PHP Mcrypt extension
  sudo php5enmod mcrypt
fi

if ! type phpunit; then
  echo Installing PHPUnit
  curl -L -sS -o $PHPUNIT_EXECUTABLE 'https://phar.phpunit.de/phpunit.phar' && \
    chmod +x $PHPUNIT_EXECUTABLE
fi

if ! type composer; then
  echo Installing composer
  curl -sSL https://getcomposer.org/installer | \
    php -- --install-dir=$(dirname $COMPOSER_EXECUTABLE) --filename=composer && \
    chmod +x $COMPOSER_EXECUTABLE
fi

