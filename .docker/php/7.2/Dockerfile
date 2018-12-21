FROM php:7.2-cli
# Install packages
RUN apt-get update \
    && apt-get install -y libpq-dev libicu-dev libzip-dev zlib1g-dev zip -y
# Install xdebug
RUN pecl install xdebug-2.7.0beta1
# Install extensions
RUN docker-php-ext-install intl zip \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip
# Install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === '93b54496392c062774670ac18b134c3b3a95e5a5e5c8f1a9f115f203b75bf9a129d5daa8ba6a13e2cc8a1da0806388a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php --install-dir=/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"
# Cleanup
RUN apt-get autoremove --purge -y \
    && apt-get autoclean -y \
    && apt-get clean -s -y
COPY xdebug.ini ${PHP_INI_DIR}/conf.d/xdebug.ini
WORKDIR /code