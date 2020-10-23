FROM php:8.0-rc-fpm-alpine
ENV XDEBUG_VERSION "3.0.0beta1"
# Update system
RUN set -ex && apk update && apk upgrade --available && apk add --no-cache $PHPIZE_DEPS
# Install Xdebug
RUN set -ex && mkdir -p /usr/src/php/ext/xdebug \
	&& curl -fsSL https://pecl.php.net/get/xdebug-${XDEBUG_VERSION} | tar xvz -C "/usr/src/php/ext/xdebug" --strip 1 \
	&& docker-php-ext-install xdebug
RUN set -ex && apk del $PHPIZE_DEPS && rm -rf /var/cache/apk/* && rm -rf /tmp/*
VOLUME ["/app"]
WORKDIR /app