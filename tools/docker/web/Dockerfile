FROM debian:wheezy
MAINTAINER Henrik Farre <henrik@bellcom.dk>

ENV DEBIAN_FRONTEND noninteractive
ENV DEBIAN_PRIORITY critical
ENV DEBCONF_NOWARNINGS yes
ENV MYSQL_PASS my5QLpw

# Default config
RUN echo tzdata tzdata/Areas select Europe | debconf-set-selections; \
  echo tzdata tzdata/Zones/Europe select Copenhagen | debconf-set-selections;
#  echo phpmyadmin phpmyadmin/app-password-confirm password | debconf-set-selections;\
#  echo phpmyadmin phpmyadmin/dbconfig-install boolean true | debconf-set-selections;\
#  echo phpmyadmin phpmyadmin/mysql/admin-pass password ${MYSQL_PASS} | debconf-set-selections;\
#  echo phpmyadmin phpmyadmin/mysql/app-pass password ${MYSQL_PASS} | debconf-set-selections;\
#  echo phpmyadmin phpmyadmin/reconfigure-webserver multiselect nginx | debconf-set-selections;

# nginx is not supported, install apache + phpmyadmin on db?
# FIXME: tcp/ip host

# Update and install
RUN apt-get -q -y update;\
  apt-get -q -y install wget;\
  wget --quiet -O - http://www.dotdeb.org/dotdeb.gpg | apt-key add -

ADD ./add-files/dotdeb.list /etc/apt/sources.list.d/dotdeb.list

RUN apt-get -q -y update;\
  apt-get -q -y upgrade;\
  apt-get -q -y install ssh ssmtp vim pwgen locales nginx php5-fpm php5-cli php5-curl php5-gd php5-imagick php5-intl php5-mcrypt php5-mysql php5-redis php5-xdebug php5-xsl php-pear php5-dev mercurial redis-tools heirloom-mailx

# Locales
RUN sed -i -e 's/# da_DK.UTF-8/da_DK.UTF-8/' /etc/locale.gen;\
  sed -i -e 's/# en_DK.UTF-8/de_DE.UTF-8/' /etc/locale.gen;\
  sed -i -e 's/# de_DE.UTF-8/de_DE.UTF-8/' /etc/locale.gen;\
  sed -i -e 's/# fi_FI.UTF-8/fi_FI.UTF-8/' /etc/locale.gen;\
  sed -i -e 's/# nb_NO.UTF-8/nb_NO.UTF-8/' /etc/locale.gen;\
  sed -i -e 's/# nl_NL.UTF-8/nl_NL.UTF-8/' /etc/locale.gen;\
  sed -i -e 's/# sv_SE.UTF-8/sv_SE.UTF-8/' /etc/locale.gen;\
  locale-gen

# Nginx
RUN sed -i -e 's/sendfile on;/sendfile off;/' /etc/nginx/nginx.conf
ADD ./add-files/hanzo.php-fpm.conf /etc/php5/fpm/pool.d/hanzo.php-fpm.conf
RUN mkdir /etc/nginx/ssl
ADD ./add-files/server.crt /etc/nginx/ssl/server.crt
ADD ./add-files/server.key /etc/nginx/ssl/server.key

# Composer
ADD ./add-files/composer /usr/local/bin/composer

# PHP settings
RUN sed -i -e 's/post_max_size = 8M/post_max_size = 20M/' /etc/php5/fpm/php.ini;\
  sed -i -e 's/upload_max_filesize = 2M/upload_max_filesize = 20M/' /etc/php5/fpm/php.ini;\
  sed -i -e 's/;date.timezone =/date.timezone = Europe\/Copenhagen/' /etc/php5/fpm/php.ini;\
  sed -i -e 's/;date.timezone =/date.timezone = Europe\/Copenhagen/' /etc/php5/cli/php.ini;\
  sed -i -e 's/memory_limit = 128M/memory_limit = 256M/' /etc/php5/fpm/php.ini;\
  sed -i -e 's/;error_log = php_errors.log/error_log = /var/www/logs/php.log/' /etc/php5/fpm/php.ini;\
  echo xdebug.remote_enable=1 >> /etc/php5/fpm/conf.d/20-xdebug.ini;\
  echo xdebug.remote_autostart=1 >> /etc/php5/fpm/conf.d/20-xdebug.ini;\
  echo xdebug.remote_host=172.17.42.1 >> /etc/php5/fpm/conf.d/20-xdebug.ini;\
  echo xdebug.remote_port=9000 >> /etc/php5/fpm/conf.d/20-xdebug.ini;\
  echo xdebug.remote_log=/tmp/php5-xdebug.log >> /etc/php5/fpm/conf.d/20-xdebug.ini;

# SSMTP to mailcatcher
RUN sed -i -e 's/mailhub=mail/mailhub=172.17.42.1:1025/' /etc/ssmtp/ssmtp.conf

# Root password + fix /var/www/ permissions to allow ssh pub key auth
RUN echo "root:root" | chpasswd;\
  chsh -s /bin/bash www-data;

# phpmyadmin
# file exists in /var/www (volume)
# ADD add-files/root.my.cnf /root/.my.cnf
ADD ./add-files/phpmyadmin-config.inc.php /etc/phpmyadmin/config.inc.php
ADD ./add-files/dot.bashrc /root/.bashrc

ADD ./add-files/startup-in-container.sh /opt/startup-in-container.sh
ADD ./add-files/locale /etc/default/locale

# Permissions?

ENTRYPOINT ["/opt/startup-in-container.sh"]

# So that we at somepoint can do stuff in startup-in-container.sh
CMD ["--dev"]
