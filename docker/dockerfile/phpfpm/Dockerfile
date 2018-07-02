FROM php:7.2-fpm-alpine

# Install git
RUN apk update
RUN apk add --no-cache git
RUN docker-php-ext-install pdo_mysql opcache

# install composer
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

# Goto temporary directory.
WORKDIR /tmp

# Run composer and phpunit installation.
RUN composer selfupdate && \
  composer require "phpunit/phpunit:~5.4" && \
  ln -s /tmp/vendor/bin/phpunit /usr/local/bin/phpunit && \
  composer require "codeception/codeception" && \
  ln -s /tmp/vendor/bin/codecept /usr/local/bin/codecept

RUN { \
  echo 'memory_limit=756M'; \
  echo 'max_execution_time=18000'; \
  echo 'session.auto_start=off'; \
  echo 'suhosin.session.cryptua=off'; \
  echo 'zlib.output_compression=on'; \
  echo 'error_reporting=E_ALL | E_STRICT'; \
} >> /usr/local/etc/php/conf.d/php.ini

# Set up the application directory.
WORKDIR /var/www/html/