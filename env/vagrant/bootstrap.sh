#!/usr/bin/env bash

PROJECT_NAME="icehawk"

# link the uploaded nginx config to enable
echo -e "\e[0m--"
rm -rf /etc/nginx/sites-enabled/*
for name in dist doc test pma; do
    # link
    ln -sf /etc/nginx/sites-available/$name /etc/nginx/sites-enabled/020-$name
    # check link
    test -L /etc/nginx/sites-enabled/020-$name && echo -e "\e[0mLinking nginx $name config: \e[1;32mOK\e[0m" || echo -e "Linking nginx $name config: \e[1;31mFAILED\e[0m";
done

# set correct permissions for private key
chmod 0700 /root/.ssh
chmod 0600 /root/.ssh/id_rsa
chmod 0600 /root/.ssh/config

# restart nginx
echo -e "\e[0m--"
service nginx restart

# link the document root (forced)
ln -sf /vagrant /var/www/$PROJECT_NAME
# check link
echo -e "\e[0m--"
test -L /var/www/$PROJECT_NAME && echo -e "\e[0mLinking document root: \e[1;32mOK\e[0m" || echo -e "\e[0mLinking document root: \e[1;31mFAILED\e[0m"

# Install phpunit and composer with current release
echo -e "\e[0m--\n\e[0mInstalling composer and phpunit ..."

# composer
if [ -x /usr/local/bin/composer ]
then
    /usr/local/bin/composer self-update --clean-backups
else
    rm -rf /usr/local/bin/composer
    curl -sS 'https://getcomposer.org/installer' | php -- --filename=composer --install-dir=/usr/local/bin 2>&1 >/dev/null
    chmod +x /usr/local/bin/composer
    echo -e "\e[0m--"
    test -x /usr/local/bin/composer && echo -e "\e[0mInstalling composer: \e[1;32mOK\e[0m" || echo -e "\e[0mInstalling composer: \e[1;31mFAILED\e[0m"
fi

# phpunit
rm -rf /usr/local/bin/phpunit
curl -sS 'https://phar.phpunit.de/phpunit.phar' > /usr/local/bin/phpunit
chmod +x /usr/local/bin/phpunit

# Test if exists and is executable
echo -e "\e[0m--"
test -x /usr/local/bin/phpunit && echo -e "\e[0mInstalling phpunit: \e[1;32mOK\e[0m" || echo -e "\e[0mInstalling phpunit: \e[1;31mFAILED\e[0m"

# Show versions
echo -e "\e[0m--"
/usr/local/bin/composer --version
/usr/local/bin/phpunit --version

# Run composer
echo -e "\e[0m--"
/usr/local/bin/composer update -d="/vagrant" -n -o --no-progress

# Determine the public ip address and show a message
IP_ADDR=`ifconfig eth1 | grep inet | grep -v inet6 | awk '{print $2}' | cut -c 6-`

echo -e "\e[0m--\nYour machine's ip address is: \e[1;31m$IP_ADDR\e[0m\n--\n"
