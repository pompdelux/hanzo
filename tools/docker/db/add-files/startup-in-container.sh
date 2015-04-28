#!/bin/bash
MYSQL_PASS=my5QLpw

service ssh start
service mysql start

mysql -u root --password=$MYSQL_PASS -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY '${MYSQL_PASS}' WITH GRANT OPTION; FLUSH PRIVILEGES;"

service redis-cache start
service redis-server start
service beanstalkd start

tail -f /var/log/dmesg
