#!/bin/bash
MYSQL_PASS=my5QLpw

# Variables
VOLUME_HOME="/var/lib/mysql"
LOG="/var/log/mysql/error.log"


# Start services
service ssh start
service redis-cache start
service redis-server start
service beanstalkd start


# No database is set
if [[ ! -d $VOLUME_HOME/mysql ]]; then

    # Set permissions
    chmod -R 777 $VOLUME_HOME

    # Install default database
    mysql_install_db

    # Set permissions correctly
    mysql -u root --password=$MYSQL_PASS -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY '${MYSQL_PASS}' WITH GRANT OPTION; FLUSH PRIVILEGES;"
fi

# Prevent script-stopping and start MySQL
tail -F $LOG &
exec mysqld_safe