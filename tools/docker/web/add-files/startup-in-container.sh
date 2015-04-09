#!/bin/bash

service ssh start
service php5-fpm start
service nginx start

tail -f /var/log/dmesg
