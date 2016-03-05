#!/usr/bin/env bash

cd "$(dirname "$0")"

rm -rf ./*.phar

# Composer
rm -f composer.phar
curl -sS 'https://getcomposer.org/installer' | php --
chmod +x composer.phar

# PHPUNIT
rm -f phpunit.phar
wget -c https://phar.phpunit.de/phpunit.phar
chmod +x phpunit.phar

# PHPLOC
rm -f phploc.phar
wget -c https://phar.phpunit.de/phploc.phar
chmod +x phploc.phar

# PHP_DEPEND
rm -f pdepend.phar
wget -c http://static.pdepend.org/php/latest/pdepend.phar
chmod +x pdepend.phar

# PHP Mess Detector
rm -f phpmd.phar
wget -c http://static.phpmd.org/php/latest/phpmd.phar
chmod +x phpmd.phar

# PHP Code Sniffer
rm -f phpcs.phar
wget -c https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar
chmod +x phpcs.phar

# PHP Copy Paste Detector
rm -f phpcpd.phar
wget -c https://phar.phpunit.de/phpcpd.phar
chmod +x phpcpd.phar

# PHP Dox
PHPDOX_VERSION='0.8.1.1'
rm -f phpdox.phar
wget "https://github.com/theseer/phpdox/releases/download/$PHPDOX_VERSION/phpdox-$PHPDOX_VERSION.phar"
mv "phpdox-$PHPDOX_VERSION.phar" phpdox.phar
chmod +x phpdox.phar

# Coveralls
COVERALLS_VERSION='v1.0.1'
wget "https://github.com/satooshi/php-coveralls/releases/download/$COVERALLS_VERSION/coveralls.phar"
chmod +x coveralls.phar
