FROM php:8.1-cli-alpine
RUN set -ex && apk update && apk upgrade --available
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN set -ex && install-php-extensions xdebug-stable
VOLUME ["/repo"]
WORKDIR /repo