#!/usr/bin/env bash

# PHPUNIT
wget -c https://phar.phpunit.de/phpunit.phar
chmod +x phpunit.phar

# PHPLOC
wget -c https://phar.phpunit.de/phploc.phar
chmod +x phploc.phar

# PHP_DEPEND
wget -c http://static.pdepend.org/php/latest/pdepend.phar
chmod +x pdepend.phar

# PHP Mess Detector
wget -c http://static.phpmd.org/php/latest/phpmd.phar
chmod +x phpmd.phar

# PHP Code Sniffer
wget -c https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar
chmod +x phpcs.phar

# PHP Copy Paste Detector
wget -c https://phar.phpunit.de/phpcpd.phar
chmod +x phpcpd.phar

# PHP Dox
export PHPDOX_VERSION='0.8.0'
wget https://github.com/theseer/phpdox/releases/download/0.8.0/phpdox-$PHPDOX_VERSION.phar
mv phpdox-$PHPDOX_VERSION.phar phpdox.phar
chmod +x phpdox.phar