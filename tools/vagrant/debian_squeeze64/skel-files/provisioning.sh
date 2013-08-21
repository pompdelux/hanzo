#!/bin/bash

# only run this stuff once
if [ -f /var/www/hanzo/README.md ];
then
    exit
fi

export DEBIAN_FRONTEND=noninteractive

# setup locales
sed -i -e 's/# da_DK.UTF-8/da_DK.UTF-8/' /etc/locale.gen
sed -i -e 's/# de_DE.UTF-8/de_DE.UTF-8/' /etc/locale.gen
sed -i -e 's/# fi_FI.UTF-8/fi_FI.UTF-8/' /etc/locale.gen
sed -i -e 's/# nb_NO.UTF-8/nb_NO.UTF-8/' /etc/locale.gen
sed -i -e 's/# nl_NL.UTF-8/nl_NL.UTF-8/' /etc/locale.gen
sed -i -e 's/# sv_SE.UTF-8/sv_SE.UTF-8/' /etc/locale.gen
locale-gen

# i need ll
sed -i -e 's/#alias ll/alias ll/' /home/vagrant/.bashrc

# setup dotdeb
echo -e '# dotdeb\ndeb http://packages.dotdeb.org squeeze all\ndeb-src http://packages.dotdeb.org squeeze all\ndeb http://packages.dotdeb.org squeeze-php54 all\ndeb-src http://packages.dotdeb.org squeeze-php54 all\n' > /etc/apt/sources.list.d/dotdeb.list
wget --quiet -O - http://www.dotdeb.org/dotdeb.gpg | apt-key add -

# setup percona
echo -e '# percona\ndeb http://repo.percona.com/apt squeeze main\ndeb-src http://repo.percona.com/apt squeeze main\n' > /etc/apt/sources.list.d/percona.list
apt-key adv --keyserver keys.gnupg.net --recv-keys 0xCD2EFD2A

# remove exim and update apt
apt-get -qq -y remove --purge exim4
apt-get -qq update > /dev/null
apt-get -qq -y upgrade

# install required packages:
# - ssmtp
# - ack-grep
# - vim
# - git
# - tree
# - nginx
# - percona (mysql)
# - redis
# - php 5.4
#   - php5-imagick
#   - php5-apc
#   - php5-redis
#   - php5-fpm
apt-get -qq install -y ssmtp ack-grep vim vim-scripts ctags git tree percona-server-server-5.5 percona-server-client-5.5 redis-server nginx php5-fpm php5-apc php5-cli php5-curl php5-gd php5-imagick php5-intl php5-mcrypt php5-mysqlnd php5-redis php5-xdebug php5-xsl php-pear php5-dev

# php.ini changes
sed -i -e 's/post_max_size = 8M/post_max_size = 20M/' /etc/php5/fpm/php.ini
sed -i -e 's/upload_max_filesize = 2M/upload_max_filesize = 20M/' /etc/php5/fpm/php.ini
sed -i -e 's/;date.timezone =/date.timezone = Europe\/Copenhagen/' /etc/php5/fpm/php.ini
sed -i -e 's/memory_limit = 128M/memory_limit = 256M/' /etc/php5/fpm/php.ini

mkdir -p /var/run/php5-fpm/

# setup site config for hanzo.tld
cp /var/www/hanzo/tools/vagrant/debian_squeeze64/skel-files/hanzo.nginx.conf /etc/nginx/sites-available/
sed -i -e "s/.tld/.$1/g" /etc/nginx/sites-available/hanzo.nginx.conf
ln -s /etc/nginx/sites-available/hanzo.nginx.conf /etc/nginx/sites-enabled/hanzo.nginx.conf

# setup php5-fpm for hanzo.tld
cp /var/www/hanzo/tools/vagrant/debian_squeeze64/skel-files/hanzo.php-fpm.conf /etc/php5/fpm/pool.d/
sed -i -e "s/.tld/.$1/g" /etc/php5/fpm/pool.d/hanzo.php-fpm.conf

# setup site config for cdn.hanzo.tld
cp /var/www/hanzo/tools/vagrant/debian_squeeze64/skel-files/hanzo-cdn.nginx.conf /etc/nginx/sites-available/
sed -i -e "s/.tld/.$1/g" /etc/nginx/sites-available/hanzo-cdn.nginx.conf
ln -s /etc/nginx/sites-available/hanzo-cdn.nginx.conf /etc/nginx/sites-enabled/hanzo-cdn.nginx.conf

# setup php5-fpm for cdn.hanzo.tld
cp /var/www/hanzo/tools/vagrant/debian_squeeze64/skel-files/hanzo-cdn.php-fpm.conf /etc/php5/fpm/pool.d/
sed -i -e "s/.tld/.$1/" /etc/php5/fpm/pool.d/hanzo-cdn.php-fpm.conf

# turn sendfile off
sed -i -e 's/sendfile on;/sendfile off;/' /etc/nginx/nginx.conf

# create main database and setup my.cnf
mysql -u root -e "CREATE DATABASE hanzo"
mysql -u root hanzo < /var/www/hanzo/app/propel/sql/default.sql
cp /var/www/hanzo/tools/vagrant/debian_squeeze64/skel-files/my.cnf /etc/mysql/

# restart services
service php5-fpm restart
service nginx restart
service mysql restart
