FROM php:7.4-cli-alpine
ENV XDEBUG_VERSION "2.9.5"
RUN apk update && apk add --no-cache $PHPIZE_DEPS
RUN pecl install xdebug-${XDEBUG_VERSION} \
    && docker-php-ext-enable xdebug
RUN apk del $PHPIZE_DEPS && rm -rf /var/cache/apk/*
VOLUME ["/app"]
WORKDIR /app