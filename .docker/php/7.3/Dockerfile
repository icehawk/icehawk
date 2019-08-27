FROM php:7.3-cli-alpine
ENV XDEBUG_VERSION "2.8.0beta2"
RUN apk update && apk add --no-cache $PHPIZE_DEPS bash git curl
RUN pecl install xdebug-${XDEBUG_VERSION} \
    && docker-php-ext-enable xdebug
RUN apk del $PHPIZE_DEPS && rm -rf /var/cache/apk/*
WORKDIR /code
ENTRYPOINT ["/bin/bash"]