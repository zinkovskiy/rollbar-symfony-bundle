FROM php:8.1-cli-alpine as rollbar-symfony-bundle

ARG DEV_HOST_IP
ENV DEV_HOST_IP=$DEV_HOST_IP

ARG PHP_IDE_CONFIG="serverName=rollbar-symfony-bundle"
ENV PHP_IDE_CONFIG=$PHP_IDE_CONFIG

ARG DEV_XDEBUG_AUTOSTART="yes"
ENV DEV_XDEBUG_AUTOSTART=$DEV_XDEBUG_AUTOSTART

ARG DEV_XDEBUG_IDE_KEY
ENV DEV_XDEBUG_IDE_KEY=$DEV_XDEBUG_IDE_KEY

RUN set -eux
RUN apk update

# look here: https://github.com/php/php-src/issues/8681#issuecomment-1354733347
RUN apk add --no-cache linux-headers
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN pecl install xdebug;\
    docker-php-ext-enable xdebug ;\
    echo "xdebug.mode=coverage,debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.start_with_request=$DEV_XDEBUG_AUTOSTART" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.client_host=$DEV_HOST_IP" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.log=/var/log/xdebug.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.idekey=$DEV_XDEBUG_IDE_KEY" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini;

WORKDIR /var/www/app

ENTRYPOINT ["tail", "-f", "/dev/null"]
