#!/bin/bash

service ssh start
service php5-fpm start
service nginx start

mailcatcher


# Create logs directory
if [[ ! -d /var/www/logs ]]; then
    mkdir /var/www/logs
    chown -R www-data: /var/www/logs
fi

tail -f /var/log/dmesg
